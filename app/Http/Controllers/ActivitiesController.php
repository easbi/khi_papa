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
        $activities = DB::table('daily_activity')->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();
        $act_count_today = Activity::whereDate('tgl', Carbon::today())->count();

        $yesterday = date("Y-m-d", strtotime( '-1 days' ) );
        $act_count_yesterday = Activity::whereDate('tgl', $yesterday )->count();

        $userfill = Activity::whereDate('tgl', Carbon::today())->distinct('nip')->count();

        // Query to get the top 5 employees with the most daily activity submissions
        $topEmployees = DB::table('daily_activity')
            ->join('users', 'daily_activity.nip', '=', 'users.nip')
            ->select('users.fullname', 'daily_activity.nip', DB::raw('COUNT(*) as jumlah_kegiatan'))
            ->whereMonth('daily_activity.created_at', '=', date('m'))
            ->whereYear('daily_activity.created_at', '=', date('Y'))
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
                     ->whereMonth('daily_activity.created_at', '=', date('m'))
                     ->whereYear('daily_activity.created_at', '=', date('Y'));
            })
            ->whereNotIn('users.nip', ['199111052014102001', '199906092021121002', '197111211994032002', '196701201993031001' ]) // Mengecualikan pegawai tertentu
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
                    json_decode(file_get_contents("https://dayoffapi.vercel.app/api?month=" . $today->format('m') . "&year=" . $today->format('Y')), true),
                    fn($holiday) => (new \DateTime($holiday['tanggal']))->format('N') <= 5 && $holiday['tanggal'] <= $datenow
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

    public function selftable()
    {
        $bulan = "";
        $tahun = "";

        $activities = DB::table('daily_activity')->where('daily_activity.nip', Auth::user()->nip)->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();

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

        // dd($years);
        return view('dailyactivity.selftable', compact('activities', 'months', 'years', 'bulan', 'tahun'))->with('i', (request()->input('page', 1) - 1) * 5 );
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
        return view('dailyactivity.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'wfo_wfh' => 'required',
            'jenis_kegiatan' => 'required',
            'kegiatan'=> 'required',
            'satuan'=> 'required',
            'kuantitas'=> 'required',
            'tgl'=> 'required',
        ]);

        $result = Activity::create([
                'nip' => Auth::user()->nip,
                'wfo_wfh' => $request->wfo_wfh,
                'jenis_kegiatan' => $request->jenis_kegiatan,
                'kegiatan'=> $request->kegiatan,
                'satuan'=> $request->satuan,
                'kuantitas'=> $request->kuantitas,
                'tgl'=> $request->tgl,
                'created_by' => Auth::user()->nip,
            ]);

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
        $activity = DB::table('daily_activity')->where('id', $id)->first();
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
        $activity = DB::table('daily_activity')->where('id', $id)->first();
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
            $activity->nip = Auth::user()->nip;
            $activity->wfo_wfh = $request->wfo_wfh;
            $activity->kegiatan = $request->kegiatan;
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
        return redirect()->route('act.selftable')->with('success', 'The activity updated successfully');
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
            ->whereNotIn('users.nip', ['199111052014102001', '199906092021121002', '197111211994032002', '196701201993031001']) // Mengecualikan pegawai tertentu
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
                        json_decode(file_get_contents("https://dayoffapi.vercel.app/api?month=$bulan&year=$tahun"), true),
                        fn($holiday) => (new \DateTime($holiday['tanggal']))->format('N') <= 5 && $holiday['tanggal'] <= $datenow));

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
                $hariLibur = count(array_filter(json_decode(file_get_contents("https://dayoffapi.vercel.app/api?month=$bulan&year=$tahun"), true), fn($holiday) => (new \DateTime($holiday['tanggal']))->format('N') <= 5));

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
}
