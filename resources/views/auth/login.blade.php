<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t) {
                document.documentElement.setAttribute('data-theme', t);
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'business');
            } else {
                document.documentElement.setAttribute('data-theme', 'corporate');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-base-200 flex items-center justify-center">
    <div class="card w-full max-w-md bg-base-100 shadow-xl">
        <div class="card-body">
            <h1 class="text-2xl font-bold text-center mb-2">{{ config('app.name') }}</h1>
            <p class="text-center text-base-content/60 mb-6">Sign in to your account</p>

            @if ($errors->any())
                <div role="alert" class="alert alert-error mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <fieldset class="fieldset mb-4">
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="input w-full @error('email') input-error @enderror" required autofocus
                        autocomplete="email">
                </fieldset>

                <fieldset class="fieldset mb-6">
                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password"
                        class="input w-full @error('password') input-error @enderror" required
                        autocomplete="current-password">
                </fieldset>

                <fieldset class="fieldset mb-4">
                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="checkbox" name="remember" class="checkbox checkbox-sm" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                </fieldset>

                <button type="submit" class="btn btn-primary w-full">Sign In</button>
            </form>
        </div>
    </div>
</body>

</html>