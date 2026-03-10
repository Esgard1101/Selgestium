<div
    class="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-10 sm:px-6 lg:px-8"
    style="background-image: url('{{ asset(config('university.login_bg_url')) }}'); background-size: cover; background-position: center;"
>
    <div class="absolute inset-0 bg-gradient-to-br from-primary/85 via-primary-dark/80 to-black/70"></div>

    <div class="relative z-10 w-full max-w-md">
        <div class="mb-8 flex justify-center">
            {{ $logo }}
        </div>

        <div class="w-full rounded-2xl bg-white/90 px-6 py-8 shadow-2xl ring-1 ring-white/60 backdrop-blur-sm sm:px-8">
            {{ $slot }}
        </div>
    </div>
</div>
