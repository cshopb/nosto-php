<?php

use Spatie\Csp\Directive;

return [

    /*
     * Presets will determine which CSP headers will be set. A valid CSP preset is
     * any class that implements `Spatie\Csp\Preset`
     */
    'presets' => [
        Spatie\Csp\Presets\Basic::class,
    ],

    /**
     * Register additional global CSP directives here.
     */
    'directives' => [
        [
            Directive::STYLE,
            [
                'https://fonts.bunny.net',
                'sha256-60LHlRjW/B3CtzIoE/Lf1/NEDvko9efWMFaGVhHu/cs=',
                'sha256-JPiV+zbElb6F8GfxmDt1fq5wNR89mwpjxdpPeFaGQ1M=',
                'sha256-/iF0k6dLIJ9LRdSg6ajLthaRblAJ27qiGqWpz/t/aBg=',
                'sha256-8vggE/+jkSbJ8383wSw+FmS6uoBZoCDGhl0c50W63Pc=',
                'sha256-leIxuAmBUJ1g/sM/Zdcgo+r245wC2pAaWxzG/FrZLWQ=',
                'sha256-47DEQpj8HBSa+/TImW+5JCeuQeRkm5NMpJWZG3hSuFU=',
                'sha256-e20qbD4TJDgYOeJgiathX9cyISnz0AM03GGmw9Zb4F8=',
            ],
        ],
        [
            Directive::FONT,
            [
                'https://fonts.bunny.net',
            ],
        ],
    ],

    /*
     * These presets which will be put in a report-only policy. This is great for testing out
     * a new policy or changes to existing CSP policy without breaking anything.
     */
    'report_only_presets' => [
        //
    ],

    /**
     * Register additional global report-only CSP directives here.
     */
    'report_only_directives' => [
        // [Directive::SCRIPT, [Keyword::UNSAFE_EVAL, Keyword::UNSAFE_INLINE]],
    ],

    /*
     * All violations against a policy will be reported to this url.
     * A great service you could use for this is https://report-uri.com/
     */
    'report_uri' => env('CSP_REPORT_URI', ''),

    /*
     * Headers will only be added if this setting is set to true.
     */
    'enabled' => env('CSP_ENABLED', true),

    /**
     * Headers will be added when Vite is hot reloading.
     */
    'enabled_while_hot_reloading' => env('CSP_ENABLED_WHILE_HOT_RELOADING', false),

    /*
     * The class responsible for generating the nonces used in inline tags and headers.
     */
    'nonce_generator' => \App\Support\LaravelViteNonceGenerator::class,

    /*
     * Set false to disable automatic nonce generation and handling.
     * This is useful when you want to use 'unsafe-inline' for scripts/styles
     * and cannot add inline nonces.
     * Note that this will make your CSP policy less secure.
     */
    'nonce_enabled' => env('CSP_NONCE_ENABLED', true),
];
