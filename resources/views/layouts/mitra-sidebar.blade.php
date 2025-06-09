{{-- Contoh penambahan di sidebar --}}
<div class="sb-sidenav-menu">
    <div class="nav">
        <div class="sb-sidenav-menu-heading">Core</div>
        <a class="nav-link" href="{{ route('mitra.dashboard') }}">
            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
            Dashboard
        </a>
        
        {{-- ... menu lainnya ... --}}

        {{-- TAMBAHKAN INI --}}
        <a class="nav-link" href="{{ route('mitra.ulasan.index') }}">
        <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
        Ulasan Pelanggan
        </a>

        {{-- ... menu lainnya ... --}}
    </div>
</div>