<?php

namespace App\Config;

enum OrientationEnum: string
{
    case Left = 'Left/Start aligned';
    case Center = 'Center/Middle aligned';
    case Right = 'Right/End aligned';
}
