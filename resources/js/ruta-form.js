/**
 * Configuración de paradas de ida y vuelta en el formulario de rutas.
 * Los datos iniciales se reciben por data-* para no incrustar scripts en Blade.
 */
const initRutaForm = () => {
    const config = document.getElementById('rutaFormConfig');

    if (!config) {
        return;
    }

    const listaDisponibles = document.getElementById('listaDisponibles');
    const listaIda = document.getElementById('listaIda');
    const listaVuelta = document.getElementById('listaVuelta');
    const hiddenIdaInputs = document.getElementById('hiddenIdaInputs');
    const hiddenVueltaInputs = document.getElementById('hiddenVueltaInputs');
    const emptyIdaMsg = document.getElementById('emptyIdaMsg');
    const emptyVueltaMsg = document.getElementById('emptyVueltaMsg');
    const contadorIda = document.getElementById('contadorIda');
    const contadorVuelta = document.getElementById('contadorVuelta');
    const badgeIdaCount = document.getElementById('badgeIdaCount');
    const badgeVueltaCount = document.getElementById('badgeVueltaCount');
    const searchInput = document.getElementById('searchParada');
    const noParadasMsg = document.getElementById('noParadasMsg');

    if (!listaDisponibles || !listaIda || !listaVuelta) {
        return;
    }

    const parseList = (value) => {
        try {
            return JSON.parse(value || '[]');
        } catch {
            return [];
        }
    };

    const paradasIdaExistentes = parseList(config.dataset.paradasIda);
    const paradasVueltaExistentes = parseList(config.dataset.paradasVuelta);

    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    };

    const updateOrder = (sentido) => {
        const container = sentido === 'ida' ? listaIda : listaVuelta;
        const hiddenContainer = sentido === 'ida' ? hiddenIdaInputs : hiddenVueltaInputs;
        const inputName = sentido === 'ida' ? 'paradas_ida[]' : 'paradas_vuelta[]';
        const emptyMsg = sentido === 'ida' ? emptyIdaMsg : emptyVueltaMsg;
        const badgeCounter = sentido === 'ida' ? badgeIdaCount : badgeVueltaCount;
        const headerCounter = sentido === 'ida' ? contadorIda : contadorVuelta;

        const items = container.querySelectorAll('.parada-item');
        hiddenContainer.innerHTML = '';

        items.forEach((item, index) => {
            const badge = item.querySelector('.orden-badge');

            if (badge) {
                badge.textContent = index + 1;
            }

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName;
            input.value = item.getAttribute('data-id');
            hiddenContainer.appendChild(input);
        });

        if (badgeCounter) badgeCounter.textContent = items.length;
        if (headerCounter) headerCounter.textContent = items.length;
        emptyMsg?.classList.toggle('d-none', items.length !== 0);
    };

    const addParada = (sentido, id, nombre, referencia) => {
        const container = sentido === 'ida' ? listaIda : listaVuelta;
        const existente = container.querySelector('.parada-item[data-id="' + id + '"]');

        if (existente) {
            existente.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }

        const badgeClass = sentido === 'ida' ? 'badge-status--progress' : 'badge-status--pending';
        const count = container.querySelectorAll('.parada-item').length + 1;

        const div = document.createElement('div');
        div.className = 'parada-item d-flex align-items-center justify-content-between gap-2 p-2 mb-1 rounded-2 bg-white border';
        div.setAttribute('data-id', id);
        div.innerHTML = `
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <span class="badge orden-badge ${badgeClass}">${count}</span>
                <div class="text-truncate">
                    <div class="fw-semibold text-truncate small">${escapeHtml(nombre)}</div>
                    ${referencia ? `<small class="text-muted text-truncate d-block">${escapeHtml(referencia)}</small>` : ''}
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <button type="button" class="btn btn-outline-brand btn-icon btn-orden btn-up" title="Subir" aria-label="Subir parada">
                    <i class="bi bi-arrow-up" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-outline-brand btn-icon btn-orden btn-down" title="Bajar" aria-label="Bajar parada">
                    <i class="bi bi-arrow-down" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-wine btn-icon btn-orden btn-remove" title="Quitar" aria-label="Quitar parada">
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                </button>
            </div>`;

        container.appendChild(div);
        updateOrder(sentido);
    };

    const moveUp = (item, sentido) => {
        const prev = item.previousElementSibling;

        if (prev && prev.classList.contains('parada-item')) {
            item.parentNode.insertBefore(item, prev);
            updateOrder(sentido);
        }
    };

    const moveDown = (item, sentido) => {
        const next = item.nextElementSibling;

        if (next && next.classList.contains('parada-item')) {
            item.parentNode.insertBefore(next, item);
            updateOrder(sentido);
        }
    };

    paradasIdaExistentes.forEach((parada) => {
        addParada('ida', parada.id, parada.nombre, parada.referencia || '');
    });

    paradasVueltaExistentes.forEach((parada) => {
        addParada('vuelta', parada.id, parada.nombre, parada.referencia || '');
    });

    updateOrder('ida');
    updateOrder('vuelta');

    listaDisponibles.addEventListener('click', (event) => {
        const btnIda = event.target.closest('.btn-add-ida');
        const btnVuelta = event.target.closest('.btn-add-vuelta');

        if (!btnIda && !btnVuelta) {
            return;
        }

        const item = (btnIda || btnVuelta).closest('.parada-catalogo');

        if (!item) {
            return;
        }

        const { id, nombre, referencia = '' } = item.dataset;

        if (btnIda) {
            addParada('ida', id, nombre, referencia);
        } else {
            addParada('vuelta', id, nombre, referencia);
        }
    });

    [listaIda, listaVuelta].forEach((container) => {
        container.addEventListener('click', (event) => {
            const btn = event.target.closest('button');
            const item = btn?.closest('.parada-item');

            if (!btn || !item) {
                return;
            }

            const sentido = container === listaIda ? 'ida' : 'vuelta';

            if (btn.classList.contains('btn-up')) {
                moveUp(item, sentido);
            } else if (btn.classList.contains('btn-down')) {
                moveDown(item, sentido);
            } else if (btn.classList.contains('btn-remove')) {
                item.remove();
                updateOrder(sentido);
            }
        });
    });

    searchInput?.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const items = listaDisponibles.querySelectorAll('.parada-catalogo');
        let visibleCount = 0;

        items.forEach((item) => {
            const nombre = (item.getAttribute('data-nombre') || '').toLowerCase();
            const referencia = (item.getAttribute('data-referencia') || '').toLowerCase();
            const coincide = nombre.includes(query) || referencia.includes(query);

            item.classList.toggle('d-none', !coincide);
            item.classList.toggle('d-flex', coincide);

            if (coincide) {
                visibleCount++;
            }
        });

        noParadasMsg?.classList.toggle('d-none', visibleCount !== 0);
    });
};

document.addEventListener('DOMContentLoaded', initRutaForm);
