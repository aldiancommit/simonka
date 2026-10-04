<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', 'Dashboard') | {{ config('app.name', 'Hope UI') }}</title>

<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

<!-- Library / Plugin Css Build -->
<link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">

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
<link rel="stylesheet" href="{{ asset('assets/css/simonka.css') }}">

@livewireStyles

@stack('styles')
