<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-theme">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Theme initialization script -->
        <script>
            // Initialize theme on page load
            (function() {
                try {
                    const theme = localStorage.getItem('theme') || 'light';
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch (error) {
                    console.warn('Theme initialization failed:', error);
                    // Fallback to light mode
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
    </head>
    <body class="transition-theme">
        <div class="font-sans text-gray-900 dark:text-gray-100 antialiased transition-theme">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
