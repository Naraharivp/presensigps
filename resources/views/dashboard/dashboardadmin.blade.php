@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <!-- Page pre-title -->
          <div class="page-pretitle">
            Overview
          </div>
          <h2 class="page-title">
            Dashboard
          </h2>
        </div>
      </div>
    </div>
  </div>
  <div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-success text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  
                            width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  
                            stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-fingerprint">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18.9 7a8 8 0 0 1 1.1 5v1a6 6 0 0 0 .8 3" />
                            <path d="M8 11a4 4 0 0 1 8 0v1a10 10 0 0 0 2 6" /><path d="M12 11v2a14 14 0 0 0 2.5 8" />
                            <path d="M8 15a18 18 0 0 0 1.8 6" /><path d="M4.9 19a22 22 0 0 1 -.9 -7v-1a8 8 0 0 1 12 -6.95" />
                            </svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          {{ $rekap_presensi->jmlhadir }}
                        </div>
                        <div class="text-muted">
                          Karyawan Hadir
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            {{-- izin --}}
            <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-info text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                            </svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                            {{ $rekap_izin->jmlizin != null? $rekap_izin->jmlizin : 0 }}
                        </div>
                        <div class="text-muted">
                          Karyawan Izin
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              {{-- sakit --}}
              <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-warning text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mood-sick"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18z" />
                                <path d="M9 10h-.01" /><path d="M15 10h-.01" />
                                <path d="M8 16l1 -1l1.5 1l1.5 -1l1.5 1l1.5 -1l1 1" />
                            </svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                          {{ $rekap_izin->jmlsakit != null? $rekap_izin->jmlsakit : 0}}
                        </div>
                        <div class="text-muted">
                          Karyawan Sakit
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              {{-- terlambat --}}
              <div class="col-md-6 col-xl-3">
                <div class="card card-sm">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-auto">
                        <span class="bg-danger text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clock-exclamation"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M20.986 12.502a9 9 0 1 0 -5.973 7.98" />
                                <path d="M12 7v5l3 3" /><path d="M19 16v3" />
                                <path d="M19 22v.01" /></svg>
                        </span>
                      </div>
                      <div class="col">
                        <div class="font-weight-medium">
                            {{ $rekap_presensi->jamterlambat != null? $rekap_presensi->jamterlambat : 0 }}
                        </div>
                        <div class="text-muted">
                          Karyawan Terlambat
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
        </div>
    </div>
</div>
       
    
@endsection