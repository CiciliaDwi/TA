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
                    <h1 class="mt-4">Dashboard</h1>
                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-primary text-white mb-4 h-100">
                                <div class="card-body d-flex flex-column">
                                    <h4>Total Pendapatan Hari Ini</h4>
                                    <h2 class="mt-auto">Rp {{ number_format($recentTransactions->where('Tanggal', '>=', \Carbon\Carbon::today())->sum(function ($transaction) 
                                    {
                                        return $transaction->detil->sum(function ($item) {
                                            return $item->Jumlah * $item->Harga;
                                        });
                                    }), 0, ',', '.') }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-success text-white mb-4 h-100">
                                <div class="card-body d-flex flex-column">
                                    <h4>Total Pendapatan Minggu Ini</h4>
                                    <h2 class="mt-auto">Rp {{ number_format($recentTransactions->where('Tanggal', '>=', \Carbon\Carbon::now()->startOfWeek())->sum(function ($transaction) 
                                        {
                                            return $transaction->detil->sum(function ($item) {
                                                return $item->Jumlah * $item->Harga;
                                            });
                                        }), 0, ',', '.') }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-info text-white mb-4 h-100">
                                <div class="card-body d-flex flex-column">
                                    <h4>Total Pendapatan Bulan</h4>
                                    <div class="mb-3">
                                        <input type="month" class="form-control" id="monthPicker"
                                            value="{{ date('Y-m') }}" onchange="calculateMonthlyIncome(this.value)">
                                    </div>
                                    <h2 id="monthlyIncome" class="mt-auto">Rp {{ number_format($recentTransactions->where('Tanggal', '>=', \Carbon\Carbon::now()->startOfMonth())->sum(function ($transaction) 
                                    {
                                        return $transaction->detil->sum(function ($item) {
                                            return $item->Jumlah * $item->Harga;
                                        });
                                    }), 0, ',', '.') }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        10 Transaksi Terbaru
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple" class="table table-striped table-bordered">
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
                                @foreach($recentTransactions as $transaction)
                                                                <tr>
                                                                    <td>{{ $transaction->NoNota }}</td>
                                                                    <td>{{ date('d/m/Y H:i', strtotime($transaction->Tanggal)) }}</td>
                                                                    <td>{{ $transaction->pegawai->nama }}</td>
                                                                    <td>{{ $transaction->detil->sum('Jumlah') }}</td>
                                                                    <td>Rp {{ number_format($transaction->detil->sum(function ($item) {
                                        return $item->Jumlah * $item->Harga;
                                    }), 0, ',', '.') }}</td>
                                                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
            @include('include.footer')
        </div>
    </div>
    @include('include.script')
</body>

</html>