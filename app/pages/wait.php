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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Блок ожидания (БЭМ: .wait-screen) */
        .wait-screen {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(1.5rem, 1.083rem + 2.08vw, 3rem);
            width: 100%;
            max-width: 490px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

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
        }

        .wait-screen__username {
            color: var(--text-main);
            font-weight: 500;
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

    <div class="wait-screen">
        <h1 class="wait-screen__title">Ожидайте включение</h1>
        <h2 class="wait-screen__user"><span>Ваш ник: </span>
            <span class="wait-screen__username-wrapper">
                <span class="wait-screen__username"><?php
                session_start();
                echo htmlspecialchars($_SESSION['username']);
                ?></span>
                <!-- Сюда пишите нужный цвет вместо #58a6ff -->
                <span class="wait-screen__username-underline" style="background-color: <?php
                echo htmlspecialchars($_SESSION['color']);
                ?>;"></span>
            </span>
        </h2>
        
        <form class="wait-screen__actions" action="" method="POST">
            <button type="submit" class="wait-screen__button wait-screen__button_edit" name="name_edit" value="true">Изменить</button>
            <button type="submit" class="wait-screen__button wait-screen__button_logout" name="name_edit" value="false">Выйти</button>
        </form>
    </div>

</body>
</html>