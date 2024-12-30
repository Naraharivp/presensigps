@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <h2 class="page-title">
            Vertifikasi Izin
          </h2>
        </div>
      </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered table-striped table-hover">
                    <thead style="text-align: center">
                       <th>No</th>
                       <th>NIK</th>
                       <th>Tanggal</th>
                       <th>Nama Karyawan</th>
                       <th>Jabatan</th>
                       <th>Status</th>
                       <th>Keterangan</th>
                       <th>Status Approve</th>
                       <th>Aksi</th>
                    </thead>
                    <tbody>
                        @foreach ($izinsakit as $d)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $d->nik }}</td>
                                <td>{{ date('d-m-Y',strtotime($d->tgl_izin)) }}</td>
                                <td>{{ $d->nama }}</td>
                                <td>{{ $d->jabatan }}</td>
                                <td>{{ $d->status == "i" ? "Izin" : "Sakit" }}</td>
                                <td>{{ $d->keterangan }}</td>
                                <td style="text-align: center">
                                    @if ($d->status_acc == 1)
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif ($d->status_acc == 2)
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                    <span class="badge bg-warning">Panding</span>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($d->status_acc == 0)
                                    <a href="#" class="btn btn-sm btn-primary" id="approve" id_izinsakit="{{ $d->id }}">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-external-link"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6" /><path d="M11 13l9 -9" /><path d="M15 4h5v5" /></svg>
                                    </a>
                                    @else
                                    <a href="/presensi/{{$d->id}}/cancelapprove" class="btn btn-sm btn-danger">
                                       Cancel
                                    </a>
                                    @endif
                                    
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="modal-izinsakit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Izin/Sakit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="/presensi/accizinsakit" method="POST">
                @csrf
                <input type="hidden" id="idizinform" name="idizinform">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <select name="status_acc" id="status_acc" class="form-select">
                                <option value="1">Disetujui</option>
                                <option value="2">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">
                                Simpan
                            </button>
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
        $('#approve').click(function(e) {
            e.preventDefault();
            var id_izinsakit = $(this).attr("id_izinsakit");
            $('#idizinform').val(id_izinsakit);
            $('#modal-izinsakit').modal('show');
        })
    })
</script>
@endpush