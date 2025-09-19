<?php

namespace App\Enums;

enum TodoStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
}