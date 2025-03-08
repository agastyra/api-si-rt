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

        $select = ($blok !== null ? "rumahs.blok, rumahs.status_rumah, " : "")
            . "tipe_transaksis.nama, tipe_transaksis.jenis, transaksi_details.periode_bulan, transaksi_details.periode_tahun, transaksi_details.nominal";

        $from = ($blok !== null ? "rumahs LEFT JOIN transaksis ON rumahs.id = transaksis.rumah_id " : "transaksis ")
            . "INNER JOIN transaksi_details ON transaksis.id = transaksi_details.transaksi_id
           RIGHT JOIN tipe_transaksis ON tipe_transaksis.id = transaksi_details.tipe_transaksi_id";

        $conditions = [];
        if ($blok !== null) {
            $conditions[] = "rumahs.blok = :blok";
        }
        if ($periode_bulan !== null) {
            $conditions[] = "transaksi_details.periode_bulan = :periode_bulan";
        }
        if ($periode_tahun !== null) {
            $conditions[] = "transaksi_details.periode_tahun = :periode_tahun";
        }
        if ($blok !== null) {
            $conditions[] = "rumahs.deletion_token = 'NA'";
        }
        $conditions[] = "transaksis.deletion_token = 'NA'";
        $conditions[] = "tipe_transaksis.deletion_token = 'NA'";
        $conditions[] = "transaksi_details.deletion_token = 'NA'";

        $sql = "SELECT $select FROM $from WHERE " . implode(' AND ', $conditions);

        $params = [];
        if ($blok !== null) {
            $params['blok'] = $blok;
        }
        if ($periode_bulan !== null) {
            $params['periode_bulan'] = $periode_bulan;
        }
        if ($periode_tahun !== null) {
            $params['periode_tahun'] = $periode_tahun;
        }

        return DB::select($sql, $params);
    }

}
