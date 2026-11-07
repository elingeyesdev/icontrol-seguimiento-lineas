/**
 * Mapas de paradas: formulario (edición), modal del índice y vista de detalle.
 * Solo se inicializa el mapa cuyo contenedor exista en la página.
 */
const escapeHtml = (text) => {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(text ?? ''));
    return div.innerHTML;
};

const initParadaFormMap = () => {
    const container = document.getElementById('paradaMap');

    if (!container || typeof L === 'undefined') {
        return;
    }

    const defaultLat = Number.parseFloat(container.dataset.lat) || -17.7833;
    const defaultLng = Number.parseFloat(container.dataset.lng) || -63.1821;
    const hasLocation = container.dataset.hasLocation === 'true';

    const map = L.map('paradaMap').setView([defaultLat, defaultLng], hasLocation ? 16 : 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    const customIcon = L.divIcon({
        html: '<div class="parada-marker-pin"></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30],
        className: '',
    });

    let marker = null;

    const nombreInput = document.getElementById('nombre');
    const latInput = document.getElementById('latitud');
    const lngInput = document.getElementById('longitud');

    const getStopName = () => nombreInput?.value.trim() || 'Nueva parada';

    const setMarker = (lat, lng) => {
        marker?.remove();

        marker = L.marker([lat, lng], { icon: customIcon }).addTo(map);
        marker.bindPopup(`<strong>${escapeHtml(getStopName())}</strong>`).openPopup();

        if (latInput) latInput.value = lat.toFixed(8);
        if (lngInput) lngInput.value = lng.toFixed(8);
    };

    nombreInput?.addEventListener('input', () => {
        marker?.setPopupContent(`<strong>${escapeHtml(getStopName())}</strong>`);
    });

    if (hasLocation) {
        setMarker(defaultLat, defaultLng);
    }

    map.on('click', (event) => {
        const { lat, lng } = event.latlng;

        setMarker(lat, lng);
        map.setView(event.latlng, Math.max(map.getZoom(), 16));

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=es`)
            .then((response) => response.json())
            .then((data) => {
                if (!nombreInput || !data || !data.address) {
                    return;
                }

                const name = data.name
                    || data.address.road
                    || data.address.neighbourhood
                    || data.address.suburb
                    || data.address.city
                    || (data.display_name || '').split(',').slice(0, 2).join(', ');

                if (!name) {
                    return;
                }

                nombreInput.value = name;
                marker?.setPopupContent(`<strong>${escapeHtml(name)}</strong>`);
            })
            .catch(() => {
                // Sin geocodificación inversa se conservan las coordenadas elegidas.
            });
    });

    const searchInput = document.getElementById('searchLocation');
    const btnSearch = document.getElementById('btnSearch');
    const resultsContainer = document.getElementById('searchResults');

    const searchLocation = () => {
        const query = searchInput?.value.trim();

        if (!query || !resultsContainer) {
            return;
        }

        if (btnSearch) {
            btnSearch.disabled = true;
            btnSearch.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Buscando…';
        }

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&addressdetails=1`)
            .then((response) => response.json())
            .then((data) => {
                resultsContainer.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    resultsContainer.innerHTML = '<div class="list-group-item text-muted text-center py-3"><i class="bi bi-geo-alt me-2" aria-hidden="true"></i>No se encontraron resultados</div>';
                } else {
                    data.forEach((item) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'list-group-item list-group-item-action d-flex align-items-start gap-2 py-2';
                        button.innerHTML = `<i class="bi bi-geo-alt-fill mt-1 text-wine flex-shrink-0" aria-hidden="true"></i><span><span class="fw-semibold small d-block">${escapeHtml((item.display_name || '').split(',').slice(0, 2).join(', '))}</span><small class="text-muted">${escapeHtml(item.display_name)}</small></span>`;

                        button.addEventListener('click', () => {
                            const lat = Number.parseFloat(item.lat);
                            const lng = Number.parseFloat(item.lon);

                            if (nombreInput && !nombreInput.value.trim()) {
                                nombreInput.value = (item.display_name || '').split(',').slice(0, 2).join(', ');
                            }

                            setMarker(lat, lng);
                            map.setView([lat, lng], 17);
                            resultsContainer.classList.add('d-none');

                            if (searchInput) {
                                searchInput.value = (item.display_name || '').split(',').slice(0, 3).join(', ');
                            }
                        });

                        resultsContainer.appendChild(button);
                    });
                }

                resultsContainer.classList.remove('d-none');
            })
            .catch(() => {
                resultsContainer.innerHTML = '<div class="list-group-item text-danger text-center py-3"><i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>Error al buscar. Intente nuevamente.</div>';
                resultsContainer.classList.remove('d-none');
            })
            .finally(() => {
                if (btnSearch) {
                    btnSearch.disabled = false;
                    btnSearch.innerHTML = '<i class="bi bi-search me-1" aria-hidden="true"></i>Buscar';
                }
            });
    };

    btnSearch?.addEventListener('click', searchLocation);
    searchInput?.addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchLocation();
        }
    });

    setTimeout(() => map.invalidateSize(), 200);
};

const initParadaModalMap = () => {
    const modal = document.getElementById('mapModal');
    const container = document.getElementById('modalMap');
    const label = document.getElementById('mapModalLabel');

    if (!modal || !container || typeof L === 'undefined') {
        return;
    }

    let modalMap = null;
    let modalMarker = null;

    modal.addEventListener('shown.bs.modal', (event) => {
        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        const lat = Number.parseFloat(button.getAttribute('data-lat'));
        const lng = Number.parseFloat(button.getAttribute('data-lng'));
        const nombre = button.getAttribute('data-nombre') || 'Parada';

        if (Number.isNaN(lat) || Number.isNaN(lng)) {
            return;
        }

        if (label) {
            label.innerHTML = `<i class="bi bi-map me-1" aria-hidden="true"></i>Ubicación: ${escapeHtml(nombre)}`;
        }

        if (!modalMap) {
            modalMap = L.map('modalMap').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
            }).addTo(modalMap);
        } else {
            modalMap.setView([lat, lng], 16);
        }

        modalMarker?.remove();

        modalMarker = L.marker([lat, lng]).addTo(modalMap)
            .bindPopup(`<strong>${escapeHtml(nombre)}</strong><br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`)
            .openPopup();

        setTimeout(() => modalMap.invalidateSize(), 100);
    });
};

const initParadaShowMap = () => {
    const container = document.getElementById('showMap');

    if (!container || typeof L === 'undefined') {
        return;
    }

    const lat = Number.parseFloat(container.dataset.lat);
    const lng = Number.parseFloat(container.dataset.lng);

    if (Number.isNaN(lat) || Number.isNaN(lng)) {
        return;
    }

    const map = L.map('showMap').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup(`<strong>${escapeHtml(container.dataset.nombre)}</strong><br>${escapeHtml(container.dataset.referencia)}<br><small class="text-muted">Lat: ${lat.toFixed(6)} | Lng: ${lng.toFixed(6)}</small>`)
        .openPopup();

    setTimeout(() => map.invalidateSize(), 200);
};

document.addEventListener('DOMContentLoaded', () => {
    initParadaFormMap();
    initParadaModalMap();
    initParadaShowMap();
});
