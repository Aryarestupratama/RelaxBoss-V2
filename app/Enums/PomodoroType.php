<?php

namespace App\Enums;

enum PomodoroType: string
{
    case Work = 'work';
    case ShortBreak = 'short_break';
    case LongBreak = 'long_break';
}