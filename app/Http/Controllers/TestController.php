<?php

namespace App\Http\Controllers;

use App\Repositories\Apis\Interfaces\ApiServiceProviderInterface;

class TestController extends Controller
{
    public function __construct(private readonly ApiServiceProviderInterface $repository)
    {
    }

    public function test()
    {
        $f = $this->repository->get(
            'https://swop.cx/rest/currencies',
            [
                'headers' => [
                    'Authorization' => 'ApiKey d0147553c4cd3b3302f0bd3e9bd28ef9a884718c087733002c4ba45739772a6a',
                ],
            ],
        );

        $t = 1;

        return $f;
    }
}
