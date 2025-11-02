<?php

namespace App\Repositories\Monitoring\Interface;

use App\Dtos\Monitoring\MonitoringApiCallDto;
use Throwable;

interface MonitoringRepositoryInterface
{
    public function recordApiCall(MonitoringApiCallDto $apiCallRecord): void;

    public function recordException(Throwable $exception): void;
}
