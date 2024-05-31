<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Exports_CKP extends Controller
{
    public function exportToExcel($tahun, $bulan)
    {
        $activities = DB::table('daily_activity')->whereYear('tgl', '=', date($tahun))->whereMonth('tgl', '=', date($bulan))->where('daily_activity.nip', auth()->user()->nip)->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();

        // Load the template Excel file
        $templatePath = public_path('template.xlsx');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Populate data starting from a specific row (e.g., row 2)
        $dummydate = $tahun . '-' . $bulan . '-1'; // Example date
        $row = 13;
        $sheet->setCellValue('C' . 5, ":  ". auth()->user()->fullname);
        $sheet->setCellValue('C' . 6, ":  ". auth()->user()->jabatan);
        $sheet->setCellValue('C' . 7, ":  1-". carbon::parse($dummydate)->endOfMonth()->translatedFormat('j F Y'));
        $sheet->setCellValue('C' . 25, auth()->user()->fullname);
        $sheet->setCellValue('C' . 26, "NIP. ". auth()->user()->nip);
        $sheet->setCellValue('B' . 21, "Tanggal : ". Carbon::now()->translatedFormat('j F Y'));
        // $sheet->setCellValue('C' . 6, ":  ". auth()->user()->);

        foreach ($activities as $activity) {
            $sheet->setCellValue('A' . $row, $row-12);
            $sheet->setCellValue('B' . $row, $activity->kegiatan);
            $sheet->setCellValue('D' . $row, $activity->satuan);
            $sheet->setCellValue('E' . $row, $activity->kuantitas);
            $sheet->setCellValue('F' . $row, $activity->kuantitas);
            $sheet->setCellValue('G' . $row, "100");
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H' . $row, "100");
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // Add more data fields as needed
            $row++;
            $sheet->insertNewRowBefore($row);
            $sheet->mergeCells('B' . $row . ':C' . $row);
        }

        $sheet->removeRow($row);

        // Save the new Excel file
        $fileName = $tahun . sprintf('%02d', $bulan) . '_CKP_' . auth()->user()->nip . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/' . $fileName));

        // Download the file
        $fileName = $tahun . sprintf('%02d', $bulan) . '_CKP_' . auth()->user()->nip . '.xlsx';
        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}
