@php
    $currentUser = Auth::user();
@endphp

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                @if ($currentUser->jabatan == 'admin')
                    <div class="sb-sidenav-menu-heading">Main</div>
                    <a href="{{ route('home') }}" @class(['nav-link', 'active' => Route::is('home')])>
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <div class="sb-sidenav-menu-heading">Master Data</div>
                    <a href="{{ route('users.index') }}" @class(['nav-link', 'active' => Route::is('users.*')])>
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Kelola User
                    </a>
                    <a href="{{ route('categories.index') }}" @class(['nav-link', 'active' => Route::is('categories.*')])>
                        <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                        Kelola Kategori
                    </a>
                    <a href="{{ route('products.index') }}" @class(['nav-link', 'active' => Route::is('products.*')])>
                        <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                        Kelola Produk
                    </a>
                @endif

                <div class="sb-sidenav-menu-heading">Transaksi</div>
                <a href="{{ route('transactions.index') }}" @class(['nav-link', 'active' => Route::is('transactions.*')])>
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                    Penjualan
                </a>

                @if ($currentUser->jabatan == 'admin')
                    <div class="sb-sidenav-menu-heading">Laporan</div>
                    <a href="{{ route('laporan.index') }}" @class(['nav-link', 'active' => Route::is('laporan.*')])>
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        Laporan Penjualan
                    </a>

                    <div class="sb-sidenav-menu-heading">Prediksi</div>
                    <a href="{{ route('prediction.form') }}" @class(['nav-link', 'active' => Route::is('prediction.*')])>
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        Prediksi Penjualan
                    </a>
                @endif
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{ $currentUser->nama }} - {{ $currentUser->jabatan }}
        </div>
    </nav>
</div>
