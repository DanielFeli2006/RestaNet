// js/app.js - Restanet Modern UI (CORREGIDO)
// 
// CAMBIOS REALIZADOS:
// 1. FIX: initMesasRealtime() - Añadido csrf_token en el body de la petición
//    POST para cambiar estado de mesa. Sin este token, el servidor rechazaba
//    todas las peticiones con error 403 "Token CSRF inválido".
// 2. FIX: Cambiado endpoint de actualización de mesas de cped.php?a=actualizar_mesa
//    a cmesas.php?a=update para usar el controlador correcto.
// 3. FIX: Añadido manejo de errores más robusto con mensajes al usuario.
//
(function () {
  'use strict';

  const ready = (cb) => document.readyState !== 'loading'
    ? cb()
    : document.addEventListener('DOMContentLoaded', cb);

  // Initialize DataTables
  const initDataTables = () => {
    if (typeof DataTable === 'undefined') return;
    document.querySelectorAll('table.datatable').forEach((tbl) => {
      new DataTable(tbl, {
        perPage: 10,
        layout: { top: '{search}', bottom: '{info}{pager}' },
        labels: {
          placeholder: 'Buscar...',
          perPage: '{select} registros por página',
          noRows: 'No hay datos para mostrar',
          info: 'Mostrando {start} a {end} de {rows} registros'
        }
      });
    });
  };

  // Checkout guard for minimum items
  const initCheckoutGuard = () => {
    const btnCheckout = document.getElementById('btnCheckout');
    const countEl = document.getElementById('cartCount');
    if (!btnCheckout || !countEl) return;
    const count = parseInt(countEl.textContent, 10) || 0;
    if (count >= 2) {
      btnCheckout.classList.remove('disabled');
      btnCheckout.removeAttribute('aria-disabled');
      btnCheckout.removeAttribute('tabindex');
    }
  };

  // Session inactivity watcher
  const initInactivityWatcher = () => {
    const cfg = window.restanetConfig || {};
    const timeout = (cfg.sessionTimeout || 600) * 1000;
    const grace = (cfg.sessionGrace || 0) * 1000;
    let warningTimer;
    let logoutTimer;
    let isActive = true;

    const heartbeat = () => {
      if (!isActive) return;
      fetch(`${cfg.baseUrl}controllers/auth/cauth.php?a=heartbeat`, {
        method: 'POST',
        credentials: 'include'
      })
        .then((res) => res.ok ? res.json() : Promise.reject())
        .then((data) => {
          if (data.timedOut) {
            window.location.href = `${cfg.baseUrl}views/auth/vlogin.php?expired=1`;
          }
        })
        .catch(() => {});
    };

    const showWarning = () => {
      const toast = document.getElementById('timeoutWarning');
      if (toast) {
        toast.hidden = false;
        toast.classList.add('show');
      }
    };

    const hideWarning = () => {
      const toast = document.getElementById('timeoutWarning');
      if (toast) {
        toast.hidden = true;
        toast.classList.remove('show');
      }
    };

    const scheduleTimers = () => {
      clearTimeout(warningTimer);
      clearTimeout(logoutTimer);
      hideWarning();
      warningTimer = setTimeout(showWarning, timeout);
      logoutTimer = setTimeout(() => {
        window.location.href = `${cfg.baseUrl}controllers/auth/cauth.php?a=logout&inactive=1`;
      }, timeout + grace);
    };

    // Throttle activity events
    let lastActivity = Date.now();
    const handleActivity = () => {
      const now = Date.now();
      if (now - lastActivity > 30000) { // 30 seconds throttle
        lastActivity = now;
        heartbeat();
        scheduleTimers();
      }
    };

    ['mousemove', 'keydown', 'touchstart', 'click', 'scroll'].forEach((evt) => {
      document.addEventListener(evt, handleActivity, { passive: true });
    });

    scheduleTimers();
  };

  // Real-time table status (CORREGIDO)
  const initMesasRealtime = () => {
    const grid = document.querySelector('[data-mesas-grid]');
    if (!grid) return;

    const render = (mesas) => {
      if (!mesas || mesas.length === 0) {
        grid.innerHTML = `
          <div class="text-center py-3">
            <i class="fa-solid fa-table-cells-large fa-2x text-muted opacity-25 mb-2"></i>
            <p class="text-muted small mb-0">No hay mesas configuradas</p>
          </div>
        `;
        return;
      }

      grid.innerHTML = mesas.map((mesa) => `
        <article class="mesa-card ${mesa.estado}">
          <header class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Mesa ${mesa.numero}</h5>
            <span class="badge bg-secondary-subtle text-secondary-emphasis">${mesa.capacidad} pax</span>
          </header>
          <div class="mesa-card__estado my-2">
            <span class="badge ${mesa.estado === 'ocupada' ? 'bg-danger-subtle text-danger-emphasis' : 'bg-success-subtle text-success-emphasis'} w-100 py-2">
              <i class="fa-solid ${mesa.estado === 'ocupada' ? 'fa-user-clock' : 'fa-check-circle'} me-1"></i>
              ${mesa.estado === 'ocupada' ? 'Ocupada' : 'Disponible'}
            </span>
          </div>
          <footer>
            <button data-mesa="${mesa.id}" data-estado="${mesa.estado === 'ocupada' ? 'disponible' : 'ocupada'}" 
                    class="btn btn-sm ${mesa.estado === 'ocupada' ? 'btn-outline-success' : 'btn-brand'} w-100">
              <i class="fa-solid ${mesa.estado === 'ocupada' ? 'fa-circle-check' : 'fa-user-plus'} me-1"></i>
              ${mesa.estado === 'ocupada' ? 'Liberar' : 'Ocupar'}
            </button>
          </footer>
        </article>
      `).join('');
    };

    const load = () => {
      const cfg = window.restanetConfig || {};
      fetch(`${cfg.baseUrl}controllers/pedidos/cmesas.php`, { credentials: 'include' })
        .then((res) => res.json())
        .then((data) => render(data.mesas || []))
        .catch(() => {
          grid.innerHTML = `
            <div class="text-center py-3 text-muted">
              <i class="fa-solid fa-exclamation-circle me-1"></i>
              Error al cargar mesas
            </div>
          `;
        });
    };

    grid.addEventListener('click', (evt) => {
      const btn = evt.target.closest('button[data-mesa]');
      if (!btn) return;
      const mesa = btn.getAttribute('data-mesa');
      const estado = btn.getAttribute('data-estado');
      const cfg = window.restanetConfig || {};
      
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
      
      // =====================================================
      // FIX: Obtener CSRF token del meta tag o de restanetConfig
      // =====================================================
      const csrfToken = cfg.csrfToken 
        || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
        || '';
      
      // FIX: Incluir csrf_token en el body de la petición
      // Sin este token, el servidor rechaza la petición con 403
      fetch(`${cfg.baseUrl}controllers/pedidos/cmesas.php?a=update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'include',
        body: `mesa_id=${encodeURIComponent(mesa)}&estado=${encodeURIComponent(estado)}&csrf_token=${encodeURIComponent(csrfToken)}`
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.ok) {
            load(); // Recargar mesas
          } else {
            // FIX: Mostrar error al usuario en vez de fallar silenciosamente
            const errorMsg = data.error || 'Error al actualizar mesa';
            console.error('Error actualizando mesa:', errorMsg);
            // Mostrar toast o alerta simple
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `<i class="fa-solid fa-exclamation-triangle me-2"></i>${errorMsg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            document.body.appendChild(alertDiv);
            setTimeout(() => alertDiv.remove(), 5000);
            load(); // Recargar de todos modos para reflejar estado real
          }
        })
        .catch(() => load());
    });

    load();
    setInterval(load, 15000);
  };

  // Add page load animations
  const initAnimations = () => {
    // Animate cards on page load
    const cards = document.querySelectorAll('.card, .alert');
    cards.forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      setTimeout(() => {
        card.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, 50 + (index * 50));
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
          position: absolute;
          width: ${size}px;
          height: ${size}px;
          left: ${x}px;
          top: ${y}px;
          background: rgba(255,255,255,0.3);
          border-radius: 50%;
          transform: scale(0);
          animation: ripple 0.6s linear;
          pointer-events: none;
        `;
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
      });
    });
  };

  // Add ripple animation CSS
  const addRippleStyles = () => {
    if (document.getElementById('ripple-styles')) return;
    const style = document.createElement('style');
    style.id = 'ripple-styles';
    style.textContent = `
      @keyframes ripple {
        to {
          transform: scale(4);
          opacity: 0;
        }
      }
    `;
    document.head.appendChild(style);
  };

  // Smooth scroll for anchor links
  const initSmoothScroll = () => {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  };

  // Initialize all modules
  ready(() => {
    addRippleStyles();
    initDataTables();
    initCheckoutGuard();
    initInactivityWatcher();
    initMesasRealtime();
    initAnimations();
    initSmoothScroll();
  });
})();
