<style>
    :root {
        --font-mono: ui-monospace, SFMono-Regular, 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace;
        --font-size-input: clamp(1.25rem, 1.05rem + 0.9vw, 1.85rem);
    }

    .answer-area {
        width: 100%;
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        overflow-y: auto;
    }

    .answer-form {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: clamp(20px, 2.5vw, 36px) clamp(1.5rem, 3vw, 3rem);
        width: 100%;
        min-height: clamp(240px, 42vh, 360px);
        max-height: 560px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
        display: flex;
        flex-direction: column;
        justify-content: center;
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
</style>

<main class="answer-area">
    <form class="answer-form" id="quiz-form" action="" method="POST" autocomplete="off">
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
    </form>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('char-cells-container');
        if (!container) return;

        const cells = Array.from(container.querySelectorAll('.char-cell'));
        const hiddenInput = document.getElementById('final-hidden-answer');
        const form = document.getElementById('quiz-form');

        function updateHiddenValue() {
            if (hiddenInput) {
                hiddenInput.value = cells.map(cell => cell.value).join('');
            }
        }

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
            cell.addEventListener('input', () => {
                if (cell.value.length > 1) {
                    cell.value = cell.value.slice(-1);
                }

                updateHiddenValue();

                if (cell.value !== '' && idx < cells.length - 1) {
                    focusAndScroll(idx + 1);
                }
            });

            cell.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace') {
                    e.preventDefault();

                    if (e.ctrlKey || e.metaKey) {
                        let i = idx;

                        if (cells[i].value === '' && i > 0) {
                            i--;
                        }

                        while (i >= 0 && (cells[i].value === '' || cells[i].value === ' ')) {
                            cells[i].value = '';
                            i--;
                        }

                        while (i >= 0 && cells[i].value !== '' && cells[i].value !== ' ') {
                            cells[i].value = '';
                            i--;
                        }

                        updateHiddenValue();
                        focusAndScroll(Math.max(0, i + 1));
                        return;
                    }

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

            cell.addEventListener('focus', () => {
                setTimeout(() => cell.select(), 0);
            });
        });

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
