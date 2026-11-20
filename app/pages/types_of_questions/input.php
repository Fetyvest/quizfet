<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Ввод ответа</title>
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
            --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            --font-mono: ui-monospace, SFMono-Regular, 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace;
            --font-size-h1: clamp(1.35rem, 1.1rem + 1.5vw, 2.25rem);
            --font-size-h2: clamp(1rem, 0.9rem + 0.5vw, 1.25rem);
            --font-size-input: clamp(1.25rem, 1.05rem + 0.9vw, 1.85rem);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-color);
            color: var(--text-main);
            height: 100dvh;
            display: flex;
            flex-direction: column;
            gap: clamp(16px, 3vh, 32px);
            padding: clamp(12px, 2vw, 24px);
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

        .question-box {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(1.25rem, 1rem + 1.5vw, 2.5rem);
            width: 100%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
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

        .answer-area {
            width: 100%;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }

        .answer-form {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(2rem, 4vh, 4rem) clamp(1.5rem, 3vw, 3rem);
            width: 100%;
            min-height: clamp(240px, 40vh, 420px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: clamp(24px, 4vh, 40px);
        }

        .input-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            width: 100%;
            min-width: 0;
        }

        .input-affix,
        .input-center-scroll:has(.char-cells-wrapper) {
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 8px;
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }

        .input-affix::-webkit-scrollbar,
        .input-center-scroll:has(.char-cells-wrapper)::-webkit-scrollbar {
            height: 6px;
        }

        .input-affix::-webkit-scrollbar-track,
        .input-center-scroll:has(.char-cells-wrapper)::-webkit-scrollbar-track {
            background: 0 0;
        }

        .input-affix::-webkit-scrollbar-thumb,
        .input-center-scroll:has(.char-cells-wrapper)::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
            border-radius: 4px;
        }

        .input-affix::-webkit-scrollbar-thumb:hover,
        .input-center-scroll:has(.char-cells-wrapper)::-webkit-scrollbar-thumb:hover {
            background-color: var(--text-muted);
        }

        .input-affix {
            font-family: var(--font-mono);
            font-size: var(--font-size-input);
            color: var(--text-main);
            white-space: nowrap;
            max-width: 25%;
            min-width: 0;
            flex: 0 1 auto;
        }

        .input-affix:empty {
            display: none;
        }

        .input-center-scroll {
            display: flex;
            align-items: center;
            min-width: 0;
            flex: 0 1 auto;
        }

        .input-center-scroll:has(.char-cells-wrapper) {
            max-width: 50%;
            padding-top: 4px;
        }

        .input-center-scroll:has(.text-input-standard) {
            overflow: visible;
            padding-bottom: 8px;
        }

        .text-input-standard {
            background-color: var(--player-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 16px 20px;
            color: var(--text-main);
            font-family: var(--font-mono);
            font-size: var(--font-size-input);
            font-weight: 500;
            width: clamp(260px, 35vw, 520px);
            max-width: 100%;
            outline: 0;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .text-input-standard:focus {
            border-color: var(--border-active);
            box-shadow: 0 0 0 3px rgba(31, 111, 235, .25);
        }

        .char-cells-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .char-cell {
            width: clamp(2.8rem, 2.4rem + 1.5vw, 3.8rem);
            height: clamp(3.4rem, 3rem + 1.5vw, 4.4rem);
            background-color: var(--player-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-main);
            font-family: var(--font-mono);
            font-size: var(--font-size-input);
            font-weight: 600;
            text-align: center;
            outline: 0;
            caret-color: var(--border-active);
            transition: border-color .15s ease, transform .1s ease, box-shadow .15s ease;
        }

        .char-cell:focus {
            border-color: var(--border-active);
            box-shadow: 0 0 0 3px rgba(31, 111, 235, .25);
            transform: translateY(-2px);
        }

        .btn-submit {
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            padding: 16px 28px;
            background-color: var(--accent-green);
            color: #fff;
            border: 1px solid rgba(240, 246, 252, .1);
            border-radius: 6px;
            font-family: var(--font-sans);
            font-size: clamp(1.05rem, 1rem + .3vw, 1.25rem);
            font-weight: 600;
            cursor: pointer;
            transition: background-color .2s, transform .1s;
        }

        .btn-submit:hover {
            background-color: var(--accent-green-hover);
        }

        .btn-submit:active {
            transform: scale(.99);
        }
    </style>
</head>

<body>
    <!-- Оверлей плавного появления страницы -->
    <div class="screen-reveal"></div>

    <header class="question-box">
        <h1 class="question-box__title"><?php echo QUESTION['title']; ?></h1>
        <p class="question-box__subtitle"><?php echo QUESTION['subtitle'] ?? null; ?></p>
    </header>

    <main class="answer-area">
        <form class="answer-form" id="answer-form" action="" method="POST" autocomplete="off">
            <div class="input-row">
                <span class="input-affix"><?php echo QUESTION['answers']['done']['before'] ?? null ?></span>
                <div class="input-center-scroll">
                    <?php
                    if(isset(QUESTION['answers']['showCountSymbols']) && QUESTION['answers']['showCountSymbols'] == true){
                        echo '<input type="hidden" name="answer" id="final-hidden-answer">';
                        echo '<div class="char-cells-wrapper" id="char-cells-container">';

                        $maxlength = 0;
                        if(is_array(QUESTION['answers']['correct'])){
                            foreach(QUESTION['answers']['correct'] as $el){
                                if(mb_strlen((string)$el) > $maxlength){
                                    $maxlength = mb_strlen((string)$el);
                                }
                            }
                        }
                        else{
                            $maxlength = mb_strlen((string)QUESTION['answers']['correct']);
                        }
                        echo '<input type="text" maxlength="1" class="char-cell" autofocus>';
                        for($i = 0; $i < $maxlength - 1; $i++){
                            echo '<input type="text" maxlength="1" class="char-cell">';
                        }

                        echo '</div>';
                    }
                    else{
                        echo '<input type="text" name="answer" class="text-input-standard" required autofocus>';
                    }
                    ?>
                </div>
                <span class="input-affix"><?php echo QUESTION['answers']['done']['after'] ?? null ?></span>
            </div>
            <button type="submit" class="btn-submit">Ответить</button>
        </form>
    </main>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('char-cells-container');
        if (!container) return;

        const cells = Array.from(container.querySelectorAll('.char-cell'));
        const hiddenInput = document.getElementById('final-hidden-answer');
        const form = document.getElementById('answer-form');

        // Собираем все символы из ячеек в единую строку для отправки на сервер
        function updateHiddenValue() {
            if (hiddenInput) {
                hiddenInput.value = cells.map(cell => cell.value).join('');
            }
        }

        // Плавно прокручиваем горизонтальный контейнер к активной ячейке
        function focusAndScroll(index) {
            if (index >= 0 && index < cells.length) {
                const target = cells[index];
                target.focus();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            }
        }

        cells.forEach((cell, idx) => {
            // 1. Ввод символа
            cell.addEventListener('input', () => {
                if (cell.value.length > 1) {
                    cell.value = cell.value.slice(-1);
                }

                updateHiddenValue();

                if (cell.value !== '' && idx < cells.length - 1) {
                    focusAndScroll(idx + 1);
                }
            });

            // 2. Обработка клавиш управления
            cell.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    e.preventDefault();

                    // Поддержка Ctrl + Backspace (и Cmd + Backspace на Mac)
                    if (e.ctrlKey || e.metaKey) {
                        let i = idx;

                        // Если текущая ячейка пустая и не первая — шагаем назад
                        if (cells[i].value === '' && i > 0) {
                            i--;
                        }

                        // Пропускаем и очищаем пробелы/пустые ячейки перед словом
                        while (i >= 0 && (cells[i].value === '' || cells[i].value === ' ')) {
                            cells[i].value = '';
                            i--;
                        }

                        // Стираем само слово до пробела, пустой ячейки или начала
                        while (i >= 0 && cells[i].value !== '' && cells[i].value !== ' ') {
                            cells[i].value = '';
                            i--;
                        }

                        updateHiddenValue();
                        // Ставим фокус на первую ячейку стёртого слова
                        focusAndScroll(Math.max(0, i + 1));
                        return;
                    }

                    // Обычный Backspace
                    if (cell.value !== '') {
                        cell.value = '';
                    } else if (idx > 0) {
                        cells[idx - 1].value = '';
                        focusAndScroll(idx - 1);
                    }
                    updateHiddenValue();

                } else if (e.key === 'Delete') {
                    e.preventDefault();
                    if (cell.value !== '') {
                        cell.value = '';
                    } else {
                        for (let i = idx; i < cells.length - 1; i++) {
                            cells[i].value = cells[i + 1].value;
                        }
                        cells[cells.length - 1].value = '';
                    }
                    updateHiddenValue();

                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    focusAndScroll(idx - 1);

                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    focusAndScroll(idx + 1);

                } else if (e.key === 'Home') {
                    e.preventDefault();
                    focusAndScroll(0);

                } else if (e.key === 'End') {
                    e.preventDefault();
                    focusAndScroll(cells.length - 1);
                }
            });

            // 3. Вставка текста целиком из буфера обмена (Ctrl+V / Cmd+V)
            cell.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteText = (e.clipboardData || window.clipboardData).getData('text');
                if (!pasteText) return;

                const chars = pasteText.split('');
                let currentIdx = idx;

                chars.forEach(char => {
                    if (currentIdx < cells.length) {
                        cells[currentIdx].value = char;
                        currentIdx++;
                    }
                });

                updateHiddenValue();

                const nextFocusIdx = Math.min(currentIdx, cells.length - 1);
                focusAndScroll(nextFocusIdx);
            });

            // 4. Выделение символа при фокусе
            cell.addEventListener('focus', () => {
                setTimeout(() => cell.select(), 0);
            });
        });

        // Финальная синхронизация перед отправкой формы
        if (form) {
            form.addEventListener('submit', (e) => {
                updateHiddenValue();
                if (hiddenInput && hiddenInput.value.trim() === '') {
                    e.preventDefault();
                    focusAndScroll(0);
                }
            });
        }
    });
</script>

</body>
</html>