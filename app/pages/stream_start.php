<?php
// 1. Отключаем буферизацию и сжатие веб-сервера Apache (как в твоем админском стриме)
if (function_exists('apache_setenv')) { 
    @apache_setenv('no-gzip', '1'); 
}
@ini_set('output_buffering', 'Off');
@ini_set('zlib.output_compression', 'Off');

// 2. Устанавливаем обязательные заголовки для SSE-потока
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Путь к файлу состояния игры (из app/pages/ поднимаемся на уровень выше в app/database/)
$statePath = __DIR__ . '/../database/game_state.json';

// Пробиваем буфер Apache сразу при подключении, чтобы браузер мгновенно установил связь
echo ":" . str_repeat(" ", 4096) . "\n\n";
@ob_flush();
flush();

$lastKnownHash = '';

// 3. Бесконечный цикл проверки старта игры
while (true) {
    // Если игрок закрыл вкладку — завершаем скрипт
    if (connection_aborted()) {
        exit;
    }

    clearstatcache();

    if (file_exists($statePath)) {
        $currentHash = md5_file($statePath);

        // Проверяем файл, только если его содержимое реально изменилось
        //закоментировано по той же причене, что и в admin/stream.php
        // if ($currentHash !== $lastKnownHash) {
            $lastKnownHash = $currentHash;
            $stateData = json_decode(file_get_contents($statePath), true);

            // Если статус изменился на started — отправляем команду и завершаем стрим
            if (isset($stateData['status']) && $stateData['status'] === 'started') {
                echo ":" . str_repeat(" ", 4096) . "\n";
                echo "event: game_start\n";
                echo "data: " . json_encode(['status' => 'started']) . "\n\n";

                @ob_flush();
                flush();
                
                // Выходим из цикла, так как игра началась и стрим больше не нужен
                break;
            }
        // }
    }

    sleep(1);
}