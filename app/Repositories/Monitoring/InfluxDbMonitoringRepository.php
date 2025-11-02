<?php

namespace App\Repositories\Monitoring;

use App\Dtos\Monitoring\Enums\MonitoringApiCallFieldEnum;
use App\Dtos\Monitoring\Enums\MonitoringExceptionCallFieldEnum;
use App\Dtos\Monitoring\Enums\MonitoringPointNameEnum;
use App\Dtos\Monitoring\Enums\MonitoringPointTagEnum;
use App\Dtos\Monitoring\MonitoringApiCallDto;
use App\Repositories\Monitoring\Interface\MonitoringRepositoryInterface;
use DateTimeImmutable;
use InfluxDB2\Client as InfluxDbClient;
use InfluxDB2\Point;
use InfluxDB2\WriteType;
use Throwable;

readonly class InfluxDbMonitoringRepository implements MonitoringRepositoryInterface
{
    public function __construct(
        private InfluxDbClient $influxDbClient,
        private DateTimeImmutable $date = new DateTimeImmutable(),
    )
    {
    }

    public function recordApiCall(MonitoringApiCallDto $apiCallRecord): void
    {
        $point = new Point(MonitoringPointNameEnum::API_CALL->value)
            ->addTag(
                MonitoringPointTagEnum::API_CLIENT->value,
                $apiCallRecord->client,
            )
            ->addField(
                MonitoringApiCallFieldEnum::URL->value,
                $apiCallRecord->url,
            )
            ->addField(
                MonitoringApiCallFieldEnum::OPTIONS->value,
                $apiCallRecord->options->toJson(),
            )
            ->time($this->date);

        $this->writeToInfluxDb($point);
    }

    public function recordException(Throwable $exception): void
    {
        $point = new Point(MonitoringPointNameEnum::EXCEPTION->value)
            ->addTag(
                MonitoringPointTagEnum::EXCEPTION_CLASS->value,
                get_class($exception),
            )
            ->addField(
                MonitoringExceptionCallFieldEnum::CODE->value,
                $exception->getCode(),
            )
            ->addField(
                MonitoringExceptionCallFieldEnum::MESSAGE->value,
                $exception->getMessage(),
            )
            ->addField(
                MonitoringExceptionCallFieldEnum::TRACE->value,
                $exception->getTraceAsString(),
            )
            ->time($this->date);

        $this->writeToInfluxDb($point);
    }

    private function writeToInfluxDb(Point $point): void
    {
        $writeInfluxDb = $this->influxDbClient
            ->createWriteApi(
                [
                    'writeType' => WriteType::SYNCHRONOUS,
                ],
            );

        $writeInfluxDb->write($point);
        $writeInfluxDb->close();
    }
}
