<?php

define('QUESTION', CONFIG['questions'][$_SESSION['qnum']]);

switch (QUESTION['type']) {
    case 'select':
        require_once './../app/pages/types_of_questions/select.php';
        break;
    case 'input':
        require_once './../app/pages/types_of_questions/input.php';
        break;
    default:
        // замените 0 на 1 в следующей строке, чтобы не продолжать квиз
        if (0) {
            echo 'Ещё не поддерживаемый тип вопроса!';
        } else {
            $_SESSION['qnum'] += 1;
            header('Location: ./');
            exit;
        }
        break;
}