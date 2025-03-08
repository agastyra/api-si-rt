<?php

namespace App\Services;

use App\Enum\StatusRumah;
use App\Models\Rumah;
use Illuminate\Support\Facades\DB;

class ApiService
{

    public static function checkStatusForRumah(Rumah $rumah): void
    {
        if ($rumah->status_rumah == StatusRumah::TIDAK_DIHUNI->value) {
            throw new \Exception("House is not inhabited", 400);
        }
    }

    public static function generateTransactionReport(
        ?int $periode_bulan = null,
        ?int $periode_tahun = null,
        ?string $blok = null,
        ?int $tipe_transaksi_id = null,
        ?string $jenis_transaksi = null
    ): array
    {
        $select = ($blok !== null ? "rumahs.blok, rumahs.status_rumah, " : "")
            . "transaksis.tanggal_transaksi, tipe_transaksis.nama, tipe_transaksis.jenis, transaksi_details.periode_bulan, transaksi_details.periode_tahun, transaksi_details.nominal";

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
        if ($tipe_transaksi_id !== null) {
            $conditions[] = "tipe_transaksis.id = :tipe_transaksi_id";
        }
        if ($jenis_transaksi !== null) {
            $conditions[] = "tipe_transaksis.jenis = :jenis_pengeluaran";
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
        if ($tipe_transaksi_id !== null) {
            $params['tipe_transaksi_id'] = $tipe_transaksi_id;
        }
        if ($jenis_transaksi !== null) {
            $params['jenis_pengeluaran'] = $jenis_transaksi;
        }

        return DB::select($sql, $params);
    }

    public static function generateBalanceSummary(int $periode_tahun): array
    {
        $sql = "
            WITH MonthlySummary AS (
                SELECT
                    transaksi_details.periode_bulan,
                    transaksi_details.periode_tahun,
                    SUM(CASE WHEN tipe_transaksis.jenis = 'Pemasukan' THEN transaksi_details.nominal ELSE 0 END) AS total_pemasukan,
                    SUM(CASE WHEN tipe_transaksis.jenis = 'Pengeluaran' THEN transaksi_details.nominal ELSE 0 END) AS total_pengeluaran,
                    (
                        SUM(CASE WHEN tipe_transaksis.jenis = 'Pemasukan' THEN transaksi_details.nominal ELSE 0 END) -
                        SUM(CASE WHEN tipe_transaksis.jenis = 'Pengeluaran' THEN transaksi_details.nominal ELSE 0 END)
                    ) AS saldo_sisa
                FROM transaksis
                INNER JOIN transaksi_details ON transaksis.id = transaksi_details.transaksi_id
                INNER JOIN tipe_transaksis ON tipe_transaksis.id = transaksi_details.tipe_transaksi_id
                WHERE
                    transaksi_details.periode_tahun = :periode_tahun AND
                    transaksis.deletion_token = 'NA' AND
                    tipe_transaksis.deletion_token = 'NA' AND
                    transaksi_details.deletion_token = 'NA'
                GROUP BY
                    transaksi_details.periode_bulan,
                    transaksi_details.periode_tahun
            )

            SELECT
                current_month.periode_bulan,
                current_month.periode_tahun,
                COALESCE(
                    SUM(previous_month.saldo_sisa),
                    0
                ) AS saldo_awal,
                current_month.total_pemasukan,
                current_month.total_pengeluaran,
                current_month.saldo_sisa,
                COALESCE(
                    SUM(previous_month.saldo_sisa),
                    0
                ) + current_month.saldo_sisa AS saldo_akhir
            FROM MonthlySummary current_month
            LEFT JOIN MonthlySummary previous_month
                ON current_month.periode_tahun = previous_month.periode_tahun
                AND current_month.periode_bulan > previous_month.periode_bulan
            GROUP BY
                current_month.periode_bulan,
                current_month.periode_tahun,
                current_month.total_pemasukan,
                current_month.total_pengeluaran,
                current_month.saldo_sisa
            ORDER BY
                current_month.periode_bulan;
        ";

        return DB::select($sql, [
            'periode_tahun' => $periode_tahun
        ]);
    }

}
