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

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Kesalahan:</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Validasi gagal:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
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
                            {{-- <form method="POST" action="{{ route('prediction.process') }}"> --}}
                            <form id="predictForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="product_code" class="form-label">Produk</label>
                                    <select
                                        class="form-control product-select @error('product_code') is-invalid @enderror"
                                        id="product_code" name="product_code" required>
                                        <option value="">Pilih Produk</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->KodeBarang }}"
                                                {{ old('product_code') == $product->KodeBarang ? 'selected' : '' }}>
                                                {{ $product->Nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="qty" class="form-label">Jumlah Produk Terjual di Bulan
                                        Sebelumnya</label>
                                    <input type="number" id="qty" name="qty" value="{{ old('qty') }}"
                                        class="form-control @error('qty') is-invalid @enderror" placeholder="Contoh: 50"
                                        required>
                                    @error('qty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button class="btn btn-success" id="buttonSubmit">
                                    <i class="fas fa-bolt me-1"></i> Prediksi Jumlah Unit
                                </button>


                            </form>

                            <div id="loading" class="mt-3"></div>

                            <div id="resultData" class="d-none">
                                <hr>
                                <div class="mt-3 p-4 bg-light border rounded">
                                    <p class="mb-1"><strong>Hasil Prediksi Jumlah Unit:</strong></p>
                                    <h2 class="display-6" id="resultQty"></h2>
                                    <p class="text-muted small">Nilai ini adalah perkiraan Jumlah Unit yang akan
                                        terjual
                                        (Qty) bulan depan.</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </main>
            @include('include.footer')
        </div>
    </div>
    @include('include.script')


    <script>
        let taskInterval = null;

        // ====== Save Response Data Prediction ======
        function saveResponsePrediction(response) {
            $.ajax({
                url: "{{ route('prediction.save-result') }}",
                method: "POST",
                data: {
                    response
                },
                headers: {
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.success) {
                        const message = response.message;
                        const resultQty = response.result.result_qty;

                        $("#loading").html(
                            `<div class="alert alert-success">${message}</div>`
                        );

                        $("#resultData").removeClass('d-none');
                        $("#resultQty").html(resultQty);

                        // $("#predictForm")[0].reset(); // reset form

                        clearInterval(taskInterval); // stop loop checking
                        return;
                    }
                },
                error: function() {
                    clearInterval(taskInterval);
                    $('#buttonSubmit').removeClass('disabled');

                    $("#loading").html(
                        `<div class="alert alert-danger">Terjadi kesalahan saat proses prediksi.</div>`
                    );
                },
            })
        }

        // ====== Cek status background task ======
        function checkTaskStatus(taskId) {
            $.ajax({
                url: "{{ route('prediction.task') }}",
                method: "POST",
                data: {
                    task_id: taskId
                },
                headers: {
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                success: function(response) {
                    try {
                        const message = response.message;

                        if (response.status === "DONE") {
                            saveResponsePrediction(response);
                        } else {
                            $("#loading").html(`${message}        
                              <div class="spinner-border" role="status">
                                  <span class="visually-hidden">Loading...</span>
                              </div>`);
                        }
                    } catch (error) {
                        $("#loading").html(
                            `<div class="alert alert-danger">Terjadi kesalahan saat proses prediksi.</div>`
                        );
                        clearInterval(taskInterval); // stop loop checking
                    } finally {
                        $('#buttonSubmit').removeClass('disabled');
                    }
                },
                error: function() {
                    clearInterval(taskInterval);
                    $('#buttonSubmit').removeClass('disabled');

                    $("#loading").html(
                        `<div class="alert alert-danger">Terjadi kesalahan saat proses prediksi.</div>`
                    );
                },
            });
        }

        // ====== Submit prediction form ======
        $("#predictForm").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('prediction.process') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $("#loading").html(`Processing request, please wait...         
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`);
                    $('#buttonSubmit').addClass('disabled');

                    $("#resultData").addClass('d-none');
                    $("#resultQty").val('');
                },
                success: function(response) {

                    const taskId = response.task_id;
                    const message = response.message;

                    $("#loading").html(`${message}        
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>`);

                    // delay 5 detik sebelum polling pertama
                    setTimeout(() => {
                        taskInterval = setInterval(() => {
                            checkTaskStatus(taskId);
                        }, 5000); // interval 5 detik
                    }, 5000); // delay 5 detik

                },
                error: function(xhr) {
                    console.error(xhr.responseJSON?.message)
                    $('#buttonSubmit').removeClass('disabled');
                    $("#loading").html(
                        `<div class="alert alert-danger">${"Terjadi kesalahan pada server"}</div>`
                    );
                }
            });
        });
    </script>
</body>

</html>
