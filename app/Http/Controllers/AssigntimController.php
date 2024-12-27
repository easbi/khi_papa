<?php

namespace App\Http\Controllers;

use App\Models\Assigntim;
use App\Models\Project;
use App\Models\Timkerja;
use App\Models\Kegiatanutama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class AssigntimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assigntim =  DB::table('master_assign_anggota')
        ->join('master_kegiatan_utama', 'master_assign_anggota.kegiatan_utama_id', '=', 'master_kegiatan_utama.id')
        ->join('master_project', 'master_project.id', '=', 'master_assign_anggota.project_id')
        ->join('master_tim_kerja', 'master_tim_kerja.id', '=', 'master_assign_anggota.tim_kerja_id')
        ->join('users as ketua_tim', 'master_tim_kerja.nip_ketua_tim', '=', 'ketua_tim.nip')
        ->join('users as anggota_tim', 'master_assign_anggota.anggota_nip', '=', 'anggota_tim.nip')
        ->select(
            'master_tim_kerja.nama_tim_kerja', 
            'ketua_tim.fullname as nama_ketua_tim', 
            'ketua_tim.nip as nip_ketua_tim',
            'master_tim_kerja.tahun_kerja', 
            'master_project.nama_project', 
            'master_kegiatan_utama.nama_kegiatan_utama',
            'anggota_tim.fullname as nama_anggota_tim',
            'master_assign_anggota.*')
        ->get();

        // dd($assigntim);
        return view('assignteam.index', compact('assigntim'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->id == 1) {
            $timkerja=  DB::table('master_tim_kerja')->select('id', 'nama_tim_kerja')->get();
        } else {
            $timkerja=  DB::table('master_tim_kerja')->where('master_tim_kerja.nip_ketua_tim','=', Auth::user()->nip)->select('id', 'nama_tim_kerja')->get();
        }
        $candidate=  DB::table('users')->select('nip', 'fullname')->whereNotIn('id', [2, 10, 14])->get();
        return view('assignteam.create', compact('timkerja', 'candidate'));
    }



    public function getProject(Request $request)
    {
        $projects = DB::table('master_project') // Ganti 'projects' dengan nama tabel Anda
            ->where('tim_kerja_id', $request->tim_kerja_id)
            ->pluck('nama_project', 'id');

        return response()->json($projects);
    }



    public function getKegiatanutama(Request $request)
    {
        $kegiatanutama = DB::table('master_kegiatan_utama') 
            ->where('project_id', $request->project_id)
            ->pluck('nama_kegiatan_utama', 'id');

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

        $isDuplicate = DB::table('master_assign_anggota')
            ->where('anggota_nip', $request->anggota_nip)
            ->where('kegiatan_utama_id', '=', $request->kegiatan_utama_id)
            ->exists();

        if ($isDuplicate) {
            $candidate=  DB::table('users')->select('nip', 'fullname')->where('nip', '=', $request->anggota_nip)->first();
            return redirect()->route('assigntim.create')
                ->withErrors(['anggota_nip' => 'Anggota dengan nama ' . ($candidate->fullname ?? 'Unknown') . ' ini sudah dialokasikan pada Kegaitan Utama yang sama.'])
                ->withInput();
        }

        $request->validate([
            'tim_kerja_id' => 'required',
            'project_id' => 'required',
            'kegiatan_utama_id' => 'required',
            'anggota_nip.*' => 'required',
        ]);

        foreach ($request->anggota_nip as $nip) {
            AssignTim::create([
                'tim_kerja_id' => $request->tim_kerja_id,
                'project_id' => $request->project_id,
                'kegiatan_utama_id' => $request->kegiatan_utama_id,
                'anggota_nip' => $nip,
                'created_by' => Auth::user()->id,
            ]);
        }

        return redirect()->route('assigntim.index')
                        ->with('success','Alokasi Anggota Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Assigntim  $assigntim
     * @return \Illuminate\Http\Response
     */
    public function show(Assigntim $assigntim)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Assigntim  $assigntim
     * @return \Illuminate\Http\Response
     */
    public function edit(Assigntim $assigntim)
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
                ->select('master_project.id', 'master_project.nama_project', 'tim_kerja_id')
                ->get();
        }

        $kegiatanutama = DB::table('master_kegiatan_utama')->get();

        $candidate=  DB::table('users')->select('nip', 'fullname')->where('id', '!=', 2)->get(); //gass
        return view('assignteam.edit', compact('candidate', 'assigntim', 'timkerja', 'projects', 'kegiatanutama'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assigntim  $assigntim
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assigntim $assigntim)
    {
        // dd($request);
        $request->validate([
            'anggota_nip' => 'required',
        ]);

        $isDuplicate = DB::table('master_assign_anggota')
        ->where('anggota_nip', $request->anggota_nip)
        ->where('kegiatan_utama_id', '=', $request->kegiatan_utama_id)
        ->exists();

        if ($isDuplicate) {
            $candidate=  DB::table('users')->select('nip', 'fullname')->where('nip', '=', $request->anggota_nip)->first();
            return redirect()->route('assigntim.edit', $assigntim->id)
                ->withErrors(['anggota_nip' => 'Pegawai dengan nama '. ($candidate->fullname ?? 'Unknown') . ' ini sudah dialokasikan pada Kegiatan Utama yang sama.'])
                ->withInput();
        }

        $assigntim->update([
            'anggota_nip' => $request->anggota_nip,
            'created_by' => Auth::user()->id,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('assigntim.index')->with('success', 'Anggota berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Assigntim  $assigntim
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assigntim $assigntim)
    {
        $assigntim->delete();
        return redirect()->route('assigntim.index')->with('success', 'Anggota berhasil dihapus!');
    }
}
