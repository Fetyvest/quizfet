<?php

define('QUESTION', CONFIG['questions'][$_SESSION['qnum']]);
define('SUPPORT_TYPES', ['select', 'input', 'multiselect']);


if(in_array(QUESTION['type'], SUPPORT_TYPES)){
    require_once './../app/pages/types_of_questions/BEFORE.php';
    require_once './../app/pages/types_of_questions/CENTRE.php';
    require_once './../app/pages/types_of_questions/AFTER.php';
}
else{
    // замените 0 на 1 в следующей строке, чтобы не продолжать квиз
    if (0) {
        echo 'Ещё не поддерживаемый тип вопроса!';
    } else {
        $_SESSION['qnum'] += 1;
        header('Location: ./');
        exit;
    }
}