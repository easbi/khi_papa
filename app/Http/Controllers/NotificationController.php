<?php

namespace App\Http\Controllers;
use DB;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'always_show' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '-' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/notifications');

            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }

            $image->move($destination, $filename);
            $imagePath = 'uploads/notifications/' . $filename;
        }

        Notification::create([
            'title' => $request->judul,
            'description' => $request->deskripsi,
            'type' => $request->tipe,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image_path' => $imagePath,
            'always_show' => $request->has('always_show'),
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
        abort(404);
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
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'always_show' => 'nullable|boolean',
        ]);

        $notification = Notification::findOrFail($id);
        $imagePath = $notification->image_path;

        if ($request->hasFile('image')) {
            if ($notification->image_path && File::exists(public_path($notification->image_path))) {
                File::delete(public_path($notification->image_path));
            }

            $image = $request->file('image');
            $filename = time() . '-' . Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $destination = public_path('uploads/notifications');

            if (!File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }

            $image->move($destination, $filename);
            $imagePath = 'uploads/notifications/' . $filename;
        }

        $notification->update([
            'title' => $request->judul,
            'description' => $request->deskripsi,
            'type' => $request->tipe,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image_path' => $imagePath,
            'always_show' => $request->has('always_show'),
        ]);

        return redirect()->route('notif.index')
                        ->with('success','Notifikasi sukses diperbaharui');
    }

    public function getActiveNotifications()
    {
        $now = now();
        $notifications = Notification::where('start_date', '<=', $now->toDateString())
                                      ->where('end_date', '>=', $now->toDateString())
                                      ->get();

        return response()->json($notifications);
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
