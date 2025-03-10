<?php

namespace App\Services;

use App\Enum\StatusRumah;
use App\Models\Rumah;
use App\Models\Transaksi;
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
        ?string $jenis_transaksi = null,
        int $perPage = 10, int $page = 1
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

        $sql = "SELECT $select FROM $from WHERE " . implode(' AND ', $conditions) . " ORDER BY transaksis.tanggal_transaksi DESC";

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

        $results = DB::select($sql, $params);

        return self::paginate($results, $page, $perPage);
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

    public static function getLatestTransactionNumber(): string
    {
        $latestTransaction = Transaksi::orderBy('no_transaksi', 'desc')->first();
        $initialValue = 'TRX-01';

        if (!$latestTransaction) {
            return $initialValue;
        }

        $latestNumber = (int) str_replace('TRX-', '', $latestTransaction->no_transaksi);
        $newNumber = $latestNumber + 1;

        return 'TRX-' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    }

    public static function generateMonthlyBilling(int $periode_bulan, int $periode_tahun, int $perPage = 10, int $page = 1): array {
        $sql = "
            WITH MonthlyRecord AS (
                SELECT rumahs.id AS 'rumah_id', rumahs.blok, rumahs.status_rumah, transaksi_details.periode_bulan, transaksi_details.periode_tahun,
                    tipe_transaksis.nama, tipe_transaksis.jenis
                FROM rumahs

                LEFT JOIN transaksis
                    ON rumahs.id = transaksis.rumah_id
                    AND transaksis.deletion_token = 'NA'

                LEFT JOIN transaksi_details
                    ON transaksis.id = transaksi_details.transaksi_id
                    AND transaksi_details.deletion_token = 'NA'

                LEFT JOIN tipe_transaksis
                    ON tipe_transaksis.id = transaksi_details.tipe_transaksi_id
                    AND tipe_transaksis.deletion_token = 'NA'

                WHERE
                    rumahs.deletion_token = 'NA'

                ORDER BY
                    rumahs.blok ASC,
                    transaksi_details.periode_bulan ASC,
                    transaksi_details.periode_tahun ASC
            )

            SELECT rumahs.id, rumahs.blok, rumahs.status_rumah, COALESCE(mr.periode_bulan, ?) AS 'periode_bulan', COALESCE(mr.periode_tahun, ?) AS 'periode_tahun',
                CASE COUNT(mr.periode_bulan)
                    WHEN 0 THEN 'Belum Bayar'
                    WHEN 1 THEN 'Belum Lunas'
                    WHEN 2 THEN 'Lunas'
                END AS 'status_pembayaran'

            FROM MonthlyRecord mr
            RIGHT JOIN rumahs
            ON rumahs.id = mr.rumah_id
            AND mr.periode_bulan = ?
            AND mr.periode_tahun = ?

            WHERE rumahs.deletion_token = 'NA'
            GROUP BY mr.periode_bulan, mr.periode_tahun, rumahs.blok, rumahs.id
        ";


        $results = DB::select($sql, [$periode_bulan, $periode_tahun, $periode_bulan, $periode_tahun]);

        return self::paginate($results, $page, $perPage);
    }

    public static function paginate(array $results, int $page, int $perPage): array
    {
        $collection = collect($results);

        $total = $collection->count();
        $currentPage = $page ?: 1;
        $offset = ($currentPage - 1) * $perPage;

        $items = $collection->slice($offset, $perPage)->values();

        return [
            'results' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => ceil($total / $perPage)
        ];
    }
}
