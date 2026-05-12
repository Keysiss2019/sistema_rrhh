@extends('layouts.app')



@section('content')
<div class="container py-4">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" id="success-alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

   <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Configuración de Firmas</h3>
        <p class="text-muted small">Administra el orden y estado de las aprobaciones</p>
    </div>
    <div class="d-flex gap-2">
        {{-- BOTÓN PARA REGRESAR --}}
        <a href="{{ route('horas_extras.gestion') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver a Gestión
        </a>

        {{-- BOTÓN NUEVO PASO --}}
        <button class="btn btn-success shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAgregarFirma">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Paso
        </button>
    </div>
</div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Orden</th>
                        <th>Nombre del Paso</th>
                        <th>Etiqueta Corta</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pasosConfigurados as $p)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $p->orden }}</td>
                        <td>{{ $p->nombre_paso }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $p->nombre_corto }}</span></td>
                        <td class="text-center">
                            <form action="{{ route('configuracion.toggle', $p->id) }}" method="POST">
                                @csrf
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" onchange="this.form.submit()" {{ $p->activo ? 'checked' : '' }}>
                                </div>
                            </form>
                        </td>
                        <td class="text-center">
  
    <button type="button" 
            class="btn btn-outline-warning btn-sm btn-edit-firma"
            data-id="{{ $p->id }}"
            data-nombre="{{ $p->nombre_paso }}"
            data-corto="{{ $p->nombre_corto }}"
            data-orden="{{ $p->orden }}">
        <i class="fa-solid fa-pen-to-square"></i>
    </button>

    <form action="{{ route('configuracion.destroy', $p->id) }}" method="POST" class="d-inline delete-form-firma">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-firma">
            <i class="fa-solid fa-trash"></i>
        </button>
    </form>
</td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('horas_extras.modal_editar_firma_js')
@include('horas_extras.modal_agregar')

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Auto-cierre de alertas
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 3000);
    }

    // 2. ELIMINAR: Corregido para SweetAlert2
   document.addEventListener('DOMContentLoaded', function () {
    // ELIMINAR
    document.querySelectorAll('.btn-delete-firma').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = this.closest('form');
            Swal.fire({
                title: '¿Eliminar paso?',
                text: "Esta acción borrará la configuración del nivel de firma.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // EDITAR
    const modalEl = document.getElementById('modalEditarFirma');
    const editModal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.btn-edit-firma').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_nombre_paso').value = this.dataset.nombre;
            document.getElementById('edit_nombre_corto').value = this.dataset.corto;
            document.getElementById('edit_orden_paso').value = this.dataset.orden;
            
            document.getElementById('formEditarFirma').action = `/configuracion-firmas/actualizar/${this.dataset.id}`;
            editModal.show();
        });
    });

    // Limpieza de pantalla oscura al cerrar
    modalEl.addEventListener('hidden.bs.modal', function () {
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    });
});

    // 3. EDITAR: Carga dinámica y corrección de ruta
    const modalElement = document.getElementById('modalEditarFirma');
    const editForm = document.getElementById('formEditarFirma');
    const editModal = new bootstrap.Modal(modalElement);

    document.querySelectorAll('.btn-edit-firma').forEach(btn => {
        btn.addEventListener('click', function () {
            // Llenar campos
            document.getElementById('edit_nombre_paso').value = this.dataset.nombre;
            document.getElementById('edit_nombre_corto').value = this.dataset.corto;
            document.getElementById('edit_orden_paso').value = this.dataset.orden;

            // RUTA CORREGIDA: debe incluir "/actualizar/" según tu web.php
            editForm.action = `/configuracion-firmas/actualizar/${this.dataset.id}`;

            editModal.show();
        });
    });

    // SOLUCIÓN AL BLOQUEO DE PANTALLA:
    // Al cerrarse el modal, eliminamos manualmente cualquier rastro del fondo oscuro
    modalElement.addEventListener('hidden.bs.modal', function () {
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    });
});
</script>
@endsection