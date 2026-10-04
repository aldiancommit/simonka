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

    <!-- Sidebar Start -->
    @include('partials.sidebar')
    <!-- Sidebar End -->

    <!-- Main Content Start -->
    <main class="main-content">
        <div class="position-relative iq-banner">
            <!-- Navbar Start -->
            @include('partials.navbar')
            <!-- Navbar End -->

            <!-- Header Banner Start -->
            @hasSection('header-banner')
                @yield('header-banner')
            @else
                @include('partials.header-banner')
            @endif
            <!-- Header Banner End -->
        </div>

        <!-- Content Inner Start -->
        <div class="container-fluid content-inner mt-n5 py-0">
            @yield('content')
        </div>
        <!-- Content Inner End -->

        <!-- Footer Start -->
        @include('partials.footer')
        <!-- Footer End -->
    </main>
    <!-- Main Content End -->

    <!-- Scripts Start -->
    @include('partials.scripts')
    <!-- Scripts End -->
</body>
</html>
