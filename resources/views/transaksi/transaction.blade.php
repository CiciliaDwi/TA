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
                    <h1 class="mt-4">Transaksi Penjualan</h1>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-cart-plus me-1"></i>
                            Form Transaksi Penjualan
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('transactions.store') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="no_nota">Nomor Nota</label>
                                            <input type="text" class="form-control" id="no_nota" name="no_nota"
                                                readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="transaction_date">Tanggal Transaksi</label>
                                            <input type="text" class="form-control" id="transaction_date" readonly>
                                            <input type="hidden" name="transaction_date" value="{{ now() }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="id_pegawai">Nama Kasir</label>
                                            <input type="text" class="form-control" id="id_pegawai"
                                                value="{{ Auth::user()->nama }}" readonly>
                                            <input type="hidden" name="id_pegawai" value="{{ Auth::user()->id }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="customer_code">Nama Pelanggan</label>
                                            <select class="form-control" id="customer_code" name="customer_code"
                                                required>
                                                <option value="">Pilih Pelanggan</option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->KodePelanggan }}"
                                                        @selected(old('customer_code') == $customer->KodePelanggan)>
                                                        {{ $customer->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="payment_method">Metode Pembayaran</label>
                                            <select class="form-control" id="payment_method" name="payment_method"
                                                required>
                                                <option value="">Pilih Metode Pembayaran</option>
                                                @foreach ($paymentMethods as $method)
                                                    <option value="{{ $method }}" @selected(old('payment_method') == $method)>
                                                        {{ ucfirst($method) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <h5>Produk</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="products_table">
                                                <thead>
                                                    <tr>
                                                        <th>Kode Produk</th>
                                                        <th>Nama Produk</th>
                                                        <th>Barcode</th>
                                                        <th>Harga</th>
                                                        <th>Jumlah</th>
                                                        <th>Subtotal</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control product-code"
                                                                name="product_codes[]" readonly>
                                                        </td>
                                                        <td>
                                                            <select class="form-control product-select select2"
                                                                name="products[]" required>
                                                                <option value="">Pilih Produk</option>
                                                                @foreach ($products as $product)
                                                                    <option value="{{ $product->KodeBarang }}"
                                                                        data-price="{{ $product->HargaJual }}"
                                                                        data-barcode="{{ $product->Barcode }}">
                                                                        {{ $product->Nama }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control product-barcode"
                                                                readonly>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control product-price"
                                                                readonly>
                                                            <input type="hidden" name="product_prices[]"
                                                                class="product-price-hidden">
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control product-quantity"
                                                                name="quantities[]" min="1" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control product-subtotal"
                                                                readonly>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm remove-product">
                                                                <i class="fas fa-trash"></i>Hapus
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-success btn-sm" id="add_product">
                                            <i class="fas fa-plus"></i> Tambah Produk
                                        </button>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="form-group mb-3">
                                            <label for="total_amount">Total Pembayaran</label>
                                            <input type="number" class="form-control" id="total_amount"
                                                name="total_amount" readonly>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                                        <button type="button" id="reset-form" class="btn btn-secondary">Reset
                                            Form</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            @include('include.footer')
        </div>
        @include('include.script')

</body>

</html>
