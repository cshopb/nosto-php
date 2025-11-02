<?php

namespace App\Dtos\Monitoring\Enums;

enum MonitoringExceptionCallFieldEnum: string
{
    case CODE = 'code';
    case MESSAGE = 'message';
    case TRACE = 'trace';
}
