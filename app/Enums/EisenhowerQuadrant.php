<?php

namespace App\Enums;

enum EisenhowerQuadrant: string
{
    case Do = 'do';
    case Schedule = 'schedule';
    case Delegate = 'delegate';
    case Delete = 'delete';
}