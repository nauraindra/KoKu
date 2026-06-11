// ============================
// COOKIE HELPER FUNCTIONS
// ============================

// Simpan cookie
function setCookie(name, value, days = 30) {
    const expires = new Date();

    expires.setTime(
        expires.getTime() + (days * 24 * 60 * 60 * 1000)
    );

    document.cookie =
        `${name}=${encodeURIComponent(value)};` +
        `expires=${expires.toUTCString()};` +
        `path=/;SameSite=Lax`;
}

// Ambil cookie
function getCookie(name) {
    const cookies = document.cookie.split(';');

    for (const cookie of cookies) {

        const [key, val] = cookie.trim().split('=');

        if (key === name) {
            return decodeURIComponent(val);
        }
    }

    return null;
}

// Hapus cookie
function deleteCookie(name) {

    document.cookie =
        `${name}=;` +
        `expires=Thu, 01 Jan 1970 00:00:00 UTC;` +
        `path=/`;
}

document.addEventListener('DOMContentLoaded', () => {
    // ============================
// LOCAL STORAGE WEATHER CITY
// ============================

    const weatherCityInput = document.querySelector('#weatherCity');

    if (weatherCityInput) {

        const savedCity = localStorage.getItem('weather_city');

        if (savedCity) {
            weatherCityInput.value = savedCity;
        }

        weatherCityInput.addEventListener('input', () => {
            localStorage.setItem(
                'weather_city',
                weatherCityInput.value
            );
        });
    }

    const clearWeatherCity = document.querySelector('#clearWeatherCity');

    if (clearWeatherCity) {

        clearWeatherCity.addEventListener('click', () => {

            localStorage.removeItem('weather_city');

            weatherCityInput.value = '';
        });
    }

    const weatherBox = document.querySelector('[data-weather-box]');

    if (weatherBox) {
        fetch('/weather/current')
            .then(response => response.json())
            .then(data => {
                weatherBox.innerHTML = `
                    <div>
                        <p class="text-muted mb-1">Cuaca ${data.city ?? 'Jakarta'}</p>
                        <h3 class="fw-bold mb-0">${data.temperature ?? '-'}°C</h3>
                    </div>
                    <div class="weather-meta">
                        <span>Kelembapan ${data.humidity ?? '-'}%</span>
                        <span>Angin ${data.wind ?? '-'} km/j</span>
                    </div>
                `;
            })
            .catch(() => {
                weatherBox.innerHTML = '<p class="mb-0 text-muted">Data cuaca belum tersedia.</p>';
            });
    }

    const searchInput = document.querySelector('#tenantSearch');
    const searchResult = document.querySelector('#tenantSearchResult');

    if (searchInput && searchResult) {
        searchInput.addEventListener('input', () => {
            const keyword = searchInput.value.trim();

            if (keyword.length < 2) {
                searchResult.innerHTML = '';
                return;
            }

            searchResult.innerHTML = '<div class="search-empty">Mencari data penyewa...</div>';

            fetch(`/admin/tenants-search?q=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.length) {
                        searchResult.innerHTML = '<div class="search-empty">Penyewa tidak ditemukan.</div>';
                        return;
                    }

                    searchResult.innerHTML = data.map(item => `
                        <div class="search-item">
                            <div>
                                <strong>${item.name}</strong>
                                <small>${item.email}</small>
                            </div>
                            <span>${item.room ? item.room.room_number : 'Belum ada kamar'}</span>
                        </div>
                    `).join('');
                })
                .catch(() => {
                    searchResult.innerHTML = '<div class="search-empty">Gagal mengambil data.</div>';
                });
        });
    }

    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.querySelector('.sidebar');
    const sidebarClose = document.querySelector('[data-sidebar-close]');

    if (sidebarToggle && sidebar) {

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.add('show');
        });

        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.remove('show');
            });
        }
    }

    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', event => {
            const submitter = event.submitter;

            if (submitter && submitter.dataset.skipValidation === 'true') {
                return;
            }

            let valid = true;

            form.querySelectorAll('.client-error').forEach(error => error.remove());
            form.querySelectorAll('.is-invalid-client').forEach(input => {
                input.classList.remove('is-invalid-client');
            });

            const requiredFields = form.querySelectorAll('[required]');

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    showClientError(field, 'Field ini wajib diisi.');
                }
            });

            const emailFields = form.querySelectorAll('input[type="email"]');

            emailFields.forEach(field => {
                if (field.value.trim() && !isValidEmail(field.value.trim())) {
                    valid = false;
                    showClientError(field, 'Format email tidak valid.');
                }
            });

            const passwordFields = form.querySelectorAll('input[name="password"]');

            passwordFields.forEach(field => {
                if (field.value.trim() && field.value.length < 8) {
                    valid = false;
                    showClientError(field, 'Password minimal 8 karakter.');
                }
            });

            const confirmPassword = form.querySelector('input[name="password_confirmation"]');
            const password = form.querySelector('input[name="password"]');

            if (confirmPassword && password && confirmPassword.value !== password.value) {
                valid = false;
                showClientError(confirmPassword, 'Konfirmasi password tidak cocok.');
            }

            const numberFields = form.querySelectorAll('input[type="number"]');

            numberFields.forEach(field => {
                if (field.value && Number(field.value) < 0) {
                    valid = false;
                    showClientError(field, 'Angka tidak boleh negatif.');
                }
            });

            const fileFields = form.querySelectorAll('input[type="file"]');

            fileFields.forEach(field => {
                if (field.files.length > 0) {
                    const file = field.files[0];
                    const maxSize = 2 * 1024 * 1024;
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                    if (!allowedTypes.includes(file.type)) {
                        valid = false;
                        showClientError(field, 'File harus berupa JPG, PNG, atau WEBP.');
                    }

                    if (file.size > maxSize) {
                        valid = false;
                        showClientError(field, 'Ukuran file maksimal 2 MB.');
                    }
                }
            });

            if (!valid) {
                event.preventDefault();
            }
        });
    });

    function showClientError(field, message) {
        field.classList.add('is-invalid-client');

        const error = document.createElement('small');
        error.className = 'client-error';
        error.textContent = message;

        field.insertAdjacentElement('afterend', error);
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
});
