<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    @include('partials.head')
    <title>403 - Akses Ditolak | SIMONKA</title>
</head>
<body class="bg-light">
    <div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center py-5">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6 col-xl-5 text-center">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-sm-5">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-soft-danger text-danger rounded-circle p-4 mb-3" style="width: 90px; height: 90px;">
                                <i class="fas fa-shield-alt fa-3x"></i>
                            </div>
                            <h2 class="fw-bold text-dark mb-1">Akses Ditolak (403)</h2>
                            <p class="text-muted small">Anda tidak memiliki hak akses atau wewenang untuk membuka halaman atau melakukan aksi ini.</p>
                        </div>

                        <div class="simonka-alert simonka-alert-warning mb-4 text-start" role="alert">
                            <div class="simonka-alert-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="simonka-alert-content">
                                <div class="simonka-alert-title">Batasan Akses</div>
                                <div class="simonka-alert-message">
                                    {{ $exception->getMessage() ?: 'Aksi ini dibatasi sesuai dengan peran akun Anda di sistem SIMONKA.' }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-fundflow-glass px-3 py-2">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-fundflow-primary px-3 py-2">
                                <i class="fas fa-home me-1"></i> Ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.scripts')
</body>
</html>
