<?php

namespace App\Enums;

enum AiActionStatus: string
{
    case Success = 'success';
    case Failure = 'failure';
}