<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password — {{ config('app.name') }}</title>
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
            <h1 class="text-2xl font-bold text-center mb-6">Reset Password</h1>

            @if ($errors->any())
                <div role="alert" class="alert alert-error mb-4">
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <fieldset class="fieldset mb-4">
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                        class="input w-full @error('email') input-error @enderror" required autofocus>
                </fieldset>

                <fieldset class="fieldset mb-4">
                    <label class="label" for="password">New Password</label>
                    <input id="password" type="password" name="password"
                        class="input w-full @error('password') input-error @enderror" required
                        autocomplete="new-password">
                </fieldset>

                <fieldset class="fieldset mb-6">
                    <label class="label" for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="input w-full"
                        required autocomplete="new-password">
                </fieldset>

                <button type="submit" class="btn btn-primary w-full">Reset Password</button>
            </form>
        </div>
    </div>
</body>

</html>