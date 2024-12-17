<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Timkerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class ProjectController extends Controller
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
        $project =  DB::table('master_project')
            ->join('master_tim_kerja', 'master_tim_kerja.id', '=', 'master_project.tim_kerja_id')
            ->join('users', 'master_tim_kerja.nip_ketua_tim', '=', 'users.nip')
            ->select('master_tim_kerja.nama_tim_kerja', 'users.fullname as nama_ketua_tim', 'users.nip as nip_ketua_tim', 'master_tim_kerja.tahun_kerja', 'master_project.*')
            ->get();
        return view('masterproject.index', compact('project'))->with('i');
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
        return view('masterproject.create', compact('timkerja'));
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
            'nama_project' => 'required',
        ]);

        $result = Project::create([
            'tim_kerja_id' => $request->tim_kerja_id,
            'nama_project' => $request->nama_project,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('project.index')
                        ->with('success','Lisensi Aplikasi Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        if (Auth::user()->id == 1) {
            $timkerja=  DB::table('master_tim_kerja')->select('id', 'nama_tim_kerja')->get();
        } else {
            $timkerja=  DB::table('master_tim_kerja')->where('master_tim_kerja.nip_ketua_tim','=', Auth::user()->nip)->select('id', 'nama_tim_kerja')->get();
        }
        return view('masterproject.edit', compact('timkerja', 'project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {         
        $request->validate([
            'tim_kerja_id' => 'required',
            'nama_project' => 'required',
        ]);

        $project->update([
            'tim_kerja_id' => $request->tim_kerja_id,
            'nama_project' => $request->nama_project,
            'created_by' => Auth::user()->id,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('project.index')->with('success', 'Project berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('project.index')->with('success', 'Project berhasil dihapus!');

    }
}
