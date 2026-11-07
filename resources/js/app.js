import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Control accesible del sidebar móvil.
 * Una sola implementación; el layout solo expone los controles.
 */
const initSidebar = () => {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const closeButton = document.getElementById('sidebarClose');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (!sidebar || !toggle) {
        return;
    }

    const setOpen = (open) => {
        sidebar.classList.toggle('is-open', open);
        document.body.classList.toggle('sidebar-open', open);
        backdrop?.classList.toggle('is-visible', open);
        toggle.setAttribute('aria-expanded', String(open));
        backdrop?.setAttribute('aria-hidden', String(!open));
    };

    toggle.addEventListener('click', () => {
        setOpen(!sidebar.classList.contains('is-open'));
    });

    closeButton?.addEventListener('click', () => setOpen(false));
    backdrop?.addEventListener('click', () => setOpen(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 991) {
            setOpen(false);
        }
    });
};

/**
 * Confirmación declarativa para acciones destructivas:
 * <form data-confirm="¿Eliminar este registro?">…</form>
 * Evita manejadores inline en las vistas.
 */
const initConfirmations = () => {
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm]');

        if (form && !window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
};

/**
 * Impresión declarativa: <button data-print>Imprimir</button>
 */
const initPrintActions = () => {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-print]');

        if (trigger) {
            event.preventDefault();
            window.print();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initConfirmations();
    initPrintActions();
});
