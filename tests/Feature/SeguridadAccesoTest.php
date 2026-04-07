<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeguridadAccesoTest extends TestCase
{
    use RefreshDatabase;

    private int $sucursalId;
    private int $personaId;
    private User $userAdmin;
    private User $userAlumno;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolSeeder::class);
        $this->seed(\Database\Seeders\FaseSeeder::class);
        $this->crearDatosBase();
    }

    private function crearDatosBase(): void
    {
        $this->sucursalId = DB::table('sucursal')->insertGetId([
            'descripcion' => 'Facultad Test',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Persona + User Administrativo
        $this->personaId = DB::table('persona')->insertGetId([
            'nombre'      => 'Admin',
            'apellido'    => 'Test',
            'dni'         => '99999999',
            'email'       => 'admin@test.com',
            'sucursal_id' => $this->sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->userAdmin = User::create([
            'name'       => 'Admin Test',
            'email'      => 'admin@test.com',
            'password'   => Hash::make('password'),
            'persona_id' => $this->personaId,
        ]);

        DB::table('rolpersona')->insert([
            'persona_id'  => $this->personaId,
            'usuario_id'  => $this->userAdmin->id,
            'rol_id'      => 9, // Administrativo
            'sucursal_id' => $this->sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Persona + User Alumno
        $personaAlumnoId = DB::table('persona')->insertGetId([
            'nombre'      => 'Alumno',
            'apellido'    => 'Test',
            'dni'         => '88888888',
            'email'       => 'alumno@test.com',
            'sucursal_id' => $this->sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->userAlumno = User::create([
            'name'       => 'Alumno Test',
            'email'      => 'alumno@test.com',
            'password'   => Hash::make('password'),
            'persona_id' => $personaAlumnoId,
        ]);

        DB::table('rolpersona')->insert([
            'persona_id'  => $personaAlumnoId,
            'usuario_id'  => $this->userAlumno->id,
            'rol_id'      => 6, // Alumno
            'sucursal_id' => $this->sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    // ─── Test 1: Login correcto carga sesión enriquecida ─────────────────────

    public function test_login_correcto_carga_sesion_enriquecida(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect();

        $this->assertAuthenticated();

        // Verificar variables de sesión críticas
        $response->assertSessionHas('perfil_id');
        $response->assertSessionHas('sucursal_id');
        $response->assertSessionHas('persona_id');
        $response->assertSessionHas('nombres');

        // Verificar valores correctos
        $this->assertEquals(9, session('perfil_id'));
        $this->assertEquals($this->sucursalId, session('sucursal_id'));
        $this->assertEquals($this->personaId, session('persona_id'));
    }

    // ─── Test 2: Login denegado con credenciales inválidas ───────────────────

    public function test_login_denegado_credenciales_invalidas(): void
    {
        $response = $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'contraseña_incorrecta',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    // ─── Test 3: Acceso bloqueado sin autenticación ───────────────────────────

    public function test_acceso_bloqueado_sin_autenticacion(): void
    {
        $response = $this->get('/dashboard');

        // Redirige a login si no está autenticado
        $response->assertRedirect('/login');
    }

    // ─── Test 4: Acceso bloqueado cuando sesión no tiene datos de rol ─────────

    public function test_verificar_sesion_bloquea_sin_datos_de_rol(): void
    {
        // Autenticamos pero sin datos de rol en sesión (simula sesión rota)
        $this->actingAs($this->userAdmin);

        // Sin session('perfil_id') → VerificarSesion debe redirigir
        $response = $this->get('/dashboard');

        // Como la sesión no tiene perfil_id ni sucursal_id, debe redirigir a login
        $response->assertRedirect('/login');
    }

    // ─── Test 5: Bitácora registra login ─────────────────────────────────────

    public function test_bitacora_registra_login(): void
    {
        $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('bit_accesousuario', [
            'usuario_id' => $this->userAdmin->id,
            'accion'     => 'login',
        ]);
    }

    // ─── Test 6: Bitácora registra logout ────────────────────────────────────

    public function test_bitacora_registra_logout(): void
    {
        // Login primero
        $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password',
        ]);

        // Logout
        $this->post('/logout');

        $this->assertDatabaseHas('bit_accesousuario', [
            'usuario_id' => $this->userAdmin->id,
            'accion'     => 'logout',
        ]);
    }

    // ─── Test 7: Seeders funcionales ─────────────────────────────────────────

    public function test_logout_redirige_al_login(): void
    {
        $this->post('/login', [
            'email'    => 'admin@test.com',
            'password' => 'password',
        ]);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_seeders_funcionales(): void
    {
        // Ya se llamaron RolSeeder y FaseSeeder en setUp()
        // Verificar roles con IDs correctos (heredados de db_delicia)
        $this->assertDatabaseHas('rol', ['id' => 6, 'descripcion' => 'Alumno']);
        $this->assertDatabaseHas('rol', ['id' => 9, 'descripcion' => 'Administrativo']);
        $this->assertDatabaseHas('rol', ['id' => 10, 'descripcion' => 'Unidad de Investigacion']);
        $this->assertDatabaseHas('rol', ['id' => 11, 'descripcion' => 'Comite Cientifico']);
        $this->assertDatabaseHas('rol', ['id' => 12, 'descripcion' => 'Decanato']);

        // Verificar que hay fases cargadas
        $totalFases = DB::table('fase')->count();
        $this->assertGreaterThanOrEqual(1, $totalFases, 'FaseSeeder debe cargar al menos una fase');

        // Verificar que persona y user están vinculados
        $this->assertDatabaseHas('users', [
            'email'      => 'admin@test.com',
            'persona_id' => $this->personaId,
        ]);
    }
}
