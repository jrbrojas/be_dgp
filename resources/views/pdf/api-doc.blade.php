<!doctype html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>{{ $apiSpec['info']['title'] ?? 'Documentación de API' }}</title>

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        h1, h2, h3, h4 {
            margin: 0 0 8px 0;
            font-weight: bold;
        }

        h1 { font-size: 24px; }
        h2 { font-size: 18px; margin-top: 20px; }
        h3 { font-size: 14px; margin-top: 14px; }
        h4 { font-size: 12px; margin-top: 10px; }

        p { margin: 0 0 6px 0; }

        .page-break { page-break-after: always; }

        .muted { color: #777; }

        .tag {
            display: inline-block;
            border-radius: 3px;
            padding: 2px 6px;
            font-size: 9px;
            margin-right: 4px;
            border: 1px solid #ddd;
        }

        .badge-method {
            display: inline-block;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 3px;
            color: #fff;
            text-transform: uppercase;
        }

        .badge-get    { background: #0b7285; }
        .badge-post   { background: #2b8a3e; }
        .badge-put    { background: #5f3dc4; }
        .badge-patch  { background: #e67700; }
        .badge-delete { background: #c92a2a; }
        .badge-other  { background: #495057; }

        .badge-secured {
            display: inline-block;
            padding: 1px 5px;
            font-size: 8px;
            border-radius: 3px;
            background: #fff3bf;
            border: 1px solid #f08c00;
            color: #d9480f;
            margin-left: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0 10px 0;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            vertical-align: top;
        }

        th { background: #f1f3f5; }

        .code {
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 10px;
            background: #f8f9fa;
            padding: 2px 4px;
            border-radius: 2px;
        }

        .code-block {
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 9px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 6px;
            border-radius: 3px;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin-top: 4px;
        }

        .section-header {
            border-bottom: 1px solid #ccc;
            margin-bottom: 6px;
            padding-bottom: 3px;
        }
    </style>
</head>
<body>

@php
    /**
     * Convierte cualquier valor a string seguro.
     */
    function strv($value): string {
        if (is_array($value)) {
            return json_encode($value);
        }
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        return $value !== null ? (string) $value : '';
    }

    /**
     * JSON pretty-print para ejemplos.
     */
    function jsonPretty($value): string {
        if ($value === null) {
            return '';
        }
        return json_encode(
            $value,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Genera un ejemplo sintético a partir de un schema OpenAPI.
     */
    function buildExampleFromSchema($schema) {
        if (!is_array($schema)) {
            return null;
        }

        // Si ya trae "example", úsalo
        if (array_key_exists('example', $schema)) {
            return $schema['example'];
        }

        // Resolver allOf/oneOf/anyOf de forma simple (tomamos el primero)
        foreach (['allOf', 'oneOf', 'anyOf'] as $combineKey) {
            if (!empty($schema[$combineKey]) && is_array($schema[$combineKey])) {
                return buildExampleFromSchema($schema[$combineKey][0]);
            }
        }

        $type = $schema['type'] ?? null;

        // Si no hay type pero hay properties, asumimos object
        if (!$type && !empty($schema['properties'])) {
            $type = 'object';
        }

        switch ($type) {
            case 'string':
                $format = $schema['format'] ?? null;
                return match($format) {
                    'date'      => '2025-01-01',
                    'date-time' => '2025-01-01T00:00:00Z',
                    'email'     => 'user@example.com',
                    'uri', 'url'=> 'https://example.com/recurso',
                    default     => ($schema['enum'][0] ?? 'string')
                };
            case 'integer':
            case 'number':
                return $schema['default'] ?? 0;
            case 'boolean':
                return $schema['default'] ?? true;
            case 'array':
                $items = $schema['items'] ?? ['type' => 'string'];
                return [ buildExampleFromSchema($items) ];
            case 'object':
                $example = [];
                $props = $schema['properties'] ?? [];
                foreach ($props as $name => $propSchema) {
                    $example[$name] = buildExampleFromSchema($propSchema);
                }
                return $example;
            default:
                return null;
        }
    }
@endphp

{{-- ================================
     PORTADA
   ================================ --}}
<section>
    <h1>{{ $apiSpec['info']['title'] ?? 'Documentación de API' }}</h1>

    <p><strong>Versión:</strong> {{ strv($apiSpec['info']['version'] ?? 'N/A') }}</p>

    @if(!empty($apiSpec['info']['description']))
        <p style="margin-top: 10px;">
            {{ strv($apiSpec['info']['description']) }}
        </p>
    @endif

    @if(!empty($apiSpec['servers']) && is_array($apiSpec['servers']))
        <h3 style="margin-top: 20px;">Servidores</h3>
        <table>
            <thead>
            <tr>
                <th>URL</th>
                <th>Descripción</th>
            </tr>
            </thead>
            <tbody>
            @foreach($apiSpec['servers'] as $server)
                <tr>
                    <td class="code">{{ strv($server['url'] ?? '') }}</td>
                    <td>{{ strv($server['description'] ?? '') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</section>

<div class="page-break"></div>

{{-- ================================
     SEGURIDAD (JWT, etc.)
   ================================ --}}
<section>
    <h2>Seguridad de la API</h2>

    @if(!empty($apiSpec['components']['securitySchemes']))
        @foreach($apiSpec['components']['securitySchemes'] as $name => $scheme)
            <div class="section-header">
                <h3>{{ $name }}</h3>
            </div>

            <p><strong>Tipo:</strong> {{ strv($scheme['type'] ?? 'N/A') }}</p>

            @if(isset($scheme['scheme']))
                <p><strong>Esquema:</strong> {{ strv($scheme['scheme']) }}</p>
            @endif

            @if(isset($scheme['bearerFormat']))
                <p><strong>Formato:</strong> {{ strv($scheme['bearerFormat']) }}</p>
            @endif

            @if(!empty($scheme['description']))
                <p><strong>Descripción:</strong> {{ strv($scheme['description']) }}</p>
            @endif

            @if(($scheme['type'] ?? '') === 'http' && ($scheme['scheme'] ?? '') === 'bearer')
                <p>
                    Ejemplo de header:
                    <span class="code">Authorization: Bearer &lt;token_jwt&gt;</span>
                </p>
            @endif

            <br>
        @endforeach
    @else
        <p class="muted">No se han definido esquemas de seguridad en el documento OpenAPI.</p>
    @endif
</section>

<div class="page-break"></div>

{{-- ================================
     ENDPOINTS
   ================================ --}}
<section>
    <h2>Endpoints</h2>

    @php
        $paths = $apiSpec['paths'] ?? [];
    @endphp

    @forelse($paths as $path => $methods)
        @foreach($methods as $httpMethod => $operation)
            @php
                $upperMethod = strtoupper($httpMethod);
                $badgeClass = match($upperMethod) {
                    'GET' => 'badge-method badge-get',
                    'POST' => 'badge-method badge-post',
                    'PUT' => 'badge-method badge-put',
                    'PATCH' => 'badge-method badge-patch',
                    'DELETE' => 'badge-method badge-delete',
                    default => 'badge-method badge-other',
                };
                $tags        = $operation['tags'] ?? [];
                $parameters  = $operation['parameters'] ?? [];
                $requestBody = $operation['requestBody'] ?? null;
                $responses   = $operation['responses'] ?? [];
                $securityOp  = $operation['security'] ?? [];
                $usesBearer  = false;

                foreach ($securityOp as $sec) {
                    if (is_array($sec)) {
                        if (array_key_exists('bearer', $sec) || array_key_exists('bearerAuth', $sec)) {
                            $usesBearer = true;
                            break;
                        }
                    }
                }

                $headerParams = array_filter(
                    $parameters,
                    fn ($p) => ($p['in'] ?? '') === 'header'
                );

                if ($usesBearer) {
                    $headerParams[] = [
                        'name'        => 'Authorization',
                        'in'          => 'header',
                        'required'    => true,
                        'description' => 'Token JWT en formato: Bearer {token}',
                        'schema'      => ['type' => 'string'],
                    ];
                }
            @endphp

            <div style="margin-bottom: 18px;">

                <div class="section-header">
                    <span class="{{ $badgeClass }}">{{ $upperMethod }}</span>
                    <span class="code">{{ $path }}</span>

                    @if($usesBearer)
                        <span class="badge-secured">JWT protegido</span>
                    @endif
                </div>

                @if(!empty($operation['summary']))
                    <p><strong>Resumen:</strong> {{ strv($operation['summary']) }}</p>
                @endif

                @if(!empty($operation['description']))
                    <p><strong>Descripción:</strong> {{ strv($operation['description']) }}</p>
                @endif

                @if(!empty($tags))
                    <p>
                        <strong>Tags:</strong>
                        @foreach($tags as $tag)
                            <span class="tag">{{ strv($tag) }}</span>
                        @endforeach
                    </p>
                @endif

                {{-- Parámetros no-header --}}
                @php
                    $nonHeaderParams = array_filter(
                        $parameters,
                        fn ($p) => ($p['in'] ?? '') !== 'header'
                    );
                @endphp

                @if(!empty($nonHeaderParams))
                    <h4>Parámetros</h4>
                    <table>
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Tipo</th>
                            <th>Requerido</th>
                            <th>Descripción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($nonHeaderParams as $param)
                            @php
                                $schemaParam = $param['schema'] ?? [];
                            @endphp
                            <tr>
                                <td>{{ strv($param['name'] ?? '') }}</td>
                                <td>{{ strv($param['in'] ?? '') }}</td>
                                <td>{{ strv($schemaParam['type'] ?? '') }}</td>
                                <td>{{ !empty($param['required']) ? 'Sí' : 'No' }}</td>
                                <td>{{ strv($param['description'] ?? '') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Headers --}}
                @if(!empty($headerParams))
                    <h4>Headers</h4>
                    <table>
                        <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Requerido</th>
                            <th>Descripción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($headerParams as $param)
                            @php
                                $schemaHeader = $param['schema'] ?? [];
                            @endphp
                            <tr>
                                <td>{{ strv($param['name'] ?? '') }}</td>
                                <td>{{ strv($schemaHeader['type'] ?? 'string') }}</td>
                                <td>{{ !empty($param['required']) ? 'Sí' : 'No' }}</td>
                                <td>{{ strv($param['description'] ?? '') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Request Body --}}
                @if($requestBody)
                    <h4>Request Body</h4>
                    <p><strong>Requerido:</strong> {{ !empty($requestBody['required']) ? 'Sí' : 'No' }}</p>

                    @if(!empty($requestBody['content']) && is_array($requestBody['content']))
                        @foreach($requestBody['content'] as $mime => $content)
                            <p><strong>Tipo de contenido:</strong> <span class="code">{{ strv($mime) }}</span></p>

                            @php
                                $schemaReq   = $content['schema'] ?? null;
                                $propsReq    = $schemaReq['properties'] ?? [];
                                $requiredReq = $schemaReq['required'] ?? [];
                            @endphp

                            @if($schemaReq && !empty($propsReq))
                                <table>
                                    <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Tipo</th>
                                        <th>Requerido</th>
                                        <th>Descripción</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($propsReq as $field => $propSchema)
                                        <tr>
                                            <td>{{ strv($field) }}</td>
                                            <td>{{ strv($propSchema['type'] ?? '') }}</td>
                                            <td>{{ in_array($field, $requiredReq ?? []) ? 'Sí' : 'No' }}</td>
                                            <td>{{ strv($propSchema['description'] ?? '') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif

                            {{-- Ejemplo JSON de Request (auto para cualquier endpoint) --}}
                            @php
                                // 1) Example definido en OpenAPI
                                $exampleReq = $content['example'] ?? null;
                                if ($exampleReq === null && !empty($content['examples']) && is_array($content['examples'])) {
                                    $firstExample = reset($content['examples']);
                                    if (is_array($firstExample) && array_key_exists('value', $firstExample)) {
                                        $exampleReq = $firstExample['value'];
                                    }
                                }
                                // 2) Si no hay example, lo generamos desde el schema
                                if ($exampleReq === null && $schemaReq) {
                                    $exampleReq = buildExampleFromSchema($schemaReq);
                                }
                            @endphp

                            @if($mime === 'application/json' && $exampleReq !== null)
                                <p><strong>Ejemplo de Request (JSON):</strong></p>
                                <pre class="code-block">{{ jsonPretty($exampleReq) }}</pre>
                            @endif
                        @endforeach
                    @endif
                @endif

                {{-- Responses --}}
                @if(!empty($responses))
                    <h4>Respuestas</h4>
                    <table>
                        <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Tipo / Esquema</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($responses as $status => $response)
                            @php
                                $contentRes  = $response['content'] ?? [];
                                $firstRes    = is_array($contentRes) ? reset($contentRes) : null;
                                $schemaRes   = $firstRes['schema'] ?? null;
                            @endphp
                            <tr>
                                <td class="code">{{ strv($status) }}</td>
                                <td>{{ strv($response['description'] ?? '') }}</td>
                                <td>
                                    @if($schemaRes)
                                        @if(isset($schemaRes['$ref']))
                                            <span class="code">{{ strv($schemaRes['$ref']) }}</span>
                                        @else
                                            <span class="code">{{ strv($schemaRes['type'] ?? 'object') }}</span>
                                        @endif
                                    @else
                                        <span class="muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    {{-- Ejemplo JSON de respuesta 200/201 (auto) --}}
                    @php
                        $responseOk = $responses['200'] ?? $responses['201'] ?? null;
                    @endphp

                    @if($responseOk)
                        @php
                            $contentOk = $responseOk['content'] ?? [];
                            $jsonOk    = $contentOk['application/json'] ?? null;
                            $exampleRes = null;

                            if ($jsonOk) {
                                $exampleRes = $jsonOk['example'] ?? null;

                                if ($exampleRes === null && !empty($jsonOk['examples']) && is_array($jsonOk['examples'])) {
                                    $firstEx = reset($jsonOk['examples']);
                                    if (is_array($firstEx) && array_key_exists('value', $firstEx)) {
                                        $exampleRes = $firstEx['value'];
                                    }
                                }

                                if ($exampleRes === null && !empty($jsonOk['schema'])) {
                                    $exampleRes = buildExampleFromSchema($jsonOk['schema']);
                                }
                            }
                        @endphp

                        @if($exampleRes !== null)
                            <p><strong>Ejemplo de Respuesta (JSON):</strong></p>
                            <pre class="code-block">{{ jsonPretty($exampleRes) }}</pre>
                        @endif
                    @endif
                @endif

            </div>
        @endforeach
    @empty
        <p class="muted">No se han definido rutas en el documento OpenAPI.</p>
    @endforelse
</section>

</body>
</html>
