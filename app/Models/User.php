<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
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
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

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
        "updated_at",
        "deleted_at",
        "deletion_token"
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

    public function delete(): void
    {
        $this->update(['deletion_token' => Str::uuid()]);
        parent::delete();
    }

    public function createdByPenghuni(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penghuni::class, "created_by");
    }

    public function createdByRumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rumah::class, "created_by");
    }

    public function createdByPenghuniRumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenghuniRumah::class, "created_by");
    }

    public function createdByTipeTransaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TipeTransaksi::class, "created_by");
    }

    public function createdByTransaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaksi::class, "created_by");
    }

    public function createdByTransaksiDetail(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransaksiDetail::class, "created_by");
    }
    public function updatedByPenghuni(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Penghuni::class, "updated_by");
    }

    public function updatedByRumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rumah::class, "updated_by");
    }

    public function updatedByPenghuniRumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenghuniRumah::class, "updated_by");
    }

    public function updatedByTipeTransaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TipeTransaksi::class, "updated_by");
    }

    public function updatedByTransaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaksi::class, "updated_by");
    }

    public function updatedByTransaksiDetail(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransaksiDetail::class, "updated_by");
    }
}
