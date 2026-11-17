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
            padding: clamp(12px, 2vw, 24px);
            overflow: hidden;
        }

        /* Блок вопроса на всю ширину (БЭМ: .question-box) */
        .question-box {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(1.25rem, 1rem + 1.5vw, 2.5rem);
            width: 100%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            text-align: center;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
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

        /* Контейнер со скроллом для ответов (БЭМ: .answers-area) */
        .answers-area {
            width: 100%;
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }

        .answers-area::-webkit-scrollbar {
            width: 8px;
        }

        .answers-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .answers-area::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
            border-radius: 4px;
        }

        .answers-area::-webkit-scrollbar-thumb:hover {
            background-color: var(--text-muted);
        }

        /* Форма-сетка карточек (БЭМ: .answers-grid) */
        .answers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
            grid-auto-rows: auto;
            align-content: end;
            gap: clamp(12px, 1.5vw, 20px);
            width: 100%;
            min-height: 100%;
        }

        /* Карточка варианта ответа (БЭМ: .answer-card) */
        .answer-card {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            min-height: clamp(240px, 42vh, 360px);
            max-height: 560px;
            padding: clamp(20px, 2.5vw, 36px);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            color: #ffffff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
            font-family: inherit;
            font-size: var(--font-size-option);
            font-weight: 600;
            line-height: 1.35;
            cursor: pointer;
            user-select: none;
            transition: filter 0.15s ease, border-color 0.15s ease, transform 0.1s ease;
        }

        .answer-card:hover {
            filter: brightness(1.12);
            border-color: rgba(255, 255, 255, 0.55);
        }

        .answer-card:active {
            transform: scale(0.99);
            filter: brightness(0.95);
        }

        /* Текст внутри карточки ответа */
        .answer-card__text {
            width: 100%;
            max-height: 100%;
            overflow-y: auto;
            overflow-wrap: anywhere;
            word-break: break-word;
            scrollbar-width: none;
        }

        .answer-card__text::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body>
    <?php
    if(QUESTION === null){
        define('QUESTION', CONFIG['questions'][$_SESSION['qnum']]);
    }
    ?>

    <!-- Верхний блок с вопросом -->
    <header class="question-box">
        <h1 class="question-box__title"><?php echo QUESTION['title']; ?></h1>
        <p class="question-box__subtitle"><?php echo QUESTION['subtitle'] ?? null; ?></p>
    </header>

    <!-- Область ответов -->
    <main class="answers-area">
        <form class="answers-grid" id="answers-container" action="" method="POST">

            
            <?php
            
            $colors = get_colors_answer(count(QUESTION['answers']['variants']), 65, 35);
            foreach(QUESTION['answers']['variants'] as $key => $value){
                $st = '<button type="submit" name="answer" class="answer-card" value="';
                $fin = '</span></button>';
                echo $st . $key . '" style="background-color: ' . $colors[$key] . ';"><span class="answer-card__text">' . QUESTION['answers']['variants'][$key] . $fin;
            };

            ?>
        </form>
    </main>

</body>
</html>