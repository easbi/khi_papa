<?php

namespace App\Http\Controllers;

use App\Models\Timkerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class TimkerjaController extends Controller
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
        $timkerja =  DB::table('master_tim_kerja')
            ->join('users', 'master_tim_kerja.nip_ketua_tim', '=', 'users.nip')
            ->select('users.fullname as ketua_tim_kerja', 'master_tim_kerja.*')
            ->get();
        return view('mastertim.index', compact('timkerja'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $candidate=  DB::table('users')->select('nip', 'fullname')->where('id', '!=', 2)->get();
        return view('mastertim.create', compact('candidate'));
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
            'nama_tim_kerja' => 'required|string|max:255',
            'nip_ketua_tim' => 'required',
            'tahun_kerja' => 'required',
        ]);

        $result = Timkerja::create([
            'nama_tim_kerja' => $request->nama_tim_kerja,
            'nip_ketua_tim' => $request->nip_ketua_tim,
            'tahun_kerja' => $request->tahun_kerja,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('timkerja.index')
                        ->with('success','Lisensi Aplikasi Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function show(Timkerja $timkerja)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function edit(Timkerja $timkerja)
    {
        $candidate=  DB::table('users')->select('nip', 'fullname')->where('id', '!=', 2)->get();
        return view('mastertim.edit', compact('timkerja', 'candidate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Timkerja $timkerja)
    {         
        $request->validate([
            'nama_tim_kerja' => 'required|string|max:255',
            'nip_ketua_tim' => 'required',
            'tahun_kerja' => 'required',
        ]);

        $timkerja->update([
            'nama_tim_kerja' => $request->nama_tim_kerja,
            'nip_ketua_tim' => $request->nip_ketua_tim,
            'tahun_kerja' => $request->tahun_kerja,
            'created_by' => Auth::user()->id,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('timkerja.index')->with('success', 'Tim berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Timkerja  $timkerja
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timkerja $timkerja)
    {
        $timkerja->delete();
        return redirect()->route('timkerja.index')->with('success', 'Tim berhasil dihapus!');

    }
}
