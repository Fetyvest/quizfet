<?php

define('QUESTION', CONFIG['questions'][$_SESSION['qnum']]);

if(QUESTION['type'] == 'select'){
    require_once './../app/pages/types_of_questions/selection.php';
}
else{
    //замените 0 на 1 в следующей строке, чтобы не продолжать квиз
    if(0){
        echo 'Ещё не поддерживаемый тип вопроса!';
    }
    else{
        $_SESSION['qnum'] += 1;
        header('Location: ./');
        exit;
    }
}