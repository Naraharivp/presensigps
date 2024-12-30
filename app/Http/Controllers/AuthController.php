<?php

namespace App\Http\Controllers;

use App\Models\karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{    public function login(Request $request)
    {
        $credentials = $request->only('nik', 'password');
    
        if (Auth::guard('karyawan')->attempt($credentials)) {
            return redirect('/dashboard'); 
        }
        return redirect()->route('login')->with('error', 'NIK atau password yang Anda masukkan salah');
    }

    // jika ingin login tanpa password enkripsi
    // public function login(Request $request){
    // $credentials = $request->only('nik', 'password');
    
    // // Mendapatkan karyawan berdasarkan NIK
    // $karyawan = Karyawan::where('nik', $credentials['nik'])->first();

    // // Jika karyawan ditemukan dan password sesuai
    // if ($karyawan && $karyawan->password == $credentials['password']) {
    //     // Login berhasil
    //     Auth::guard('karyawan')->login($karyawan);
    //     return redirect('/dashboard'); 
    // }
    // Login gagal
//     return redirect()->route('login')->with('error', 'NIK atau password yang Anda masukkan salah');
// }

    public function prosesloginadmin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('user')->attempt($credentials)) {
            return redirect('/admin/dashboardadmin'); 
        }
        return redirect()->route('loginadmin')->with('warning', 'Email atau password yang Anda masukkan salah');
    }


    
    public function proseslogout(){
        if(Auth::guard('karyawan')->check()){
            Auth::guard('karyawan')->logout();
            return redirect('/login');
        }
    }

    public function proseslogoutadmin() {
        if(Auth::guard('user')->check()){
            Auth::guard('user')->logout();
            return redirect('/admin');
        }
    }
    
}
