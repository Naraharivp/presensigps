<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Laporan Presensi</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
  <style>
    @page { size: A4 }
    #title{
        font-family: Arial, Helvetica, sans-serif;
        font-size: 16px;
        font-weight: bold;
    }
    .tabeldatakaryawan{
        margin-top: 40px;
    }
    .tabeldatakaryawan td {
        padding: 3px;
    }
    .tabelpresensi {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
        border: 1px solid #2d2d2d;
    }
    .tabelpresensi tr th {
        border: 1px solid #2d2d2d !important;
        background-color: rgb(241, 241, 241);
        padding: 8px;
    }
    .tabelpresensi tr td {
        border: 1px solid #2d2d2d !important;
        padding: 5px;
        font-size: 12px;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
    }
  </style>
</head>

<body class="A4">
    @php
        function selisih($jam_masuk, $jam_keluar)
        {
            list($h, $m, $s) = explode(":", $jam_masuk);
            $dtAwal = mktime($h, $m, $s, "1", "1", "1");
            list($h, $m, $s) = explode(":", $jam_keluar);
            $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
            $dtSelisih = $dtAkhir - $dtAwal;
            $totalmenit = $dtSelisih / 60;
            $jam = explode(".", $totalmenit / 60);
            $sisamenit = ($totalmenit / 60) - $jam[0];
            $sisamenit2 = $sisamenit * 60;
            $jml_jam = $jam[0];
            return $jml_jam . ":" . round($sisamenit2);
        }
    @endphp
  <section class="sheet padding-10mm">
    <table style="width: 100%">
        <tr>
            <td style="width: 30px">
                <img src="{{ asset('assets/img/cetakpresensi.png') }}" width="70" height="70" alt="">
            </td>
            <td>
                <span id="title">
                    LAPORAN PRESENSI KARYAWAN
                    <br>
                    Periode {{ $namabulan[$bulan] }} {{ $tahun }}
                </span>
            </td>
        </tr>
    </table>
    <table class="tabeldatakaryawan">
        <tr>
            <td rowspan="5">
                @php
                    $path = Storage::url('uploads/karyawan/'.$karyawan->foto);
                @endphp
                <img src="{{ url($path) }}" alt="" width="100" height="120">
            </td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $karyawan->nik }}</td>
        </tr>
        <tr>
            <td>Nama Karyawan</td>
            <td>:</td>
            <td>{{ $karyawan->nama }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $karyawan->jabatan }}</td>
        </tr>
        <tr>
            <td>Nomor Handphone</td>
            <td>:</td>
            <td>{{ $karyawan->nmr_hp }}</td>
        </tr>
    </table>
    <table class="tabelpresensi">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jam Masuk</th>
            <th>Foto</th>
            <th>Jam Pulang</th>
            <th>Foto</th>
            <th>Keterangan</th>
            <th>Jam Kerja</th>
        </tr>
        @foreach ($presensi as $d)
        @php
            $path_in = Storage::url('uploads/absensi/'.$d->foto_in);
            $path_out = Storage::url('uploads/absensi/'.$d->foto_out);
        @endphp
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ date('d-m-y', strtotime($d->tgl_presensi)) }}</td>
            <td>{{ $d->jam_in }}</td>
            <td><img src="{{ url($path_in) }}" alt="Foto Masuk" width="30"></td>
            <td>{{ $d->jam_out != null ? $d->jam_out : 'Belum Absen'}}</td>
            <td>
                @if ($d->jam_out != null)
                <img src="{{ url($path_out) }}" alt="Foto Pulang" width="30">
                @else
                <img src="{{ asset('assets/img/noavatar.png') }}" alt="Foto Pulang" width="30">
                @endif
                
            </td>
            <td>
                @if ($d->jam_in >= '07:00')
            @php
                $jamterlambat = selisih('07:00:00',$d->jam_in);
            @endphp
                Terlambat {{ $jamterlambat }} 
            @else
                Tepat Waktu
            @endif
            </td>
            <td>
                @if ($d->jam_out != null)
                @php
                    $jml_jk = selisih($d->jam_in,$d->jam_out);
                @endphp
                @else
                @php
                    $jml_jk = 0;
                @endphp
                @endif
                {{ $jml_jk }}
            </td>
        </tr>
        @endforeach
    </table>
  </section>
</body>
</html>
