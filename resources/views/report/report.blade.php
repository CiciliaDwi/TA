<!DOCTYPE html>
<html lang="en">

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
                    <h1 class="mt-4">Laporan</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan</li>
                    </ol>

                    <!-- Produk terlaris dibulan tersebut -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-1"></i>
                            10 Produk Terlaris Bulan {{ date('F Y') }}
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Produk</th>
                                        <th>Total Terjual</th>
                                        <th>Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bestSellers as $product)
                                        <tr>
                                            <td>{{ $product->KodeBarang }}</td>
                                            <td>{{ $product->NamaBarang }}</td>
                                            <td>{{ $product->TotalTerjual }}</td>
                                            <td>Rp {{ number_format($product->TotalPendapatan, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Stok <10 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Produk dengan Stok Menipis (< 10) </div>
                                <div class="card-body">
                                    <table class="table table-bordered datatable">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Nama Produk</th>
                                                <th>Kategori</th>
                                                <th>Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lowStock as $product)
                                                <tr>
                                                    <td>{{ $product->KodeBarang }}</td>
                                                    <td>{{ $product->Nama }}</td>
                                                    <td>{{ $product->kategori->Nama }}</td>
                                                    <td>{{ $product->Stok }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                        </div>

                        <!-- Semua transaksi -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-list me-1"></i>
                                Semua Transaksi
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered datatable">
                                    <thead>
                                        <tr>
                                            <th>No Nota</th>
                                            <th>Tanggal</th>
                                            <th>Kasir</th>
                                            <th>Total Item</th>
                                            <th>Total Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allSales as $sale)
                                            <tr>
                                                <td>{{ $sale->NoNota }}</td>
                                                <td>{{ date('d/m/Y H:i', strtotime($sale->Tanggal)) }}</td>
                                                <td>{{ $sale->pegawai->nama }}</td>
                                                <td>{{ $sale->detil->sum('Jumlah') }}</td>
                                                <td>Rp
                                                    {{ number_format(
                                                        $sale->detil->sum(function ($item) {
                                                            return $item->Jumlah * $item->Harga;
                                                        }),
                                                        0,
                                                        ',',
                                                        '.',
                                                    ) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
