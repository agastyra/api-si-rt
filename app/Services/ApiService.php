<?php

namespace App\Services;

use App\Enum\StatusRumah;
use App\Models\Rumah;
use Illuminate\Support\Facades\DB;

class ApiService
{

    public static function checkStatusForRumah(Rumah $rumah)
    {
        if ($rumah->status_rumah == StatusRumah::TIDAK_DIHUNI->value) {
            throw new \Exception("House is not inhabited", 400);
        }
    }

    public static function generateTransactionReport(int $periode_bulan = null, int $periode_tahun = null, string
    $blok = null)
    {
        if ($periode_bulan < 1 || $periode_bulan > 12) throw new \Exception("Invalid month", 400);
        if ($periode_tahun < 2023) throw new \Exception("Invalid year", 400);

        $sql = "
                SELECT ".
                ($blok != null ? "rumahs.blok, rumahs.status_rumah, " : "")
                    ."tipe_transaksis.nama, tipe_transaksis.jenis, transaksi_details.periode_bulan, transaksi_details.periode_tahun, transaksi_details.nominal".
                ($blok != null ? " FROM rumahs LEFT JOIN transaksis ON rumahs.id = transaksis.rumah_id " : " FROM transaksis ")
                ."INNER JOIN transaksi_details ON transaksis.id = transaksi_details.transaksi_id
                RIGHT JOIN tipe_transaksis ON tipe_transaksis.id = transaksi_details.tipe_transaksi_id
                WHERE ".
                ($blok != null ? "rumahs.blok = :blok " : "")
                . ($periode_bulan != null ? "AND transaksi_details.periode_bulan = :periode_bulan" : "")
                . ($periode_tahun != null ? "AND transaksi_details.periode_tahun = :periode_tahun" : "").
                ($blok != null ? " AND rumahs.deletion_token = 'NA' " : "")
                ."
                  AND transaksis.deletion_token = 'NA'
                  AND tipe_transaksis.deletion_token = 'NA'
                  AND transaksi_details.deletion_token = 'NA'
            ";

        $params = [];

        if ($blok !== null) {
            $params['blok'] = $blok;
        }

        if ($periode_bulan !== null) {
            $params["periode_bulan"] = $periode_bulan;
        }
        if ($periode_tahun !== null) {
            $params["periode_tahun"] = $periode_tahun;
        }

        return DB::select($sql, $params);
    }

}
