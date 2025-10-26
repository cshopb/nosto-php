<?php

use App\Dtos\Apis\Enums\ApiResponseStatusCodeEnum;
use App\Dtos\Exceptions\FrontendExceptionDto;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\UseViteNonce;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Csp\AddCspHeaders;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            UseViteNonce::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            AddCspHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(
            function (
                Response $response,
                Throwable $exception,
                Request $request,
            ) {
                $returnFullResponseForEnvironments = [
                    'local',
                    'testing',
                ];

                $frontendExceptionDto = FrontendExceptionDto::fromException($exception);

                if (app()->environment($returnFullResponseForEnvironments) === true) {
                    return $response->setStatusCode(
                        $frontendExceptionDto->code->value,
                    );
                }

                $status = ApiResponseStatusCodeEnum::from($response->getStatusCode());

                if ($status === ApiResponseStatusCodeEnum::HTTP_PAGE_EXPIRED) {
                    return back()->with(
                        [
                            'message' => 'The page expired, please try again.',
                        ],
                    );
                }

                return Inertia::render(
                    $request->header('x-inertia-partial-component'),
                    [
                        'errors' => FrontendExceptionDto::fromException($exception),
                    ],
                )
                    ->toResponse($request)
                    ->setStatusCode(
                        $frontendExceptionDto->code->value,
                    );
            },
        );
    },
    )->create();
