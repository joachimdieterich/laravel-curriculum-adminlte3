<?php

namespace App\Services\BBB;

enum MeetingLayoutEnum: string {
    case CUSTOM_LAYOUT = 'CUSTOM_LAYOUT';
    case SMART_LAYOUT = 'SMART_LAYOUT';
    case PRESENTATION_FOCUS = 'PRESENTATION_FOCUS';
    case VIDEO_FOCUS = 'VIDEO_FOCUS';
}
