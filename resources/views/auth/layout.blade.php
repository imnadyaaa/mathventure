<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Mathventure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
   <style>
    * { box-sizing: border-box; }

    body {
        margin: 0; min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
        font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
        color: #2f4a2f;
        /* path gambar tetap pakai yang sudah berfungsi di projectmu */
        background: linear-gradient(rgba(0,0,0,.25), rgba(0,0,0,.4)),
                    url('{{ asset('image/forest-bg.jpg') }}') center/cover fixed;
    }

    /* Kartu krem, sama seperti kartu kelas */
    .card {
        width: 100%; max-width: 420px; margin: 20px;
        padding: 40px 36px;
        background: rgba(247, 240, 216, .95);
        border-radius: 28px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
        text-align: center;
    }

    .title { font-size: 1.6rem; font-weight: 700; margin: 0 0 10px; color: #2f4a2f; }
    .sub   { font-size: .8rem; font-weight: 400; line-height: 1.6;
             margin: 0 auto 26px; max-width: 300px; color: #4a5a40; }

    /* Input */
    .field { margin-bottom: 14px; text-align: left; }
    .field input {
        width: 100%; padding: 13px 14px;
        border: 2px solid transparent; border-left: 4px solid #557a46;
        border-radius: 0;
        background: #fff; color: #2f4a2f;
        font-family: inherit; font-size: .95rem; outline: none;
    }
    .field input::placeholder { color: #9aa58f; }
    .field input:focus { border-color: #557a46; }
    .error { color: #b3261e; font-size: .78rem; margin: 5px 0 0 4px; }

    /* Baris "Ingat saya" dan link */
    .row {
        display: flex; justify-content: space-between; align-items: center;
        margin: 22px 0 26px; font-size: .78rem; color: #2f4a2f;
    }
    .row label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .row input[type=checkbox] { accent-color: #557a46; width: 16px; height: 16px; }
    .row a { color: #2f4a2f; font-weight: 600; text-decoration: none; }
    .row a:hover { text-decoration: underline; }

    /* Tombol hijau */
    button {
        width: 100%; padding: 13px; border: none; border-radius: 999px;
        background: #557a46; color: #fff; cursor: pointer;
        font-family: inherit; font-size: .9rem; font-weight: 700;
        letter-spacing: 1.5px; text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
        transition: background .2s, transform .1s;
    }
    button:hover  { background: #466a39; }
    button:active { transform: scale(.98); }

    .alt { margin-top: 22px; font-size: .82rem; color: #4a5a40; }
    .alt a { color: #2f4a2f; font-weight: 700; }
    </style>
</head>
<body>
    <div class="card">
        @yield('content')
    </div>

    <script src="{{ asset('js/sfx.js') }}"></script>
</body>
</html>