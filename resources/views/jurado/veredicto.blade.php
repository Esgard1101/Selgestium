<x-app-layout>
    <!-- Tailwind and Alpine JS already provided by Jetstream -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        :root {
            --blue-deep: var(--color-primary-dark);
            --blue-mid: var(--color-primary);
            --blue-bright: var(--color-primary-light);
            --yellow: var(--color-accent);
            --white: var(--color-surface);
            --gray-light: var(--color-bg);
            --gray-mid: var(--color-border);
            --text-dark: var(--color-text-primary);
            --muted: var(--color-text-secondary);
            --success: var(--color-success);
            --warning: var(--color-warning);
            --danger: var(--color-danger);
        }

        .page-header { background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-mid) 62%); padding: 28px 34px 36px; border-radius: 16px; margin-bottom: 24px; position: relative; overflow: hidden; }
        .page-header::before { content: '🗳️'; position: absolute; right: 20px; top: 10px; font-size: 130px; opacity: 0.07; }
        .breadcrumb { font-size: 12px; color: var(--gray-mid); margin-bottom: 6px; }
        .breadcrumb span { color: var(--yellow); }
        .page-title { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 27px; color: var(--white); margin-bottom: 5px; }
        .page-desc { font-size: 13px; color: var(--gray-mid); max-width: 700px; line-height: 1.5; }

        .verdict-card { background: var(--white); border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0, 30, 90, 0.05); border: 1px solid var(--color-border); margin-bottom: 24px; }
        .progress-strip { background: #F8FAFC; border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .progress-info { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 800; color: var(--text-dark); }
        
        .avatars { display: flex; align-items: center; gap: 8px; }
        .avatar-badge { display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; }
        .avatar-badge.approved { background: #E6F9F0; color: var(--success); border: 1px solid #A3E2C9; }
        .avatar-badge.observed { background: #FFF4E5; color: var(--warning); border: 1px solid #FCE2C2; }
        .avatar-badge.pending { background: #F1F5F9; color: #64748B; border: 1px solid #CBD5E1; }

        .btn-action { padding: 14px 28px; border-radius: 12px; font-size: 14px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s ease; }
        .btn-approve { background-color: var(--success); color: white; border: 1px solid rgba(0,0,0,0.1); }
        .btn-approve:hover { background-color: #037A53; }
        .btn-observe { background-color: var(--warning); color: white; border: 1px solid rgba(0,0,0,0.1); }
        .btn-observe:hover { background-color: #D97706; }

        /* Modal Styles */
        .modal-backdrop { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 9999; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .modal-backdrop.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: var(--white); border-radius: 16px; padding: 28px; width: 100%; max-width: 440px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .modal-backdrop.active .modal-content { transform: scale(1); }
        .modal-title { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 18px; color: var(--text-dark); margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .modal-desc { font-size: 13px; color: var(--muted); line-height: 1.5; margin-bottom: 18px; }
        
        .otp-input { letter-spacing: 6px; font-size: 20px; text-align: center; font-weight: 800; padding: 10px; border-radius: 10px; border: 2px solid var(--color-border); width: 100%; margin-bottom: 16px; }
        .otp-input:focus { border-color: var(--blue-mid); outline: none; box-shadow: 0 0 0 4px rgba(0, 85, 212, 0.1); }
    </style>

    <div class="page-header">
        <div class="breadcrumb">Jurado › <span>Veredicto</span></div>
        <div class="page-title">Registro de Veredicto Consolidado</div>
        <div class="page-desc">Evaluación legal formal del expediente {{ $expediente->numero_radicacion }}.</div>
    </div>

    <div class="verdict-card">
        @php
            $aprobados = $jurados->where('aprobado', true)->count();
        @endphp
        
        <div class="progress-strip">
            <div class="progress-info">
                <i data-lucide="check-circle" style="color: var(--success);"></i>
                <span>{{ $aprobados }}/3 jurados aprobaron</span>
            </div>
            <div class="avatars">
                @foreach($jurados as $j)
                    <div class="avatar-badge @if(is_null($j->aprobado)) pending @elseif($j->aprobado) approved @else observed @endif">
                        <i data-lucide="user"></i>
                        <span>{{ $j->nombre }} ({{ ucfirst($j->rol_jurado) }})</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl mb-4 text-sm font-bold" style="background-color: var(--color-danger-bg); color: var(--color-danger);">
                {{ $errors->first() }}
            </div>
        @endif

        @if($juradoActual && !is_null($juradoActual->aprobado))
            <div class="p-6 rounded-xl text-center" style="background-color: #F8FAFC; border: 1px dashed var(--color-border); color: var(--muted);">
                <i data-lucide="shield-check" class="mx-auto mb-2" style="width:32px; height:32px; color: var(--success);"></i>
                Ya has registrado tu veredicto para este expediente.
            </div>
        @else
            <div class="text-center py-6">
                <p class="text-sm font-bold mb-6" style="color: var(--text-dark);">Seleccione su veredicto legal para el expediente:</p>
                
                <div class="flex justify-center gap-4">
                    <button onclick="abrirModal2FA(true)" class="btn-action btn-approve">
                        <i data-lucide="thumbs-up"></i> Aprobar Proyecto
                    </button>
                    
                    <button onclick="abrirModal2FA(false)" class="btn-action btn-observe">
                        <i data-lucide="thumbs-down"></i> Registrar Observación
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal 2FA -->
    <div id="modal-2fa" class="modal-backdrop">
        <div class="modal-content">
            <div class="modal-title">
                <i data-lucide="shield-alert" style="color: var(--blue-mid);"></i> Confirmación 2FA requerida
            </div>
            <div class="modal-desc">
                Por seguridad e integridad legal, confirme la firma digital de su voto ingresando el código recibido.
            </div>
            
            <form action="{{ route('jurado.veredicto.store') }}" method="POST">
                @csrf
                <input type="hidden" name="expediente_id" value="{{ $expediente->id }}">
                <input type="hidden" name="voto" id="voto-seleccionado">
                
                <input type="text" name="codigo_2fa" placeholder="••••••" maxlength="6" required class="otp-input">
                
                <div class="flex gap-3">
                    <button type="button" onclick="cerrarModal2FA()" class="btn-action flex-1 text-center justify-center" style="background:#F1F5F9; color:#475569; padding:10px;">Cancelar</button>
                    <button type="submit" class="btn-action flex-1 text-center justify-center" style="background: var(--blue-mid); color: white; padding:10px;">Confirmar Voto</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModal2FA(esAprobado) {
            document.getElementById('voto-seleccionado').value = esAprobado ? 1 : 0;
            
            // AJAX para gatillar generación de OTP en backend
            fetch("{{ route('jurado.otp.generar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ expediente_id: {{ $expediente->id }} })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('modal-2fa').classList.add('active');
                } else {
                    alert("Error al generar código: " + data.message);
                }
            });
        }

        function cerrarModal2FA() {
            document.getElementById('modal-2fa').classList.remove('active');
        }

        lucide.createIcons();
    </script>
</x-app-layout>
