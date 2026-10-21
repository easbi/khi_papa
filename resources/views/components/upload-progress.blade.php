{{--
    Upload Progress Overlay
    ------------------------------------------------------------
    Include sekali di layout utama → otomatis aktif di setiap
    halaman yang punya <form data-upload-progress>.
    ------------------------------------------------------------
--}}
@once
    <link rel="stylesheet" href="{{ asset('css/upload-progress.css') }}">

    <div id="uploadOverlay" class="upload-overlay hidden" aria-hidden="true">
        <div class="upload-modal" role="status" aria-live="polite">
            <div class="upload-icon" id="uploadIcon">☁️</div>
            <div class="upload-title" id="uploadTitle">Mengunggah file...</div>
            <div class="upload-subtitle" id="uploadSubtitle">Mohon tunggu, jangan tutup halaman ini</div>

            <div class="upload-progress-wrap">
                <div class="upload-progress-bar" id="uploadProgressBar" style="width: 0%"></div>
            </div>
            <div class="upload-percent" id="uploadPercent">0%</div>

            <div class="upload-phase" id="uploadPhase">Menyiapkan...</div>
        </div>
    </div>

    <script src="{{ asset('js/upload-progress.js') }}" defer></script>
@endonce
