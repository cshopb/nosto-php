<?php

namespace App\Dtos\Monitoring\Enums;

enum MonitoringPointNameEnum: string
{
    case API_CALL = 'api_call';
    case EXCEPTION = 'exception';
}
