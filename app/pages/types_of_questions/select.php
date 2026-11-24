<style>
    :root {
        --font-size-option: clamp(1.2rem, 1rem + 0.8vw, 1.85rem);
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
        --cols: 1;
        --grid-gap: clamp(12px, 1.5vw, 20px);
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-content: flex-end;
        gap: var(--grid-gap);
        width: 100%;
        min-height: 100%;
    }

    /* Адаптивное количество колонок по ширине экрана */
    @media (min-width: 540px) {
        .answers-grid { --cols: 2; }
    }
    @media (min-width: 820px) {
        .answers-grid { --cols: 3; }
    }
    @media (min-width: 1100px) {
        .answers-grid { --cols: 4; }
    }
    @media (min-width: 1380px) {
        .answers-grid { --cols: 5; }
    }
    @media (min-width: 1640px) {
        .answers-grid { --cols: 6; }
    }

    /* Карточка варианта ответа (БЭМ: .answer-card) */
    .answer-card {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        flex: 0 0 calc((100% - (var(--cols) - 1) * var(--grid-gap)) / var(--cols));
        min-height: clamp(205px, 34.5dvh, 330px);
        max-height: 520px;
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

    /* Если все варианты помещаются в 1 строчку — растягиваем на всю ширину и оставляем прежнюю высоту */
    .answers-grid:has(.answer-card:nth-child(1):last-child) {
        --cols: 1;
    }
    .answers-grid:has(.answer-card:nth-child(1):last-child) .answer-card {
        min-height: clamp(240px, 42dvh, 360px);
    }

    @media (min-width: 540px) {
        .answers-grid:has(.answer-card:nth-child(2):last-child) {
            --cols: 2;
        }
        .answers-grid:has(.answer-card:nth-child(2):last-child) .answer-card {
            min-height: clamp(240px, 42dvh, 360px);
        }
    }

    @media (min-width: 820px) {
        .answers-grid:has(.answer-card:nth-child(3):last-child) {
            --cols: 3;
        }
        .answers-grid:has(.answer-card:nth-child(3):last-child) .answer-card {
            min-height: clamp(240px, 42dvh, 360px);
        }
    }

    @media (min-width: 1100px) {
        .answers-grid:has(.answer-card:nth-child(4):last-child) {
            --cols: 4;
        }
        .answers-grid:has(.answer-card:nth-child(4):last-child) .answer-card {
            min-height: clamp(240px, 42dvh, 360px);
        }
    }

    @media (min-width: 1380px) {
        .answers-grid:has(.answer-card:nth-child(5):last-child) {
            --cols: 5;
        }
        .answers-grid:has(.answer-card:nth-child(5):last-child) .answer-card {
            min-height: clamp(240px, 42dvh, 360px);
        }
    }

    @media (min-width: 1640px) {
        .answers-grid:has(.answer-card:nth-child(6):last-child) {
            --cols: 6;
        }
        .answers-grid:has(.answer-card:nth-child(6):last-child) .answer-card {
            min-height: clamp(240px, 42dvh, 360px);
        }
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

<!-- Область ответов -->
<main class="answers-area">
    <form class="answers-grid" id="quiz-form" action="" method="POST">
        <?php
        $colors = get_colors_answer(count(QUESTION['answers']['variants']), 65, 35);
        foreach (QUESTION['answers']['variants'] as $key => $value) {
            $st = '<button type="submit" name="answer" class="answer-card" value="';
            $fin = '</span></button>';
            echo $st . $key . '" style="background-color: ' . $colors[$key] . ';"><span class="answer-card__text">' . QUESTION['answers']['variants'][$key] . $fin;
        }
        ?>
    </form>
</main>
