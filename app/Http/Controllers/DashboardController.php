<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date('m') * 1;
        $tahunini = date('Y');
        $nik = Auth::guard('karyawan')->user()->nik;
        $presensi_hariini = DB::table('presensi')->where('nik', $nik)->where('tgl_presensi', $hariini)->first();
        $history_bulanini = DB::table('presensi')
            
            ->where('nik',$nik)
            ->whereRaw('MONTH(tgl_presensi)="'.$bulanini.'"')
            ->whereRaw('YEAR(tgl_presensi)="'.$tahunini.'"')   
            ->orderBy('tgl_presensi')
            ->get();

        $rekap_presensi = DB::table('presensi') 
        ->selectRaw('COUNT(nik) as jmlhadir, SUM(IF(jam_in > "07:30",1,0)) as jamterlambat')
        ->where('nik',$nik)
        ->whereRaw('MONTH(tgl_presensi)="'.$bulanini.'"')
        ->whereRaw('YEAR(tgl_presensi)="'.$tahunini.'"') 
        ->first(); 

        $leaderboard = DB::table('presensi')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();

        $namabln = ['','Januari', 'Febuari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $rekap_izin = DB::table('izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('nik',$nik)
            ->whereRaw('MONTH(tgl_izin)="'.$bulanini.'"')
            ->whereRaw('YEAR(tgl_izin)="'.$tahunini.'"') 
            ->where('status_acc',1)
            ->first();

        return view('dashboard.dashboard', compact('presensi_hariini', 'history_bulanini', 'namabln', 'bulanini', 'tahunini',
        'rekap_presensi', 'leaderboard','rekap_izin'));
    }

    public function dashboardadmin(){
        $hariini = date('Y-m-d');
        $rekap_presensi = DB::table('presensi') 
        ->selectRaw('COUNT(nik) as jmlhadir, SUM(IF(jam_in > "07:30",1,0)) as jamterlambat')
        ->where('tgl_presensi', $hariini)
        ->first();

        $rekap_izin = DB::table('izin')
            ->selectRaw('SUM(IF(status="i",1,0)) as jmlizin,SUM(IF(status="s",1,0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('status_acc',1)
            ->first();

        return view('dashboard.dashboardadmin',compact('rekap_presensi','rekap_izin'));
    } 
}


