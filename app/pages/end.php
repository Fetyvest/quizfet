<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Квиз завершён</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --badge-bg: #21262d;
            --border-color: #30363d;
            --text-main: #c9d1d9;
            --text-muted: #8b949e;
            --accent-green: #2ea043;
            --font-size-h1: clamp(1.5rem, 1.188rem + 1.5vw, 2.25rem);
            --font-size-h2: clamp(1.125rem, 0.969rem + 0.75vw, 1.5rem);
            --font-size-body: clamp(0.95rem, 0.919rem + 0.15vw, 1.05rem);
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
            flex-direction: column;
            justify-content: start;
            align-items: center;
            gap: 20px;
            padding: 60px 20px;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(1.5rem, 1.083rem + 2.08vw, 3rem);
            width: 100%;
            max-width: 600px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        /* Верхний блок с информацией о квизе */
        .game-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .game-info:empty,
        .game-info__badge:empty,
        .game-info__meta:empty,
        .game-info__subtitle:empty,
        .game-info__title:empty {
            display: none;
        }

        .game-info__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            line-height: 1.25;
            color: var(--text-main);
        }

        .game-info__subtitle {
            font-size: var(--font-size-body);
            color: var(--text-muted);
            line-height: 1.5;
        }

        .game-info__meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }

        .game-info__badge {
            display: inline-block;
            background-color: var(--badge-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* Нижний блок конца квиза */
        .end-screen__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--text-main);
        }

        .end-screen__user {
            font-size: var(--font-size-h2);
            font-weight: 400;
            color: var(--text-muted);
            display: flex;
            justify-content: center;
            align-items: start;
            gap: 0 0.5ch;
        }

        .end-screen__username-wrapper {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            vertical-align: bottom;
            max-width: 70%;
        }

        .end-screen__username {
            color: var(--text-main);
            font-weight: 500;
            display: inline-block;
            width: 100%;
            white-space: nowrap;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .end-screen__username::-webkit-scrollbar {
            display: none;
        }

        .end-screen__username-underline {
            display: block;
            width: 100%;
            height: 4px;
            border-radius: 2px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <?php
    if (!empty(CONFIG['game']['title']) || !empty(CONFIG['game']['subtitle'])) {
        echo '<div class="card game-info">';
    }
    ?>
        <h1 class="game-info__title"><?php echo CONFIG['game']['title'] ?? ''; ?></h1>
        <p class="game-info__subtitle"><?php echo CONFIG['game']['subtitle'] ?? ''; ?></p>
        
        <!--
        <div class="game-info__meta">
            <span class="game-info__badge">
                <?php if (isset(CONFIG['questions'])) echo 'Вопросов: ' . count(CONFIG['questions']); ?>
            </span>
        </div>
        -->
    <?php
    if (!empty(CONFIG['game']['title']) || !empty(CONFIG['game']['subtitle'])) {
        echo '</div>';
    }
    ?>

    <div class="card end-screen">
        <h2 class="end-screen__title">Квиз пройден!</h2>
        <div class="end-screen__user">
            <span>Ваш ник:</span>
            <span class="end-screen__username-wrapper">
                <span class="end-screen__username"><?php
                    echo htmlspecialchars($_SESSION['username'] ?? 'Игрок');
                ?></span>
                <span class="end-screen__username-underline" style="background-color: <?php
                    echo htmlspecialchars($_SESSION['color'] ?? '#2ea043');
                ?>"></span>
            </span>
        </div>
    </div>
</body>
</html>