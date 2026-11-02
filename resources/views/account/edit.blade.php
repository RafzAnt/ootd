<x-layouts.app title="Account" subtitle="Your profile and password.">
    <div class="max-w-xl space-y-6">
        <form method="POST" action="{{ route('account.update') }}" class="card space-y-5 p-6">
            @csrf
            @method('PUT')
            <h2 class="font-semibold">Profile</h2>

            <div>
                <label for="name" class="label">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="field">
                @error('name') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="field">
                @error('email') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary">Save</button>
        </form>

        <form method="POST" action="{{ route('account.password') }}" class="card space-y-5 p-6">
            @csrf
            @method('PUT')
            <h2 class="font-semibold">Change password</h2>

            <div>
                <label for="current_password" class="label">Current password</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="field">
                @error('current_password', 'password') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="label">New password</label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="field">
                @error('password', 'password') <p class="error-text">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="label">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="field">
            </div>

            <button type="submit" class="btn-primary">Change password</button>
        </form>
    </div>
</x-layouts.app>
