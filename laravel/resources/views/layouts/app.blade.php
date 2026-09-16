<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config("app.name", "VMS") }}</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])
    @stack("styles")
</head>
<body class="bg-gray-50 font-sans antialiased">
    @yield("body")
    @stack("scripts")
</body>
</html>
