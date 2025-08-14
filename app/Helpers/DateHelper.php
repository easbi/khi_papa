<?php

namespace App\Helpers;

use DateTime;
use Carbon\Carbon;

class DateHelper
{
    // Ambil data libur dari API
    protected static function getApiHolidays($bulan, $tahun)
    {
        $formattedMonth = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $apiUrl = "https://dayoffapi.vercel.app/api?month={$formattedMonth}&year={$tahun}";

        $response = @file_get_contents($apiUrl);
        if (!$response) return [];

        $data = json_decode($response, true);
        return is_array($data) ? $data : [];
    }

    // Mengambil semua tanggal libur (Senin - Jumat) dalam 1 bulan penuh
    public static function getWeekdayHolidaysFullMonth($bulan, $tahun)
    {
        return array_map(
            fn($holiday) => (new DateTime($holiday['tanggal']))->format('Y-m-d'),
            array_filter(self::getApiHolidays($bulan, $tahun), function ($holiday) use ($bulan, $tahun) {
                if (!isset($holiday['tanggal'])) return false;
                $date = new DateTime($holiday['tanggal']);
                return $date->format('N') <= 5 && $date->format('Y-m') === sprintf('%04d-%02d', $tahun, $bulan);
            })
        );
    }

    // Menghitung jumlah hari libur Senin-Jumat dalam 1 bulan
    public static function countWeekdayHolidaysFullMonth($bulan, $tahun)
    {
        return count(self::getWeekdayHolidaysFullMonth($bulan, $tahun));
    }

    // Mengambil tanggal libur (Senin - Jumat) dari awal bulan hingga hari ini
    public static function getWeekdayHolidaysUntilToday($bulan, $tahun)
    {
        $today = Carbon::today();
        return array_map(
            fn($holiday) => (new DateTime($holiday['tanggal']))->format('Y-m-d'),
            array_filter(self::getApiHolidays($bulan, $tahun), function ($holiday) use ($today) {
                if (!isset($holiday['tanggal'])) return false;
                $date = new Carbon($holiday['tanggal']);
                return $date->isWeekday() && $date->lessThanOrEqualTo($today);
            })
        );
    }

    // Menghitung jumlah hari libur (Senin - Jumat) sampai hari ini
    public static function countWeekdayHolidaysUntilToday($bulan, $tahun)
    {
        return count(self::getWeekdayHolidaysUntilToday($bulan, $tahun));
    }

    // Mengambil semua hari kerja dari awal bulan hingga hari ini yang bukan hari libur
    public static function getWorkingDaysWithoutHolidaysUntilToday()
    {
        $today = Carbon::today();
        $bulan = $today->month;
        $tahun = $today->year;
        $holidayDates = self::getWeekdayHolidaysUntilToday($bulan, $tahun);

        $workingDays = [];
        $date = $today->copy()->startOfMonth();

        while ($date->lessThanOrEqualTo($today)) {
            if ($date->isWeekday() && !in_array($date->format('Y-m-d'), $holidayDates)) {
                $workingDays[] = $date->format('Y-m-d');
            }
            $date->addDay();
        }

        return $workingDays;
    }

    // Di DateHelper.php tambahkan method ini:
    public static function getWorkingDaysWithoutHolidaysFullMonth($bulan, $tahun)
    {
        $holidayDates = self::getWeekdayHolidaysFullMonth($bulan, $tahun);
        $workingDays = [];
        $date = Carbon::create($tahun, $bulan)->startOfMonth();
        $endOfMonth = Carbon::create($tahun, $bulan)->endOfMonth();

        while ($date->lessThanOrEqualTo($endOfMonth)) {
            if ($date->isWeekday() && !in_array($date->format('Y-m-d'), $holidayDates)) {
                $workingDays[] = $date->format('Y-m-d');
            }
            $date->addDay();
        }

        return $workingDays;
    }
}
