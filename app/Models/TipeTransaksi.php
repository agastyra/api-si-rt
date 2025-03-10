<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\TransaksiDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TipeTransaksi extends Model
{
    use SoftDeletes;

    protected $guarded = ["id", "created_at", "updated_at"];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "deletion_token"
    ];

    public function delete(): void
    {
        $this->update(['deletion_token' => Str::uuid()]);
        parent::delete();
    }

    public function scopeFilters($query, array $filters): void
    {
        $query->when($filters["jenis"] ?? false, function($query) use($filters) {
            return $query->where("jenis", $filters["jenis"]);
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

    public function transaksi_detail(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransaksiDetail::class, "tipe_transaksi_id");
    }
}
