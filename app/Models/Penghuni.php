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

    protected $with = ["penghuniRumah"];

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

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updatedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function penghuniRumah(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PenghuniRumah::class, "penghuni_id");
    }
}
