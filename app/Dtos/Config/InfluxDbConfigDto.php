<?php

namespace App\Dtos\Config;

use Illuminate\Support\Facades\Config;
use Spatie\LaravelData\Data;

class InfluxDbConfigDto extends Data
{
    public function __construct(
        public string $url,
        public string $adminUser,
        public string $adminPassword,
        public string $organization,
        public string $bucket,
        public string $token,
    )
    {
    }

    public static function getFromConfig(): InfluxDbConfigDto
    {
        return self::from(
            Config::get('influxdb'),
        );
    }
}
