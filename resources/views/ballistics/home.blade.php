<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Apex Ballistics</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 flex justify-between items-center">
            <h1 class="text-xl font-bold">Apex Ballistics</h1>
            @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                            >
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="flex max-w-[335px] w-full lg:max-w-4xl p-6 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
            <div class="w-full">
                <h2 class="text-2xl font-medium mb-4">Welcome to Apex Ballistics</h2>
                <p class="mb-6">Your advanced ballistics calculator and tool suite.</p>
                
                <div class="flex flex-col gap-4">
                    <div class="border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 rounded-sm">
                        <h3 class="font-medium mb-2">Ballistics Calculator</h3>
                        <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">Calculate trajectory, windage, and other ballistic data for improved accuracy.</p>
                        <a href="{{ route('calculator') }}" class="inline-block px-5 py-1.5 bg-[#1b1b18] text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A] rounded-sm border border-black dark:border-[#eeeeec] hover:bg-black dark:hover:bg-white">
                            Start Calculating
                        </a>
                    </div>
                    
                    <div class="border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 rounded-sm">
                        <h3 class="font-medium mb-2">Load Data</h3>
                        <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">Access and manage your custom load data for different firearms.</p>
                        <a href="{{ route('load-data') }}" class="inline-block px-5 py-1.5 bg-[#1b1b18] text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A] rounded-sm border border-black dark:border-[#eeeeec] hover:bg-black dark:hover:bg-white">
                            View Load Data
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
