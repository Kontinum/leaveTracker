<?php

namespace App\Enums;

enum HttpStatus :int
{
    case OK = 200;
    case UNAUTHORIZED = 401;
    case NOT_FOUND = 404;
    case SERVER_ERROR = 500;
}
