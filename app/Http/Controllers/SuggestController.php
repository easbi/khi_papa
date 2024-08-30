<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class SuggestController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');

        // Fetch activities where the 'kegiatan' field matches the query
        $data = Activity::where('kegiatan', 'LIKE', "%{$query}%")
            ->distinct()
            ->get(['kegiatan']); // Select only the 'kegiatan' column

        // Return data as JSON
        return response()->json($data);
    }
}
