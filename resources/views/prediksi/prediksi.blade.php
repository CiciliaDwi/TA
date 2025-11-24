<!DOCTYPE html>
<html lang="id">

<head>
    @include('include.head')
</head>
 
    <body class="sb-nav-fixed">
        @include('include.navbar')
        <div id="layoutSidenav">
            @include('include.sidebar')
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Prediksi Penjualan</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Prediksi Penjualan</li>
                        </ol>

                            @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Kesalahan:</strong> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                            @if($errors->any())
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <strong>Validasi gagal:</strong>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chart-line me-1"></i>
                                    Form Prediksi
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('prediction.process') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="kategori_id" class="form-label">ID Kategori Produk</label>
                                            <input type="number" id="kategori_id" name="kategori_id" value="{{ old('kategori_id', $input_data['kategori_id'] ?? 2) }}" class="form-control @error('kategori_id') is-invalid @enderror" placeholder="Contoh: 2" required>
                                            @error('kategori_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="qty_konversi" class="form-label">Jumlah Produk Terjual di Bulan Sebelumnya</label>
                                            <input type="number" id="qty_konversi" name="qty_konversi" value="{{ old('qty_konversi', $input_data['qty_konversi'] ?? 50) }}" class="form-control @error('qty_konversi') is-invalid @enderror" placeholder="Contoh: 50" required>
                                            @error('qty_konversi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                    </div>

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-bolt me-1"></i> Prediksi Jumlah Unit
                                    </button>
                                </form>

                                @if(isset($prediction_result))
                                <hr>
                                <div class="mt-3 p-4 bg-light border rounded">
                                    <p class="mb-1"><strong>Hasil Prediksi Jumlah Unit:</strong></p>
                                    <h2 class="display-6">{{ number_format(round($prediction_result), 0, ',', '.') }}</h2>
                                    <p class="text-muted small">Nilai ini adalah perkiraan Jumlah Unit yang akan terjual (Qty) bulan depan.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </main>
                @include('include.footer')
            </div>
        </div>
        @include('include.script')
    </body>
</html>