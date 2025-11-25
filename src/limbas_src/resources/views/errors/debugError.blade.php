@php
    /**
     * @copyright Limbas GmbH <https://limbas.com>
     * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
     *
     * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
     */
    
    use Limbas\lib\general\StackTrace;
    use Limbas\lib\general\StackTraceItem;
    use Symfony\Component\HttpFoundation\Request;
    
    /** @var Throwable $error */
    /** @var Request $request */
    /** @var StackTrace $stackTrace */
    /** @var StackTraceItem $stackTraceItem */

@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta charset="utf-8">

    <link rel="stylesheet" type="text/css" href="{{ $request->getBaseUrl() }}/assets/css/default.css">
</head>
<body>
<div class="container py-3">

    <h1 class="pb-3">Internal Server Error</h1>

    <div class="card rounded-3 shadow mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8">
                    <p>
                        <span class="badge text-bg-danger fs-3 rounded-1">{{ (new ReflectionClass($error))->getShortName() }}</span>
                    </p>
                    <p class="fs-3">{{ $error->getMessage() }}</p>
                    <p class="text-muted mb-0">{{ $error->getFile() }}: {{ $error->getLine() }}</p>
                </div>
                <div class="col-sm-4 d-none d-sm-block text-end">
                    <span class="badge text-bg-secondary fs-4 rounded-1">{{ $request->getMethod() }} {{ $request->getHost() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded-3 shadow mb-3">
        <div class="card-body">
            <h2 class="pb-3">Trace</h2>
            @foreach ($stackTrace->getItems() as $key => $stackTraceItem)

                <div class="{{ $key > 0 ? 'border-top pt-2 mt-2' : '' }}">
                    <p class="mb-1">{{ $stackTraceItem->getContext() }}: {{ $stackTraceItem->line }}</p>
                    <p class="text-muted">{{ $stackTraceItem->function }}{{ $stackTraceItem->getFunctionArguments() }}</p>
                </div>

            @endforeach
        </div>
    </div>

    <div class="card rounded-3 shadow mb-3">
        <div class="card-body">
            <h2 class="pb-3">Request</h2>
            <p>
                <span class="badge text-bg-secondary">{{ $request->getMethod() }}</span> {{ $request->getScheme() }}
                ://{{ $request->getHost() }}{{ $request->getRequestUri() }}</p>

            @if (!empty($request->query->all()))
                <div class="mb-3">
                    <h3>Parameters</h3>

                    <div class="table-responsive">
                        <table class="table">
                            @foreach ($request->query as $key => $value)

                                <tr>
                                    <td>{{ $key }}</td>
                                    <td>
                                        @if(is_array($value))
                                            <pre>{!! print_r($value, 1) !!}</pre>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                        </table>
                    </div>
                </div>

            @endif

            <div>
                <h3>Body</h3>

                @if (!empty($request->getPayload()->all()))
                    <pre class="border p-2">
                    {!! print_r($request->getPayload()->all(),1) !!}
                    </pre>
                @else
                    <div class="border p-2">
                        No body data
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
</body>
</html>
