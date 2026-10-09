<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    @include('partials.head')
</head>
<body class="@yield('body-class', '')">
    <!-- Loader Start -->
    <div id="loading" data-navigate-once>
        <div class="loader simple-loader">
            <div class="loader-body"></div>
        </div>
    </div>
    <!-- Loader End -->

    <!-- App Shell Wrapper (FundFlow Architecture) -->
    <div class="simonka-app-shell">
        <!-- Global Flash Alerts & Notifications Carrier -->
        @include('partials.alerts')

        <!-- Sidebar Backdrop for Mobile -->
        <div class="simonka-sidebar-backdrop" data-toggle="sidebar"></div>

        <!-- Left Sidebar Navigation -->
        @include('partials.sidebar')

        <!-- Main Workspace Container -->
        <div class="simonka-workspace">
            <!-- Unified Top Header -->
            @include('partials.header')

            <!-- Page Content Area -->
            <main class="simonka-content">
                @yield('content')
            </main>

            <!-- Workspace Footer -->
            @include('partials.footer')
        </div>
    </div>

    <!-- Scripts Start -->
    @include('partials.scripts')
</body>
</html>
