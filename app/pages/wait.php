<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ожидайте включения</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --badge-bg: #21262d;
            --border-color: #30363d;
            --text-main: #c9d1d9;
            --text-muted: #8b949e;
            --btn-secondary: #21262d;
            --btn-secondary-hover: #30363d;
            --btn-danger: #b62324;
            --btn-danger-hover: #da3637;

            /* Адаптивные шрифты */
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

        /* Общий стиль карточки для обоих блоков */
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

        /* Верхний блок с информацией о квизе (БЭМ: .game-info) */
        .game-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        /* Автоматическое скрытие пустых элементов (когда бэк не передал данные) */
        .game-info:empty,
        .game-info__title:empty,
        .game-info__subtitle:empty,
        .game-info__meta:empty,
        .game-info__badge:empty {
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

        /* Блок ожидания (БЭМ: .wait-screen) */
        .wait-screen__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            margin-bottom: 12px;
        }

        .wait-screen__user {
            font-size: var(--font-size-h2);
            font-weight: 400;
            color: var(--text-muted);
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: start;
            gap: 0 0.5ch;
        }

        /* Обертка для ника и линии */
        .wait-screen__username-wrapper {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            vertical-align: bottom;
            max-width: 70%;
        }

        .wait-screen__username {
            color: var(--text-main);
            font-weight: 500;
            display: inline-block;
            width: 100%;
            white-space: nowrap;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .wait-screen__username::-webkit-scrollbar {
            display: none;
        }

        /* Базовые стили полосы под ником */
        .wait-screen__username-underline {
            display: block;
            width: 100%;
            height: 4px;
            border-radius: 2px;
            margin-top: 4px;
        }

        .wait-screen__actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .wait-screen__button {
            width: 100%;
            padding: 12px 16px;
            font-size: var(--font-size-body);
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
        }

        /* Модификаторы кнопок */
        .wait-screen__button_edit {
            background-color: var(--btn-secondary);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .wait-screen__button_edit:hover {
            background-color: var(--btn-secondary-hover);
        }

        .wait-screen__button_logout {
            background-color: transparent;
            border-color: transparent;
            color: var(--text-muted);
        }

        .wait-screen__button_logout:hover {
            color: var(--btn-danger-hover);
        }
    </style>
</head>
<body>

    <!-- Верхний блок с информацией об игре -->
    <?php
    if(!empty(FILE['game']['title']) || !empty(FILE['game']['subtitle'])){
        echo '<div class="card game-info">';
    }
    ?>
    
        <h1 class="game-info__title"><?php echo FILE['game']['title']; ?></h1>
        <p class="game-info__subtitle"><?php echo FILE['game']['subtitle']; ?></p>
        <!-- <div class="game-info__meta">
            <span class="game-info__badge"><?php /* echo 'Вопросов: ' . count(FILE['questions']); */ ?></span>
        </div> -->
    
    <?php
    if(!empty(FILE['game']['title']) || !empty(FILE['game']['subtitle'])){
        echo '</div>';
    }
    ?>

    <!-- Блок ожидания -->
    <div class="card wait-screen">
        <h2 class="wait-screen__title">Ожидайте включение</h2>
        <div class="wait-screen__user">
            <span>Ваш ник: </span>
            <span class="wait-screen__username-wrapper">
                <span class="wait-screen__username"><?php
                session_start();
                echo htmlspecialchars($_SESSION['username']);
                ?></span>
                <span class="wait-screen__username-underline" style="background-color: <?php
                echo htmlspecialchars($_SESSION['color']);
                ?>;"></span>
            </span>
        </div>
        
        <form class="wait-screen__actions" action="" method="POST">
            <button type="submit" class="wait-screen__button wait-screen__button_edit" name="name_edit" value="true">Изменить</button>
            <button type="submit" class="wait-screen__button wait-screen__button_logout" name="name_edit" value="false">Выйти</button>
        </form>
    </div>

<script>
    // Подключаемся к роуту стрима через index.php
    const startStream = new EventSource('./?action=stream_start');

    // Слушаем конкретное событие 'game_start', которое шлет PHP
    startStream.addEventListener('game_start', (event) => {
        // Принудительно закрываем соединение со стороны браузера
        startStream.close();
        
        // Перезагружаем страницу (или перенаправляем на страницу игры)
        window.location.reload();
    });

    // Обработка ошибок (браузер сам попытается переподключиться при временном обрыве)
    startStream.onerror = (error) => {
        console.warn('Ожидание переподключения к потоку старта...', error);
    };
</script>
</body>
</html>