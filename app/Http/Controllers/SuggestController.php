<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class SuggestController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get(key: 'query');

        // Return empty array if query length is less than 5
        if (strlen($query) < 5) {
            return response()->json([]);
        }

        // Fetch activities where the 'kegiatan' field matches the query
        $data = Activity::where('kegiatan', 'LIKE', "%{$query}%")
            ->distinct()
            ->limit(11)
            ->get(['kegiatan']); // Select only the 'kegiatan' column

        // Return data as JSON
        return response()->json($data);
    }
}
