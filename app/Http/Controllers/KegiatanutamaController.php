<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timkerja;
use App\Models\Kegiatanutama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class KegiatanutamaController extends Controller
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
        $query = DB::table('master_kegiatan_utama')
        ->join('master_project', 'master_project.id', '=', 'master_kegiatan_utama.project_id')
        ->join('master_tim_kerja', 'master_tim_kerja.id', '=', 'master_project.tim_kerja_id')
        ->join('users', 'master_tim_kerja.nip_ketua_tim', '=', 'users.nip')
        ->select('master_tim_kerja.nama_tim_kerja', 'users.fullname as nama_ketua_tim','users.nip as nip_ketua_tim', 'master_tim_kerja.tahun_kerja', 'master_project.nama_project', 'master_kegiatan_utama.*');

        $tahun = request()->get('tahun', date('Y'));
        $query->where('master_tim_kerja.tahun_kerja', $tahun);

        $kegiatanutama = $query->get();
        return view('kegiatanutama.index', compact('kegiatanutama'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currentYear = date('Y');
        if (Auth::user()->id == 1) {
            $timkerja=  DB::table('master_tim_kerja')->where('tahun_kerja', $currentYear)->select('id', 'nama_tim_kerja')->get();
        } else {
            $timkerja=  DB::table('master_tim_kerja')->where('master_tim_kerja.nip_ketua_tim','=', Auth::user()->nip)->where('tahun_kerja', $currentYear)->select('id', 'nama_tim_kerja')->get();
        }
        return view('kegiatanutama.create', compact('timkerja'));
    }

    public function getProject(Request $request)
    {
        $currentYear = date('Y');
        $projects = DB::table('master_project')
        ->join('master_tim_kerja', 'master_tim_kerja.id', '=', 'master_project.tim_kerja_id')
        ->where('master_project.tim_kerja_id', $request->tim_kerja_id)
        ->where('master_tim_kerja.tahun_kerja', $currentYear)
        ->pluck('nama_project', 'master_project.id');

        return response()->json($projects);
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
            'tim_kerja_id' => 'required',
            'project_id' => 'required',
            'nama_kegiatan_utama' => 'required',
        ]);

        $result = Kegiatanutama::create([
            'tim_kerja_id' => $request->tim_kerja_id,
            'project_id' => $request->project_id,
            'nama_kegiatan_utama' => $request->nama_kegiatan_utama,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('kegiatanutama.index')
                        ->with('success','Kegiatan Utama Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kegiatanutama  $Kegiatanutama
     * @return \Illuminate\Http\Response
     */
    public function show(Kegiatanutama $kegiatanutama)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kegiatanutama  $Kegiatanutama
     * @return \Illuminate\Http\Response
     */
    public function edit(Kegiatanutama $kegiatanutama)
    {
        if (Auth::user()->id == 1) {
            $timkerja=  DB::table('master_tim_kerja')->select('id', 'nama_tim_kerja')->get();
            $projects = DB::table('master_project')->select('id', 'nama_project', 'tim_kerja_id')->get();
        } else {
            $timkerja =  DB::table('master_tim_kerja')->where('master_tim_kerja.nip_ketua_tim','=', Auth::user()->nip)->select('id', 'nama_tim_kerja')->get();
            $projects = DB::table('master_project')
                ->join('master_tim_kerja', 'master_tim_kerja.id', '=', 'master_project.tim_kerja_id')
                ->join('users', 'master_tim_kerja.nip_ketua_tim', '=', 'users.nip')
                ->select('master_tim_kerja.nama_tim_kerja', 'master_tim_kerja.nip_ketua_tim', 'users.fullname as nama_ketua_tim', 'master_tim_kerja.tahun_kerja', 'master_project.nama_project', 'master_kegiatan_utama.*')
                ->where('master_tim_kerja.nip_ketua_tim','=', Auth::user()->nip)
                ->select('master_project.id', 'nama_project', 'tim_kerja_id')
                ->get();
        }
        return view('kegiatanutama.edit', compact('timkerja', 'projects', 'kegiatanutama'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kegiatanutama  $Kegiatanutama
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kegiatanutama $kegiatanutama)
    {
        $request->validate([
            'nama_kegiatan_utama' => 'required',
        ]);

        $kegiatanutama->update([
            'nama_kegiatan_utama' => $request->nama_kegiatan_utama,
            'created_by' => Auth::user()->id,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('kegiatanutama.index')->with('success', 'kegiatan utama berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kegiatanutama  $Kegiatanutama
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kegiatanutama $kegiatanutama)
    {
        $kegiatanutama->delete();
        return redirect()->route('kegiatanutama.index')->with('success', 'Kegiatan nutama berhasil dihapus!');
    }
}
