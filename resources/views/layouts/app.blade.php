<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css"
        rel="stylesheet"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('custom.style')
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    <main class="content">
        {{ $slot }}
    </main>
</div>
</body>
@stack('custom.script')
<script>
    let sender_id="{{\Illuminate\Support\Facades\Auth::user()->id}}";
    let receiver_id
</script>
@vite('resources/js/custom.js')
{{--<script>--}}
{{--    setTimeout(() => {--}}
{{--        window.Echo.private('private-chat')--}}
{{--            .listen('.private_msg', (e) => {--}}
{{--                console.log(e)--}}
{{--            })--}}
{{--    }, 200)--}}
{{--</script>--}}
</html>
