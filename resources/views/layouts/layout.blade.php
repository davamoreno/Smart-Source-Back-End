<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('Group.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet"> 
    <title>@yield('title', 'Default Title')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="/public/node_modules/animejs/lib/anime.min.js"></script>
</head>
<body>
    @include("partials.navbar")
        <main>
            @yield('content')
        </main>
    @include("partials.footer")
</body>
</html>