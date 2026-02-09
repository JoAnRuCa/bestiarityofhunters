<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedGuide; 

class SavedItemController extends Controller
{
    /**
     * Muestra la lista de guías guardadas.
     * Vista: resources/views/seccion/savedGuides.blade.php
     */
    public function indexGuides()
    {
        $userId = Auth::id();
        
        // Obtenemos los registros con sus relaciones
        $savedData = SavedGuide::where('user_id', $userId)
            ->with(['guide.user', 'guide.tags'])
            ->latest()
            ->paginate(10);

        // Apuntamos a la carpeta 'seccion' y al archivo 'savedGuides'
        return view('seccion.savedGuides', compact('savedData'));
    }

    /**
     * Método universal para Guardar/Quitar (Toggle)
     */
    public function toggle($type, $id)
    {
        try {
            $userId = Auth::id();
            if (!$userId) return response()->json(['error' => 'Unauthenticated'], 401);

            $table = ($type === 'guide') ? 'saved_guides' : 'saved_builds';
            $foreignKey = ($type === 'guide') ? 'guide_id' : 'build_id';

            $query = DB::table($table)
                ->where('user_id', $userId)
                ->where($foreignKey, $id);

            if ($query->exists()) {
                $query->delete();
                return response()->json(['status' => 'removed']);
            }

            DB::table($table)->insert([
                'user_id'    => $userId,
                $foreignKey  => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'added']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}