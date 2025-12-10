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
                                <div class="my-3">
                                    <div class="row">
                                        <div class="col">
                                            <div id="responseMessage">
                                                {{-- <div class="alert alert-success">
                                                    Laporan periode $newDate sedang diproses dan akan siap
                                                    diunduh setelah selesai
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-4">

                                            <form id="downloadReportForm">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Unduh Laporan Per Bulan</label>

                                                    <div class="d-flex align-self-stretch gap-2">
                                                        <input type="month" class="form-control flex-shrink"
                                                            id="date" name="date">

                                                        <button class="btn btn-primary w-50" type="submit"
                                                            id="btnDownloadReport">
                                                            <i class="fa-solid fa-download"></i> Download
                                                        </button>
                                                    </div>

                                                    <div class="invalid-feedback d-block" id="error-date"></div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>

                                </div>

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

    <script>
        // ====== Handle Form - Trigger Download Report ======
        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid'); // hapus class error
            $('.invalid-feedback').html(''); // kosongkan pesan
            $("#responseMessage").html(''); // hapus alert 422
        }

        $('#downloadReportForm').submit(function(event) {
            event.preventDefault();

            $.ajax({
                url: "{{ route('report.trigger.download') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('#loadingReport')
                        .html(
                            `<div class="spinner-border" role="status">
                                  <span class="visually-hidden">Loading...</span>
                                </div>`
                        );
                    $('#btnDownloadReport').prop('disabled', true).html(
                        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...`
                    )
                },
                success: function(response) {
                    const {
                        message,
                        data: {
                            taskId,
                            taskCategory
                        }
                    } = response;

                    console.log("Trigger Download:", response)

                    // loop every 5 sec
                    $('#responseMessage').html(
                        `<div class="alert alert-success">
                                ${message}
                             </div>`
                    );
                    $('#btnDownloadReport').prop('disabled', false).html(
                        '<i class="fa-solid fa-download"></i> Download'
                    )

                    const payload = {
                        event,
                        taskId,
                        taskCategory
                    }
                    taskInterval = setInterval(() => {
                        // function at -> resources\views\include\script.blade.php
                        poolDownloadReport(payload);
                    }, 5000);

                },
                error: function(xhr) {
                    const {
                        status,
                        responseJSON: response
                    } = xhr;
                    const {
                        errors
                    } = response;

                    // console.error(response, xhr)

                    $('#btnDownloadReport').prop('disabled', false).html(
                        '<i class="fa-solid fa-download"></i> Download'
                    )

                    if (status === 422 && errors) {
                        errors.map(({
                            key,
                            message
                        }) => {
                            $(`#${key}`).addClass('is-invalid')
                            $(`#error-${key}`).html(message)
                        });
                        return;
                    }

                    $("#responseMessage").html(
                        `<div class="alert alert-danger">
                        Terjadi kesalahan saat proses unduh.
                      </div>`
                    );
                }
            });
        })
    </script>
</body>

</html>
