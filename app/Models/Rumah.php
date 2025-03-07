<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PenghuniRumah;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Rumah extends Model
{
    use SoftDeletes;

    protected $guarded = ["id", "created_at", "updated_at"];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "deletion_token"
    ];

    protected $with = ["penghuni_rumah"];
    protected $cascadeDeletes = ["penghuni_rumah"];

    public function delete(): void
    {
        $this->update(['deletion_token' => Str::uuid()]);
        parent::delete();
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function($rumah) {
            $rumah->penghuni_rumah()->update(['deletion_token' => Str::uuid()]);
            $rumah->penghuni_rumah()->delete();
        });
        static::restoring(function($rumah) {
            $rumah->penghuni_rumah()->update(['deletion_token' => "NA"]);
            $rumah->penghuni_rumah()->restore();
        });
    }

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function penghuni_rumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenghuniRumah::class, "rumah_id");
    }

    public function transaksi(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaksi::class, "rumah_id");
    }
}
