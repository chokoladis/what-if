<?php

namespace App\Enums\Http;

enum ResponseStatus : int
{
    case RESPONSE_OK = 200;
    case RESPONSE_NO_CONTENT = 201;
    case RESPONSE_BAD_REQUEST = 400;
    case RESPONSE_FORBIDDEN = 403;
    case RESPONSE_NOT_FOUND = 404;
}
