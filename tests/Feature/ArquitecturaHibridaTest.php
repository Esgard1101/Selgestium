<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ArquitecturaHibridaTest extends TestCase
{
    use RefreshDatabase;

    private int $sucursalId;
    private int $personaId;
    private User $userAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolSeeder::class);
        $this->crearUsuarioAdmin();
    }

    private function crearUsuarioAdmin(): void
    {
        $this->sucursalId = DB::table('sucursal')->insertGetId([
            'descripcion' => 'Facultad Test',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->personaId = DB::table('persona')->insertGetId([
            'nombre'      => 'Admin',
            'apellido'    => 'Test',
            'dni'         => '99999999',
            'email'       => 'admin-hibrido@test.com',
            'sucursal_id' => $this->sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $this->userAdmin = User::create([
            'name'       => 'Admin Test',
            'email'      => 'admin-hibrido@test.com',
            'password'   => Hash::make('password'),
            'persona_id' => $this->personaId,
        ]);

        DB::table('rolpersona')->insert([
            'persona_id'  => $this->personaId,
            'usuario_id'  => $this->userAdmin->id,
            'rol_id'      => 9,
            'sucursal_id' => $this->sucursalId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    private function actingAsAdminConSesion(): static
    {
        return $this->actingAs($this->userAdmin)->withSession([
            'perfil_id'   => 9,
            'perfil'      => 'Administrativo',
            'sucursal_id' => $this->sucursalId,
            'persona_id'  => $this->personaId,
            'nombres'     => 'Admin',
            'apellidos'   => 'Test',
            'nrodoc'      => '99999999',
            'permisos'    => [],
        ]);
    }

    public function test_el_erp_redirige_a_login_si_no_hay_sesion(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_responde_html_completo_y_no_json_de_pantalla(): void
    {
        $response = $this->actingAsAdminConSesion()->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $this->assertStringContainsString('text/html', $response->headers->get('content-type', ''));
        $this->assertStringNotContainsString('{"title":', $response->getContent());
    }

    public function test_registro_publico_no_esta_disponible(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_la_navegacion_principal_no_expone_profile_show(): void
    {
        $response = $this->actingAsAdminConSesion()->get('/dashboard');

        $response->assertOk();
        $this->assertStringNotContainsString(route('profile.show', absolute: false), $response->getContent());
    }
}
