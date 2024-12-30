<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class karyawan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'karyawan';
    protected $primaryKey = 'nik';

  /**
     * Set the user's password.
     *
     * @param  string  $password
     * @return void
     */
   public function setPasswordAttribute($password)
   {
       $this->attributes['password'] = Hash::make($password);
   }
    protected $fillable = [
        'nik',
        'nama',
        'jabatan',
        'nmr_hp',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
