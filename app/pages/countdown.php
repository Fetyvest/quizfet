<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Обратный отсчёт</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --player-bg: #21262d;
            --border-color: #30363d;
            --border-active: #1f6feb;
            --text-main: #c9d1d9;
            --text-muted: #8b949e;
            --accent-green: #2ea043;
            --accent-green-hover: #3fb950;
            --font-size-h1: clamp(1.25rem, 1.05rem + 1vw, 1.75rem);
            --font-size-body: clamp(0.9rem, 0.88rem + 0.15vw, 1rem);
            --font-size-countdown: clamp(4.5rem, 2.5rem + 10vw, 9rem);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .admin-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            width: 100%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        /* Компактная шапка, прижатая к верхнему краю до упора */
        .game-header {
            position: fixed;
            top: 0;
            left: 20px;
            right: 20px;
            width: auto;
            padding: 12px 20px;
            border-top: none;
            border-radius: 0 0 8px 8px;
            text-align: center;
            z-index: 10;
        }

        .game-header__info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .game-header__info:empty,
        .game-header__meta:has(.game-header__badge:empty),
        .game-header__subtitle:empty,
        .game-header__title:empty {
            display: none;
        }

        .game-header__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            line-height: 1.2;
            color: var(--text-main);
        }

        .game-header__subtitle {
            font-size: var(--font-size-body);
            color: var(--text-muted);
            line-height: 1.3;
        }

        .game-header__meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 2px;
        }

        .game-header__badge {
            display: inline-block;
            background-color: var(--player-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 2px 10px;
            border-radius: 20px;
        }

        /* Блок обратного отсчёта строго по центру экрана */
        .countdown-panel {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: clamp(1.5rem, 3vw, 2.5rem) 20px;
        }

        .countdown__value {
            font-size: var(--font-size-countdown);
            font-weight: 700;
            line-height: 1;
            color: var(--text-main);
            font-variant-numeric: tabular-nums;
            user-select: none;
            min-height: calc(1ch + 2 * 38.4px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
        }

        /* Анимация плавного появления и исчезновения */
        .countdown__value--animate {
            animation: fadePop 1s ease-in-out forwards;
        }

        /* Анимация только для появления слова «Старт!» */
        .countdown__value--start {
            color: var(--accent-green-hover);
            animation: startPop 0.35s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        /* Оверлей для плавного затемнения экрана в цвет фона */
        .screen-fade {
            position: fixed;
            inset: 0;
            background-color: var(--bg-color);
            opacity: 0;
            pointer-events: none;
            z-index: 999;
            transition: opacity 0.4s ease-in-out;
        }

        .screen-fade--active {
            opacity: 1;
        }

        @keyframes fadePop {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            20% {
                opacity: 1;
                transform: scale(1);
            }
            80% {
                opacity: 1;
                transform: scale(1);
            }
            100% {
                opacity: 0;
                transform: scale(1.1);
            }
        }

        @keyframes startPop {
            0% {
                opacity: 0;
                transform: scale(0.8);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

    <div class="admin-panel game-header">
        <div class="game-header__info">
            <h1 class="game-header__title"><?php echo CONFIG['game']['title']; ?></h1>
            <p class="game-header__subtitle"><?php echo CONFIG['game']['subtitle']; ?></p>
        </div>
    </div>

    <div class="admin-panel countdown-panel">
        <div class="countdown__value" id="countdown-value"></div>
    </div>

    <!-- Слой затемнения -->
    <div class="screen-fade" id="screen-fade"></div>

    <script>
        const count = document.getElementById('countdown-value');
        const screenFade = document.getElementById('screen-fade');
        const startTime = <?php echo json_decode(file_get_contents('../app/database/game_state.json'), true)['started_at']; ?>;

        // Вспомогательная функция для обновления текста и перезапуска анимации
        function updateCountdownText(text, isStart = false) {
            if (count.textContent === String(text)) return;

            count.textContent = text;
            count.classList.remove('countdown__value--animate', 'countdown__value--start');
            
            void count.offsetWidth;

            if (isStart) {
                count.classList.add('countdown__value--start');
            } else {
                count.classList.add('countdown__value--animate');
            }
        }

        if (Date.now() / 1000 - startTime >= 5) {
            updateCountdownText('Старт!', true);
            window.location.reload();
        } else {
            let interval = setInterval(() => {
                const elapsed = Date.now() / 1000 - startTime;

                if (count.textContent === 'Старт!') {
                    clearInterval(interval);

                    // Начинаем затемнение на 0.5с раньше (через 250мс вместо 600мс)
                    setTimeout(() => {
                        screenFade.classList.add('screen-fade--active');
                    }, 250);

                    // Перезагружаем страницу через 1200мс — экран пробудет полностью тёмным ~550мс
                    setTimeout(() => {
                        window.location.reload();
                    }, 1200);
                    return;
                }

                if (elapsed < 3) {
                    updateCountdownText(3);
                } else if (elapsed >= 3 && elapsed < 4) {
                    updateCountdownText(2);
                } else if (elapsed >= 4 && elapsed < 5) {
                    updateCountdownText(1);
                } else if (elapsed >= 5) {
                    updateCountdownText('Старт!', true);
                }
            }, 50);
        }
    </script>
</body>
</html>