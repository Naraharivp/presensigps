@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Keterangan Izin</div>
    <div class="right"></div>
</div>
@endsection
@section('content')
<div class="row" style="margin-top: 70px">
    <div class="col">
        @php
        $messagesuccsess = Session::get('succsess');
        $messageerror = Session::get('error');
        @endphp
        @if(Session::get('succsess'))
        <div class="alert alert-success">
            {{ $messagesuccsess }}
        </div>
        @endif
        @if(Session::get('error'))
        <div class="alert alert-danger">
            {{ $messageerror }}
        </div>
        @endif
    </div>
</div>
<div class="row" >
    <div class="col">
        @foreach ($data_izin as $d)
        <ul class="listview image-listview" style="border-radius: 6px; margin-bottom: 10px;">
            <li>
                <div class="item">
                    <div class="in">
                        <div>
                            <b>{{ date("d-m-Y",strtotime($d->tgl_izin)) }} ({{ $d->status == "s" ? "Sakit" : "Izin" }})</b><br>
                            <small class="text-muted">{{ $d->keterangan ?? '' }}</small>
                        </div>
                        @if ($d->status_acc == 0)
                            <span class="badge bg-warning" style="border-radius: 8px !important">Waiting</span>
                        @elseif ($d->status_acc == 1)
                            <span class="badge bg-success" style="border-radius: 8px !important">Approved</span>
                        @elseif ($d->status_acc == 2)
                            <span class="badge bg-danger" style="border-radius: 8px !important">Decline</span>
                        @endif
                    </div>
                </div>
            </li>
        </ul>
        @endforeach 
    </div>
</div>
    <div class="fab-button bottom-right" style="margin-bottom: 70px">
        <a href="/presensi/utkizin" class="fab">
            <ion-icon name="add-outline"></ion-icon>
        </a>
    </div>
@endsection
