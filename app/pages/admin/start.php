<?php
// Проверяем, что запрос пришел именно методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$statePath = __DIR__ . '/../../database/game_state.json';

$newState = [
    'status' => 'started',
    'started_at' => time()
];

// Перезаписываем файл состояния с блокировкой от одновременной записи
file_put_contents($statePath, json_encode($newState, JSON_PRETTY_PRINT), LOCK_EX);

// Отдаем успешный JSON-ответ
header('Content-Type: application/json');
echo json_encode(['success' => true]);