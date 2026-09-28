<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>YT Clipper</title>

    <style>
        /* Font opsional. Jika offline, otomatis pakai font sistem (fallback). */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * {
            box-sizing: border-box;
        }

        :root {
            /* Palet: Petrol + Sea-glass + Brass */
            --ink: #10262c;
            --petrol: #16424b;
            --petrol-deep: #0d2c33;
            --petrol-light: #2a6470;
            --brass: #b8935a;
            --brass-soft: #f1e3c8;

            --text: #10262c;
            --muted: #61777c;
            --faint: #8a9ca0;

            --border: #dbe5e4;
            --field: #f7faf9;
            --surface: #ffffff;
            --background: #eef3f2;

            --primary: #16424b;
            --primary-hover: #1d5560;
            --white: #ffffff;
        }

        ::selection {
            background: rgba(184, 147, 90, .28);
        }

        body {
            margin: 0;
            min-height: 100vh;

            font-family:
                "Plus Jakarta Sans",
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            color: var(--text);

            -webkit-font-smoothing: antialiased;

            background:
                radial-gradient(
                    circle at 8% 0%,
                    rgba(42, 100, 112, .16) 0,
                    transparent 38%
                ),
                radial-gradient(
                    circle at 100% 100%,
                    rgba(184, 147, 90, .14) 0,
                    transparent 36%
                ),
                linear-gradient(
                    180deg,
                    #f4f8f7,
                    #e6eeed
                );

            background-attachment: fixed;

            padding: 40px 18px;
        }

        .container {
            width: min(880px, 100%);
            margin: auto;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            position: relative;

            overflow: hidden;

            background: rgba(255, 255, 255, .84);

            border:
                1px solid
                rgba(16, 38, 44, .07);

            border-radius: 28px;

            padding: 42px;

            box-shadow:
                0 1px 2px rgba(13, 44, 51, .05),
                0 30px 70px -24px rgba(13, 44, 51, .28);

            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        /* garis brass tipis di sisi atas kartu */
        .card::before {
            content: "";

            position: absolute;
            top: 0;
            left: 12%;
            right: 12%;

            height: 2px;

            border-radius: 0 0 4px 4px;

            background:
                linear-gradient(
                    90deg,
                    transparent,
                    var(--brass),
                    transparent
                );

            opacity: .75;
        }

        /* =========================
           HEADER
        ========================= */

        .header {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo {
            width: 68px;
            height: 68px;

            margin:
                0 auto 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 22px;

            color: var(--brass-soft);

            font-size: 28px;

            background:
                linear-gradient(
                    145deg,
                    #1f5560,
                    #0d2c33
                );

            box-shadow:
                inset 0 0 0 1px rgba(184, 147, 90, .55),
                inset 0 1px 0 rgba(255, 255, 255, .12),
                0 16px 30px -10px rgba(13, 44, 51, .55);
        }

        h1 {
            margin: 0;

            font-size: 32px;
            font-weight: 800;

            letter-spacing: -.03em;

            color: var(--ink);
        }

        .subtitle {
            margin: 10px 0 0;

            color: var(--muted);

            font-size: 15px;
            line-height: 1.65;
        }

        /* =========================
           FORM
        ========================= */

        .field {
            margin-bottom: 22px;
        }

        label {
            display: flex;
            align-items: center;

            margin-bottom: 9px;

            font-size: 13.5px;
            font-weight: 600;

            letter-spacing: .005em;

            color: #2f4a51;
        }

        input {
            width: 100%;

            padding:
                14px 16px;

            border:
                1px solid
                var(--border);

            border-radius: 14px;

            background: var(--field);

            color: var(--ink);

            font-family: inherit;
            font-size: 15px;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        input:hover {
            background: #ffffff;
            border-color: #c5d5d3;
        }

        input:focus {
            background: #ffffff;

            border-color: var(--petrol-light);

            box-shadow:
                0 0 0 4px
                rgba(42, 100, 112, .14);
        }

        input::placeholder {
            color: #9db0b3;
        }

        .time-grid {
            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 16px;
        }

        .time-help {
            margin-top: -8px;
            margin-bottom: 20px;

            color: var(--faint);

            font-size: 12px;
            line-height: 1.7;
        }

        /* =========================
           DURATION
        ========================= */

        .duration-box {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 18px;

            padding:
                15px 18px;

            border:
                1px solid
                #d5e5e2;

            border-radius: 14px;

            background:
                linear-gradient(
                    135deg,
                    #f1f7f6,
                    #e8f1f0
                );

            color: var(--muted);

            font-size: 14px;
        }

        .duration-box strong {
            color: var(--petrol);

            font-size: 15px;
            font-weight: 700;
        }

        /* =========================
           SUBMIT BUTTON
        ========================= */

        .submit-btn {
            width: 100%;

            display: flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            padding: 16px 18px;

            border: 0;

            border-radius: 15px;

            background:
                linear-gradient(
                    135deg,
                    #1f5560,
                    #0f3239
                );

            color: white;

            font-family: inherit;
            font-size: 15px;
            font-weight: 700;

            letter-spacing: .01em;

            cursor: pointer;

            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, .14),
                0 14px 26px -10px rgba(13, 44, 51, .55);

            transition:
                transform .2s ease,
                box-shadow .2s ease,
                opacity .2s ease;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-1px);

            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, .18),
                0 18px 30px -10px rgba(13, 44, 51, .6);
        }

        .submit-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .submit-btn:focus-visible,
        .download-btn:focus-visible,
        .exit-btn:focus-visible {
            outline: 3px solid rgba(184, 147, 90, .55);
            outline-offset: 3px;
        }

        .submit-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        /* =========================
           SPINNER
        ========================= */

        .spinner {
            display: none;

            width: 17px;
            height: 17px;

            border:
                2px solid
                rgba(255,255,255,.3);

            border-top-color: var(--brass-soft);

            border-radius: 50%;

            animation:
                spin .7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* =========================
           PREVIEW
        ========================= */

        .preview {
            display: none;

            overflow: hidden;

            margin-bottom: 30px;

            border:
                1px solid
                rgba(13, 44, 51, .9);

            border-radius: 20px;

            background: var(--petrol-deep);

            box-shadow:
                0 22px 44px -18px
                rgba(13, 44, 51, .45);
        }

        .preview-title {
            display: flex;
            align-items: center;

            padding:
                13px 18px;

            color: #e8f0ef;

            font-size: 13px;
            font-weight: 600;

            letter-spacing: .01em;
        }

        .preview-title::before {
            content: "";

            width: 7px;
            height: 7px;

            margin-right: 9px;

            border-radius: 50%;

            background: #5fd1a5;

            box-shadow:
                0 0 0 3px
                rgba(95, 209, 165, .2);
        }

        .preview iframe {
            width: 100%;

            aspect-ratio: 16 / 9;

            display: block;

            border: 0;
        }

        /* =========================
           PROGRESS
        ========================= */

        .progress-box {
            display: none;

            margin-top: 26px;

            padding: 20px;

            border:
                1px solid
                var(--border);

            border-radius: 18px;

            background: var(--field);
        }

        .progress-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;

            margin-bottom: 13px;

            font-size: 13px;
            font-weight: 700;

            color: #2f4a51;
        }

        .progress-track {
            width: 100%;
            height: 10px;

            overflow: hidden;

            border-radius: 999px;

            background: #dde8e6;

            box-shadow:
                inset 0 1px 2px
                rgba(13, 44, 51, .08);
        }

        .progress-bar {
            width: 0%;
            height: 100%;

            border-radius: 999px;

            background:
                linear-gradient(
                    90deg,
                    #1f5560,
                    #2f7a86 62%,
                    #b8935a
                );

            transition:
                width .3s ease;
        }

        .progress-percent {
            margin-top: 10px;

            text-align: right;

            color: var(--faint);

            font-size: 12px;
        }

        /* =========================
           SUCCESS
        ========================= */

        .success {
            display: none;

            margin-top: 20px;

            padding: 20px;

            border:
                1px solid
                #c6e2d3;

            border-radius: 18px;

            background:
                linear-gradient(
                    135deg,
                    #f0f8f3,
                    #e8f4ee
                );

            color: #1d5b41;

            font-size: 14px;

            line-height: 1.7;
        }

        .success strong {
            font-size: 15px;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;

            margin-top: 13px;

            padding:
                11px 18px;

            border-radius: 12px;

            background:
                linear-gradient(
                    135deg,
                    #26775a,
                    #1d5b41
                );

            color: white;

            text-decoration: none;

            font-size: 13px;
            font-weight: 700;

            box-shadow:
                0 10px 20px -10px
                rgba(29, 91, 65, .6);

            transition:
                filter .2s ease,
                transform .2s ease;
        }

        .download-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        /* =========================
           ERROR
        ========================= */

        .error {
            display: none;

            margin-top: 20px;

            padding: 17px 18px;

            border:
                1px solid
                #f0c9c6;

            border-radius: 16px;

            background: #fdf2f1;

            color: #97302c;

            font-size: 14px;

            line-height: 1.6;
        }

        /* =========================
           HELP
        ========================= */

        .help {
            margin-top: 24px;

            color: var(--faint);

            font-size: 12px;

            line-height: 1.7;

            text-align: center;
        }

        .help strong {
            color: var(--petrol);
        }

        /* =========================
           EXIT BUTTON
        ========================= */

        .exit-btn {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 100%;

            margin-top: 14px;

            padding: 12px;

            border:
                1px solid
                var(--border);

            border-radius: 14px;

            background: transparent;

            color: var(--muted);

            font-size: 13px;
            font-weight: 600;

            text-align: center;
            text-decoration: none;

            cursor: pointer;

            transition:
                background .2s ease,
                color .2s ease,
                border-color .2s ease;
        }

        .exit-btn:hover {
            background: #f1f6f5;

            color: var(--ink);

            border-color: #c5d5d3;
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 620px) {

            body {
                padding: 18px 12px;
            }

            .card {
                padding: 27px 20px;

                border-radius: 22px;
            }

            .logo {
                width: 60px;
                height: 60px;

                font-size: 25px;

                border-radius: 19px;
            }

            h1 {
                font-size: 27px;
            }

            .subtitle {
                font-size: 14px;
            }

            .time-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .duration-box {
                flex-direction: column;
                align-items: flex-start;

                gap: 5px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            * {
                transition: none !important;
            }

            .spinner {
                animation-duration: 1.4s;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        {{-- HEADER --}}
        <div class="header">

            <div class="logo">
                ✂
            </div>

            <h1>
                YT Clipper
            </h1>

            <p class="subtitle">
                Potong video YouTube menjadi MP4
                dengan cepat dan mudah.
            </p>

        </div>


        {{-- PREVIEW --}}
        <div
            id="previewBox"
            class="preview"
        >

            <div class="preview-title">
                Preview Video
            </div>

            <iframe
                id="previewFrame"
                allowfullscreen
            ></iframe>

        </div>


        {{-- FORM --}}
        <form id="clipForm">

            @csrf

            <div class="field">

                <label for="url">
                    Link YouTube
                </label>

                <input
                    type="url"
                    id="url"
                    name="url"
                    placeholder="https://youtube.com/..."
                    required
                >

            </div>


            <div class="time-grid">

                <div class="field">

                    <label for="start">
                        Mulai (MM:SS)
                    </label>

                    <input
                        type="text"
                        id="start"
                        name="start"
                        value="00:00"
                        placeholder="00:00"
                        inputmode="numeric"
                        required
                    >

                </div>


                <div class="field">

                    <label for="end">
                        Selesai (MM:SS)
                    </label>

                    <input
                        type="text"
                        id="end"
                        name="end"
                        value="00:30"
                        placeholder="00:30"
                        inputmode="numeric"
                        required
                    >

                </div>

            </div>


            <div class="time-help">

                Contoh:
                00:30 = 30 detik,
                01:30 = 1 menit 30 detik,
                05:00 = 5 menit.

            </div>


            <div class="duration-box">

                <span>
                    Durasi potongan
                </span>

                <strong id="durationText">
                    30 detik
                </strong>

            </div>


            <button
                type="submit"
                id="submitBtn"
                class="submit-btn"
            >

                <span
                    class="spinner"
                    id="spinner"
                ></span>

                <span id="buttonText">
                    ✂ Ambil & Potong Video
                </span>

            </button>

        </form>


        {{-- PROGRESS --}}
        <div
            class="progress-box"
            id="progressBox"
        >

            <div class="progress-header">

                <span id="stageText">
                    Menunggu...
                </span>

                <span>
                    <span id="percentText">
                        0
                    </span>%
                </span>

            </div>


            <div class="progress-track">

                <div
                    class="progress-bar"
                    id="progressBar"
                ></div>

            </div>


            <div
                class="progress-percent"
                id="messageText"
            >
                Memulai...
            </div>

        </div>


        {{-- SUCCESS --}}
        <div
            class="success"
            id="successBox"
        >

            <strong>
                ✓ Video berhasil dipotong!
            </strong>

            <br>

            Video MP4 sudah siap.

            <br>

            <a
                href="#"
                id="downloadBtn"
                class="download-btn"
            >
                Download MP4
            </a>

        </div>


        {{-- ERROR --}}
        <div
            class="error"
            id="errorBox"
        ></div>


        <div class="help">

            Gunakan format waktu
            <strong>MM:SS</strong>.
            Tidak dibatasi 30 detik.

        </div>


        {{-- EXIT --}}
        <a
            href="/shutdown"
            class="exit-btn"
            id="exitBtn"
        >
            Keluar dari YT Clipper
        </a>

    </div>

</div>


<script>

    const form =
        document.getElementById('clipForm');

    const urlInput =
        document.getElementById('url');

    const startInput =
        document.getElementById('start');

    const endInput =
        document.getElementById('end');

    const durationText =
        document.getElementById('durationText');

    const submitBtn =
        document.getElementById('submitBtn');

    const buttonText =
        document.getElementById('buttonText');

    const spinner =
        document.getElementById('spinner');

    const progressBox =
        document.getElementById('progressBox');

    const progressBar =
        document.getElementById('progressBar');

    const percentText =
        document.getElementById('percentText');

    const stageText =
        document.getElementById('stageText');

    const messageText =
        document.getElementById('messageText');

    const successBox =
        document.getElementById('successBox');

    const downloadBtn =
        document.getElementById('downloadBtn');

    const errorBox =
        document.getElementById('errorBox');

    const previewBox =
        document.getElementById('previewBox');

    const previewFrame =
        document.getElementById('previewFrame');


    /*
    |--------------------------------------------------------------------------
    | MM:SS -> seconds
    |--------------------------------------------------------------------------
    */

    function timeToSeconds(value) {

        const parts =
            value.trim().split(':');

        if (parts.length !== 2) {
            return NaN;
        }

        const minutes =
            parseInt(parts[0], 10);

        const seconds =
            parseInt(parts[1], 10);

        if (
            Number.isNaN(minutes) ||
            Number.isNaN(seconds)
        ) {
            return NaN;
        }

        if (
            minutes < 0 ||
            seconds < 0 ||
            seconds > 59
        ) {
            return NaN;
        }

        return (
            minutes * 60
        ) + seconds;
    }


    /*
    |--------------------------------------------------------------------------
    | Format duration
    |--------------------------------------------------------------------------
    */

    function formatDuration(seconds) {

        seconds =
            Math.floor(seconds);

        const minutes =
            Math.floor(seconds / 60);

        const remain =
            seconds % 60;

        if (minutes === 0) {
            return `${remain} detik`;
        }

        if (remain === 0) {
            return `${minutes} menit`;
        }

        return (
            `${minutes} menit ` +
            `${remain} detik`
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Durasi realtime
    |--------------------------------------------------------------------------
    */

    function updateDuration() {

        const start =
            timeToSeconds(
                startInput.value
            );

        const end =
            timeToSeconds(
                endInput.value
            );

        const duration =
            end - start;

        if (duration > 0) {

            durationText.textContent =
                formatDuration(duration);

        } else {

            durationText.textContent =
                'Waktu tidak valid';

        }
    }


    startInput.addEventListener(
        'input',
        updateDuration
    );

    endInput.addEventListener(
        'input',
        updateDuration
    );


    /*
    |--------------------------------------------------------------------------
    | YouTube ID
    |--------------------------------------------------------------------------
    */

    function getYoutubeId(url) {

        try {

            const parsed =
                new URL(url);

            const host =
                parsed.hostname
                    .replace(
                        'www.',
                        ''
                    )
                    .toLowerCase();

            if (
                host === 'youtube.com' ||
                host === 'm.youtube.com' ||
                host === 'music.youtube.com'
            ) {

                const id =
                    parsed.searchParams.get('v');

                if (id) {
                    return id;
                }

                const shorts =
                    parsed.pathname.match(
                        /\/shorts\/([^/?]+)/
                    );

                if (shorts) {
                    return shorts[1];
                }
            }

            if (host === 'youtu.be') {

                return parsed.pathname
                    .replace('/', '')
                    .split('/')[0];

            }

        } catch (error) {

            return null;

        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    urlInput.addEventListener(
        'input',
        function () {

            const id =
                getYoutubeId(
                    urlInput.value.trim()
                );

            if (id) {

                previewFrame.src =
                    `https://www.youtube.com/embed/${id}`;

                previewBox.style.display =
                    'block';

            } else {

                previewFrame.src = '';

                previewBox.style.display =
                    'none';
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Poll status
    |--------------------------------------------------------------------------
    */

    async function pollStatus(token) {

        try {

            const response =
                await fetch(
                    `/clip/status/${token}`,
                    {
                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );

            const data =
                await response.json();


            if (
                data.status === 'failed'
            ) {

                throw new Error(
                    data.message ||
                    'Proses gagal.'
                );
            }


            /*
            | Progress
            */

            const progress =
                Math.min(
                    100,
                    Math.max(
                        0,
                        Number(
                            data.progress
                        ) || 0
                    )
                );

            progressBar.style.width =
                `${progress}%`;

            percentText.textContent =
                Math.round(progress);


            /*
            | Stage
            */

            if (
                data.stage ===
                'downloading'
            ) {

                stageText.textContent =
                    'Mengunduh video';

            } else if (
                data.stage ===
                'cutting'
            ) {

                stageText.textContent =
                    'Memotong video';

            } else if (
                data.stage ===
                'queued'
            ) {

                stageText.textContent =
                    'Menyiapkan';

            } else if (
                data.stage ===
                'completed'
            ) {

                stageText.textContent =
                    'Selesai';
            }


            messageText.textContent =
                data.message || '';


            /*
            | Completed
            */

            if (
                data.status ===
                'completed'
            ) {

                progressBar.style.width =
                    '100%';

                percentText.textContent =
                    '100';

                submitBtn.disabled =
                    false;

                spinner.style.display =
                    'none';

                buttonText.textContent =
                    '✂ Ambil & Potong Video';

                successBox.style.display =
                    'block';

                downloadBtn.href =
                    data.download_url;

                return;
            }


            /*
            | Poll lagi
            */

            setTimeout(
                () => pollStatus(token),
                1000
            );

        } catch (error) {

            submitBtn.disabled =
                false;

            spinner.style.display =
                'none';

            buttonText.textContent =
                '✂ Ambil & Potong Video';

            errorBox.textContent =
                error.message;

            errorBox.style.display =
                'block';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        async function (event) {

            event.preventDefault();

            errorBox.style.display =
                'none';

            successBox.style.display =
                'none';

            progressBox.style.display =
                'block';


            const start =
                timeToSeconds(
                    startInput.value
                );

            const end =
                timeToSeconds(
                    endInput.value
                );


            if (
                Number.isNaN(start) ||
                Number.isNaN(end)
            ) {

                errorBox.textContent =
                    'Gunakan format MM:SS, contoh 01:30.';

                errorBox.style.display =
                    'block';

                progressBox.style.display =
                    'none';

                return;
            }


            if (end <= start) {

                errorBox.textContent =
                    'Waktu selesai harus lebih besar dari waktu mulai.';

                errorBox.style.display =
                    'block';

                progressBox.style.display =
                    'none';

                return;
            }


            submitBtn.disabled =
                true;

            spinner.style.display =
                'inline-block';

            buttonText.textContent =
                'Memulai proses...';

            progressBar.style.width =
                '0%';

            percentText.textContent =
                '0';

            stageText.textContent =
                'Menyiapkan';

            messageText.textContent =
                'Membuat proses video...';


            try {

                const formData =
                    new FormData(form);

                const response =
                    await fetch(
                        '{{ route('clipper.store') }}',
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            body: formData
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    let message =
                        data.message ||
                        'Gagal membuat proses.';

                    if (data.errors) {

                        const first =
                            Object.values(
                                data.errors
                            )[0];

                        if (Array.isArray(first)) {

                            message =
                                first[0];

                        }
                    }

                    throw new Error(message);
                }


                if (!data.success) {

                    throw new Error(
                        data.message ||
                        'Gagal memulai proses.'
                    );
                }


                buttonText.textContent =
                    'Sedang memproses...';


                pollStatus(
                    data.token
                );


            } catch (error) {

                submitBtn.disabled =
                    false;

                spinner.style.display =
                    'none';

                buttonText.textContent =
                    '✂ Ambil & Potong Video';

                progressBox.style.display =
                    'none';

                errorBox.textContent =
                    error.message;

                errorBox.style.display =
                    'block';
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Exit
    |--------------------------------------------------------------------------
    */

    const exitBtn =
        document.getElementById(
            'exitBtn'
        );

    exitBtn.addEventListener(
        'click',
        function (event) {

            const confirmed =
                confirm(
                    'Tutup YT Clipper? Server dan worker akan dihentikan.'
                );

            if (!confirmed) {
                event.preventDefault();
            }
        }
    );


    updateDuration();

</script>

</body>
</html>
