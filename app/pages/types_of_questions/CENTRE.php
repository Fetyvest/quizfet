<?php

switch (QUESTION['type']) {
    case 'select':
        require_once './../app/pages/types_of_questions/select.php';
        break;
    case 'input':
        require_once './../app/pages/types_of_questions/input.php';
        break;
    case 'multiselect':
        require_once './../app/pages/types_of_questions/multiselect.php';
        break;
}