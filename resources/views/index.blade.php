<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $data['title'] ?? (config('app.name') . ' - ' . __('Swagger')) }}</title>

        <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@latest/swagger-ui.css">

        <style>
            html {
                box-sizing: border-box;
            }

            *, *:before, *:after {
                box-sizing: inherit;
            }

            body {
                margin: 0;
                background: #fafafa;
            }
        </style>

        @if (! empty($data['stylesheet']))
            <style>{{ file_get_contents($data['stylesheet']) }}</style>
        @endif
    </head>
    <body>
        <div id="swagger-ui"></div>

        <script src="https://unpkg.com/swagger-ui-dist@latest/swagger-ui-bundle.js"></script>
        <script src="https://unpkg.com/swagger-ui-dist@latest/swagger-ui-standalone-preset.js"></script>

        <script>
            window.onload = function () {
                window.ui = SwaggerUIBundle({
                    urls: [
                        @foreach ($data['versions'] as $version => $path)
                            {
                                url: '{{ url("{$data['path']}/{$version}") }}',
                                name: '{{ $version }}',
                            },
                        @endforeach
                    ],
                    "urls.primaryName": "{{ $data['default'] }}",
                    dom_id: '#swagger-ui',
                    requestInterceptor: (req) => {
                        // Example: Add CSRF token from cookie to header
                        const token = getCookieValue('XSRF-TOKEN');
                        if (token) {
                        req.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
                        }

                        // Always send cookies (important for Laravel Sanctum session-based auth)
                        req.credentials = 'include';

                        return req;
                    },
                    deepLinking: true,
                    presets: [
                        SwaggerUIBundle.presets.apis,
                        SwaggerUIStandalonePreset
                    ],
                    layout: 'StandaloneLayout',
                    @if (!is_null($data["validator_url"]))
                        validatorUrl: '{{ $data["validator_url"] }}',
                    @endif
                    oauth2RedirectUrl: '{{ url("{$data['path']}/oauth2-redirect") }}',
                });

                ui.initOAuth({
                    clientId: '{{ $data['oauth']['client_id'] }}',
                    clientSecret: '{{ $data['oauth']['client_secret'] }}',
                });
            };

            function getCookieValue(name) {
                const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                return match ? match[2] : null;
            }
        </script>
    </body>
</html>
