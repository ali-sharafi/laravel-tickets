<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width" />
    <meta name="format-detection" content="telephone=no">
    <title>LaravelTickets - @yield('title',__('home.title'))</title>
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="main">
        @yield('content')
    </div>
</body>

</html>