@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Data Karyawan
          </h2>
        </div>
      </div>
    </div>
</div>
  <div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card" style="background-color: #ededed">
                    <div class="card-body">
                        {{-- notif --}}
                        <div class="row">
                            <div class="col-12">
                                @if (Session::get('success'))
                                    <div class="alert alert-success">
                                        {{Session::get('success')}}
                                    </div>
                                @endif
                                @if (Session::get('warning'))
                                <div class="alert alert-warning">
                                    {{Session::get('warning')}}
                                </div>
                            @endif
                            </div>
                        </div>
                        {{-- button tambah --}}
                        <div class="row">
                            <div class="col-12">
                                <a href="#" class="btn btn-primary" id="btnTambahKaryawan">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M16 19h6" /><path d="M19 16v6" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                                    </svg>Tambah Data</a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <form action="/karyawan" method="GET">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group"></div>
                                            <input type="text" name="nama_karyawan" id="nama_karyawan" class="form-control" 
                                            placeholder="Search Karyawan" value="{{ Request('nama_karyawan') }}">
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <button class="btn btn-primary">
                                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                        <path d="M21 21l-6 -6" /></svg>
                                                        Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <table class="table table-bordered" style="background-color: rgb(217, 251, 255)">
                                    <thead>
                                        <tr style="text-align: center">
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Jabatan</th>
                                            <th>No. HP</th>
                                            <th>Foto</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($karyawan as $d)
                                        @php
                                            $path = Storage::url('uploads/karyawan/'.$d->foto);
                                        @endphp
                                            <tr>
                                                <td style="text-align: center">{{ $loop->iteration + $karyawan->firstItem()-1 }}</td>
                                                <td>{{ $d->nik }}</td>
                                                <td>{{ $d->nama }}</td>
                                                <td>{{ $d->jabatan }}</td>
                                                <td>{{ $d->nmr_hp }}</td>
                                                <td style="text-align: center">
                                                    @if (empty($d->foto))
                                                    <img src="{{ asset('asset/img/noavatar.png') }}" class="avatar" alt="">
                                                    @else
                                                    <img src="{{ url($path) }}" class="avatar" alt="">
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="#" class="edit btn btn-info btn-sm" nik="{{ $d->nik }}" >
                                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
                                                                <path d="M16 5l3 3" />
                                                                <path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
                                                            </svg>
                                                        </a>
                                                        <form action="/karyawan/{{ $d->nik }}/delete" method="POST" style="margin-left: 5px">
                                                            @csrf
                                                            
                                                            <a class="btn btn-danger btn-sm mr-2 deleteConfirm" >
                                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  
                                                                    stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <path d="M4 7l16 0" /><path d="M10 11l0 6" />
                                                                    <path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                            </a>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $karyawan->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     </div>
  </div>
  {{-- modal edit --}}
  <div class="modal modal-blur fade" id="modal-editkaryawan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Data Karyawan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="loadeditform">
            
        </div>
      </div>
    </div>
  </div>
  {{-- modal tambah karyawan --}}
  <div class="modal modal-blur fade" id="modal-inputkaryawan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Data Karyawan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="/karyawan/store" method="POST" id="formkaryawan" enctype="multipart/form-data">
                @csrf
                {{-- input nik --}}
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                              <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-id"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" /><path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M15 8l2 0" /><path d="M15 12l2 0" /><path d="M7 16l10 0" /></svg>
                            </span>
                            <input type="text" value="" id="nik" class="form-control" name="nik" placeholder="NIK">
                          </div>
                    </div>
                </div>
                {{-- input nama --}}
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                              <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                            </span>
                            <input type="text" value="" id="nama" class="form-control" name="nama" placeholder="Nama Lengkap">
                          </div>
                    </div>
                </div>
                {{-- input jabatan --}}
                <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                              <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-users"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M21 21v-2a4 4 0 0 0 -3 -3.85" /></svg>
                            </span>
                            <input type="text" value="" id="jabatan" class="form-control" name="jabatan" placeholder="Jabatan">
                          </div>
                    </div>
                </div>
                  {{-- input noHP --}}
                  <div class="row">
                    <div class="col-12">
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                              <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-phone"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                            </span>
                            <input type="text" value="" id="nmr_hp" class="form-control" name="nmr_hp" placeholder="Nomor Handphone">
                          </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <div class="form-label">Foto Karyawan</div>
                            <input type="file" class="form-control" name="foto">
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group">
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('myscript')
    <script>
        $(function() {
            $("#btnTambahKaryawan").click(function() {
                $("#modal-inputkaryawan").modal('show');
            });

            $(".edit").click(function() {
                var nik = $(this).attr('nik');
                $.ajax({
                    type: 'POST'
                    , url : '/karyawan/edit'
                    , cache : false
                    , data :{
                        _token: "{{ csrf_token() }}"
                        , nik : nik
                    },
                    success : function(respond){
                        $("#loadeditform").html(respond);
                    }
                });
                $("#modal-editkaryawan").modal('show');
            });

            $(".deleteConfirm").click(function(e){
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: "Apakah Anda Yakin?",
                    text: "Data Ini Akan Terhapus!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, delete!"
                    }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                        Swal.fire({
                        title: "Deleted!",
                        text: "Data Berhasil Di Hapus",
                        icon: "success"
                        });
                    }
                    });
            });

            $("#formkaryawan").submit(function() {

                var nik = $("#nik").val();
                var nama = $("#nama").val();
                var jabatan = $("#jabatan").val();
                var nmr_hp = $("#nmr_hp").val();

                if(nik == ""){
                    Swal.fire({
                        title: 'Warning!',
                        text: 'NIK Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                        }).then((result) => {
                            $("#nik").focus();
                        });
                        return false;
                }else if (nama == ""){
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Nama Lengkap Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                        }).then((result) => {
                            $("#nama").focus();
                        });
                        return false;
                }else if (jabatan == ""){
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Jabatan Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                        }).then((result) => {
                            $("#jabatan").focus();
                        });
                        return false;
                }else if (nmrhp == ""){
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Nomor Handphone Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                        }).then((result) => {
                            $("#nmr_hp").focus();
                        });
                        return false;
                    }
            });
        });
    </script>
@endpush