<?php

namespace App\Http\Controllers;
use DB;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $notifications = Notification::latest()->get();
        return view('notification.index', compact('notifications'))->with('i');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('notification.create');
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
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tipe' => 'required|string',
        ]);

        Notification::create([
            'title' => $request->judul,
            'description' => $request->deskripsi,
            'type' => $request->tipe, // Bisa disesuaikan jika ada tipe lain
        ]);

        return redirect()->route('notif.index')->with('success', 'Notifikasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {        
        $notification =  DB::table('notifications')->where('id', '=', $id)->first();
        return view('notification.edit', compact('notification'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tipe' => 'required|string',
        ]);

        $notification = Notification::findOrFail($id);

        $notification->update([
            'title' => $request->judul,
            'description' => $request->deskripsi,
            'type' => $request->tipe,
        ]);

        return redirect()->route('notif.index')
                        ->with('success','Notifikasi sukses diperbaharui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        
        $nt = Notification::find($id);
        $nt->delete();
        return redirect('/notif');
    }
}
