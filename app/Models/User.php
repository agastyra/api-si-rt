<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Penghuni;
use App\Models\Rumah;
use App\Models\PenghuniRumah;
use App\Models\TipeTransaksi;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
        "created_at",
        "updated_at"
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function penghuni(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penghuni::class);
    }

    public function rumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rumah::class);
    }

    public function penghuniRumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenghuniRumah::class);
    }

    public function tipeTransaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TipeTransaksi::class);
    }

    public function transaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaksi::class);
    }

    public function transaksiDetail(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
