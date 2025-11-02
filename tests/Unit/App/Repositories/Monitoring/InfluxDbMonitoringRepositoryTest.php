<?php

namespace Tests\Unit\App\Repositories\Monitoring;

use App\Dtos\Apis\ApiRequestOptionsDto;
use App\Dtos\Monitoring\Enums\MonitoringApiCallFieldEnum;
use App\Dtos\Monitoring\Enums\MonitoringExceptionCallFieldEnum;
use App\Dtos\Monitoring\Enums\MonitoringPointNameEnum;
use App\Dtos\Monitoring\Enums\MonitoringPointTagEnum;
use App\Dtos\Monitoring\MonitoringApiCallDto;
use App\Repositories\Monitoring\InfluxDbMonitoringRepository;
use DateTimeImmutable;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use InfluxDB2\Client;
use InfluxDB2\Point;
use InfluxDB2\WriteApi;
use Tests\TestCase;

class InfluxDbMonitoringRepositoryTest extends TestCase
{
    use WithFaker;

    private Client $influxDbClient;
    private WriteApi $writeApi;
    private DateTimeImmutable $date;

    protected function setUp(): void
    {
        parent::setUp();

        $this->date = new DateTimeImmutable();

        $this->writeApi = $this->getMockBuilder(WriteApi::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->writeApi
            ->expects($this->once())
            ->method('close');

        $this->influxDbClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testRecordApiCallWillRecordTheCorrectData(): void
    {
        // Given
        $data = MonitoringApiCallDto::from(
            [
                'client' => $this->faker->word(),
                'url' => $this->faker->url(),
                'options' => new ApiRequestOptionsDto(),
            ],
        );

        $expectedPoint = new Point(MonitoringPointNameEnum::API_CALL->value)
            ->addTag(
                MonitoringPointTagEnum::API_CLIENT->value,
                $data->client,
            )
            ->addField(
                MonitoringApiCallFieldEnum::URL->value,
                $data->url,
            )
            ->addField(
                MonitoringApiCallFieldEnum::OPTIONS->value,
                $data->options->toJson(),
            )
            ->time($this->date);

        $this->writeApi
            ->expects($this->once())
            ->method('write')
            ->with($expectedPoint);

        $this->influxDbClient
            ->expects($this->once())
            ->method('createWriteApi')
            ->willReturn($this->writeApi);

        $monitoringRepo = new InfluxDbMonitoringRepository(
            $this->influxDbClient,
            $this->date,
        );

        // When
        $monitoringRepo->recordApiCall($data);
    }

    public function testRecordExceptionWillRecordTheCorrectData(): void
    {
        // Given
        $exception = new Exception(
            message: $this->faker->word(),
            code: $this->faker->randomDigitNotNull(),
            previous: new Exception(
                message: $this->faker->word(),
                code: $this->faker->randomDigitNotNull(),
            ),
        );

        $expectedPoint = new Point(MonitoringPointNameEnum::EXCEPTION->value)
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

        $this->writeApi
            ->expects($this->once())
            ->method('write')
            ->with($expectedPoint);

        $this->influxDbClient
            ->expects($this->once())
            ->method('createWriteApi')
            ->willReturn($this->writeApi);

        $monitoringRepo = new InfluxDbMonitoringRepository(
            $this->influxDbClient,
            $this->date,
        );

        // When
        $monitoringRepo->recordException($exception);
    }
}
