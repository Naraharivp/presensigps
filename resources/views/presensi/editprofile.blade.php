@extends('layouts.presensi')
@section('header')
<div class="appHeader bg-primary text-light">
    <div class="left">
        <a href="/dashboard" class="headerButton goBack">
            <ion-icon name="chevron-back-outline"></ion-icon>
        </a>
    </div>
    <div class="pageTitle">Edit Profile</div>
    <div class="right"></div>
</div>

@endsection

@section('content')
<style>
    .custom-btn {
        background-color: #ffffff !important; 
        border: 2px solid #007bff !important; 
        color: #007bff !important; 
        text-align: left !important;  
        padding-left: 10px !important; 
        display: flex;
        text-align: center;
    }
    .custom-btn:hover {
        color: #ffffff !important;
    }   
    .left-icon {
        margin-right: 5px !important; 
    }
</style>

<div class="row" style="margin-top: 4rem">
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
<form action="/presensi/{{ $karyawan->nik }}/updateprofile" method="POST" enctype="multipart/form-data" >
    @csrf
    <div class="col">
        <div class="form-group boxed">
            <div class="input-wrapper">
                <input type="text" class="form-control" value="{{ $karyawan->nama}}" name="nama" placeholder="Nama Lengkap" autocomplete="off">
            </div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <input type="text" class="form-control" value="{{ $karyawan->nmr_hp }}" name="nmr_hp" placeholder="No. HP" autocomplete="off">
            </div>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="off">
            </div>
        </div>
        <div class="custom-file-upload" id="fileUpload1">
            <input type="file" name="foto" id="fileuploadInput" accept=".png, .jpg, .jpeg">
            <label for="fileuploadInput">
                <span>
                    <strong>
                        <ion-icon name="cloud-upload-outline" role="img" class="md hydrated" aria-label="cloud upload outline"></ion-icon>
                        <i>Tap to Upload Picture</i>
                    </strong>
                </span>
            </label>
        </div>
        <div class="form-group boxed">
            <div class="input-wrapper">
                <button type="submit" class="btn btn-primary btn-block custom-btn">
                    <ion-icon name="refresh-outline"></ion-icon>
                    <span class="btn-text">Update</span>
                </button>
            </div>
        </div>
        <div class="input-wrapper">
            <a href="/logout" class="logout" id="logout-link">
                <button type="button" class="btn btn-primary btn-block custom-btn">
                    <ion-icon name="exit-outline" class="left-icon"></ion-icon>
                    <span class="btn-text">Keluar Akun</span>
                </button>
            </a>
        </div>           
    </div>
</form>
@endsection