<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SavedGuide; // Asegúrate de tener este modelo para la vista

class SavedItemController extends Controller
{
    // Método para ver la lista de guías guardadas
    public function indexGuides()
    {
        $userId = Auth::id();
        
        // Usamos el modelo para poder usar relaciones (user, tags)
        $savedData = SavedGuide::where('user_id', $userId)
            ->with(['guide.user', 'guide.tags'])
            ->latest()
            ->paginate(10);

        return view('seccion.saved-guides', compact('savedData'));
    }

    // Tu método toggle mejorado para ser universal
    public function toggle($type, $id)
    {
        try {
            $userId = Auth::id();
            if (!$userId) return response()->json(['error' => 'Unauthenticated'], 401);

            // Configuramos dinámicamente según el tipo
            $table = ($type === 'guide') ? 'saved_guides' : 'saved_builds';
            $foreignKey = ($type === 'guide') ? 'guide_id' : 'build_id';

            // Buscamos si existe
            $query = DB::table($table)
                ->where('user_id', $userId)
                ->where($foreignKey, $id);

            if ($query->exists()) {
                $query->delete();
                return response()->json(['status' => 'removed']);
            }

            // Si no existe, lo creamos
            DB::table($table)->insert([
                'user_id' => $userId,
                $foreignKey => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'added']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}