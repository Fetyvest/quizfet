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
            
            /* Адаптивные шрифты */
            --font-size-h1: clamp(1.5rem, 1.188rem + 1.5vw, 2.25rem);
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
            padding: clamp(20px, 5vw, 60px) 20px;
        }

        /* Блок админ-панели (БЭМ: .admin-panel) */
        .admin-panel {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: clamp(1.5rem, 1.083rem + 2.08vw, 3rem);
            width: 100%;
            max-width: 600px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            height: fit-content;
        }

        .admin-panel__title {
            font-size: var(--font-size-h1);
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }

        /* Индикатор подключения */
        .admin-panel__title::after {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            background-color: var(--accent-green);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--accent-green);
        }

        .admin-panel__list {
            display: flex;
            flex-direction: column;
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

    <div class="admin-panel">
        <h1 class="admin-panel__title">Игроки</h1>
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
                    // Только цвет через JS
                    colorBadge.style.backgroundColor = player.color || '#00000000'; 

                    // Текст никнейма
                    const textNode = document.createTextNode(player.username || 'Аноним');

                    // Собираем элементы вместе
                    card.appendChild(colorBadge);
                    card.appendChild(textNode);
                    
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