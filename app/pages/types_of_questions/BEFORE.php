<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вопрос квиза</title>
    <style>
        :root {
            --bg-color: #0d1117;
            --card-bg: #161b22;
            --player-bg: #21262d;
            --border-color: #30363d;
            --border-active: #1f6feb;
            --text-main: #c9d1d9;
            --text-muted: #8b949e;

            /* Адаптивные шрифты на основе clamp() */
            --font-size-game-h1: clamp(1rem, 0.9rem + 0.6vw, 1.35rem);
            --font-size-h1: clamp(1.35rem, 1.1rem + 1.5vw, 2.25rem);
            --font-size-h2: clamp(1rem, 0.9rem + 0.5vw, 1.25rem);
            --font-size-option: clamp(1.2rem, 1rem + 0.8vw, 1.85rem);
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
            height: 100dvh;
            display: flex;
            flex-direction: column;
            gap: clamp(16px, 3vh, 32px);
            padding: 0 clamp(12px, 2vw, 24px);
            overflow: hidden;
        }

        /* Оверлей для мгновенного старта плавного выхода из темноты (500 мс) */
        .screen-reveal {
            position: fixed;
            inset: 0;
            background-color: var(--bg-color);
            z-index: 999;
            pointer-events: none;
            animation: revealScreen 0.5s ease-out forwards;
        }

        @keyframes revealScreen {
            0% {
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        /* Компактная верхняя плашка с названием игры */
        .game-header {
            position: relative;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 8px 8px;
            padding: 8px 20px;
            width: 100%;
            text-align: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .game-header__title {
            font-size: var(--font-size-game-h1);
            font-weight: 600;
            color: var(--text-muted);
            line-height: 1.2;
            overflow-wrap: anywhere;
        }

        .game-header__counter {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: calc(var(--font-size-game-h1) * 0.85);
            font-weight: 600;
            color: var(--text-muted);
            background-color: var(--player-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 2px 10px;
            line-height: 1.2;
        }

        .game-header__counter:empty {
            display: none;
        }

        /* Блок вопроса (БЭМ: .question-box) */
        .question-box {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            width: 100%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            flex-shrink: 0;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: stretch;
            max-height: 35dvh;
            overflow: hidden;
        }

        /* Если картинки нет — центрируем заголовки */
        .question-box:has(.question-box__media:empty) {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .question-box__content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
            min-width: 0;
            padding: clamp(1.25rem, 1rem + 1.5vw, 2.5rem);
            max-height: 35dvh;
            overflow-y: auto;
        }

        .question-box__subtitle:empty {
            display: none;
        }

        .question-box__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            line-height: 1.3;
            color: var(--text-main);
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .question-box__subtitle {
            font-size: var(--font-size-h2);
            font-weight: 400;
            color: var(--text-muted);
            line-height: 1.4;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        /* Контейнер картинки: занимает всю высоту плашки без её растягивания */
        .question-box__media {
            height: 0;
            min-height: 100%;
            padding: 10px 10px 10px 0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .question-box__media:empty {
            display: none;
        }

        .question-box__media img {
            height: 100%;
            width: auto;
            max-width: min(35vw, 380px);
            object-fit: contain;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            cursor: zoom-in;
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .question-box__media img:hover {
            border-color: var(--text-muted);
            transform: scale(1.02);
        }

        /* Модальное окно для увеличенной картинки */
        .image-modal {
            position: fixed;
            inset: 0;
            z-index: 1000;
            background-color: rgba(13, 17, 23, 0.88);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            pointer-events: none;
            cursor: zoom-out;
            transition: opacity 0.2s ease;
        }

        .image-modal.is-open {
            opacity: 1;
            pointer-events: auto;
        }

        .image-modal__img {
            max-width: 92vw;
            max-height: 88dvh;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.65);
            transform: scale(0.96);
            transition: transform 0.2s ease;
        }

        .image-modal.is-open .image-modal__img {
            transform: scale(1);
        }
    </style>
</head>
<body>
    <!-- Оверлей плавного появления страницы -->
    <div class="screen-reveal"></div>

    <?php
    if (QUESTION === null) {
        define('QUESTION', CONFIG['questions'][$_SESSION['qnum']]);
    }
    ?>

    <!-- Верхняя плашка с названием игры -->
    <header class="game-header">
        <div class="game-header__title"><?php echo CONFIG['game']['title']; ?></div>
        <div class="game-header__counter"><?php
            if(CONFIG['game']['showQuestionCount'] == true){
                $curent = $_SESSION['qnum'] + 1;
                $all = count(CONFIG['questions']);
                echo $curent . '/' . $all;
            }
            else{
                $curent = $_SESSION['qnum'] + 1;
                echo $curent;
            }
        ?></div>
    </header>

    <!-- Блок с вопросом и картинкой -->
    <header class="question-box">
        <div class="question-box__content">
            <h1 class="question-box__title"><?php echo QUESTION['title']; ?></h1>
            <p class="question-box__subtitle"><?php echo QUESTION['subtitle'] ?? null; ?></p>
        </div>

        <div class="question-box__media"><?php
            if(isset(QUESTION['image'])){
                $path = QUESTION['image'];
                if(mb_substr($path, 0, 1, 'UTF-8') == '.'){
                    $path = '../app/config/' . QUESTION['image'];
                }
                if(file_exists($path)){
                    $type = mime_content_type($path);
                    $data = file_get_contents($path);
                    $base64 = 'data:' . $type . ';base64,' . base64_encode($data);
                    echo "<img src=\"$base64\" alt=\"Картинка\">";
                }
                else {
                    $headers = @get_headers($path);
                    if ($headers && strpos($headers[0], '200') !== false) {
                        $type = mime_content_type($path);
                        $data = file_get_contents($path);
                        $base64 = 'data:' . $type . ';base64,' . base64_encode($data);
                        echo "<img src=\"$base64\" alt=\"Картинка\">";
                    }
                }
            }
            ?></div>
    </header>

    <?php if (!empty($base64)): ?>
    <!-- Модальное окно для просмотра картинки -->
    <div class="image-modal" id="image-modal" aria-hidden="true">
        <img class="image-modal__img" src="<?php echo $base64; ?>" alt="Увеличенное изображение">
    </div>

    <script>
        (function () {
            const mediaImg = document.querySelector('.question-box__media img');
            const imageModal = document.getElementById('image-modal');

            if (!mediaImg || !imageModal) return;

            mediaImg.addEventListener('click', () => {
                imageModal.classList.add('is-open');
            });

            imageModal.addEventListener('click', () => {
                imageModal.classList.remove('is-open');
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && imageModal.classList.contains('is-open')) {
                    imageModal.classList.remove('is-open');
                }
            });
        })();
    </script>
    <?php endif; ?>
