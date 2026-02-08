<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SavedItemController extends Controller
{
    public function toggle($type, $id)
    {
        try {
            $userId = Auth::id();
            if (!$userId) return response()->json(['error' => 'Unauthenticated'], 401);

            // Buscamos si existe
            $query = DB::table('saved_guides')
                ->where('user_id', $userId)
                ->where('guide_id', $id);

            if ($query->exists()) {
                $query->delete();
                return response()->json(['status' => 'removed']);
            }

            // Si no existe, lo creamos
            DB::table('saved_guides')->insert([
                'user_id' => $userId,
                'guide_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'added']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}