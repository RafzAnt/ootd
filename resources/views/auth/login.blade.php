<x-layouts.guest title="Log in">
    <h1 class="text-2xl font-semibold tracking-tight">Welcome back</h1>
    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Log in to see your outfits.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field">
            @error('email') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password" class="field">
            @error('password') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-stone-600 dark:text-stone-400">
            <input type="checkbox" name="remember" class="size-4 rounded border-stone-300 accent-rose-600">
            Remember me
        </label>

        <button type="submit" class="btn-primary w-full">Log in</button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-500 dark:text-stone-400">
        New here?
        <a href="{{ route('register') }}" class="font-semibold text-rose-600 hover:underline">Create an account</a>
    </p>
</x-layouts.guest>
