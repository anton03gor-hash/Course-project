{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Система управления складом и запасами')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        body {
            background-color: var(--gray-50);
            color: var(--gray-800);
        }
        
        .navbar-custom {
            background-color: var(--gray-800) !important;
            border-bottom: 1px solid var(--gray-700);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-300) 100%);
            color: var(--gray-800);
            padding: 100px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
        }
        
        .card-custom {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .btn-gray {
            background-color: var(--gray-600);
            border-color: var(--gray-600);
            color: white;
        }
        
        .btn-gray:hover {
            background-color: var(--gray-700);
            border-color: var(--gray-700);
            color: white;
        }
        
        .btn-outline-gray {
            border-color: var(--gray-600);
            color: var(--gray-600);
        }
        
        .btn-outline-gray:hover {
            background-color: var(--gray-600);
            color: white;
        }
        
        .map-container {
            height: 300px;
            background: var(--gray-100);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .footer-custom {
            background-color: var(--gray-800);
            border-top: 1px solid var(--gray-700);
        }
    </style>
</head>
<body>
    @include('layouts.navigation')
    
    <main>
        @yield('content')
    </main>
    @if(request()->routeIs('dashboard') || request()->is('dashboard') || request()->routeIs('welcome') || request()->is('welcome'))
    @include('layouts.footer')
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>