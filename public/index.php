<?php
//поставь отображение у админа норм ников и цветов
// if (isset($_GET['action']) && $_GET['action'] === 'stream_players') {
//     require_once __DIR__ . '/../app/pages/admin/stream.php';
//     exit;
// }

if($_SERVER['REMOTE_ADDR'] == '127.0.0.1'){
    // require_once '../app/pages/admin/wait.php';

    if (isset($_GET['action']) && $_GET['action'] === 'stream_players') {
        require_once __DIR__ . '/../app/pages/admin/stream.php';
        exit;
    }

    // В остальных случаях отдаем обычную страницу админки
    require_once __DIR__ . '/../app/pages/admin/wait.php';
}
else{
    function free_username($username = null){
        if($username === null){
            return true;
        }
        $username = trim($username);
        if(!file_exists('../app/database/players.json')){
            return true;
        }

        $players = json_decode(file_get_contents('../app/database/players.json'));
        return !in_array($username, $players);
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
        if(free_username($_POST['username'])){
            $_SESSION['username'] = $_POST['username'];
    
            if(!file_exists('../app/database/players.json')){
                file_put_contents('../app/database/players.json', '[]');
            }
            $players = json_decode(file_get_contents('../app/database/players.json'), true);
            $players[]['username'] = $_SESSION['username'];
            file_put_contents('../app/database/players.json', json_encode($players));
    
            // header('Location: ./');
            // exit;
        }
            
        $_SESSION['color'] = $_POST['color'];

        if(!file_exists('../app/database/players.json')){
            file_put_contents('../app/database/players.json', '[]');
        }
        $players = json_decode(file_get_contents('../app/database/players.json'), true);
        $players[findIndexByUsername($players, $_SESSION['username'])]['color'] = $_SESSION['color'];
        file_put_contents('../app/database/players.json', json_encode($players));
        
        header('Location: ./');
        exit;
    }

    //по сути, отменяет смену ника
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['username'] == $_SESSION['username']){
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