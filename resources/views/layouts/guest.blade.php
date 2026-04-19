<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MCI System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <style>
            :root {
                --font-sans: "Playfair Display", Georgia, "Times New Roman", serif;
                --font-display: "Playfair Display", Georgia, "Times New Roman", serif;
                --mci-blue: #0d6efd;
                --mci-accent: #7c3aed;
                --guest-bg: radial-gradient(900px circle at 15% 0%, rgba(13, 110, 253, 0.12) 0%, rgba(13, 110, 253, 0) 55%),
                            radial-gradient(900px circle at 85% 15%, rgba(124, 58, 237, 0.10) 0%, rgba(124, 58, 237, 0) 55%),
                            #f4f7fe;
            }
            body {
                font-family: var(--font-sans);
                font-size: 0.95rem;
                line-height: 1.45;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                background: var(--guest-bg);
                background-attachment: fixed;
            }
            h1, h2, h3, h4, h5, h6 {
                letter-spacing: -0.02em;
                font-family: var(--font-display);
            }
        </style>
    </head>
    <body>
        {{ $slot }}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </body>
</html>
