<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель ожидания Админа</title>
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
            
            /* Адаптивные шрифты */
            --font-size-h1: clamp(1.5rem, 1.188rem + 1.5vw, 2.25rem);
            --font-size-h2: clamp(1.25rem, 1.05rem + 0.9vw, 1.75rem);
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
            align-items: center;
            gap: 20px;
            padding: clamp(20px, 5vw, 60px) 20px;
        }

        /* Общий контейнер карточки (БЭМ: .admin-panel) */
        .admin-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(1.5rem, 1.083rem + 2.08vw, 3rem);
            width: 100%;
            max-width: 1000px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            height: fit-content;
        }

        /* Верхний блок управления игрой (БЭМ: .game-header) */
        .game-header {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }

        .game-header__info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        /* Скрываем пустые текстовые блоки, чтобы не оставалось лишних отступов */
        .game-header__info:empty,
        .game-header__title:empty,
        .game-header__subtitle:empty,
        .game-header__meta:empty {
            display: none;
        }

        .game-header__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            line-height: 1.25;
            color: var(--text-main);
        }

        .game-header__subtitle {
            font-size: var(--font-size-body);
            color: var(--text-muted);
            line-height: 1.5;
        }

        .game-header__meta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }

        .game-header__badge {
            display: inline-block;
            background-color: var(--player-bg);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 20px;
        }

        /* Кнопка старта (БЭМ: .btn-start) */
        .btn-start {
            width: 100%;
            padding: 12px 20px;
            background-color: var(--accent-green);
            color: #ffffff;
            border: 1px solid rgba(240, 246, 252, 0.1);
            border-radius: 6px;
            font-family: inherit;
            font-size: var(--font-size-body);
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-start:hover:not(:disabled) {
            background-color: var(--accent-green-hover);
        }
        .btn-start:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* .btn-start:active {
            transform: scale(0.99);
        } */

        /* Блок списка игроков */
        .admin-panel__title {
            font-size: var(--font-size-h2);
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            /* justify-content: space-evenly; */
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }

        /* Индикатор подключения */
        /* .admin-panel__title::after,
        .admin-panel__title::before {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            background-color: var(--accent-green);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--accent-green);
        } */

        .admin-panel__list {
            display: flex;
            /* flex-direction: column; */
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            font-size: var(--font-size-body);
        }

        .admin-panel__status {
            color: var(--text-muted);
            font-style: italic;
            text-align: center;
            padding: 24px 0;
        }

        /* Карточка игрока (БЭМ: .player-card) */
        .player-card {
            background-color: var(--player-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 14px 18px;
            color: var(--text-main);
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: border-color 0.2s, transform 0.1s;
            max-width: 80%;
        }

        .player-card__name {
  white-space: nowrap;
  overflow-x: auto;
  scrollbar-width: none;
  -ms-overflow-style: none;
              &::webkit-scrollbar{
                display: none;
            }
}

        .player-card:hover {
            border-color: var(--border-active);
        }

        /* Цветной квадрат перед ником */
        .player-card__badge {
            display: inline-block;
            width: 1.5em;
            height: 1.5em;
            margin-right: 12px;
            border-radius: 3px;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <!-- Верхний блок с информацией об игре и кнопкой Старт -->
    <div class="admin-panel game-header">
        <div class="game-header__info">
            <h1 class="game-header__title"><?php echo FILE['game']['title']; ?></h1>
            <p class="game-header__subtitle"><?php echo FILE['game']['subtitle']; ?></p>
            <div class="game-header__meta">
                <span class="game-header__badge"><?php echo 'Вопросов: ' . count(FILE['questions']); ?></span>
            </div>
        </div>
        <button type="button" class="btn-start" id="start-game-btn" disabled>Старт</button>
    </div>

    <!-- Блок списка игроков -->
    <div class="admin-panel">
        <h2 class="admin-panel__title">Игроки</h2>
        <div class="admin-panel__list" id="players-list">
            <div class="admin-panel__status">Подключение к серверу...</div>
        </div>
    </div>

    <script>
        // Подключаемся к точке входа с флагом для запуска нашего закрытого stream.php
        const eventSource = new EventSource('/?action=stream_players');
        const listContainer = document.getElementById('players-list');

        // Ловим новые данные от сервера
        eventSource.onmessage = function(event) {
            try {
                const players = JSON.parse(event.data);
                listContainer.innerHTML = '';

                if (!players || players.length === 0) {
                    listContainer.innerHTML = '<div class="admin-panel__status"><i>Игроков пока нет.</i></div>';
                    return;
                }

                // Выводим игроков на экран
players.forEach(player => {
    const card = document.createElement('div');
    card.className = 'player-card';
    
    // Создаем квадрат и вешаем БЭМ-класс
    const colorBadge = document.createElement('span');
    colorBadge.className = 'player-card__badge';
    colorBadge.style.backgroundColor = player.color || '#00000000'; 

    // Оборачиваем текст никнейма в отдельный элемент для скролла
    const nameElement = document.createElement('span');
    nameElement.className = 'player-card__name';
    nameElement.textContent = player.username || 'Аноним';

    // Собираем элементы вместе
    card.appendChild(colorBadge);
    card.appendChild(nameElement);
    
    listContainer.appendChild(card);
});


            } catch (error) {
                console.error("Ошибка парсинга JSON базы данных:", error);
            }
        };

        eventSource.onerror = function() {
            console.log("Соединение оборвалось. Скрипт переподключится сам...");
        };
    </script>
</body>
</html>