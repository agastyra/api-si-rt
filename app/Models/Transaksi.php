<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Rumah;
use App\Models\TransaksiDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaksi extends Model
{
    use SoftDeletes;
    protected $guarded = ["id", "created_at", "updated_at"];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "deletion_token"
    ];

    protected $with = ["transaksi_detail"];
    protected $cascadeDeletes = ["transaksi_detail"];

    public function delete(): void
    {
        $this->update(['deletion_token' => Str::uuid()]);
        parent::delete();
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function($transaksi) {
            $transaksi->transaksi_detail()->update(['deletion_token' => Str::uuid()]);
            $transaksi->transaksi_detail()->delete();
        });
        static::restoring(function($transaksi) {
            $transaksi->transaksi_detail()->update(['deletion_token' => "NA"]);
            $transaksi->transaksi_detail()->restore();
        });
    }

    public function scopeFilters($query, array $filters): void
    {
        $query->when($filters["jenis"] ?? false, function($query) use($filters) {
            if ($filters["jenis"] == "Pemasukan") {
                return $query->where("rumah_id", "!=", null);
            }
            return $query->where("rumah_id", null);
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
        return $this->hasMany(TransaksiDetail::class, "transaksi_id");
    }

    public function rumah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Rumah::class, "rumah_id");
    }
}
