<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;

class FamilySearchController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $families = Family::where('family_code', 'LIKE', "%{$search}%")
            ->orWhereHas('headOfFamily', function ($query) use ($search) {
                $query->where('firstname', 'LIKE', "%{$search}%")
                    ->orWhere('lastname', 'LIKE', "%{$search}%");
            })
            ->with('headOfFamily:id,firstname,lastname')
            ->take(10)
            ->get(['id', 'family_code']);

        return response()->json($families);
    }
}
