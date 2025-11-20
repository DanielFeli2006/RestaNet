// Inicialización DataTables para cualquier tabla con clase .datatable
 (function(){
  document.addEventListener('DOMContentLoaded', function(){
    // DataTables init
    if (typeof DataTable !== 'undefined') {
      document.querySelectorAll('table.datatable').forEach(function(tbl){
        new DataTable(tbl, {
          perPage: 10,
          layout: { top: '{search}', bottom: '{info}{pager}' },
          labels: {
            placeholder: 'Buscar...', perPage: '{select} registros por página',
            noRows: 'No hay datos para mostrar', info: 'Mostrando {start} a {end} de {rows} registros'
          }
        });
      });
    }

    // Carrito: habilitar dinámicamente botón checkout si cambia conteo (por degradación lo calculamos desde DOM)
    const btnCheckout = document.getElementById('btnCheckout');
    const countEl = document.getElementById('cartCount');
    if (btnCheckout && countEl) {
      const count = parseInt(countEl.textContent, 10) || 0;
      if (count >= 2) {
        btnCheckout.classList.remove('disabled');
        btnCheckout.removeAttribute('aria-disabled');
      }
    }
  });
})();