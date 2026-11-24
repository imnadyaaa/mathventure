<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mathventure</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <noscript><meta http-equiv="refresh" content="3;url={{ route('classes.index') }}"></noscript>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            /* pakai path gambar yang sama dengan layout login */
            background: linear-gradient(rgba(0,0,0,.4), rgba(0,0,0,.4)),
                        url('{{ asset('images/background.jpg') }}') center/cover fixed;
        }
        .splash { text-align: center; color: #fff; padding: 20px; }

        .logo {
            display: inline-block;
            background: rgba(47, 74, 47, .85);
            border-radius: 28px; padding: 22px 44px;
            font-size: clamp(2.2rem, 8vw, 3.6rem); font-weight: 800;
            letter-spacing: 1px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .4);
            animation: muncul 1s ease-out both;
        }
        .tagline {
            margin: 18px 0 0; font-size: 1.05rem; font-weight: 400;
            text-shadow: 0 2px 6px rgba(0, 0, 0, .6);
            animation: muncul 1s .5s ease-out both;
        }

        .loading {
            width: 220px; 
            height: 8px; 
            margin: 34px auto 0;
            background: rgba(255, 255, 255, .3); border-radius: 999px;
            overflow: hidden;
            animation: muncul .8 1s ease-out both;
        }
        .loading span {
            display: block; height: 100%; width: 0;
            background: #f7f0d8; border-radius: 999px;
            animation: isi 4s 0s linear forwards;
        }

        @keyframes muncul {
            from { opacity: 0; transform: translateY(14px) scale(.96); }
            to   { opacity: 1; transform: none; }
        }
        @keyframes isi { to { width: 100%; } }

        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: .01s !important; animation-delay: 0s !important; }
        }
    </style>
</head>
<body>
    <div class="splash">
        <div class="logo">Mathventure</div>
        <p class="tagline">Belajar matematika sambil bertualang 🌿</p>
        <div class="loading"><span></span></div>
    </div>

    <script>
        const tujuan = "{{ route('classes.index') }}";
        const pindah = () => window.location.replace(tujuan);
        setTimeout(pindah, 2500);
        document.body.addEventListener('click', pindah); // klik untuk melewati
    </script>

    <script src="{{ asset('js/sfx.js') }}"></script>
</body>
</html>