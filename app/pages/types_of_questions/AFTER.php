<style>
    body {
        padding-bottom: 0;
    }

    .game-footer {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        width: 100%;
        height: 46px;
        padding: 0 16px;
        box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .game-footer__hint {
        font-size: 0.9rem;
        color: var(--text-muted);
        user-select: none;
    }

    .game-footer__submit {
        height: 30px;
        padding: 0 48px;
        background-color: var(--accent-green, #2ea043);
        color: #ffffff;
        border: 1px solid rgba(240, 246, 252, 0.1);
        border-radius: 6px;
        font-family: inherit;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s ease, transform 0.1s ease;
    }

    .game-footer__submit:hover {
        background-color: var(--accent-green-hover, #3fb950);
    }

    .game-footer__submit:active {
        transform: scale(0.98);
    }
</style>

<footer class="game-footer">
    <?php if (QUESTION['type'] === 'select'): ?>
        <span class="game-footer__hint">Нажмите на правильный вариант</span>
    <?php else: ?>
        <button type="submit" form="quiz-form" class="game-footer__submit">Ответить</button>
    <?php endif; ?>
</footer>

</body>
</html>
