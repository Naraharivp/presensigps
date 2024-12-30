@extends('layouts.presensi')
@section('content')
<div class="section" id="user-section">
    <div id="user-detail">
        <div class="avatar">
            @if (!empty(Auth::guard('karyawan')->user()->foto))
            @php
            $path = Storage::url('uploads/karyawan/'.Auth::guard('karyawan')->user()->foto)
            @endphp
            <img src="{{ url($path) }}" alt="avatar" class="imaged w64 rounded" style="height: 60px">
            @else
            <img src="assets/img/sample/avatar/avatar1.jpg" alt="avatar" class="imaged w64 rounded">
            @endif
        </div>
        <div id="user-info">
            <h2 id="user-name" style="font-size: 20px;">{{Auth::guard('karyawan')->user()->nama}}</h2>
            <span id="user-role">{{Auth::guard('karyawan')->user()->jabatan}}</span>
        </div>
    </div>
</div>

<div class="section" id="menu-section">
    <div class="card">
        <div class="card-body text-center">
            <div class="list-menu">
                {{-- <div class="item-menu text-center">
                    <div class="menu-icon">
                         <a href="" class="green" style="font-size: 40px;">
                            <ion-icon name="person-sharp"></ion-icon>
                        </a> 
                     </div>
                    <div class="menu-name">
                        <span class="text-center">Profil</span>
                    </div> 
                 </div>   --}}
                {{-- <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="" class="blue" style="font-size: 40px;">
                            <ion-icon name="today-outline"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Cuti</span>
                    </div>
                </div> --}}
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/presensi/history" class="blue" style="font-size: 40px;">
                            <ion-icon name="document-text-outline"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        <span class="text-center">Histori</span>
                    </div>
                </div>
                <div class="item-menu text-center">
                    <div class="menu-icon">
                        <a href="/presensi/izin" class="blue" style="font-size: 40px;">
                            <ion-icon name="clipboard-outline"></ion-icon>
                        </a>
                    </div>
                    <div class="menu-name">
                        Izin
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="section mt-2" id="presence-section">
    <div class="todaypresence">
        <div class="row">
            <div class="col-6">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                @if ($presensi_hariini != null)
                                @php
                                    $path = Storage::url('uploads/absensi/'.$presensi_hariini->foto_in);
                                @endphp
                                <img src="{{url($path)}}" alt="" class="imaged" style="width : 44px; height : auto;">
                                @else
                                <ion-icon name="enter-outline"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle" style="margin-left: 15px;">Masuk</h4>
                                <span style="margin-left: 3px;">{{ $presensi_hariini != null ? $presensi_hariini->jam_in : "Belum Presensi" }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="presencecontent">
                            <div class="iconpresence">
                                @if ($presensi_hariini != null && $presensi_hariini->jam_out != null)
                                @php
                                    $path = Storage::url('uploads/absensi/'.$presensi_hariini->foto_out);
                                @endphp
                                <img src="{{url($path)}}" alt="" class="imaged" style="width : 44px; height : auto;">
                                @else
                                <ion-icon name="exit-outline"></ion-icon>
                                @endif
                            </div>
                            <div class="presencedetail">
                                <h4 class="presencetitle" style="margin-left: 15px;"> Pulang </h4>
                                <span style="margin-left: 3px;">{{ $presensi_hariini != null && $presensi_hariini->jam_out != null ? 
                                            $presensi_hariini->jam_out : "Belum Presensi" }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="rekapresensi">
        <h3>Rekap Presensi Bulan {{ $namabln[$bulanini]}} Tahun {{$tahunini}}</h3>
        <div class="row">
            <div class="col-3">
                <div class="card">
                    <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem;">
                        <span class="badge bg-danger" style="position :absolute; 
                        top : 3px; right : 10px; font-size : 0.6rem; z-index : 999">{{ $rekap_presensi->jmlhadir }}</span>
                        <ion-icon name="body-outline" style="font-size: 1.6rem;" class="text-primary mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.8rem; font-weight : 500">Hadir</span>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem; ">
                        <span class="badge bg-danger" style="position :absolute; 
                        top : 3px; right : 10px; font-size : 0.6rem; z-index : 999" >{{ $rekap_izin->jmlizin }}</span>
                        <ion-icon name="clipboard-outline" style="font-size: 1.6rem;" class="green mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.8rem; font-weight : 500">Izin</span>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem;">
                        <span class="badge bg-danger" style="position :absolute; 
                        top : 3px; right : 10px; font-size : 0.6rem; z-index : 999" >{{ $rekap_izin->jmlsakit }}</span>
                        <ion-icon name="medkit-outline" style="font-size: 1.6rem;" class="text-warning mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.8rem; font-weight : 500" >Sakit</span>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card" title="Potong Gaji 1%">
                    <div class="card-body text-center" style="padding: 12px 12px !important; line-height:0.8rem;" >
                        <span class="badge bg-danger" style="position :absolute; 
                        top : 3px; right : 10px; font-size : 0.6rem; z-index : 999" >{{ $rekap_presensi->jamterlambat }}</span>
                        <ion-icon name="alarm-outline" style="font-size: 1.6rem;" class="text-danger mb-1"></ion-icon>
                        <br>
                        <span style="font-size: 0.8rem; font-weight : 500" >Telat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="presencetab mt-2">
        <div class="tab-pane fade show active" id="pilled" role="tabpanel">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home" role="tab">
                        Bulan Ini
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
                        Leaderboard
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content mt-2" style="margin-bottom:100px;">
            <div class="tab-pane fade show active" id="home" role="tabpanel">
                <ul class="listview image-listview">
                    @foreach ($history_bulanini as $d)
                    @php
                        $path = Storage::url("uploads/absensi/".$d->foto_in);
                    @endphp
                    <li>
                        <div class="item">
                           <div class="icon-box bg-primary">
                            <ion-icon name="file-tray-full-outline"></ion-icon>
                           </div>
                            <div class="in">
                               <div>
                                {{date("d-m-Y", strtotime($d->tgl_presensi)) }}
                               </div>
                               <span class="badge badge-success" style="border-radius: 6px !important">{{$d->jam_in}}</span>
                               <span class="badge badge-danger" style="border-radius: 6px !important">
                                {{$presensi_hariini != null && $d->jam_out != null? $d->jam_out : 'Belum Presensi'}}</span>
                           </div>
                       </div>
                   </li>
                    @endforeach
                    
                </ul>
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <ul class="listview image-listview">
                    @foreach ($leaderboard as $d)
                    <li>
                        <div class="item">
                            <img src="assets/img/sample/avatar/avatar1.jpg" alt="image" class="image">
                            <div class="in">
                                <div>
                                    <b>{{ $d->nama }}</b><br>
                                    <small class="text-muted">{{ $d->jabatan }}</small>
                                </div>
                                <span class="badge {{ $d->jam_in < "07:30" ? "bg-success" : "bg-danger"}}" style="border-radius: 6px !important">{{ $d->jam_in }}</span>
                            </div>
                        </div>
                    </li>
                    @endforeach

                </ul>
            </div>

        </div>
    </div>
</div>
@endsection