<?php

namespace App\Services\Roles;

enum Roles: int
{
    case ADMINISTRATOR = 1;
    case CREATOR       = 2;
    case INDEXER       = 3;
    case SCHOOL_ADMIN   = 4;
    case TEACHER       = 5;
    case STUDENT       = 6;
    case PARENT        = 7;
    case GUEST         = 8;
    case TOKEN         = 9;

}
