<?php

namespace App\Services\BBB;

enum GuestPolicyEnum: string {
    case ALWAYS_ACCEPT = 'ALWAYS_ACCEPT';
    case ALWAYS_DENY = 'ALWAYS_DENY';
    case ASK_MODERATOR = 'ASK_MODERATOR';
}
