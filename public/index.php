<?php

if(!file_exists('../app/config/config.json')){
    file_put_contents('../app/config/config.json', '{}');
}
if (file_exists('../app/config/config.json')){
    define('CONFIG', array_replace_recursive(json_decode(file_get_contents('../app/config/default_config.json'), true), json_decode(file_get_contents('../app/config/config.json'), true)));
}
if(!file_exists('../app/database/game_state.json') || filesize('../app/database/game_state.json') === 0){
    file_put_contents('../app/database/game_state.json', '{"status": "waiting"}');
}
if(!file_exists('../app/database/players.json')){
    file_put_contents('../app/database/players.json', '[]');
}

function get_colors_answer(int $count, float $s = 100.0, float $l = 50.0, float $hStart = 0){
    function hslToHex(float $h, float $s, float $l): string {
        $s /= 100.0;
        $l /= 100.0;

        $c = (1.0 - abs(2.0 * $l - 1.0)) * $s;
        $x = $c * (1.0 - abs(fmod($h / 60.0, 2.0) - 1.0));
        $m = $l - $c / 2.0;

        $r = 0.0;
        $g = 0.0;
        $b = 0.0;

        if ($h >= 0 && $h < 60) {
            $r = $c; $g = $x; $b = 0;
        } elseif ($h >= 60 && $h < 120) {
            $r = $x; $g = $c; $b = 0;
        } elseif ($h >= 120 && $h < 180) {
            $r = 0; $g = $c; $b = $x;
        } elseif ($h >= 180 && $h < 240) {
            $r = 0; $g = $x; $b = $c;
        } elseif ($h >= 240 && $h < 300) {
            $r = $x; $g = 0; $b = $c;
        } else {
            $r = $c; $g = 0; $b = $x;
        }

        $r = (int) round(($r + $m) * 255);
        $g = (int) round(($g + $m) * 255);
        $b = (int) round(($b + $m) * 255);

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
    function customHueToHex(float $customHue, float $s = 100.0, float $l = 50.0): string {
        // Нормализуем угол, чтобы он всегда был в пределах [0, 360)
        $h = fmod($customHue, 360.0);
        if ($h < 0) {
            $h += 360.0;
        }

        // Ремаппинг кастомного Hue в стандартный Hue (0..360)
        if ($h < 90.0) {
            // 0..90 -> 0..60 (Красный -> Жёлтый)
            $standardHue = ($h / 90.0) * 60.0;
        } elseif ($h < 180.0) {
            // 90..180 -> 60..120 (Жёлтый -> Зелёный)
            $standardHue = 60.0 + (($h - 90.0) / 90.0) * 60.0;
        } elseif ($h < 270.0) {
            // 180..270 -> 120..240 (Зелёный -> Синий)
            $standardHue = 120.0 + (($h - 180.0) / 90.0) * 120.0;
        } else {
            // 270..360 -> 240..360 (Синий -> Красный)
            $standardHue = 240.0 + (($h - 270.0) / 90.0) * 120.0;
        }

        return hslToHex($standardHue, $s, $l);
    }
    $result = [];
    $step = 360 / $count;
    $hStart = $step * 0;
    for ($i = 0; $i < $count; $i++){
        $result[] = customHueToHex($hStart + $step * $i, $s, $l);
    }

    return $result;
}

if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    //required stop game
    if(isset($_POST['GAME']) && $_POST['GAME'] === 'delete_gameState'){
        unlink('../app/database/game_state.json');
        header('Location: ./');
        exit;
    }

    if (isset($_GET['action']) && $_GET['action'] === 'stream_players' && strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/event-stream') !== false) {
        require_once __DIR__ . '/../app/pages/admin/stream.php';
        exit;
    }
    // 1. SSE-поток списка игроков
    if (isset($_GET['action']) && $_GET['action'] === 'stream_players') {
        require_once __DIR__ . '/../app/pages/admin/stream.php';
        exit;
    }

    // 2. Обработчик кнопки "Старт"
    if (isset($_GET['action']) && $_GET['action'] === 'start_game') {
        require_once __DIR__ . '/../app/pages/admin/start.php';
        exit;
    }

    // 3. Выбор страницы в зависимости от текущего статуса игры
    $gameState = json_decode(file_get_contents(__DIR__ . '/../app/database/game_state.json'), true);

    if (isset($gameState['status']) && $gameState['status'] === 'started') {
        require_once __DIR__ . '/../app/pages/admin/quiz.php';
    } else {
        require_once __DIR__ . '/../app/pages/admin/wait.php';
    }
}
else{
    if (isset($_GET['action']) && $_GET['action'] === 'stream_start' && strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/event-stream') !== false) {
        require_once __DIR__ . '/../app/pages/stream_start.php';
        exit;
    }
    if(json_decode(file_get_contents('../app/database/game_state.json'), true)['status'] == 'waiting'){
        session_start();
        unset($_SESSION['qnum']);
        session_write_close();
        unset($_POST['answer']);

        // if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //     header('Location: ./');
        //     exit;
        // }
    }

    function free_username($username = null){
        if($username === null){
            return true;
        }
        $username = trim($username);
        if(!file_exists('../app/database/players.json')){
            return true;
        }

        $players = json_decode(file_get_contents('../app/database/players.json'), true);
        foreach ($players as $el){
            if($el['username'] === $username){
                return false;
            }
        }
        return true;
    }
    function isValidColor(string $hex): bool {
        // 1. Проверяем базовый формат #RRGGBB (регистронезависимо)
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
            return false;
        }

        // Извлекаем RGB компоненты
        $r = hexdec(substr($hex, 1, 2)) / 255;
        $g = hexdec(substr($hex, 3, 2)) / 255;
        $b = hexdec(substr($hex, 5, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        // Вычисляем Lightness (Яркость)
        $l = ($max + $min) / 2;

        // Вычисляем Saturation (Насыщенность)
        $s = 0;
        if ($delta != 0) {
            $s = $delta / (1 - abs(2 * $l - 1));
        }

        // Переводим в проценты (0-100)
        $saturationPct = $s * 100;
        $lightnessPct = $l * 100;

        // Границы из JS-функции:
        // Saturation: Math.floor(Math.random() * 51) + 50  => [50, 100]
        // Lightness:  Math.floor(Math.random() * 51) + 25  => [25, 75]
        // Из-за округления Math.round() в JS при переводе в RGB, закладываем погрешность в 1%
        $epsilon = 1.0; 

        if ($saturationPct < (50 - $epsilon) || $saturationPct > (100 + $epsilon)) {
            return false;
        }

        if ($lightnessPct < (25 - $epsilon) || $lightnessPct > (75 + $epsilon)) {
            return false;
        }

        return true;
    }
    function findIndexByUsername(array $arr, string $username){
        foreach($arr as $index => $player){
            if($player['username'] == $username){
                return $index;
            }
        }
        return -1;
    }

    session_start();

    //удаление ника (почти выход)
    if(isset($_POST['name_edit']) && $_POST['name_edit'] === 'false'){
        if(!file_exists('../app/database/players.json')){
            file_put_contents('../app/database/players.json', '[]');
        }
        else{
            if(!isset($_POST['username'])){
                $players = json_decode(file_get_contents('../app/database/players.json'), true);
                unset($players[findIndexByUsername($players, $_SESSION['username'] ?? null)]);
                $players = array_values($players);
                file_put_contents('../app/database/players.json', json_encode($players));
                unset($_SESSION['username']);
                unset($_SESSION['color']);
            }
        }

        //сохранение ника при его смене
        if((isset($_POST['username']) && $_SESSION['username'] == $_POST['username']) || isset($_POST['username'])){
            if((isset($_POST['username']) && $_SESSION['username'] == $_POST['username']) || free_username($_POST['username'])){
                $players = json_decode(file_get_contents('../app/database/players.json'), true);
                unset($players[findIndexByUsername($players, $_SESSION['username'])]);
                $players = array_values($players);
                // file_put_contents('../app/database/players.json', json_encode($players));

                $_SESSION['username'] = $_POST['username'];
                $_SESSION['color'] = isset($_POST['color']) ? $_POST['color'] : $_SESSION['color'];
                unset($_SESSION['relogin']);
    
                if(!file_exists('../app/database/players.json')){
                    file_put_contents('../app/database/players.json', '[]');
                }
                // $players = json_decode(file_get_contents('../app/database/players.json'), true);
                $players[] = ['username' => $_SESSION['username'], 'color' => $_SESSION['color']];
                file_put_contents('../app/database/players.json', json_encode($players));
            }
        }


        header('Location: ./');
        exit;
    }
    //активация смены ника
    else if(isset($_POST['name_edit']) && $_POST['name_edit'] === 'true'){
        $_SESSION['relogin'] = true;

        header('Location: ./');
        exit;
    }

    //сохранение ника при первой регистрации
    if((isset($_POST['username']) && $_POST['username'] !== '' && !isset($_SESSION['relogin'])) || (isset($_POST['color']) && isValidColor($_POST['color']))){
        if(!file_exists('../app/database/players.json')){
            file_put_contents('../app/database/players.json', '[]');
        }
        if(free_username($_POST['username'])){
            $_SESSION['username'] = $_POST['username'];
    
            if(!file_exists('../app/database/players.json')){
                file_put_contents('../app/database/players.json', '[]');
            }
            $players = json_decode(file_get_contents('../app/database/players.json'), true);
            $players[]['username'] = $_SESSION['username'];
            file_put_contents('../app/database/players.json', json_encode($players));
    
            $_SESSION['color'] = $_POST['color'];

            $players = json_decode(file_get_contents('../app/database/players.json'), true);
            $players[findIndexByUsername($players, $_SESSION['username'])]['color'] = $_SESSION['color'];
            file_put_contents('../app/database/players.json', json_encode($players));
        }
            

        
        header('Location: ./');
        exit;
    }

    //запускает/продолжает игру
    if (isset($_SESSION['username']) && json_decode(file_get_contents('../app/database/game_state.json'), true)['status'] == 'started' && count(CONFIG['questions']) !== 0){
        //после ответа
        if(isset($_SESSION['qnum']) && isset($_POST['answer'])){
            if(CONFIG['questions'][$_SESSION['qnum']]['type'] == 'input' || (int)$_POST['answer'] < count(CONFIG['questions'][$_SESSION['qnum']]['answers']['variants'])){
                //здесь будет запись ответа в бд

                $_SESSION['qnum'] = $_SESSION['qnum'] + 1;
                header('Location: ./');
                exit;
            }
        }
        //продолжение
        else if(!isset($_POST['answer']) && json_decode(file_get_contents('../app/database/game_state.json'), true)['started_at'] < time() - 5){
            if (!isset($_SESSION['qnum'])){
                $_SESSION['qnum'] = 0;
                require_once '../app/pages/quiz.php';
            }
            else if($_SESSION['qnum'] === count(CONFIG['questions'])){
                require_once '../app/pages/end.php';
            }
            else{
                require_once '../app/pages/quiz.php';
            }
        }
        //первый старт
        else if(!isset($_SESSION['qnum']) && !isset($_POST['answer'])){
            // $_SESSION['qnum'] = 0;
            // require_once '../app/pages/quiz.php';
            require_once '../app/pages/countdown.php';
        }
        else{
            echo 'Error!';
        }
    }
    //по сути, отменяет смену ника
    else if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username']) && $_POST['username'] == $_SESSION['username']){
        unset($_SESSION['relogin']);

        header('Location: ./');
        exit;
    }
    //либо начальный логин, либо смена ника
    else if(!isset($_SESSION['username']) || (isset($_SESSION['relogin']) && $_SESSION['relogin'] === true)){
        require_once '../app/pages/login.php';
    }
    //всё чётко
    else {
        require_once '../app/pages/wait.php';
    }

}