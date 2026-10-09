<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', 'Dashboard') | {{ config('app.name', 'Hope UI') }}</title>

<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

<!-- Google Fonts: Plus Jakarta Sans (FundFlow Design System) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Library / Plugin Css Build -->
<link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- Aos Animation Css -->
<link rel="stylesheet" href="{{ asset('assets/vendor/aos/dist/aos.css') }}">

<!-- Hope Ui Design System Css -->
<link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=4.0.0') }}">

<!-- Custom Css -->
<link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=4.0.0') }}">

<!-- Dark Css -->
<link rel="stylesheet" href="{{ asset('assets/css/dark.min.css') }}">

<!-- Customizer Css -->
<link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css') }}">

<!-- RTL Css -->
<link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css') }}">

<!-- SIMONKA Custom Sidebar & Layout Styles -->
<link rel="stylesheet" href="{{ asset('assets/css/simonka.css') }}?v={{ filemtime(public_path('assets/css/simonka.css')) }}">

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

@livewireStyles

@stack('styles')
