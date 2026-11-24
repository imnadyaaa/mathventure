<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Level {{ $schoolClass->name }} - Mathventure</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;

            background-image:
                linear-gradient(
                    rgba(20, 45, 25, 0.30),
                    rgba(20, 45, 25, 0.30)
                ),
                url('/image/forest-bg.jpg');

            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .page {
            min-height: 100vh;
            padding: 40px 60px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
        }

        .logo {
            background: #3f6248;
            color: white;
            padding: 14px 25px;
            border-radius: 18px;
            font-size: 25px;
            font-weight: bold;
        }

        .back-button {
            background: #f4ead2;
            color: #344d35;
            padding: 13px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
        }

        .title {
            text-align: center;
            color: white;
            margin-bottom: 40px;
            text-shadow: 2px 3px 5px rgba(0, 0, 0, 0.5);
        }

        .title h1 {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .title p {
            font-size: 18px;
        }

        .levels {
            max-width: 900px;
            margin: auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .level-card {
            background: rgba(250, 243, 220, 0.95);
            padding: 18px 12px;
            border-radius: 22px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            aspect-ratio: 1 / 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
        }

        .level-card:hover{
            transform: translateY(-8px);
        }

        .level-info h2 {
            color: #304b32;
            font-size: 1.25rem;
            margin-bottom: 8px;
        
        }

        .level-button {
            background: #557744;
            color: white;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 25px;
            font-weight: bold;
            white-space: nowrap;
        }

        .level-bintang {
            margin-top: 10px;
            font-size: 1.5rem;
            letter-spacing: 4px;
            color: #cfc8ad;
        }

        .level-bintang .nyala {
            color: #f2b01e;
        }

        .level-terkunci .level-button {
            background: #8a9580;
            cursor: not-allowed;
        }

        .locked {
            opacity: 0.65;
        }

        .locked .level-button {
            background: #888;
            cursor: not-allowed;
        }

        @media (max-width: 700px) {

            .page {
                padding: 25px 20px;
            }

            .levels {
                grid-template-columns: 1fr;
            }

            .title h1 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="page">

        <!-- HEADER -->

        <div class="header">

            <div class="logo">
                🌿 Mathventure
            </div>

            <a href="{{ route('classes.index') }}" class="back-button">
                ← Pilih Kelas
            </a>

        </div>


        <!-- TITLE -->

        <div class="title">

            <h1>{{ $schoolClass->name }}</h1>

            <p>
                Pilih level untuk memulai petualanganmu!
            </p>

        </div>


        <!-- LEVELS -->

        <div class="levels">

            @foreach ($levels as $level)

            <div class="level-card {{ $level->terbuka ? '' : 'level-terkunci' }}">

                <div class="level-info">
                    <h2>
                        {{ $level->terbuka ? '🔓' : '🔒' }}
                        Level {{ $level->level_number }}
                    </h2>

                    <p>{{ $level->name }}</p>

                    <div class="level-bintang">
                        @for ($i = 1; $i <= 3; $i++)
                            <span class="{{ $i <= $level->bintang ? 'nyala' : '' }}">★</span>
                        @endfor
                    </div>
                </div>

                @if ($level->terbuka)
                    <a href="{{ route('quiz.show', $level->id) }}" class="level-button">
                        {{ $level->bintang > 0 ? 'Main Lagi →' : 'Mulai →' }}
                    </a>
                @else
                    <span class="level-button">Terkunci</span>
                @endif

            </div>

            @endforeach

        </div>

    </div>

</body>

</html>