/**
 * Upload Progress Overlay — KHI
 * -----------------------------------------------------------
 * Aktif otomatis pada form yang punya atribut:
 *   <form data-upload-progress>
 *
 * Atribut opsional:
 *   data-upload-progress-redirect="/url/tujuan"
 *     → URL redirect setelah sukses (default: halaman saat ini)
 *   data-upload-progress-method="POST|PUT"
 *     → method override (untuk form edit yg butuh _method)
 * -----------------------------------------------------------
 */
(function () {
    'use strict';

    function init() {
        var form = document.querySelector('form[data-upload-progress]');
        if (!form) return;                          // halaman tanpa form upload → skip

        var overlay    = document.getElementById('uploadOverlay');
        if (!overlay) return;                       // komponen belum di-include → skip

        var bar        = document.getElementById('uploadProgressBar');
        var percentEl  = document.getElementById('uploadPercent');
        var phaseEl    = document.getElementById('uploadPhase');
        var titleEl    = document.getElementById('uploadTitle');
        var subtitleEl = document.getElementById('uploadSubtitle');
        var iconEl     = document.getElementById('uploadIcon');

        var current    = 0;
        var fakeTimer  = null;
        var startTime  = 0;

        function setProgress(pct, phase, title, subtitle) {
            current = Math.max(current, Math.min(100, pct));
            bar.style.width       = current + '%';
            percentEl.textContent = Math.round(current) + '%';
            if (phase)    phaseEl.textContent    = phase;
            if (title)    titleEl.textContent    = title;
            if (subtitle) subtitleEl.textContent = subtitle;
        }

        function show() {
            overlay.classList.remove('hidden', 'done', 'error');
            requestAnimationFrame(function () { overlay.classList.add('show'); });
            current = 0;
            setProgress(0, 'Menyiapkan...', 'Mengunggah file...', 'Mohon tunggu, jangan tutup halaman ini');
        }

        function hide(delay) {
            setTimeout(function () {
                overlay.classList.remove('show');
                setTimeout(function () { overlay.classList.add('hidden'); }, 250);
            }, delay || 0);
        }

        function startFake() {
            stopFake();
            fakeTimer = setInterval(function () {
                if (current >= 95) return;
                var remaining = 95 - current;
                var step = Math.max(0.3, remaining * 0.06);

                var elapsed = (Date.now() - startTime) / 1000;
                var phase = 'Memproses di server...';
                if (elapsed > 6)  phase = 'Mengunggah ke Google Drive...';
                if (elapsed > 20) phase = 'Hampir selesai, mohon tunggu...';

                setProgress(current + step, phase);
            }, 400);
        }

        function stopFake() {
            if (fakeTimer) { clearInterval(fakeTimer); fakeTimer = null; }
        }

        function markComplete(redirectUrl) {
            stopFake();
            overlay.classList.add('done');
            iconEl.textContent = '✅';
            setProgress(100, 'Selesai!', 'Berhasil disimpan', 'Mengalihkan halaman...');
            setTimeout(function () {
                window.location.href = redirectUrl;
            }, 700);
        }

        function markError(message) {
            stopFake();
            overlay.classList.add('error');
            iconEl.textContent = '⚠️';
            setProgress(current, 'Gagal', 'Upload gagal', message || 'Silakan coba lagi');
            setTimeout(function () { hide(); setTimeout(function () { location.reload(); }, 400); }, 2500);
        }

        // ============================================================
        // Submit handler
        // ============================================================
        form.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;         // validasi JS lain sudah tolak → skip
            e.preventDefault();

            var formData   = new FormData(form);
            var redirect   = form.dataset.uploadProgressRedirect || window.location.href;
            var xhr        = new XMLHttpRequest();
            startTime      = Date.now();

            show();

            // Fase 1: browser → server (real, 0-40%)
            xhr.upload.addEventListener('progress', function (evt) {
                if (!evt.lengthComputable) return;
                setProgress((evt.loaded / evt.total) * 40, 'Mengunggah file ke server...');
            });

            // Fase 2: server → Drive (simulasi, 40-95%)
            xhr.upload.addEventListener('load', function () {
                setProgress(40, 'File diterima server', 'Memproses...', 'Meneruskan ke Google Drive');
                startFake();
            });

            // Fase 3: response
            xhr.addEventListener('load', function () {
                stopFake();
                if (xhr.status >= 200 && xhr.status < 400) {
                    var hasError = xhr.responseText.includes('alert-danger');
                    if (hasError) { markError('Data tidak valid. Periksa isian Anda.'); return; }
                    markComplete(redirect);
                } else {
                    markError('Server error (' + xhr.status + '). Coba lagi.');
                }
            });

            xhr.addEventListener('error',   function () { markError('Koneksi gagal. Periksa jaringan.'); });
            xhr.addEventListener('timeout', function () { markError('Upload terlalu lama. Coba file lebih kecil.'); });

            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            var tokenEl = form.querySelector('input[name="_token"]');
            if (tokenEl) xhr.setRequestHeader('X-CSRF-TOKEN', tokenEl.value);
            xhr.timeout = 180000;                   // 3 menit
            xhr.send(formData);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
