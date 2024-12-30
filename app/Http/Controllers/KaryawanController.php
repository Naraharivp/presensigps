<?php

namespace App\Http\Controllers;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class KaryawanController extends Controller
{
    public function index(Request $request){


        $query = Karyawan::query();
        $query->select('karyawan.*');
        $query->orderBy('nama');
        if (!empty($request->nama_karyawan)){
            $query->where('nama','like','%' .$request->nama_karyawan .'%');
        }
        $karyawan = $query-> paginate(5);

        return view('karyawan.index',compact('karyawan'));
    }

    public function store(Request $request){
        $nik = $request->nik;
        $nama = $request->nama;
        $jabatan = $request->jabatan;
        $nmr_hp = $request->nmr_hp;
        $password = Hash::make('12345');
       
        
        if($request->hasFile('foto')) {
            $foto = $nik. "." .$request->file('foto')->getClientOriginalExtension();
        }else {
            $foto = null;
        }

        try {
            $data = [
                'nik' => $nik,
                'nama' => $nama,
                'jabatan' => $jabatan,
                'nmr_hp' => $nmr_hp,
                'foto' => $foto,
                'password' => $password
            ];
            $simpan = DB::table('karyawan')->insert($data);
            if ($simpan) {
                if($request->hasFile('foto')) {
                    $folderPath = 'public/uploads/karyawan/';
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success'=>'Data Berhasil Di Simpan']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning'=>'Data Gagal Di Simpan']);
        }
    }

    public function edit (Request $request){
        $nik = $request->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();

        return view('karyawan.edit',compact('karyawan'));
    }

    public function update ($nik, Request $request){
        $nik = $request->nik;
        $nama = $request->nama;
        $jabatan = $request->jabatan;
        $nmr_hp = $request->nmr_hp;
        $password = Hash::make('12345');
       
       $old_foto = $request->old_foto; 
        if($request->hasFile('foto')) {
            $foto = $nik. "." .$request->file('foto')->getClientOriginalExtension();
        }else {
            $foto = $old_foto;
        }

        try {
            $data = [
                'nama' => $nama,
                'jabatan' => $jabatan,
                'nmr_hp' => $nmr_hp,
                'foto' => $foto,
                'password' => $password
            ];
            $update = DB::table('karyawan')->where('nik', $nik)->update($data);
            if ($update) {
                if($request->hasFile('foto')) {
                    $folderPath = 'public/uploads/karyawan/';
                    $folderPathOld = 'public/uploads/karyawan/' . $old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success'=>'Data Berhasil Di Update']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning'=>'Data Gagal Di Update']);
        }
    }

    public function delete ($nik) {
        $delete = DB::table('karyawan')->where('nik', $nik)->delete();
        if($delete){
            return Redirect::back()->with(['success'=>'Data Berhasil DiHapus']);
        }else {
            return Redirect::back()->with(['warning'=>'Data Gagal DiHapus']);
        }
    }
}
