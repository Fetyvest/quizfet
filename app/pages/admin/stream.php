<?php
// 1. Отключаем буферизацию и сжатие веб-сервера Apache
if (function_exists('apache_setenv')) { 
    @apache_setenv('no-gzip', '1'); 
}
@ini_set('output_buffering', 'Off');
@ini_set('zlib.output_compression', 'Off');

// 2. Устанавливаем обязательные заголовки для SSE-потока
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Путь к файлу базы данных игроков
$dbPath = __DIR__ . '/../../database/players.json';

// Переменная для хранения хэша последней отправленной версии файла
$lastKnownHash = '';

// 3. Бесконечный цикл трансляции данных
while (true) {
    // Принудительно очищаем кэш состояния файлов в PHP
    clearstatcache();

    if (file_exists($dbPath)) {
        // Считаем хэш файла. Если файл перезаписан (даже с тем же размером) — хэш изменится.
        $currentHash = md5_file($dbPath);

        // Условие срабатывает ТОЛЬКО если база данных реально обновилась
        //чёт с ним не работает, странно
        // if ($currentHash !== $lastKnownHash) {
            
            // Читаем свежий JSON игроков
            $playersJson = file_get_contents($dbPath);

            // Пробиваем буфер Apache невидимым для JS комментарием (4 КБ пробелов)
            echo ":" . str_repeat(" ", 4096) . "\n"; 
            
            // Отправляем актуальные данные в формате SSE
            echo "data: " . $playersJson . "\n\n";

            // Силой выталкиваем отправленные строки из памяти сервера прямо в сеть к админу
            ob_flush();
            flush();

            // Запоминаем новый хэш, чтобы цикл ушел в режим ожидания и не спамил процессор
            $lastKnownHash = $currentHash;
        // }
    }

    // Спим 1 секунду перед следующей проверкой файла на диске
    sleep(1);
}