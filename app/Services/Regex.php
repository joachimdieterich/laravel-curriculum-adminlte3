<?php

namespace App\Services;

enum Regex: string {
    case HEX_COLOR = '/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})$/';
}
