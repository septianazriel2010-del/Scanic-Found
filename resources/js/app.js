import './bootstrap';

const filterForm = document.querySelector('#item-report-filters');

if (filterForm instanceof HTMLFormElement) {
    const results = document.querySelector('#item-report-results');
    const status = document.querySelector('#item-report-search-status');
    const searchInput = filterForm.elements.namedItem('q');
    let activeRequest;
    let searchTimeout;

    if (results instanceof HTMLElement && status instanceof HTMLElement) {
        const buildUrl = () => {
            const url = new URL(filterForm.action, window.location.origin);
            new FormData(filterForm).forEach((value, key) => {
                if (typeof value === 'string' && value !== '') {
                    url.searchParams.set(key, value);
                }
            });
            return url;
        };

        const updateResults = async (url, historyMode = 'replace') => {
            activeRequest?.abort();
            const requestController = new AbortController();
            activeRequest = requestController;
            results.setAttribute('aria-busy', 'true');
            status.textContent = 'Memuat hasil pencarian...';

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: requestController.signal,
                });

                if (!response.ok) {
                    throw new Error(`Permintaan pencarian gagal (${response.status}).`);
                }

                results.innerHTML = await response.text();
                if (historyMode === 'push') {
                    window.history.pushState({}, '', url);
                } else if (historyMode === 'replace') {
                    window.history.replaceState({}, '', url);
                }
                status.textContent = 'Hasil pencarian diperbarui.';
            } catch (error) {
                if (error.name !== 'AbortError') {
                    status.textContent = 'Hasil pencarian gagal dimuat. Silakan coba lagi.';
                    console.error(error);
                }
            } finally {
                if (!requestController.signal.aborted) {
                    results.setAttribute('aria-busy', 'false');
                }
            }
        };

        const syncFiltersFromUrl = () => {
            const url = new URL(window.location.href);
            for (const element of filterForm.elements) {
                if (element instanceof HTMLInputElement || element instanceof HTMLSelectElement) {
                    element.value = url.searchParams.get(element.name) ?? '';
                }
            }
            updateResults(url, 'none');
        };

        filterForm.addEventListener('submit', (event) => {
            event.preventDefault();
            updateResults(buildUrl(), 'push');
        });

        if (searchInput instanceof HTMLInputElement) {
            searchInput.addEventListener('input', () => {
                window.clearTimeout(searchTimeout);
                searchTimeout = window.setTimeout(() => updateResults(buildUrl()), 300);
            });
        }

        filterForm.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => updateResults(buildUrl(), 'push'));
        });

        results.addEventListener('click', (event) => {
            const link = event.target instanceof Element
                ? event.target.closest('a[href]')
                : null;

            if (!(link instanceof HTMLAnchorElement)) {
                return;
            }

            const url = new URL(link.href);
            if (url.origin !== window.location.origin
                || url.pathname !== window.location.pathname
                || !url.searchParams.has('page')) {
                return;
            }

            event.preventDefault();
            updateResults(url, 'push');
        });

        window.addEventListener('popstate', syncFiltersFromUrl);
    }
}
