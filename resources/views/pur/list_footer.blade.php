<script>
    // Inicializa la tabla con DataTables (si usas esa librería)
    if ($.fn.DataTable.isDataTable('#tabla-pur')) {
        $('#tabla-pur').DataTable().destroy();
    }

    $('#tabla-pur').DataTable({
        responsive: true,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        }
    });

    function editarPur(id) {
        // Esta función abre el formulario de edición en el sistema SPA
        setRun('/pur/' + id + '/edit', '', 'frame', 'Registrar Expediente', 'loading');
    }

    function eliminarPur(id) {
        Swal.fire({
            title: '¿Eliminar expediente?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí iría tu lógica de borrado AJAX
                console.log("Eliminando ID: " + id);
            }
        });
    }
</script>