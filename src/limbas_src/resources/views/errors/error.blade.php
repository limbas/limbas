<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta charset="utf-8">

    <style>
        
        body, html {
            width: 100%;
            height: 100%;
            font-family: sans-serif;
            overflow: hidden;
            background: #f7fafc;
            color: #adadad
        }

        .flex-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-text {
            text-align: center;
            line-height: 2;
        }
        
        .code {
            font-weight: bold;
            font-size: 2rem;
        }
    </style>
</head>
<body>
<div class="flex-container">
    <div class="error-text">
        <span class="code">{{ $code }}</span><br>{{ $message }}
    </div>
</div>
</body>
</html>
