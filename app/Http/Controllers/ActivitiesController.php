<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
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
        $activities = DB::table('daily_activity')->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();
        $act_count_today = Activity::whereDate('tgl', Carbon::today())->count();

        $yesterday = date("Y-m-d", strtotime( '-1 days' ) );
        $act_count_yesterday = Activity::whereDate('tgl', $yesterday )->count();

        $userfill = Activity::whereDate('tgl', Carbon::today())->distinct('nip')->count();


        // status WFH/WFO/dll
        $record_status_wfo_wfh = Activity::whereDate('tgl', Carbon::today())
                ->select('wfo_wfh', \DB::raw("COUNT('id') as count"))
                ->groupBy('wfo_wfh')
                ->get();
        $status_wfo_wfh = [];
        foreach($record_status_wfo_wfh as $row) {
            $status_wfo_wfh['label'][] = $row->wfo_wfh;
            $status_wfo_wfh['data'][] = (int) $row->count;
        } 
        $status_wfo_wfh = json_encode($status_wfo_wfh);

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

        // dd($status_penyelesaian);

        return view('dailyactivity.index', 
            compact(
                'activities', 
                'userfill', 
                'act_count_today',
                'act_count_yesterday',
                'status_wfo_wfh',
                'status_penyelesaian'
            ))
        ->with('i');
    }

    public function selftable()
    {
        $activities = DB::table('daily_activity')->where('daily_activity.nip', Auth::user()->nip)->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();

        $months =  DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year, MONTH(tgl) month, MONTHNAME(tgl) month_name'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $years = DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        // dd($years);
        return view('dailyactivity.selftable', compact('activities', 'months', 'years'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function filterMonthYear(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $activities = DB::table('daily_activity')->whereYear('tgl', '=', date($tahun))->whereMonth('tgl', '=', date($bulan))->where('daily_activity.nip', Auth::user()->nip)->join('users', 'daily_activity.nip', 'users.nip')->select('daily_activity.*', 'users.fullname')->orderBy('id', 'desc')->get();

        $months =  DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year, MONTH(tgl) month, MONTHNAME(tgl) month_name'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $years = DB::table('daily_activity')
            ->select(DB::raw('YEAR(tgl) year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        return view('dailyactivity.selftable', compact('activities', 'months', 'years'))->with('i', (request()->input('page', 1) - 1) * 5);
        
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
            'kegiatan'=> 'required',
            'satuan'=> 'required',
            'kuantitas'=> 'required',
            'tgl'=> 'required',
        ]);

        $result = Activity::create([
                'nip' => Auth::user()->nip,
                'wfo_wfh' => $request->wfo_wfh,
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

        $activity = Activity::find($id);
        if($activity) {
            $activity->nip = Auth::user()->nip;
            $activity->wfo_wfh = $request->wfo_wfh;
            $activity->kegiatan = $request->kegiatan;
            $activity->satuan = $request->satuan;
            $activity->kuantitas = $request->kuantitas;
            $activity->tgl = $request->tgl;
            $activity->is_done = $request->is_done;
            $activity->created_by = Auth::user()->nip;
            $activity->berkas = $filename;
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
}
