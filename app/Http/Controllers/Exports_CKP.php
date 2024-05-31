<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use DB;

class Exports_CKP extends Controller
{
    public function exportToExcel()
    {
        // Download the file
        $fileName = $tahun . $bulan . '_CKP_' . auth()->user()->nip . '.xlsx';

        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}
