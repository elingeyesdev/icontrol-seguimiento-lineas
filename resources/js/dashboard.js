/**
 * Panel de control operativo — monitoreo GPS en vivo.
 * Requiere el contenedor #dashboardApp con data-positions-url y data-route-base.
 */

const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;',
}[character]));

const hasGpsReport = (unit) => Boolean(unit && unit.ultima_actualizacion && unit.ultima_actualizacion !== 'Sin reporte');

const initDashboard = () => {
    const root = document.getElementById('dashboardApp');
    const mapElement = document.getElementById('mapaMonitoreo');

    if (!root || !mapElement || root.dataset.dashboardInitialized === 'true') {
        return;
    }

    if (typeof L === 'undefined') {
        const alert = document.getElementById('dashboardLiveAlert');
        if (alert) {
            alert.textContent = 'No se pudo cargar la librería del mapa.';
            alert.classList.remove('d-none');
        }
        return;
    }

    root.dataset.dashboardInitialized = 'true';

    const endpoint = root.dataset.positionsUrl;
    const routeBase = root.dataset.routeBase || '/control-recorrido';
    const alertElement = document.getElementById('dashboardLiveAlert');
    const counterElement = document.getElementById('contadorUnidades');
    const updatedElement = document.getElementById('dashboardUpdatedAt');
    const listElement = document.getElementById('listaUnidadesContainer');
    const tableBody = document.getElementById('tablaFlotaBody');

    const map = L.map(mapElement, { scrollWheelZoom: true }).setView([-17.783, -63.182], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    const markers = {};
    let selectedId = null;
    let units = [];

    const showError = (message) => {
        if (!alertElement) {
            return;
        }
        alertElement.textContent = message;
        alertElement.classList.remove('d-none');
    };

    const clearError = () => alertElement?.classList.add('d-none');

    const renderList = () => {
        if (!listElement) {
            return;
        }

        if (units.length === 0) {
            listElement.innerHTML = `
                <div class="empty-state py-4">
                    <i class="bi bi-bus-front" aria-hidden="true"></i>
                    <div class="empty-state-title">Sin unidades activas</div>
                    <p class="mb-0 small">No hay asignaciones en curso para la fecha actual.</p>
                </div>`;
            return;
        }

        listElement.innerHTML = units.map((unit) => {
            const isSelected = String(unit.asignacion_id) === selectedId;
            const gps = hasGpsReport(unit);
            const state = unit.estado === 'en_curso' ? 'En curso' : escapeHtml(unit.estado);

            return `
                <button type="button"
                        class="unit-card w-100 text-start ${isSelected ? 'is-selected' : ''}"
                        data-select-unit="${unit.asignacion_id}">
                    <span class="d-flex justify-content-between align-items-center gap-2 mb-1">
                        <span class="fw-semibold">${escapeHtml(unit.placa)} <span class="text-muted small">Int. ${escapeHtml(unit.interno)}</span></span>
                        <span class="badge-status ${unit.estado === 'en_curso' ? 'badge-status--progress' : 'badge-status--pending'}">${state}</span>
                    </span>
                    <span class="d-block small text-muted text-truncate">
                        <i class="bi bi-person me-1" aria-hidden="true"></i>${escapeHtml(unit.conductor)}
                    </span>
                    <span class="d-flex justify-content-between align-items-center gap-2 small mt-1">
                        <span class="${gps ? 'text-muted' : 'text-warning-emphasis'}">
                            ${gps
                                ? `<i class="bi bi-speedometer2 me-1" aria-hidden="true"></i>${escapeHtml(unit.velocidad)} km/h`
                                : '<i class="bi bi-wifi-off me-1" aria-hidden="true"></i>Sin reporte GPS'}
                        </span>
                        <span class="text-muted">${gps ? escapeHtml(unit.ultima_actualizacion) : '—'}</span>
                    </span>
                </button>`;
        }).join('');
    };

    const selectUnit = (assignmentId) => {
        selectedId = String(assignmentId);

        const unit = units.find((item) => String(item.asignacion_id) === selectedId);

        renderList();

        if (!unit) {
            return;
        }

        if (hasGpsReport(unit)) {
            map.setView([unit.latitud, unit.longitud], 15);
        }
    };

    const renderMarkers = () => {
        const activeIds = units.map((unit) => String(unit.asignacion_id));

        Object.keys(markers).forEach((id) => {
            if (!activeIds.includes(id)) {
                map.removeLayer(markers[id]);
                delete markers[id];
            }
        });

        units.forEach((unit) => {
            if (!hasGpsReport(unit)) {
                if (markers[unit.asignacion_id]) {
                    map.removeLayer(markers[unit.asignacion_id]);
                    delete markers[unit.asignacion_id];
                }
                return;
            }

            if (markers[unit.asignacion_id]) {
                markers[unit.asignacion_id].setLatLng([unit.latitud, unit.longitud]);
                return;
            }

            const icon = L.divIcon({
                className: 'custom-bus-marker',
                html: '<i class="bi bi-bus-front" aria-hidden="true"></i>',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
            });

            markers[unit.asignacion_id] = L.marker([unit.latitud, unit.longitud], { icon })
                .bindPopup(`
                    <strong>${escapeHtml(unit.placa)} (Int. ${escapeHtml(unit.interno)})</strong><br>
                    Conductor: ${escapeHtml(unit.conductor)}<br>
                    Ruta: ${escapeHtml(unit.ruta)}<br>
                    Velocidad: ${escapeHtml(unit.velocidad)} km/h<br>
                    Reporte: ${escapeHtml(unit.ultima_actualizacion)}
                `)
                .on('click', () => selectUnit(unit.asignacion_id))
                .addTo(map);
        });
    };

    const renderTable = () => {
        if (!tableBody) {
            return;
        }

        if (units.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7">
                        <div class="empty-state py-4">
                            <i class="bi bi-bus-front" aria-hidden="true"></i>
                            <div class="empty-state-title">Sin unidades activas</div>
                            <p class="mb-0 small">No hay asignaciones en curso para la fecha actual.</p>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        tableBody.innerHTML = units.map((unit) => {
            const gps = hasGpsReport(unit);
            const stateClass = unit.estado === 'en_curso' ? 'badge-status--progress' : 'badge-status--pending';

            return `
                <tr>
                    <td>
                        <span class="fw-semibold">${escapeHtml(unit.placa)}</span>
                        <span class="text-muted small d-block">Interno ${escapeHtml(unit.interno)}</span>
                    </td>
                    <td>${escapeHtml(unit.conductor)}</td>
                    <td>${escapeHtml(unit.ruta)}</td>
                    <td><span class="badge-status ${stateClass}">${escapeHtml(unit.estado)}</span></td>
                    <td>${gps ? `${escapeHtml(unit.velocidad)} km/h` : '<span class="text-warning-emphasis">Sin reporte GPS</span>'}</td>
                    <td class="text-muted small">${gps ? escapeHtml(unit.ultima_actualizacion) : 'Sin reporte GPS'}</td>
                    <td>
                        <div class="table-actions">
                            <button type="button" class="btn btn-outline-brand btn-sm-action" data-select-unit="${unit.asignacion_id}" title="Ver en el mapa">
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                <span class="visually-hidden">Ver ${escapeHtml(unit.placa)} en el mapa</span>
                            </button>
                            <a href="${routeBase}/${unit.asignacion_id}" class="btn btn-wine btn-sm-action" title="Abrir control de recorrido">
                                <i class="bi bi-signpost-split" aria-hidden="true"></i>
                                <span class="visually-hidden">Control de recorrido de ${escapeHtml(unit.placa)}</span>
                            </a>
                        </div>
                    </td>
                </tr>`;
        }).join('');
    };

    const updatePositions = async () => {
        try {
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`Respuesta ${response.status}`);
            }

            const data = await response.json();
            units = Array.isArray(data.unidades) ? data.unidades : [];

            clearError();

            const withoutGps = units.filter((unit) => !hasGpsReport(unit)).length;

            if (counterElement) {
                counterElement.textContent = `${units.length} unidades · ${withoutGps} sin reporte GPS`;
            }

            if (updatedElement) {
                updatedElement.textContent = `Actualizado a las ${new Date().toLocaleTimeString('es-BO', { hour12: false })}`;
            }

            renderList();
            renderMarkers();
            renderTable();

            if (selectedId) {
                const selected = units.find((unit) => String(unit.asignacion_id) === selectedId);

                if (!selected) {
                    selectedId = null;
                }
            } else if (units.length > 0) {
                selectUnit(units[0].asignacion_id);
            }
        } catch (error) {
            console.error('Error al actualizar el monitoreo GPS del panel:', error);
            showError('No se pudieron actualizar las posiciones GPS. Se reintentará automáticamente.');
        }
    };

    root.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-select-unit]');

        if (!trigger) {
            return;
        }

        selectUnit(trigger.dataset.selectUnit);
    });

    updatePositions();
    window.setInterval(updatePositions, 5000);
};

document.addEventListener('DOMContentLoaded', initDashboard);
