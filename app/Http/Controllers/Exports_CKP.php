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
        $mainActivities = DB::table('daily_activity')
            ->whereYear('tgl', '=', $tahun) // Filter by year
            ->whereMonth('tgl', '=', $bulan) // Filter by month
            ->where('daily_activity.nip', auth()->user()->nip) // Filter by the authenticated user's nip
            ->where('daily_activity.jenis_kegiatan', 'UTAMA') // Filter by kind of Activities
            ->where('daily_activity.wfo_wfh', '!=', 'Lainnya') // Filter by WFH or WFO
            ->join('users', 'daily_activity.nip', '=', 'users.nip') // Join with the users table
            ->select('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname', DB::raw('SUM(daily_activity.kuantitas) as total_kuantitas'), DB::raw('MIN(daily_activity.id) as min_id')) // Select the required fields
            ->groupBy('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname') // Group by kegiatan, satuan, and fullname
            ->orderBy('min_id', 'asc') // Order by the minimum id within each group
            ->get();

        $addActivities = DB::table('daily_activity')
            ->whereYear('tgl', '=', $tahun) // Filter by year
            ->whereMonth('tgl', '=', $bulan) // Filter by month
            ->where('daily_activity.nip', auth()->user()->nip) // Filter by the authenticated user's nip
            ->where('daily_activity.jenis_kegiatan', 'TAMBAHAN') // Filter by kind of Activities
            ->where('daily_activity.wfo_wfh', '!=', 'Lainnya') // Filter by WFH or WFO
            ->join('users', 'daily_activity.nip', '=', 'users.nip') // Join with the users table
            ->select('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname', DB::raw('SUM(daily_activity.kuantitas) as total_kuantitas'), DB::raw('MIN(daily_activity.id) as min_id')) // Select the required fields
            ->groupBy('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname') // Group by kegiatan, satuan, and fullname
            ->orderBy('min_id', 'asc') // Order by the minimum id within each group
            ->get();

        // Load the template Excel file
        $templatePath = public_path('template.xlsx');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheetName = 'CKP_R';  // Replace 'SheetName' with the actual name of the sheet
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();


        #Ubah Header CKP-R Menjadi Dinamis
        $sheet->setCellValue('A2', 'CAPAIAN KINERJA PEGAWAI TAHUN ' . $tahun);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setName('Quattrocento Sans')->setBold(true)->setSize(14);

        // Populate data starting from a specific row (e.g., row 2)
        $dummydate = $tahun . '-' . $bulan . '-1'; // Example date
        $row = 13;

        $sheet->setCellValue('C' . 5, ":  ". auth()->user()->fullname);
        $sheet->setCellValue('C' . 6, ":  ". auth()->user()->jabatan);
        $sheet->setCellValue('C' . 7, ":  1-". carbon::parse($dummydate)->endOfMonth()->translatedFormat('j F Y'));
        $sheet->setCellValue('C' . 25, auth()->user()->fullname);
        $sheet->setCellValue('C' . 26, "NIP. ". auth()->user()->nip);
        $sheet->setCellValue('B' . 21, "Tanggal : ". Carbon::now()->translatedFormat('j F Y'));

        foreach ($mainActivities as $activity) {
            $sheet->setCellValue('A' . $row, $row-12);
            $sheet->setCellValue('B' . $row, $activity->kegiatan);
            $sheet->setCellValue('D' . $row, $activity->satuan);
            $sheet->setCellValue('E' . $row, $activity->total_kuantitas);
            $sheet->setCellValue('F' . $row, $activity->total_kuantitas);
            $sheet->setCellValue('G' . $row, "100");
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H' . $row, "100");
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // Add more data fields as needed
            $row++;
            $sheet->insertNewRowBefore($row);
            $sheet->mergeCells('B' . $row . ':C' . $row);
        }

        $sheet->removeRow($row);

        $row = $row+1;
        $no_dummy = $row-1;

        foreach ($addActivities as $activity) {
            $sheet->setCellValue('A' . $row, $row-$no_dummy);
            $sheet->setCellValue('B' . $row, $activity->kegiatan);
            $sheet->setCellValue('D' . $row, $activity->satuan);
            $sheet->setCellValue('E' . $row, $activity->total_kuantitas);
            $sheet->setCellValue('F' . $row, $activity->total_kuantitas);
            $sheet->setCellValue('G' . $row, "100");
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H' . $row, "100");
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // Add more data fields as needed
            $row++;
            $sheet->insertNewRowBefore($row);
            $sheet->mergeCells('B' . $row . ':C' . $row);
        }

        $sheet->removeRow($row);

        
        $sheetName = 'CKP_T';  // Replace 'SheetName' with the actual name of the sheet
        $spreadsheet->setActiveSheetIndexByName($sheetName);
        $sheet = $spreadsheet->getActiveSheet();

        $nextMonth = Carbon::create($tahun, $bulan)->addMonth();
        $tahunNext = $nextMonth->year;
        $bulanNext = $nextMonth->month;

        $mainActivitiesNext = DB::table('daily_activity')
            ->whereYear('tgl', '=', $tahun)
            ->whereMonth('tgl', '=', $bulanNext)
            ->where('daily_activity.nip', auth()->user()->nip)
            ->where('daily_activity.jenis_kegiatan', 'UTAMA')
            ->where('daily_activity.wfo_wfh', '!=', 'Lainnya')
            ->join('users', 'daily_activity.nip', '=', 'users.nip')
            ->select('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname', DB::raw('SUM(daily_activity.kuantitas) as total_kuantitas'), DB::raw('MIN(daily_activity.id) as min_id'))
            ->groupBy('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname')
            ->orderBy('min_id', 'asc')
            ->get();

        $addActivitiesNext = DB::table('daily_activity')
            ->whereYear('tgl', '=', $tahun)
            ->whereMonth('tgl', '=', $bulanNext)
            ->where('daily_activity.nip', auth()->user()->nip)
            ->where('daily_activity.jenis_kegiatan', 'TAMBAHAN')
            ->where('daily_activity.wfo_wfh', '!=', 'Lainnya')
            ->join('users', 'daily_activity.nip', '=', 'users.nip')
            ->select('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname', DB::raw('SUM(daily_activity.kuantitas) as total_kuantitas'), DB::raw('MIN(daily_activity.id) as min_id'))
            ->groupBy('daily_activity.kegiatan', 'daily_activity.satuan', 'users.fullname')
            ->orderBy('min_id', 'asc')
            ->get();


        #Ubah Header CKP-T Menjadi Dinamis
        $sheet->setCellValue('A2', 'TARGET KINERJA PEGAWAI TAHUN ' . $tahun);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setName('Quattrocento Sans')->setBold(true)->setSize(14);

        // Populate data starting from a specific row 
        $row = 13;

        // Create a DateTime object from the dummy date
        $newDate = new \DateTime($dummydate);

        // Modify the date to move to the next month
        $newDate->modify('+1 month');
        
        $sheet->setCellValue('C' . 5, ":  ". auth()->user()->fullname);
        $sheet->setCellValue('C' . 6, ":  ". auth()->user()->jabatan);
        $sheet->setCellValue('C' . 7, ":  1-". carbon::parse($newDate)->endOfMonth()->translatedFormat('j F Y'));
        $sheet->setCellValue('C' . 23, auth()->user()->fullname);
        $sheet->setCellValue('C' . 24, "NIP. ". auth()->user()->nip);
        $sheet->setCellValue('B' . 19, "Tanggal : ". Carbon::now()->translatedFormat('j F Y'));
        foreach ($mainActivitiesNext as $activity) {
            $sheet->setCellValue('A' . $row, $row-12);
            $sheet->setCellValue('B' . $row, $activity->kegiatan);
            $sheet->setCellValue('D' . $row, $activity->satuan);
            $sheet->setCellValue('E' . $row, $activity->total_kuantitas);
            $row++;
            $sheet->insertNewRowBefore($row);
            $sheet->mergeCells('B' . $row . ':C' . $row);
        }
        $sheet->removeRow($row);

        $row = $row+1;
        $no_dummy = $row-1;

        foreach ($addActivitiesNext as $activity) {
            $sheet->setCellValue('A' . $row, $row-$no_dummy);
            $sheet->setCellValue('B' . $row, $activity->kegiatan);
            $sheet->setCellValue('D' . $row, $activity->satuan);
            $sheet->setCellValue('E' . $row, $activity->total_kuantitas);
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
