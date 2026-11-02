<x-layouts.guest title="Sign up">
    <h1 class="text-2xl font-semibold tracking-tight">Create your account</h1>
    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">It only takes a minute.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="name" class="label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" class="field">
            @error('name') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="field">
            @error('email') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="field">
            <p class="mt-1.5 text-xs text-stone-500 dark:text-stone-400">At least 8 characters.</p>
            @error('password') <p class="error-text">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="field">
        </div>

        <button type="submit" class="btn-primary w-full">Sign up</button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-500 dark:text-stone-400">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-rose-600 hover:underline">Log in</a>
    </p>
</x-layouts.guest>
