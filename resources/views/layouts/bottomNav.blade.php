<!-- App Bottom Menu -->
<div class="appBottomMenu">
    <a href="/dashboard" class="item {{request()->is('dashboard') ? 'active' : ''}}">
        <div class="col">
            <ion-icon name="home-outline"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>
    {{-- <a href="/presensi/history" class="item {{request()->is('presensi/history') ? 'active' : ''}}">
        <div class="col">
            <ion-icon name="document-text-outline"></ion-icon>
            <strong>History</strong>
        </div>
    </a> --}}
    <a href="/presensi/create" class="item">
        <div class="col">
            <div class="action-button large">
                <ion-icon name="camera-outline"></ion-icon>
            </div>
        </div>
    </a>
    {{-- <a href="/presensi/izin" class="item {{ request()->is('presensi/izin') || request()->is('presensi/utkizin') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="clipboard-outline"></ion-icon>
            <strong>Izin</strong>
        </div>
    </a> --}}
    <a href="/editprofile" class="item {{request()->is('editprofile') ? 'active' : ''}}">
        <div class="col">
            <ion-icon name="person-outline" role="img" class="md hydrated" aria-label="person outline"></ion-icon>
            <strong>Profile</strong>
        </div>
    </a>
</div>
<!-- * App Bottom Menu -->