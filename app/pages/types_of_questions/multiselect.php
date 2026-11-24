<style>
    :root {
        --font-size-option: clamp(1.2rem, 1rem + 0.8vw, 1.85rem);
        --grid-gap: clamp(12px, 1.5vw, 20px);
        --cols: 2;
    }

    @media (min-width: 560px) {
        :root { --cols: 3; }
    }

    @media (min-width: 840px) {
        :root { --cols: 4; }
    }

    @media (min-width: 1120px) {
        :root { --cols: 5; }
    }

    @media (min-width: 1400px) {
        :root { --cols: 6; }
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
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-content: flex-end;
        gap: var(--grid-gap);
        width: 100%;
        min-height: 100%;
        padding: 4px;
    }

    /* Растягиваем на всю ширину и сохраняем полную высоту, если все варианты помещаются в 1 ряд */
    .answers-grid:has(.answer-card:nth-child(1):last-child) {
        --cols: 1;
        --card-min-h: clamp(240px, 42dvh, 360px);
    }

    .answers-grid:has(.answer-card:nth-child(2):last-child) {
        --cols: 2;
        --card-min-h: clamp(240px, 42dvh, 360px);
    }

    @media (min-width: 560px) {
        .answers-grid:has(.answer-card:nth-child(3):last-child) {
            --cols: 3;
            --card-min-h: clamp(240px, 42dvh, 360px);
        }
    }

    @media (min-width: 840px) {
        .answers-grid:has(.answer-card:nth-child(4):last-child) {
            --cols: 4;
            --card-min-h: clamp(240px, 42dvh, 360px);
        }
    }

    @media (min-width: 1120px) {
        .answers-grid:has(.answer-card:nth-child(5):last-child) {
            --cols: 5;
            --card-min-h: clamp(240px, 42dvh, 360px);
        }
    }

    @media (min-width: 1400px) {
        .answers-grid:has(.answer-card:nth-child(6):last-child) {
            --cols: 6;
            --card-min-h: clamp(240px, 42dvh, 360px);
        }
    }

    /* Карточка варианта ответа (БЭМ: .answer-card) */
    .answer-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        flex: 0 0 calc((100% - (var(--cols) - 1) * var(--grid-gap)) / var(--cols));
        min-height: var(--card-min-h, clamp(205px, 34.5dvh, 330px));
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
        transition: filter 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
    }

    .answer-card__input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .answer-card:hover {
        filter: brightness(1.1);
        border-color: rgba(255, 255, 255, 0.55);
    }

    .answer-card:active {
        transform: scale(0.99);
    }

    /* Выбранное состояние карточки: более широкая внутренняя рамка с мягким фейдом к центру + яркое внешнее свечение */
    .answer-card:has(.answer-card__input:checked) {
        border-color: #ffffff;
        filter: brightness(1.18);
        box-shadow:
            inset 0 0 0 6px #ffffff,
            inset 0 0 18px 6px rgba(255, 255, 255, 0.42),
            0 0 22px 2px rgba(255, 255, 255, 0.45);
    }

    .answer-card:has(.answer-card__input:checked):hover {
        filter: brightness(1.22);
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

    /* Анимация встряски при попытке отправить пустой ответ */
    @keyframes emptyShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-6px); }
        75% { transform: translateX(6px); }
    }

    .answers-grid--shake .answer-card {
        animation: emptyShake 0.25s ease-in-out 2;
    }
</style>

<!-- Область ответов -->
<main class="answers-area">
    <form class="answers-grid" id="quiz-form" action="" method="POST">
        <?php
        $colors = get_colors_answer(count(QUESTION['answers']['variants']), 65, 35);
        foreach (QUESTION['answers']['variants'] as $key => $value) {
            echo '<label class="answer-card" style="background-color: ' . $colors[$key] . ';">';
            echo '<input type="checkbox" class="answer-card__input" name="answer[]" value="' . htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') . '">';
            echo '<span class="answer-card__text">' . QUESTION['answers']['variants'][$key] . '</span>';
            echo '</label>';
        }
        ?>
    </form>
</main>

<script>
    (function () {
        const form = document.getElementById('quiz-form');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const checked = form.querySelectorAll('.answer-card__input:checked');
            if (checked.length === 0) {
                e.preventDefault();
                form.classList.remove('answers-grid--shake');
                void form.offsetWidth;
                form.classList.add('answers-grid--shake');
            }
        });
    })();
</script>
