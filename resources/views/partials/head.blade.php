<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>

{{-- Apply the saved theme before first paint to avoid a light flash. --}}
<script>
    try {
        var theme = localStorage.getItem('theme') || 'system';
        if (theme === 'dark' || (theme !== 'light' && matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    } catch (e) {}
</script>

@fonts
@vite(['resources/css/app.css', 'resources/js/app.js'])
