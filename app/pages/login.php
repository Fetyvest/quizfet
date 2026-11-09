<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --border-color: #30363d;
            --border-focus: #1f6feb;
            --text-main: #c9d1d9;
            --text-muted: #8b949e;
            --btn-bg: #238636;
            --btn-bg-hover: #2ea043;
            --btn-secondary: #21262d;
            --btn-secondary-hover: #30363d;
            
            /* Адаптивные шрифты на основе clamp() */
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

        /* Блок авторизации (БЭМ: .auth) */
        .auth {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(1.5rem, 1.083rem + 2.08vw, 3rem);
            width: 100%;
            max-width: 490px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .auth__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            margin-bottom: 8px;
            text-align: center;
        }

        .auth__subtitle {
            font-size: var(--font-size-h2);
            font-weight: 400;
            color: var(--text-muted);
            margin-bottom: 24px;
            text-align: center;
        }

        .auth__form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .auth__form_secondary {
            margin-top: 12px;
        }

        /* Контейнер для размещения инпута и квадратной кнопки в ряд */
        .auth__input-group {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .auth__input {
            flex: 1;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-main);
            padding: 12px 16px;
            font-size: var(--font-size-body);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .auth__input:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(31, 111, 235, 0.3);
        }

        .auth__input::placeholder {
            color: var(--text-muted);
            opacity: 0.6;
        }

        /* Стили для квадратной кнопки выбора цвета */
        .auth__color-btn {
            width: 45px;
            height: 45px;
            flex-shrink: 0;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .auth__color-btn:hover {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(31, 111, 235, 0.3);
        }

        .auth__button {
            width: 100%;
            padding: 12px 16px;
            font-size: var(--font-size-body);
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background-color 0.2s, opacity 0.2s;
        }

        /* Модификаторы кнопок */
        .auth__button_primary {
            background-color: var(--btn-bg);
            color: #ffffff;
        }

        .auth__button_primary:hover:not(:disabled) {
            background-color: var(--btn-bg-hover);
        }

        .auth__button_secondary {
            background-color: var(--btn-secondary);
            border-color: var(--border-color);
            color: var(--text-main);
        }

        .auth__button_secondary:hover:not(:disabled) {
            background-color: var(--btn-secondary-hover);
        }

        .auth__button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

    <div class="auth">
        <h1 class="auth__title">Вход</h1>
        <h2 class="auth__subtitle"><?php if(isset($_SESSION['relogin']) && $_SESSION['relogin'] == true){echo 'Введите новое имя';}else{echo 'Введите имя';}?></h2>
        
        <form class="auth__form" action="" method="POST">
            <div class="auth__input-group">
                <button id="color" class="auth__color-btn" type="button" style="<?php  session_start(); if(isset($_SESSION['color'])){echo 'background-color: ' . $_SESSION['color'];}?>"></button>
                <input autocomplete="on" class="auth__input" type="text" require id="inp" name="username" placeholder="<?php if(isset($_SESSION['username'])){echo htmlspecialchars($_SESSION['username']);}?>" value="<?php session_start(); if(isset($_SESSION['username'])){echo htmlspecialchars($_SESSION['username']);}?>">
                <input type="hidden" name="color" id="hcolor" value="<?php if(isset($_SESSION['color'])){echo $_SESSION['color'];}?>">
            </div>
            <?php 
            if(isset($_SESSION['relogin']) && $_SESSION['relogin'] == true){
                echo '<button type="submit" class="auth__button auth__button_primary" id="btn" name="name_edit" value="false">Сменить</button>';
            }
            else{
                echo '<button type="submit" class="auth__button auth__button_primary" id="btn" disabled>Войти</button>';
            }
            ?>
        </form>
        
        <?php
        if(isset($_SESSION['relogin']) && $_SESSION['relogin'] == true){
            echo '<form class="auth__form auth__form_secondary" action="" method="POST"><button type="submit" name="username" value="' . $_SESSION['username'] . '" class="auth__button auth__button_secondary">Отмена</button></form>';
        }
        ?>
    </div>

    <script>
        function getColor() {
            let hue = Math.floor(Math.random() * 361)
            let saturation = Math.floor(Math.random() * 51) + 50
            let lighting = Math.floor(Math.random() * 51) + 25

            const s = saturation / 100;
            const l = lighting / 100;
            const h = ((hue % 360) + 360) % 360;

            const c = (1 - Math.abs(2 * l - 1)) * s;
            const x = c * (1 - Math.abs(((h / 60) % 2) - 1));
            const m = l - c / 2;

            let r = 0, g = 0, b = 0;

            if (0 <= h && h < 60) {
                r = c; g = x; b = 0;
            } else if (60 <= h && h < 120) {
                r = x; g = c; b = 0;
            } else if (120 <= h && h < 180) {
                r = 0; g = c; b = x;
            } else if (180 <= h && h < 240) {
                r = 0; g = x; b = c;
            } else if (240 <= h && h < 300) {
                r = x; g = 0; b = c;
            } else if (300 <= h && h < 360) {
                r = c; g = 0; b = x;
            }

            const rInt = Math.round((r + m) * 255);
            const gInt = Math.round((g + m) * 255);
            const bInt = Math.round((b + m) * 255);

            const toHex = (val) => val.toString(16).padStart(2, '0');

            return `#${toHex(rInt)}${toHex(gInt)}${toHex(bInt)}`;
        }
        inp.addEventListener('input', () => {
            if(inp.value.trim() === ''){
                btn.disabled = true
            }
            else{
                btn.disabled = false
            }
        })
        color.addEventListener('click', () => {
            let rgb = getColor()

            color.style.backgroundColor = rgb
            hcolor.value = rgb
        })
        if(color.style.backgroundColor == ''){
            let rgb = getColor()
            color.style.backgroundColor = rgb
            hcolor.value = rgb
        }
    </script>
</body>
</html>