<?php

namespace App\Http\Controllers;

use App\Models\Temp;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        $TimKerja = $assigntim->unique('nama_tim_kerja', 'tim_kerja_id')->pluck('tim_kerja_id', 'nama_tim_kerja');
        // dd($TimKerja);

        return view('temp.create', compact('assigntim', 'TimKerja'));
    }

    public function getProject(Request $request)
    {
        $projects = DB::table('master_assign_anggota AS ma')
        ->where('ma.anggota_nip', '=', Auth::user()->nip)
        ->where('ma.tim_kerja_id', '=', $request->tim_kerja_id) 
        ->join('master_project AS mp', 'mp.id', '=', 'ma.project_id')
        ->select('mp.nama_project', 'mp.id') 
        ->distinct()
        ->pluck('mp.nama_project', 'mp.id');

        return response()->json($projects);
    }

    public function getKegiatanutama(Request $request)
    {
        $kegiatanutama = DB::table('master_assign_anggota AS ma')
            ->where('ma.anggota_nip', '=', Auth::user()->nip)
            ->where('ma.project_id', '=', $request->project_id) 
            ->join('master_kegiatan_utama AS mku', 'mku.id', '=', 'ma.kegiatan_utama_id')
            ->select('mku.nama_kegiatan_utama', 'mku.id') 
            ->distinct()
            ->pluck('mku.nama_kegiatan_utama', 'mku.id');

        return response()->json($kegiatanutama);
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

        $result = Temp::create([
                'nip' => Auth::user()->nip,
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

         return redirect()->route('act.index')
                        ->with('success','Kegiatan Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Temp  $temp
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
        return view('temp.show',compact('activity'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Temp  $temp
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
        return view('temp.edit',compact('activity'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Temp  $temp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // dd($request);
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
     * @param  \App\Models\Temp  $temp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Temp $temp)
    {
        //
    }
}
