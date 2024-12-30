<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Exception;

class PresensiController extends Controller
{
    public function create(){
        $hariini = date("Y-m-d");
        $nik = Auth::guard('karyawan')->user()->nik;
        
        // Cek status presensi hari ini
        $presensi = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('nik', $nik)
            ->first();
        
        $cek = 0;
        $sudahPulang = false;
        
        if ($presensi) {
            $cek = 1;
            if (!empty($presensi->jam_out)) {
                $sudahPulang = true;
            }
        }
        
        return view('presensi.create', compact('cek', 'sudahPulang'));
    }

    public function store(Request $request)
    {
        try {
            $nik = Auth::guard('karyawan')->user()->nik;
            $tgl_presensi = date("Y-m-d");
            
            // Cek apakah sudah presensi pulang
            $presensi = DB::table('presensi')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('nik', $nik)
                ->first();
            
            if ($presensi && !empty($presensi->jam_out)) {
                Log::warning("Percobaan presensi setelah pulang oleh NIK: $nik");
                return "error|Anda sudah melakukan presensi pulang hari ini|";
            }
            
            $jam = date("H:i:s");
            $lokasi = $request->lokasi;
            $image = $request->image;
    
            Log::info("Memulai proses presensi untuk NIK: $nik");
    
            // Validasi input
            if (empty($image) || strpos($image, 'data:image') !== 0) {
                Log::error("Format gambar tidak valid untuk NIK: $nik");
                return "error|Format gambar tidak valid. Pastikan menggunakan kamera|";
            }
    
            // Validasi lokasi
            if (empty($lokasi)) {
                Log::error("Data lokasi tidak ditemukan untuk NIK: $nik");
                return "error|Data lokasi tidak ditemukan. Pastikan GPS aktif|";
            }
    
            // Process and save the image with better quality
            $folderPath = "public/uploads/absensi/";
            $formatName = $nik . "-" . $tgl_presensi . "-temp.jpg";
            $image_parts = explode(";base64,", $image);
            $image_base64 = base64_decode($image_parts[1]);
    
            // Ensure directory exists
            $fullPath = storage_path('app/' . $folderPath);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
    
            $fileName = storage_path('app/' . $folderPath . $formatName);
            
            // Save with high quality
            $img = imagecreatefromstring($image_base64);
            if ($img !== false) {
                imagejpeg($img, $fileName, 100); // Save with 100% quality
                imagedestroy($img);
            } else {
                file_put_contents($fileName, $image_base64);
            }
    
            Log::info("Gambar disimpan: $fileName");
    
            // Save debug image with timestamp
            $debugImagePath = storage_path('app/public/debug/');
            if (!file_exists($debugImagePath)) {
                mkdir($debugImagePath, 0755, true);
            }
            $debugImageName = "debug-" . $nik . "-" . $tgl_presensi . "-" . time() . ".jpg";
            copy($fileName, $debugImagePath . $debugImageName);
    
            Log::info("Gambar debug disimpan: " . $debugImagePath . $debugImageName);
    
            // Adjust confidence threshold based on time of day
            $hour = (int)date('H');
            $confidence_threshold = 45;  // Default threshold
            
            // More lenient threshold for early morning and evening
            if ($hour < 7 || $hour > 17) {
                $confidence_threshold = 40;
            }
            Log::info("Menggunakan confidence threshold: $confidence_threshold");
    
            // Face recognition with specific user dataset
            $userFacesDir = storage_path('app/public/uploads/karyawan/' . $nik);
            $pythonScript = base_path('resources/python/face_recognition.py');

            // Validate if user dataset exists
            if (!file_exists($userFacesDir) || count(glob($userFacesDir . "/*.{jpg,jpeg,png}", GLOB_BRACE)) === 0) {
                Log::error("Dataset wajah tidak ditemukan untuk NIK: $nik");
                unlink($fileName);
                return "error|Dataset wajah Anda belum tersedia. Silakan hubungi admin|";
            }

            // Execute face recognition script with user-specific dataset
            $command = sprintf(
                'python "%s" "%s" "%s" %d 2>&1',
                $pythonScript,
                $fileName,
                $userFacesDir,
                $confidence_threshold
            );

            Log::info("Python script path: " . $pythonScript);
            Log::info("User faces directory: " . $userFacesDir);
            Log::info("Command yang dijalankan: " . $command);

            $output = shell_exec($command);
            Log::info("Output dari Python: " . $output);

            // Tambahkan validasi output
            if (empty($output)) {
                Log::error("Script Python tidak menghasilkan output");
                unlink($fileName);
                return "error|Terjadi kesalahan sistem saat pengenalan wajah|";
            }

            try {
                $result = json_decode($output, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error("Gagal mengurai output skrip Python: " . json_last_error_msg());
                    unlink($fileName);
                    return "error|Pengenalan wajah gagal|";
                }
            } catch (\Exception $e) {
                Log::error("Error parsing JSON output: " . $output);
                unlink($fileName);
                return "error|Terjadi kesalahan saat memproses hasil pengenalan wajah|";
            }
    
            Log::info("Hasil pencocokan: " . json_encode($result));
    
            // Handle different error cases
            if (isset($result['error'])) {
                if (strpos($result['error'], 'No face detected') !== false) {
                    unlink($fileName);
                    return "error|Wajah tidak terdeteksi dalam gambar|";
                }
                unlink($fileName);
                return "error|Terjadi kesalahan dalam pengenalan wajah. Silakan coba lagi|";
            }
    
            // Validate face recognition result
            if (!isset($result['match']) || $result['match'] !== true) {
                Log::warning("Pencocokan gagal. NIK: $nik, Hasil: " . json_encode($result));
                $confidence = isset($result['confidence']) ? round($result['confidence'], 2) : 0;
                unlink($fileName);
                return "error|Wajah tidak dikenali atau tidak sesuai (Confidence: {$confidence}%). Silakan coba lagi|";
            }
    
            // Log confidence score
            if ($result['confidence'] > 30 && $result['confidence'] < $confidence_threshold) {
                unlink($fileName);
                return "error|Wajah terdeteksi tapi kurang jelas (Confidence: " . 
                       round($result['confidence'], 2) . "%). Coba dengan pencahayaan lebih baik|";
            }
    
            // Check location
            $latitudetmpt = -7.748125757099419;
            $longitudetmpt = 110.35527710020557;
            $lokasiuser = explode(",", $lokasi);
            $latitudeuser = $lokasiuser[0];
            $longitudeuser = $lokasiuser[1];
            $jarak = $this->distance($latitudetmpt, $longitudetmpt, $latitudeuser, $longitudeuser);
            $radius = round($jarak["meters"]);
    
            Log::info("Jarak user dari kantor: $radius meter");
    
            if ($radius > 50) {
                unlink($fileName);
                Log::warning("User berada di luar jangkauan: $radius meter");
                return "error|Maaf, Anda berada di luar radius kantor ({$radius}m). Maksimal 50m dari kantor|";
            }
    
            // Rename the file if face is recognized
            $cek = DB::table('presensi')
                ->where('tgl_presensi', $tgl_presensi)
                ->where('nik', $nik)
                ->count();
    
            $newFormatName = $nik . "-" . $tgl_presensi . "-" . ($cek > 0 ? "out" : "in") . ".jpg";
            $newFileName = storage_path('app/' . $folderPath . $newFormatName);
            rename($fileName, $newFileName);
    
            // Save attendance with transaction
            DB::beginTransaction();
            try {
                if ($cek > 0) {
                    $data_pulang = [
                        'jam_out' => $jam,
                        'foto_out' => $newFormatName,
                        'lokasi_out' => $lokasi,
                        'updated_at' => now()
                    ];
                    DB::table('presensi')
                        ->where('tgl_presensi', $tgl_presensi)
                        ->where('nik', $nik)
                        ->update($data_pulang);
                    
                    DB::commit();
                    Log::info("Presensi pulang berhasil untuk NIK: $nik");
                    return "success|Terima kasih, Anda berhasil melakukan presensi pulang|out";
                } else {
                    $data = [
                        'nik' => $nik,
                        'tgl_presensi' => $tgl_presensi,
                        'jam_in' => $jam,
                        'foto_in' => $newFormatName,
                        'lokasi_in' => $lokasi,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    DB::table('presensi')->insert($data);
                    
                    DB::commit();
                    Log::info("Presensi masuk berhasil untuk NIK: $nik");
                    return "success|Terima kasih, Anda berhasil melakukan presensi masuk|in";
                }
            } catch (Exception $e) {
                DB::rollBack();
                unlink($newFileName);
                Log::error("Error saat menyimpan presensi: " . $e->getMessage());
                return "error|Terjadi kesalahan saat menyimpan presensi. Silakan coba lagi|";
            }
        } catch (Exception $e) {
            Log::error("Error dalam proses presensi: " . $e->getMessage());
            return "error|Terjadi kesalahan sistem. Silakan coba lagi|";
        }
    }
    // menghitung jarak radius
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile(){
        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();

        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request){
        $nik = Auth::guard('karyawan')->user()->nik;
        $nama = $request->nama;
        $nmr_hp = $request->nmr_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();

        if($request->hasFile('foto')) {
            $foto = $nik. "." .$request->file('foto')->getClientOriginalExtension();
        }else {
            $foto = $karyawan->foto;
        }

        if (empty($password)) {
            $data = [
                'nama' => $nama,
                'nmr_hp' => $nmr_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama' => $nama,
                'nmr_hp' => $nmr_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('karyawan')->where('nik', $nik)->update($data);
        if($update) {
            if($request->hasFile('foto')) {
                $folderPath = 'public/uploads/karyawan/';
                $request->file('foto')->storeAs($folderPath, $foto);
            }
            return Redirect::back()->with(['succsess' => 'Data Berhasil Di Update']);
        }else {
            return Redirect::back()->with(['Error' => 'Data Gagal Di Update']);
        }
    }

    public function history() {
        $namabulan = ['','Januari', 'Febuari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 
                    'September', 'Oktober', 'November', 'Desember'];


        return view('presensi.history',compact('namabulan'));
    }

    public function gethistory(Request $request) {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('karyawan')->user()->nik;
        
        $history = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi)= "' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)= "' . $tahun . '"')
            ->where('nik', $nik)
            ->orderBy('tgl_presensi')
            ->get();
        
            return view('presensi.gethistory', compact('history'));
    }

    public function izin() {
        $nik = Auth::guard('karyawan')->user()->nik;
        $data_izin = DB::table('izin')->where('nik', $nik)->get();
        $keterangan = '';

        return view('presensi.izin', compact('data_izin', 'keterangan'));
    }

    public function utkizin() {
        return view('presensi.utkizin');
    }

    public function storizin(Request $request) {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('izin')->insert($data);

        if($simpan){
            return redirect('/presensi/izin')->with(['succsess' => 'Data Berhasil Disimpan']);
        }else {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
        }
    }

    public function monitoring(){
        return view ('presensi.monitoring');
    }

    public function getpresensi(Request $request){
        $tanggal = $request->tanggal;
        $presensi = DB::table('presensi')
        ->select('presensi.*', 'nama')
        ->join('karyawan','presensi.nik','=','karyawan.nik')
        ->where('tgl_presensi',$tanggal)
        ->get();

    return view('presensi.getpresensi',compact('presensi'));
    }

    public function laporan(){
        $namabulan = ['','Januari', 'Febuari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 
                    'September', 'Oktober', 'November', 'Desember'];
        $karyawan = DB::table('karyawan')->orderBy('nama')->get();

        return view('presensi.laporan',compact('namabulan','karyawan'));
    }

    public function cetaklaporan(Request $request) {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
    
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }
    
        $presensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)= "' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)= "' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();
    
        if ($presensi->isEmpty()) {
            return redirect()->back()->with('error', 'Data presensi tidak ditemukan untuk periode yang dipilih.');
        }

        return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    }

    public function izinsakit() {
        $izinsakit = DB::table('izin')
            ->select([
                'izin.id',
                'izin.nik',
                'izin.tgl_izin',
                'izin.status',
                'izin.keterangan',
                'izin.status_acc',
                'karyawan.nama',
                'karyawan.jabatan'
            ])
            ->join('karyawan','izin.nik','=','karyawan.nik')
            ->orderBy('tgl_izin','desc')
            ->get();

        return view('presensi.izinsakit', compact('izinsakit'));
    }
    public function accizinsakit(Request $request) {
        $status_acc = $request -> status_acc; 
        $idizinform = $request -> idizinform;
        $update = DB::table('izin')->where('id', $idizinform)->update([
            'status_acc' => $status_acc
        ]);
        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil Diupdate']);
        }else{
            return Redirect::back()->with(['warning'=>'Data Berhasil Gagal']);
        }
    }

    public function cancelapprove($id){
        $update = DB::table('izin')->where('id', $id)->update([
            'status_acc' => 0
        ]);
        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil Diupdate']);
        }else{
            return Redirect::back()->with(['warning'=>'Data Berhasil Gagal']);
        }
    }
    
    public function detectFace(Request $request) 
    {
        try {
            $nik = Auth::guard('karyawan')->user()->nik;
            
            // Decode dan simpan gambar
            $image_parts = explode(";base64,", $request->image);
            $image_base64 = base64_decode($image_parts[1]);
            
            // Buat nama file yang unik
            $formatName = $nik . "-" . date("Y-m-d") . "-temp.jpg";
            $folderPath = storage_path('app/public/uploads/absensi/');
            $fileName = $folderPath . $formatName;
            
            // Pastikan direktori ada
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }
            
            // Simpan gambar
            file_put_contents($fileName, $image_base64);
            Log::info("Gambar disimpan: " . $fileName);
            
            // Validasi dataset wajah
            $userFacesDir = storage_path('app/public/uploads/karyawan/' . $nik);
            if (!file_exists($userFacesDir)) {
                Log::error("Direktori dataset wajah tidak ditemukan: " . $userFacesDir);
                return "error|Dataset wajah tidak ditemukan. Hubungi admin|";
            }
            
            // Set confidence threshold
            $confidence_threshold = 35;
            
            // Path ke script Python
            $pythonScript = base_path('resources/python/face_recognition.py');
            
            // Jalankan script Python
            $command = sprintf(
                'python "%s" "%s" "%s" %d',
                $pythonScript,
                $fileName,
                $userFacesDir,
                $confidence_threshold
            );
            
            Log::info("Menjalankan command: " . $command);
            $output = shell_exec($command . " 2>&1"); // Tangkap stderr juga
            
            // Bersihkan file temporary
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            
            // Parse output JSON
            if (empty($output)) {
                Log::error("Script Python tidak menghasilkan output");
                return "error|Sistem pengenalan wajah tidak merespons|";
            }
            
            // Coba parse JSON
            $result = json_decode(trim($output), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("Error parsing JSON: " . $output);
                return "error|Format output tidak valid|";
            }
            
            // Cek hasil pengenalan
            if (isset($result['error'])) {
                Log::error("Python script error: " . $result['error']);
                return "error|" . $result['error'] . "|";
            }
            
            if (!isset($result['match'])) {
                Log::error("Invalid result format: " . json_encode($result));
                return "error|Format hasil tidak valid|";
            }
            
            return $result['match'] ? "success|Wajah terdeteksi|" : "error|Wajah tidak cocok|";
            
        } catch (Exception $e) {
            Log::error("Error in detectFace: " . $e->getMessage());
            return "error|Terjadi kesalahan sistem|";
        }
    }
}
