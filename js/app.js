// js/app.js
(function () {
  const ready = (cb) => document.readyState !== 'loading'
    ? cb()
    : document.addEventListener('DOMContentLoaded', cb);

  const updateThemeIcon = (theme) => {
    const icon = document.querySelector('[data-theme-toggle] i');
    if (!icon) return;
    icon.classList.toggle('fa-sun', theme === 'dark');
    icon.classList.toggle('fa-moon', theme !== 'dark');
  };

  const toggleTheme = () => {
    const root = document.documentElement;
    const current = root.getAttribute('data-theme') || 'light';
    const next = current === 'light' ? 'dark' : 'light';
    root.setAttribute('data-theme', next);
    localStorage.setItem('restanet-theme', next);
    updateThemeIcon(next);
  };

  const applyStoredTheme = () => {
    const stored = localStorage.getItem('restanet-theme');
    const theme = stored === 'dark' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', theme);
    updateThemeIcon(theme);
  };

  const initThemeToggle = () => {
    applyStoredTheme();
    const btn = document.querySelector('[data-theme-toggle]');
    if (btn) {
      btn.addEventListener('click', toggleTheme);
    }
  };

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

  const initCheckoutGuard = () => {
    const btnCheckout = document.getElementById('btnCheckout');
    const countEl = document.getElementById('cartCount');
    if (!btnCheckout || !countEl) return;
    const count = parseInt(countEl.textContent, 10) || 0;
    if (count >= 2) {
      btnCheckout.classList.remove('disabled');
      btnCheckout.removeAttribute('aria-disabled');
    }
  };

  const initInactivityWatcher = () => {
    const cfg = window.restanetConfig || {};
    const timeout = (cfg.sessionTimeout || 600) * 1000;
    const grace = (cfg.sessionGrace || 0) * 1000;
    let warningTimer;
    let logoutTimer;

    const heartbeat = () => {
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
      }
    };

    const scheduleTimers = () => {
      clearTimeout(warningTimer);
      clearTimeout(logoutTimer);
      warningTimer = setTimeout(showWarning, timeout);
      logoutTimer = setTimeout(() => {
        window.location.href = `${cfg.baseUrl}controllers/auth/cauth.php?a=logout&inactive=1`;
      }, timeout + grace);
    };

    ['mousemove', 'keydown', 'touchstart'].forEach((evt) => {
      document.addEventListener(evt, () => {
        heartbeat();
        scheduleTimers();
      }, { passive: true });
    });

    scheduleTimers();
  };

  const initMesasRealtime = () => {
    const grid = document.querySelector('[data-mesas-grid]');
    if (!grid) return;

    const render = (mesas) => {
      grid.innerHTML = mesas.map((mesa) => `
        <article class="mesa-card ${mesa.estado}">
          <header>
            <h3>Mesa ${mesa.numero}</h3>
            <span>${mesa.capacidad} pax</span>
          </header>
          <div class="mesa-card__estado">
            <span class="badge ${mesa.estado === 'ocupada' ? 'bg-danger' : 'bg-success'}">
              ${mesa.estado}
            </span>
          </div>
          <footer>
            <button data-mesa="${mesa.id}" data-estado="${mesa.estado === 'ocupada' ? 'disponible' : 'ocupada'}" class="btn btn-sm btn-brand w-100">
              Marcar ${mesa.estado === 'ocupada' ? 'Disponible' : 'Ocupada'}
            </button>
          </footer>
        </article>
      `).join('');
    };

    const load = () => {
      fetch(`${window.restanetConfig.baseUrl}controllers/pedidos/cmesas.php`, { credentials: 'include' })
        .then((res) => res.json())
        .then((data) => render(data.mesas))
        .catch(() => {});
    };

    grid.addEventListener('click', (evt) => {
      const btn = evt.target.closest('button[data-mesa]');
      if (!btn) return;
      const mesa = btn.getAttribute('data-mesa');
      const estado = btn.getAttribute('data-estado');
      fetch(`${window.restanetConfig.baseUrl}controllers/pedidos/cped.php?a=actualizar_mesa`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'include',
        body: `mesa_id=${encodeURIComponent(mesa)}&estado=${encodeURIComponent(estado)}`
      })
        .then((res) => res.json())
        .then(() => load());
    });

    load();
    setInterval(load, 15000);
  };

  ready(() => {
    initThemeToggle();
    initDataTables();
    initCheckoutGuard();
    initInactivityWatcher();
    initMesasRealtime();
  });
})();