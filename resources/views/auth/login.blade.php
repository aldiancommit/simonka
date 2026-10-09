<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    @include('partials.head')
    <title>Login - SIMONKA</title>
</head>
<body class="bg-light">
    <div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center py-5">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/images/logo-palu.png') }}"
                                alt="Logo Kota Palu"
                                class="mb-3"
                                style="width: 64px; height: 64px; object-fit: contain;">

                            <h4 class="fw-bold text-fundflow-primary mb-2">
                                SIMONKA <span class="text-muted fw-normal">|</span> Kesbangpol
                            </h4>

                            <p class="text-muted small mb-0" style="line-height: 1.6;">
                                Sistem Informasi Monitoring dan<br>
                                Konsultasi Kepala Badan 
                            </p>
                        </div>
                        @include('partials.alerts')

                        @if ($errors->any())
                            <div class="simonka-alert simonka-alert-danger mb-4 text-start" role="alert">
                                <div class="simonka-alert-icon">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div class="simonka-alert-content">
                                    <div class="simonka-alert-title">Gagal Masuk!</div>
                                    <div class="simonka-alert-message">
                                        <ul class="mb-0 ps-3">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="simonka-alert simonka-alert-info mb-4 text-start" role="alert">
                                <div class="simonka-alert-icon">
                                    <i class="fas fa-info"></i>
                                </div>
                                <div class="simonka-alert-content">
                                    <div class="simonka-alert-title">Informasi</div>
                                    <div class="simonka-alert-message">{{ session('status') }}</div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label small fw-semibold">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@palukota.go.id">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label small fw-semibold">Kata Sandi</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-fundflow-primary py-2 fw-semibold rounded-3">
                                    Masuk ke SIMONKA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.scripts')
</body>
</html>
