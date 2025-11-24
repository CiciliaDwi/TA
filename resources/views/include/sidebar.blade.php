<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                @if(Auth::user()->jabatan == 'admin')
                    <div class="sb-sidenav-menu-heading">Main</div>
                    <a class="nav-link" href="{{ route('home') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <div class="sb-sidenav-menu-heading">Master Data</div>
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Kelola User
                    </a>
                    <a class="nav-link" href="{{route('products.index')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                        Kelola Produk
                    </a>
                    <a class="nav-link" href="{{route('categories.index')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                        Kelola Kategori
                    </a>
                @endif

                <div class="sb-sidenav-menu-heading">Transaksi</div>
                <a class="nav-link" href="{{route('transactions.index')}}">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                    Penjualan
                </a>

                @if(Auth::user()->jabatan == 'admin')
                    <div class="sb-sidenav-menu-heading">Laporan</div>
                    <a class="nav-link" href="{{ route('laporan.index') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        Laporan Penjualan
                    </a>

                    <div class="sb-sidenav-menu-heading">Prediksi</div>
                    <a class="nav-link" href="{{ route('prediction.form') }}">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                        Prediksi Penjualan
                    </a>
                @endif
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{ Auth::user()->nama }} - {{ Auth::user()->jabatan }}
        </div>
    </nav>
</div>