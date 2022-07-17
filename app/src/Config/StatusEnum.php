<?php

namespace App\Config;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case REFUSED = 'refused';
    case VALIDATED = 'validated';
}
