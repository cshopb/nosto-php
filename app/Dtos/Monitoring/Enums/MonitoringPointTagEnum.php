<?php

namespace App\Dtos\Monitoring\Enums;

enum MonitoringPointTagEnum: string
{
    case API_CLIENT = 'api_client';
    case EXCEPTION_CLASS = 'exception_class';
}
