<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <style>
            :root {
                --color-primary: {{ config('university.primary_color', '#003087') }};
                --color-primary-dark: {{ config('university.primary_dark_color', '#001F5C') }};
                --color-primary-light: {{ config('university.primary_light_color', '#1A4BA8') }};
                --color-accent: {{ config('university.accent_color', '#00BFFF') }};
                --color-accent-light: {{ config('university.accent_light_color', '#E0F7FF') }};
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
