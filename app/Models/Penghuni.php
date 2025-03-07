<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\PenghuniRumah;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Penghuni extends Model
{
    use SoftDeletes;

    protected $guarded = ["id", "created_at", "updated_at"];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "deletion_token"
    ];

    protected $cascadeDeletes = ["penghuni_rumah"];

    public function fotoKtp(): Attribute
    {
        return Attribute::make(
            get: fn($value) => asset("storage/$value")
        );
    }

    public function delete(): void
    {
        $this->update(['deletion_token' => Str::uuid()]);
        parent::delete();
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function($penghuni) {
            $penghuni->penghuni_rumah()->update(['deletion_token' => Str::uuid()]);
            $penghuni->penghuni_rumah()->delete();
        });
        static::restoring(function($penghuni) {
            $penghuni->penghuni_rumah()->update(['deletion_token' => "NA"]);
            $penghuni->penghuni_rumah()->restore();
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
        return $this->hasMany(PenghuniRumah::class, "penghuni_id");
    }
}
