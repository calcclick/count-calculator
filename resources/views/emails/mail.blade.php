<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
</head>
<body>
    <div>
        Hello <strong>{{ $name }}</strong>,
        <p>{{$body}}</p>
    </div>
</body>
</html>
