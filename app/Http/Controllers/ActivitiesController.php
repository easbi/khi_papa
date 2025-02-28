<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Jobs\SendWaPenugasan;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ActivitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = Carbon::now();
        $activities = DB::table('daily_activity')->join('users', 'daily_activity.nip', 'users.nip')->whereDate('daily_activity.tgl', Carbon::today())->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();
        $act_count_today = Activity::whereDate('tgl', Carbon::today())->count();

        $yesterday = date("Y-m-d", strtotime( '-1 days' ) );
        $act_count_yesterday = Activity::whereDate('tgl', $yesterday )->count();

        $userfill = Activity::whereDate('tgl', Carbon::today())->distinct('nip')->count();

        // Query to get the top 5 employees with the most daily activity submissions
        $topEmployees = DB::table('daily_activity')
            ->join('users', 'daily_activity.nip', '=', 'users.nip')
            ->select('users.fullname', 'daily_activity.nip', DB::raw('COUNT(*) as jumlah_kegiatan'))
            ->whereMonth('daily_activity.tgl', '=', date('m'))
            ->whereYear('daily_activity.tgl', '=', date('Y'))
            ->where('users.unit_kerja', '=' , 'BPS Kota Padang Panjang')
            ->groupBy('daily_activity.nip', 'users.fullname')
            ->orderByDesc('jumlah_kegiatan')
            ->limit(5)
            ->get();

        // Convert the data to a single array containing employee data
        $topEmployeesData = $topEmployees->map(function ($employee) {
            return [
                'nama' => $employee->fullname,
                'jumlah_kegiatan' => $employee->jumlah_kegiatan
            ];
        })->toArray();

        // Convert the employee data to JSON for JavaScript usage
        $topEmployeesDataJson = json_encode($topEmployeesData);

        // Query to get the 5 employees with the least daily activity submissions for the current month
        $leastEmployees = DB::table('users')
            ->leftJoin('daily_activity', function($join) {
                $join->on('users.nip', '=', 'daily_activity.nip')
                     ->whereMonth('daily_activity.tgl', '=', date('m'))
                     ->whereYear('daily_activity.tgl', '=', date('Y'));
            })
            ->whereNotIn('users.nip', ['199111052014102001', '199906092021121002', '197111211994032002', '196701201993031001', '198410302011011016' ]) // Mengecualikan pegawai tertentu
            ->where('users.unit_kerja', '=' , 'BPS Kota Padang Panjang')
            ->select('users.nip', 'users.fullname', DB::raw('COALESCE(COUNT(daily_activity.id), 0) as jumlah_pengisian'))
            ->groupBy('users.nip', 'users.fullname')
            ->orderBy('jumlah_pengisian', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($user) use ($today){
                $maxWorkDaysFiltered = Carbon::createFromDate($today->format('Y'), $today->format('m'))->startOfMonth()->diffInDaysFiltered(
                    fn($date) => $date->isWeekday(), // Hanya menghitung hari Senin - Jumat
                    Carbon::today()
                ) + 1; //adjustment to the day
                $datenow = (new \DateTime())->format('Y-m-d'); 
                // Hari Libur dalam Senin-Jumat
                $hariLibur = count(array_filter(
                    json_decode(file_get_contents("https://api-harilibur.netlify.app/api?month=" . $today->format('m') . "&year=" . $today->format('Y')), true),
                    fn($holiday) => (new \DateTime($holiday['holiday_date']))->format('N') <= 5 && $holiday['holiday_date'] <= $datenow
                ));

                // Hitung hari yang sudah diisi oleh pengguna
                $filledDays = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', date('m'))
                    ->whereYear('tgl', date('Y'))
                    ->select(DB::raw('DATE(tgl) as date'))
                    ->distinct()
                    ->get()
                    ->count();

                $maxWorkDays = $maxWorkDaysFiltered - $hariLibur;

                // Menghitung hari kerja yang tidak diisi
                $user->missed_days = $maxWorkDays-$filledDays;

                return $user;
            })
            ->sortByDesc('missed_days') 
            ->values(); 


        // dd($leastEmployees);

        // Convert the data to a single array containing employee data
        $leastEmployeesData = $leastEmployees->map(function ($employee) {
            return [
                'nama' => $employee->fullname,
                'jumlah_hari_tdk_mengisi' => $employee->missed_days
            ];
        })->toArray();

        // Convert the employee data to JSON for JavaScript usage
        $leastEmployeesDataJson = json_encode($leastEmployeesData);

        // statuspenyelesaian pekerjaan
        $record_status_penyelesaian = Activity::whereDate('tgl', Carbon::today())
                ->select('is_done', \DB::raw("COUNT('id') as count"))
                ->groupBy('is_done')
                ->get();
        $status_penyelesaian = [];
        foreach($record_status_penyelesaian as $row) {
            $status_penyelesaian['label'][] = $row->is_done;
            $status_penyelesaian['data'][] = (int) $row->count;
        }

        $status_penyelesaian = json_encode($status_penyelesaian);

        // Query Siapa yang ulang tahun sekarang ini
        $birthdayToday = User::whereRaw('SUBSTRING(nip,5,2) = ?', [$today->format('m')])
                                ->whereRaw('SUBSTRING(nip,7,2) = ?', [$today->format('d')])
                                ->pluck('fullname');


        // dd($birthdayToday);
        return view('dailyactivity.index',
            compact(
                'activities',
                'userfill',
                'act_count_today',
                'act_count_yesterday',
                'status_penyelesaian',
                'topEmployeesDataJson',
                'leastEmployeesDataJson',
                'birthdayToday'
            ))
        ->with('i');
    }

    public function allActivity()
    {
       $activities = DB::table('daily_activity')->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get(); 
       return view('dailyactivity.allactivity',compact('activities'))->with('i');
    }

    public function selftable()
    {
        $bulan = "";
        $tahun = "";

        $months = [
            ['value' => 1, 'name' => 'Januari'],
            ['value' => 2, 'name' => 'Februari'],
            ['value' => 3, 'name' => 'Maret'],
            ['value' => 4, 'name' => 'April'],
            ['value' => 5, 'name' => 'Mei'],
            ['value' => 6, 'name' => 'Juni'],
            ['value' => 7, 'name' => 'Juli'],
            ['value' => 8, 'name' => 'Agustus'],
            ['value' => 9, 'name' => 'September'],
            ['value' => 10, 'name' => 'Oktober'],
            ['value' => 11, 'name' => 'November'],
            ['value' => 12, 'name' => 'Desember']
        ];


        $years = DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        //Script Menampilkan Tanggal tidak Mengisi KHI
        $today = Carbon::today();
        $datenow = $today->format('Y-m-d');

        $allWorkingDays = [];
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy();
        while ($startOfMonth <= $endOfMonth) {
            if ($startOfMonth->isWeekday()) {
                $allWorkingDays[] = $startOfMonth->format('Y-m-d');
            }
            $startOfMonth->addDay();
        }
        $hariLibur = array_filter(
            json_decode(file_get_contents("https://api-harilibur.netlify.app/api?month=" . $today->format('m') . "&year=" . $today->format('Y')), true),
            fn($holiday) => (new \DateTime($holiday['holiday_date']))->format('N') <= 5 && $holiday['holiday_date'] <= $datenow
        );  
        $holidayDates = array_map(fn($holiday) => $holiday['tanggal'], $hariLibur);
        $workingDaysWithoutHolidays = array_diff($allWorkingDays, $holidayDates);
        $filledDays = DB::table('daily_activity')
            ->where('nip', Auth::user()->nip)
            ->whereMonth('tgl', $today->format('m'))
            ->whereYear('tgl', $today->format('Y'))
            ->select(DB::raw('DATE(tgl) as date'))
            ->pluck('date')
            ->toArray(); 
        $missedDays = array_diff($workingDaysWithoutHolidays, $filledDays);
        $missedDaysFormatted = array_map(function ($date) {
            $carbonDate = Carbon::parse($date);
            return $carbonDate->isoFormat('dddd, DD MMMM YYYY'); // Format dengan hari dalam Bahasa Indonesia
        }, $missedDays);
        // dd($missedDaysFormatted);

        $activities = DB::table('daily_activity')->where('daily_activity.nip', Auth::user()->nip)->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();
        
        return view('dailyactivity.selftable', compact('activities', 'months', 'years', 'bulan', 'tahun', 'missedDaysFormatted'))->with('i', (request()->input('page', 1) - 1) * 5 );
    }

    public function filterMonthYear(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $activities = DB::table('daily_activity')->whereYear('tgl', '=', date($tahun))->whereMonth('tgl', '=', date($bulan))->where('daily_activity.nip', Auth::user()->nip)->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();

        $months = [
            ['value' => 1, 'name' => 'Januari'],
            ['value' => 2, 'name' => 'Februari'],
            ['value' => 3, 'name' => 'Maret'],
            ['value' => 4, 'name' => 'April'],
            ['value' => 5, 'name' => 'Mei'],
            ['value' => 6, 'name' => 'Juni'],
            ['value' => 7, 'name' => 'Juli'],
            ['value' => 8, 'name' => 'Agustus'],
            ['value' => 9, 'name' => 'September'],
            ['value' => 10, 'name' => 'Oktober'],
            ['value' => 11, 'name' => 'November'],
            ['value' => 12, 'name' => 'Desember']
        ];

        $years = DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        return view('dailyactivity.selftable', compact('activities', 'months', 'years', 'bulan', 'tahun'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $assigntim=  DB::table('master_assign_anggota')
            ->where('anggota_nip','=', Auth::user()->nip)
            ->join('master_kegiatan_utama', 'master_assign_anggota.kegiatan_utama_id', '=', 'master_kegiatan_utama.id')
            ->join('master_project', 'master_project.id', '=', 'master_assign_anggota.project_id')
            ->join('master_tim_kerja', 'master_tim_kerja.id', '=', 'master_assign_anggota.tim_kerja_id')
            ->join('users as ketua_tim', 'master_tim_kerja.nip_ketua_tim', '=', 'ketua_tim.nip')
            ->select(
                'master_tim_kerja.nama_tim_kerja', 
                'ketua_tim.fullname as nama_ketua_tim',
                'master_project.nama_project', 
                'master_kegiatan_utama.nama_kegiatan_utama',
                'master_assign_anggota.*')
            ->get();

        $isKetuaTimKerja=  DB::table('master_tim_kerja')->where('master_tim_kerja.nip_ketua_tim','=', Auth::user()->nip)->select('id as tim_kerja_id', 'nama_tim_kerja')->get();

        $TimKerja = $assigntim->unique('nama_tim_kerja', 'tim_kerja_id')->pluck('tim_kerja_id', 'nama_tim_kerja', 'isKetuaTimKerja');

        return view('dailyactivity.create', compact('TimKerja'));
    }

    public function createdbyteam()
    {
        $teammember=  DB::table('master_assign_anggota')
            ->join('master_tim_kerja', 'master_tim_kerja.id', '=', 'master_assign_anggota.tim_kerja_id')
            ->where('master_tim_kerja.nip_ketua_tim', '=', Auth::user()->nip)
            ->join('users', 'users.nip', '=', 'master_assign_anggota.anggota_nip')
            ->select( 
                'users.fullname',
                'users.nip')
            ->get()            
            ->unique('nip');

        $candidate=  DB::table('users')->select('nip', 'fullname')->whereNotIn('id', [2, 10, 14])->get();

        $TimKerja=  DB::table('master_tim_kerja')->where('master_tim_kerja.nip_ketua_tim','=', Auth::user()->nip)->select('id as tim_kerja_id', 'nama_tim_kerja')->get();



        return view('dailyactivity.createdbyteam', compact('TimKerja', 'teammember'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $filename = NULL;
        if ($request->hasFile('berkas'))
        {
           $file = $request->file('berkas');
           $filename = $filename = \Carbon\Carbon::now()->format('Y-m-d H-i').'_'. Auth::user()->nip .'_'. str_replace(' ', '', substr(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 0, 25)). '.' .$file->getClientOriginalExtension();
           $file->move('bukti', $filename);
        }

        if ($request->is_done == '1') {
           $tgl_selesai = date('Y-m-d');
        } else {
           $tgl_selesai = NULL;
        }

        if ($request->is_repeated == '1') {
           $kuantitas = 1;
           $satuan = "Hari";
        } else {
           $kuantitas = $request->kuantitas;
           $satuan = $request->satuan;
        }

        // Ambil tanggal mulai dan tanggal selesai (opsional)
        $tglMulai = Carbon::createFromFormat('Y-m-d', $request->tgl);
        $tglendLoop = $request->tgl_akhir ? Carbon::createFromFormat('Y-m-d', $request->tgl_akhir) : $tglMulai; 


        $request->validate([
            'wfo_wfh' => 'required',
            'jenis_kegiatan' => 'required',
            'kegiatan'=> 'required',
            'satuan'=> 'nullable',
            'kuantitas'=> 'nullable',
            'tgl'=> 'required',
            'tgl_akhir' => 'nullable|date|after_or_equal:tgl',
        ]);

        if (Auth::user()->id == 2 || $request->jenis_kegiatan == 'TAMBAHAN'){
            $data = [
                'nip' => Auth::user()->nip,
                'wfo_wfh' => $request->wfo_wfh,
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'kegiatan' => $request->kegiatan,
                'keterangan' => $request->keterangan_kegiatan,
                'satuan' => $satuan,
                'kuantitas' => $kuantitas,
                'created_by' => Auth::user()->nip,
                'is_done' => $request->is_done,
                'tgl_selesai' => $tgl_selesai,
                'berkas' => $filename,
                'link' => $request->link,
            ];
        } elseif (Auth::user()->id != 2 && $request->jenis_kegiatan == 'UTAMA') {
            $request->validate([
                'tim_kerja_id' => 'required',
                'project_id' => 'required',
                'kegiatan_utama_id' => 'required',
            ]);
            $data = [
                'nip' => Auth::user()->nip,
                'wfo_wfh' => $request->wfo_wfh,
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'kegiatan' => $request->kegiatan,
                'keterangan' => $request->keterangan_kegiatan,
                'satuan' => $satuan,
                'kuantitas' => $kuantitas,
                'created_by' => Auth::user()->nip,
                'is_done' => $request->is_done,
                'tgl_selesai' => $tgl_selesai,
                'berkas' => $filename,
                'link' => $request->link,
                'tim_kerja_id' => $request->tim_kerja_id,
                'project_id' => $request->project_id,
                'kegiatan_utama_id' => $request->kegiatan_utama_id,
            ]; 
        } 

        $insertData = [];


        if ($tglMulai->format('Y-m-d') == $tglendLoop->format('Y-m-d')) {
            $insertData[] = array_merge($data, [
                'tgl' => $tglMulai->format('Y-m-d'),
            ]);
        } else {
            // Loop untuk setiap hari dalam rentang tanggal
            while ($tglMulai->format('Y-m-d') <= $tglendLoop->format('Y-m-d')) {
                $insertData[] = array_merge($data, [
                    'tgl' => $tglMulai->format('Y-m-d'),
                ]);
                $tglMulai->addDay(); // Tambahkan 1 hari
            }
        }        

        // Simpan semua data ke database
        $result = Activity::insert($insertData);

        return redirect()->route('act.index')
                        ->with('success','Kegiatan Sukses Ditambahkan!');
    }

    public function storebyteam(Request $request)
    {
        $request->validate([
            'anggota_nip' => 'required',
            'wfo_wfh' => 'required',
            'jenis_kegiatan' => 'required',
            'kegiatan'=> 'required',
            'satuan'=> 'required',
            'kuantitas'=> 'required',
            'tgl'=> 'required',
        ]);

        $result = Activity::create([
                'nip' => $request->anggota_nip,
                'wfo_wfh' => $request->wfo_wfh,
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'tim_kerja_id' => $request->tim_kerja_id,
                'project_id' => $request->project_id,
                'kegiatan_utama_id' => $request->kegiatan_utama_id,
                'kegiatan'=> $request->kegiatan,
                'keterangan'=> $request->keterangan_kegiatan,
                'satuan'=> $request->satuan,
                'kuantitas'=> $request->kuantitas,
                'tgl'=> $request->tgl,
                'created_by' => Auth::user()->nip,
            ]);

        // Mendapatkan ID dari aktivitas yang baru disimpan
        $activityId = $result->id;
        $taskLink = route('act.show', ['act' => $activityId]);

        $timestamp = date('d-m-y h:i:s');
        $ketua = DB::table('users')->where('nip', '=', Auth::user()->nip)->value('fullname');
        $no_hp = DB::table('users')->where('nip', '=', $request->anggota_nip)->value('no_hp');
        $message = 
"*Notifikasi Penugasan dari Ketua Tim*.
Dua Tiga Kucing Makan Sushi, Anda dapat tugas dari KHI.
Tugas ini diberikan oleh {$ketua} kepada Anda untuk segera ditindaklanjuti:

Tugas : {$request->kegiatan} 
Waktu Mulai/Selesai : {$request->tgl} 
Link Tugas (Klik Aja) : {$taskLink}
 -----------

Harap memastikan bahwa tugas tersebut diselesaikan dalam jadwal yang telah diberikan dan jangan lupa berkomunikasi dengan ketua tim anda. 
Semangat, dan mari selesaikan ini dengan baik! 💪 KHI Selalu mengingatkan bahwa tugas dengan status tidak selesai akan di-exclude dari CKP reallisasi anda.

_Pesan ini dikirimkan oleh *KHI* BPS Kota Padang Panjang Pada waktu {$timestamp} WIB_
";
        $details = [
                'message' => $message,
                'no_hp' => $no_hp,
            ];

        $delay = \DB::table('jobs')->count()*10;
        $queue = new SendWaPenugasan($details);

        // send all notification whatsapp in the queue.
        dispatch($queue->delay($delay));

         return redirect()->route('act.index')
                        ->with('success','Kegiatan Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activity=  DB::table('daily_activity')
            ->where('daily_activity.id', $id)
            ->leftJoin('master_kegiatan_utama', 'daily_activity.kegiatan_utama_id', '=', 'master_kegiatan_utama.id')
            ->leftJoin('master_project', 'master_project.id', '=', 'daily_activity.project_id')
            ->leftJoin('master_tim_kerja', 'master_tim_kerja.id', '=', 'daily_activity.tim_kerja_id')
            ->select(
                'master_tim_kerja.nama_tim_kerja', 
                'master_project.nama_project', 
                'master_kegiatan_utama.nama_kegiatan_utama',
                'daily_activity.*')
            ->first();
        return view('dailyactivity.show',compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $activity=  DB::table('daily_activity')
            ->where('daily_activity.id', $id)
            ->leftJoin('master_kegiatan_utama', 'daily_activity.kegiatan_utama_id', '=', 'master_kegiatan_utama.id')
            ->leftJoin('master_project', 'master_project.id', '=', 'daily_activity.project_id')
            ->leftJoin('master_tim_kerja', 'master_tim_kerja.id', '=', 'daily_activity.tim_kerja_id')
            ->select(
                'master_tim_kerja.nama_tim_kerja', 
                'master_project.nama_project', 
                'master_kegiatan_utama.nama_kegiatan_utama',
                'daily_activity.*')
            ->first();
        // dd($activity);
        return view('dailyactivity.edit',compact('activity'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //check the existing of file upload Berkas
        $filename = NULL;
        if ($request->hasFile('berkas'))
        {
           $file = $request->file('berkas');
           $filename = $filename = \Carbon\Carbon::now()->format('Y-m-d H-i').'_'. Auth::user()->nip .'_'. str_replace(' ', '', substr(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 0, 25)). '.' .$file->getClientOriginalExtension();
           $file->move('bukti', $filename);
        }

        if($request->has('checkbox')) {
            $tgl_selesai = $request->tgl_selesai;
        } else {
            $tgl_selesai = date('Y-m-d');
        }

        $activity = Activity::find($id);
        if($activity) {
            $activity->wfo_wfh = $request->wfo_wfh;
            $activity->kegiatan = $request->kegiatan;
            $activity->keterangan = $request->keterangan_kegiatan;
            $activity->jenis_kegiatan = $request->jenis_kegiatan;
            $activity->satuan = $request->satuan;
            $activity->kuantitas = $request->kuantitas;
            $activity->tgl = $request->tgl;
            $activity->is_done = $request->is_done;
            $activity->tgl_selesai = $tgl_selesai;
            $activity->created_by = Auth::user()->nip;
            $activity->berkas = $filename;
            $activity->link = $request->link;
            $activity->updated_at = now();
            $activity->save();
        }
        return redirect()->route('act.index')->with('success', 'The activity updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $act = Activity::find($id);
        $act->delete();
        return redirect('/act');
    }

    public function monitoring()
    {
        $bulan = date('m');
        $tahun = date('Y');
        $months = [
            ['value' => 1, 'name' => 'Januari'],
            ['value' => 2, 'name' => 'Februari'],
            ['value' => 3, 'name' => 'Maret'],
            ['value' => 4, 'name' => 'April'],
            ['value' => 5, 'name' => 'Mei'],
            ['value' => 6, 'name' => 'Juni'],
            ['value' => 7, 'name' => 'Juli'],
            ['value' => 8, 'name' => 'Agustus'],
            ['value' => 9, 'name' => 'September'],
            ['value' => 10, 'name' => 'Oktober'],
            ['value' => 11, 'name' => 'November'],
            ['value' => 12, 'name' => 'Desember']
        ];


        $years = DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();
        // Query to get employees with the most daily activity submissions
        $rankTodayEmployees = DB::table('users')
            ->leftJoin('daily_activity', function($join) {
                $join->on('users.nip', '=', 'daily_activity.nip')
                     ->whereMonth('daily_activity.created_at', '=', date('m'))
                     ->whereYear('daily_activity.created_at', '=', date('Y'));
            })
            ->whereNotIn('users.nip', ['199111052014102001', '199906092021121002', '197111211994032002', '196701201993031001', '198410302011011016']) // Mengecualikan pegawai tertentu            
            ->where('users.unit_kerja', '=' , 'BPS Kota Padang Panjang')
            ->select('users.nip', 'users.fullname', DB::raw('COALESCE(COUNT(daily_activity.id), 0) as jumlah_pengisian'))
            ->groupBy('users.nip', 'users.fullname')
            ->orderBy('jumlah_pengisian', 'desc')
            ->get()
            ->map(function ($user) use ($bulan, $tahun) {
                // Hitung hari kerja dalam bulan ini (kecuali Sabtu dan Minggu)
                $maxWorkDaysFiltered = Carbon::createFromDate($tahun, $bulan)->startOfMonth()->diffInDaysFiltered(
                    fn($date) => $date->isWeekday(), // Hanya menghitung hari Senin - Jumat
                    Carbon::today()
                ) + 1; //adjustment to the day
                $datenow = (new \DateTime())->format('Y-m-d'); 
                // Hari Libur dalam Senin-Jumat
                $hariLibur = count(array_filter(
                        json_decode(file_get_contents("https://api-harilibur.netlify.app/api?month=$bulan&year=$tahun"), true),
                        fn($holiday) => (new \DateTime($holiday['holiday_date']))->format('N') <= 5 && $holiday['holiday_date'] <= $datenow));

                // Hitung hari yang sudah diisi oleh pengguna
                $filledDays = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', date('m'))
                    ->whereYear('tgl', date('Y'))
                    ->select(DB::raw('DATE(tgl) as date'))
                    ->distinct()
                    ->get()
                    ->count();

                $maxWorkDays = $maxWorkDaysFiltered - $hariLibur;

                // Menghitung hari kerja yang tidak diisi
                $user->missed_days = $maxWorkDays-$filledDays;

                // Menambahkan jumlah hari yang diisi ke objek pengguna
                $user->filled_days = $filledDays;
                


                // Skala 50 untuk hari pengisian
                $filledDaysScore = (($filledDays - $hariLibur) / $maxWorkDays) * 50;

                // Hitung total kegiatan (total aktivitas yang dilakukan karyawan dalam bulan ini)
                $totalActivities = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->count();

                // Tentukan maksimum kegiatan untuk skala kegiatan (misalnya ambil nilai tertinggi dalam database)
                $maxActivities = DB::table('daily_activity')
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('COUNT(id) as activity_count'))
                    ->groupBy('nip')
                    ->orderByDesc('activity_count')
                    ->limit(1)
                    ->value('activity_count') ?? 1; // Beri nilai default 1 agar tidak ada pembagian nol

                // Skala 50 untuk kegiatan
                $activityScore = ($totalActivities / $maxActivities) * 50;

                // Total skor dalam skala 100
                $user->score = $filledDaysScore + $activityScore;
                $user->filledDaysScore =$filledDaysScore;///
                $user->maxWorkDays = $maxWorkDays;

                return $user;
            })
            ->sortByDesc('score') // Mengurutkan berdasarkan score secara menurun
            ->values();

        // dd($rankTodayEmployees);

        return view('dailyactivity.monitoring', compact('rankTodayEmployees', 'months', 'years', 'bulan', 'tahun'))->with('i', (request()->input('page', 1) - 1) * 5 );
    }

    public function filterMonthYear2(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $months = [
            ['value' => 1, 'name' => 'Januari'],
            ['value' => 2, 'name' => 'Februari'],
            ['value' => 3, 'name' => 'Maret'],
            ['value' => 4, 'name' => 'April'],
            ['value' => 5, 'name' => 'Mei'],
            ['value' => 6, 'name' => 'Juni'],
            ['value' => 7, 'name' => 'Juli'],
            ['value' => 8, 'name' => 'Agustus'],
            ['value' => 9, 'name' => 'September'],
            ['value' => 10, 'name' => 'Oktober'],
            ['value' => 11, 'name' => 'November'],
            ['value' => 12, 'name' => 'Desember']
        ];

        $years = DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        // dd(date($bulan));

        // Query to get employees with the most daily activity submissions
        $rankTodayEmployees = DB::table('users')
            ->leftJoin('daily_activity', function($join) use ($bulan, $tahun) {
                    $join->on('users.nip', '=', 'daily_activity.nip')
                         ->whereMonth('daily_activity.tgl', '=', $bulan)
                         ->whereYear('daily_activity.tgl', '=', $tahun);
                })
            ->whereNotIn('users.nip', ['199111052014102001', '199906092021121002', '197111211994032002', '196701201993031001']) // Mengecualikan pegawai tertentu
            ->select('users.nip', 'users.fullname', DB::raw('COALESCE(COUNT(daily_activity.id), 0) as jumlah_pengisian'))
            ->groupBy('users.nip', 'users.fullname')
            ->orderBy('jumlah_pengisian', 'desc')
            ->get()
            ->map(function ($user) use ($bulan, $tahun) {
                // Tentukan maksimum hari kerja dalam bulan ini untuk skala hari pengisian
                // Hitung Hari Senin-Jumat
                $maxWorkDaysFiltered = Carbon::createFromDate($tahun, $bulan)->startOfMonth()->diffInDaysFiltered(
                    fn($date) => $date->isWeekday(), // Hanya menghitung hari Senin - Jumat
                    Carbon::createFromDate($tahun, $bulan)->endOfMonth()
                );

                // Hari Libur dalam Senin-Jumat
                $hariLibur = count(array_filter(json_decode(file_get_contents("https://api-harilibur.netlify.app/api?month=$bulan&year=$tahun"), true), fn($holiday) => (new \DateTime($holiday['holiday_date']))->format('N') <= 5));

                // Hitung hari yang sudah diisi oleh pengguna
                $filledDays = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('DATE(tgl) as date'))
                    ->distinct()
                    ->get()
                    ->count();

                $maxWorkDays = $maxWorkDaysFiltered - $hariLibur;

                // Menghitung hari kerja yang tidak diisi
                $user->missed_days = $maxWorkDays-$filledDays;

                // Menambahkan jumlah hari yang diisi ke objek pengguna
                $user->filled_days = $filledDays;
                


                // Skala 50 untuk hari pengisian
                $filledDaysScore = (($filledDays - $hariLibur) / $maxWorkDays) * 50;

                // Hitung total kegiatan (total aktivitas yang dilakukan karyawan dalam bulan ini)
                $totalActivities = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->count();

                // Tentukan maksimum kegiatan untuk skala kegiatan (misalnya ambil nilai tertinggi dalam database)
                $maxActivities = DB::table('daily_activity')
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('COUNT(id) as activity_count'))
                    ->groupBy('nip')
                    ->orderByDesc('activity_count')
                    ->limit(1)
                    ->value('activity_count') ?? 1; // Beri nilai default 1 agar tidak ada pembagian nol

                // Skala 50 untuk kegiatan
                $activityScore = ($totalActivities / $maxActivities) * 50;

                // Total skor dalam skala 100
                $user->score = $filledDaysScore + $activityScore;

                $user->maxWorkDays = $maxWorkDays;

                return $user;
            })
            ->sortByDesc('score') // Mengurutkan berdasarkan score secara menurun
            ->values();

        // dd($rankTodayEmployees);


        return view('dailyactivity.monitoring', compact('rankTodayEmployees', 'months', 'years', 'bulan', 'tahun'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function indexkhiexportToExcel($bulan, $tahun)
    {
        $bulan = $bulan ?? date('m');
        $tahun = $tahun ?? date('Y');

        $months = [
            ['value' => 1, 'name' => 'Januari'],
            ['value' => 2, 'name' => 'Februari'],
            ['value' => 3, 'name' => 'Maret'],
            ['value' => 4, 'name' => 'April'],
            ['value' => 5, 'name' => 'Mei'],
            ['value' => 6, 'name' => 'Juni'],
            ['value' => 7, 'name' => 'Juli'],
            ['value' => 8, 'name' => 'Agustus'],
            ['value' => 9, 'name' => 'September'],
            ['value' => 10, 'name' => 'Oktober'],
            ['value' => 11, 'name' => 'November'],
            ['value' => 12, 'name' => 'Desember']
        ];

        $years = DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        // dd(date($bulan));

        // Query to get employees with the most daily activity submissions
        $rankTodayEmployees = DB::table('users')
            ->leftJoin('daily_activity', function($join) use ($bulan, $tahun) {
                    $join->on('users.nip', '=', 'daily_activity.nip')
                         ->whereMonth('daily_activity.tgl', '=', $bulan)
                         ->whereYear('daily_activity.tgl', '=', $tahun);
                })
            ->whereNotIn('users.nip', ['199111052014102001', '199906092021121002', '197111211994032002', '196701201993031001']) // Mengecualikan pegawai tertentu
            ->select('users.nip', 'users.fullname', DB::raw('COALESCE(COUNT(daily_activity.id), 0) as jumlah_pengisian'))
            ->groupBy('users.nip', 'users.fullname')
            ->orderBy('jumlah_pengisian', 'desc')
            ->get()
            ->map(function ($user) use ($bulan, $tahun) {
                // Tentukan maksimum hari kerja dalam bulan ini untuk skala hari pengisian
                // Hitung Hari Senin-Jumat
                $maxWorkDaysFiltered = Carbon::createFromDate($tahun, $bulan)->startOfMonth()->diffInDaysFiltered(
                    fn($date) => $date->isWeekday(), // Hanya menghitung hari Senin - Jumat
                    Carbon::createFromDate($tahun, $bulan)->endOfMonth()
                );

                // Hari Libur dalam Senin-Jumat
                $hariLibur = count(array_filter(json_decode(file_get_contents("https://api-harilibur.netlify.app/api?month=$bulan&year=$tahun"), true), fn($holiday) => (new \DateTime($holiday['holiday_date']))->format('N') <= 5));

                // Hitung hari yang sudah diisi oleh pengguna
                $filledDays = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('DATE(tgl) as date'))
                    ->distinct()
                    ->get()
                    ->count();

                $maxWorkDays = $maxWorkDaysFiltered - $hariLibur;

                // Menghitung hari kerja yang tidak diisi
                $user->missed_days = $maxWorkDays-$filledDays;

                // Menambahkan jumlah hari yang diisi ke objek pengguna
                $user->filled_days = $filledDays;
                


                // Skala 50 untuk hari pengisian
                $filledDaysScore = (($filledDays - $hariLibur) / $maxWorkDays) * 50;

                // Hitung total kegiatan (total aktivitas yang dilakukan karyawan dalam bulan ini)
                $totalActivities = DB::table('daily_activity')
                    ->where('nip', $user->nip)
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->count();

                // Tentukan maksimum kegiatan untuk skala kegiatan (misalnya ambil nilai tertinggi dalam database)
                $maxActivities = DB::table('daily_activity')
                    ->whereMonth('tgl', $bulan)
                    ->whereYear('tgl', $tahun)
                    ->select(DB::raw('COUNT(id) as activity_count'))
                    ->groupBy('nip')
                    ->orderByDesc('activity_count')
                    ->limit(1)
                    ->value('activity_count') ?? 1; // Beri nilai default 1 agar tidak ada pembagian nol

                // Skala 50 untuk kegiatan
                $activityScore = ($totalActivities / $maxActivities) * 50;

                // Total skor dalam skala 100
                $user->score = $filledDaysScore + $activityScore;

                $user->maxWorkDays = $maxWorkDays;

                return $user;
            })
            ->sortByDesc('score') // Mengurutkan berdasarkan score secara menurun
            ->values();

        // dd($rankTodayEmployees);
        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Nama Pegawai');
        $sheet->setCellValue('B1', 'Indeks Keaktifan KHI');
        $sheet->setCellValue('C1', 'Jumlah Kegiatan');
        $sheet->setCellValue('D1', 'Jumlah Hari Mengisi');
        $sheet->setCellValue('E1', 'Jumlah Hari Tidak Mengisi');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Isi Data
        $row = 2;
        foreach ($rankTodayEmployees as $employee) {
            $sheet->setCellValue("A{$row}", $employee->fullname);
            $sheet->setCellValue("B{$row}", number_format($employee->score, 2));
            $sheet->setCellValue("C{$row}", $employee->jumlah_pengisian);
            $sheet->setCellValue("D{$row}", $employee->filled_days);
            $sheet->setCellValue("E{$row}", $employee->missed_days);
            $row++;
        }

        // Otomatisasi Lebar Kolom
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Unduh File
        $fileName = "laporan_pengisian_{$bulan}_{$tahun}.xlsx";
        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");
        $writer->save('php://output');
        exit;
    }
}

