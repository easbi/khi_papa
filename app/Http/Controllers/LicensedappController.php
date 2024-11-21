<?php

namespace App\Http\Controllers;

use App\Models\Licensedapp;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LicensedappController extends Controller
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
        $licensedApps =  DB::table('Licensedapp')->get();
        // dd($licensedApps);
        return view('licensedapp.index', compact('licensedApps'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('licensedapp.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama_aplikasi' => 'required|string|max:255',
            'keterangan' => 'required',
            'awal_lisensi' => 'required|date',
            'akhir_lisensi' => 'required|date',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $result = Licensedapp::create([
            'nama_aplikasi' => $request->nama_aplikasi,
            'keterangan' => $request->keterangan,
            'awal_lisensi' => $request->awal_lisensi,
            'akhir_lisensi' => $request->akhir_lisensi,
            'username' => $request->username,
            'password' => $request->password,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('licensedapp.index')
                        ->with('success','Lisensi Aplikasi Sukses Ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Licensedapp  $licensedapp
     * @return \Illuminate\Http\Response
     */
    public function show(Licensedapp $licensedapp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Licensedapp  $licensedapp
     * @return \Illuminate\Http\Response
     */
    public function edit(Licensedapp $licensedapp)
    {
        // dd($licensedapp);
        return view('licensedapp.edit', compact('licensedapp'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Licensedapp  $licensedapp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Licensedapp $licensedapp)
    {

        $request->validate([
            'nama_aplikasi' => 'required|string|max:255',
            'awal_lisensi' => 'required|date',
            'keterangan'=> 'required',
            'akhir_lisensi' => 'required|date',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $keterangan = $request->keterangan ?? "";

        $licensedapp->update([
            'nama_aplikasi' => $request->nama_aplikasi,
            'keterangan' => $keterangan,
            'awal_lisensi' => $request->awal_lisensi,
            'akhir_lisensi' => $request->akhir_lisensi,
            'username' => $request->username,
            'password' => $request->password,
        ]);

        // Redirect ke halaman yang sesuai dengan pesan sukses
        return redirect()->route('licensedapp.index')->with('success', 'Lisensi berhasil diperbarui!');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Licensedapp  $licensedapp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Licensedapp $licensedapp)
    {
        $licensedapp->delete();
        return redirect('/licensedapp');
    }
}
