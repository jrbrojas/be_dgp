<?php

return [
    'paths'                => ['api/*', 'storage/*'],
    'allowed_methods'      => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_origins'      => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),
    'allowed_headers'      => ['*'],
    'exposed_headers'      => ['Content-Disposition'],
    'max_age'              => 3600,
    'supports_credentials' => false,
];
