@extends('layouts.presensi')
@section('header')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/css/materialize.min.css">
<style>
    .datepicker-modal{
        max-height: 430px !important;
    }
    .datepicker-date-display {
        background-color: #1e74fd !important;
    }
    .form-control.datepicker {
        background-color: #ffffff !important;
        height: calc(1.5em + 0.75rem + 2px) !important;
        font-size: 1rem !important;
        text-indent: 8px !important;
        border-radius: 0.25rem !important;
        width: 100% !important;
    }
</style>


<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/presensi/izin" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Form Izin</div>
    <div class="right"></div>
</div>
@endsection
@section('content')
    <div class="row" style="margin-top: 70px">
        <div class="col">
            <form  method="POST" action="/presensi/storizin" id="frmizin">
                @csrf
                <div class="form-group">
                    <input type="text" id="tgl_izin" name="tgl_izin" class="form-control datepicker" placeholder="Tanggal">
                </div>
                <div class="form-group">
                    <select name="status" id="status" class="form-control">
                        <option value="">Status</option>
                        <option value="i">Izin</option>
                        <option value="s">Sakit</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="keterangan" id="keterangan" cols="30" rows="10" class="form-control" placeholder="Keterangan..."></textarea>
                </div>
                <div class="form-group">
                    <div class="container d-flex justify-content-center align-items-center">
                    <button class="btn btn-primary" style="border-radius: 6px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('myscript')
<script>
    var currYear = (new Date()).getFullYear();

    $(document).ready(function() {
        $(".datepicker").datepicker({
            format: "yyyy-mm-dd"    
        });

        $('#frmizin').submit(function() {
            var tgl_izin = $('#tgl_izin').val();
            var status = $('#status').val();
            var keterangan = $('#keterangan').val();
            if (tgl_izin == "") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Tanggal Harus Di Isi',
                    icon: 'warning',
                    });
                return false;
            }else if (status == "") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Status Harus Di Isi',
                    icon: 'warning',
                    });
                return false;
            }else if (keterangan == "") {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Keterangan Harus Di Isi',
                    icon: 'warning',
                    });
                return false;
            }
        });
    });
</script>
@endpush