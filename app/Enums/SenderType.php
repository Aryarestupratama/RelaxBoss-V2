<?php

namespace App\Enums;

enum SenderType: string
{
    case User = 'user';
    case Ai = 'ai';
}