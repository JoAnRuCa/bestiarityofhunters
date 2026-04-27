<?php

namespace App\Services;

use App\Models\Build;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BuildService
{
    /**
     * Lógica para guardar una Build completa
     */
    public function storeBuild(array $requestData, $userId)
    {
        $buildData = json_decode($requestData['build_data'], true);
        $decoData = json_decode($requestData['decorations_data'], true);

        $categoryMap = [
            'weapon1' => 1, 'weapon2' => 1,
            'head'    => 2, 'chest'   => 2, 'arms' => 2, 'waist' => 2, 'legs' => 2,
            'charm'   => 3
        ];

        return DB::transaction(function () use ($requestData, $buildData, $decoData, $categoryMap, $userId) {
            $build = Build::create([
                'titulo'    => $requestData['titulo'],
                'playstyle' => $requestData['playstyle'],
                'user_id'   => $userId
            ]);

            foreach ($buildData as $slot => $item) {
                if (!$item || !isset($item['id'])) continue;

                $buildEquipmentId = DB::table('builds_equipments')->insertGetId([
                    'build_id'     => $build->id,
                    'equipment_id' => $item['id'],
                    'tipo'         => isset($categoryMap[$slot]) ? $categoryMap[$slot] : 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                if (isset($decoData[$slot]) && is_array($decoData[$slot])) {
                    foreach ($decoData[$slot] as $deco) {
                        if ($deco && isset($deco['id'])) {
                            DB::table('builds_equipments_decorations')->insert([
                                'build_equipment_id' => $buildEquipmentId,
                                'decoration_id'      => $deco['id'],
                                'created_at'         => now(),
                                'updated_at'         => now(),
                            ]);
                        }
                    }
                }
            }

            if (isset($requestData['tags'])) {
                $build->tags()->sync($requestData['tags']);
            }

            return $build;
        });
    }

    /**
     * Lógica para calcular todas las habilidades (Corregido: No suma Arma 2)
     */
    public function getBuildDetails(Build $build)
    {
        // Importante: El orden de la consulta influye en cuál se detecta como "primera" arma
        $equipments = DB::table('builds_equipments')->where('build_id', $build->id)->orderBy('id', 'asc')->get();
        
        $data = [
            'weapons' => json_decode(Storage::get('data/weapons.json'), true) ?: [],
            'armors'  => json_decode(Storage::get('data/armors.json'), true) ?: [],
            'charms'  => $this->getNormalizedCharms(),
            'decos'   => json_decode(Storage::get('data/decorations.json'), true) ?: [],
            'skills'  => json_decode(Storage::get('data/skills.json'), true) ?: [],
        ];

        $skillMaxLevels = [];
        foreach ($data['skills'] as $s) {
            if (isset($s['name']) && isset($s['ranks'])) {
                $skillMaxLevels[trim($s['name'])] = count($s['ranks']);
            }
        }

        $totalSkillsRaw = [];
        $weaponSkills = [];
        $tipoLabels = [1 => 'Weapon', 2 => 'Armor Piece', 3 => 'Charm'];
        
        $weaponCount = 0; // Contador para identificar el Arma 2

        foreach ($equipments as $eq) {
            $isWeapon = ((int)$eq->tipo === 1);
            $eq->tipo_label = isset($tipoLabels[(int)$eq->tipo]) ? $tipoLabels[(int)$eq->tipo] : 'Equipment';

            // Lógica de exclusión: Si es un arma y ya hemos procesado una, ignoramos sus skills
            $ignoreSkills = false;
            if ($isWeapon) {
                $weaponCount++;
                if ($weaponCount > 1) {
                    $ignoreSkills = true; 
                }
            }

            $source = [];
            switch ((int)$eq->tipo) {
                case 1: $source = $data['weapons']; break;
                case 2: $source = $data['armors']; break;
                case 3: $source = $data['charms']; break;
            }

            $itemData = collect($source)->firstWhere('id', $eq->equipment_id);
            
            if ($itemData) {
                $eq->real_name = isset($itemData['name']) ? $itemData['name'] : (isset($itemData['weaponName']) ? $itemData['weaponName'] : (isset($itemData['charmName']) ? $itemData['charmName'] : 'Unknown'));
                $eq->total_slots = isset($itemData['slots']) ? $itemData['slots'] : [];
                
                // Solo extraemos habilidades si NO es el arma secundaria
                if (!$ignoreSkills) {
                    $this->extractSkills($itemData, $totalSkillsRaw, $weaponSkills, $isWeapon);
                }
            }

            $savedDecos = DB::table('builds_equipments_decorations')->where('build_equipment_id', $eq->id)->get();
            $eq->attached_decos = [];
            
            foreach ($savedDecos as $d) {
                $decoInfo = collect($data['decos'])->firstWhere('id', $d->decoration_id);
                if ($decoInfo) {
                    $eq->attached_decos[] = [
                        'id' => $decoInfo['id'],
                        'name' => $decoInfo['name'],
                        'level' => isset($decoInfo['slot']) ? $decoInfo['slot'] : 1,
                        'is_empty' => false
                    ];
                    
                    // Solo extraemos habilidades de decoraciones si NO es el arma secundaria
                    if (!$ignoreSkills) {
                        $this->extractSkills($decoInfo, $totalSkillsRaw, $weaponSkills, $isWeapon);
                    }
                }
            }

            if (isset($eq->total_slots) && is_array($eq->total_slots)) {
                $numEquipadas = count($eq->attached_decos);
                $numTotales = count($eq->total_slots);
                for ($i = $numEquipadas; $i < $numTotales; $i++) {
                    $eq->attached_decos[] = ['name' => null, 'level' => $eq->total_slots[$i], 'is_empty' => true];
                }
            }
        }

        $totalSkills = $this->finalizeSkills($totalSkillsRaw, $skillMaxLevels, $weaponSkills, $data['skills']);

        return [
            'equipments' => $equipments,
            'totalSkills' => $totalSkills
        ];
    }

    private function extractSkills($source, &$totalSkillsRaw, &$weaponSkills, $isWeapon) {
        $skills = isset($source['skills']) ? $source['skills'] : (isset($source['skill']) ? [$source] : []);
        foreach ($skills as $s) {
            $name = trim(isset($s['skill']['name']) ? $s['skill']['name'] : (isset($s['name']) ? $s['name'] : ''));
            if ($name) {
                $totalSkillsRaw[$name] = (isset($totalSkillsRaw[$name]) ? $totalSkillsRaw[$name] : 0) + (isset($s['level']) ? $s['level'] : 1);
                if ($isWeapon) $weaponSkills[$name] = true;
            }
        }
    }

    private function finalizeSkills($raw, $maxLevels, $weaponSkills, $skillsData) {
        return collect($raw)->map(function($lvl, $name) use ($maxLevels, $weaponSkills, $skillsData) {
            $max = isset($maxLevels[$name]) ? $maxLevels[$name] : 5;
            $currentLvl = (int)min($lvl, $max);
            
            $skillInfo = collect($skillsData)->firstWhere('name', $name);
            $desc = "No desc.";
            if ($skillInfo && isset($skillInfo['ranks'][$currentLvl - 1])) {
                $rank = $skillInfo['ranks'][$currentLvl - 1];
                $desc = isset($rank['description']) ? $rank['description'] : (isset($rank['desc']) ? $rank['desc'] : $desc);
            }

            return [
                'name'      => $name, 
                'lvl'       => $currentLvl, 
                'max'       => $max,
                'percent'   => ($max > 0) ? ($currentLvl / $max) * 100 : 0,
                'desc'      => $desc, 
                'is_weapon' => isset($weaponSkills[$name]) ? 1 : 0 
            ];
        })->sort(function($a, $b) {
            if ($a['is_weapon'] !== $b['is_weapon']) {
                return $b['is_weapon'] <=> $a['is_weapon'];
            }
            if ($b['lvl'] !== $a['lvl']) {
                return $b['lvl'] <=> $a['lvl'];
            }
            return strcmp($a['name'], $b['name']);
        })->values()->toArray();
    }

    private function getNormalizedCharms() {
        $charmsRaw = json_decode(Storage::get('data/charms.json'), true) ?: [];
        $normalized = [];
        foreach ($charmsRaw as $charm) {
            if (isset($charm['ranks'])) {
                foreach ($charm['ranks'] as $rank) { $normalized[] = $rank; }
            }
        }
        return $normalized;
    }

    public function getFilteredBuilds($request, $userId = null)
    {
        $query = Build::with(['tags', 'user', 'votos'])->withSum('votos as score_sum', 'tipo');
        
        if ($userId) {
            $query->where('builds.user_id', $userId);
        }
        
        if ($request->filled('search')) {
            $query->where('builds.titulo', 'like', '%' . $request->search . '%');
        }
        
        if (!$userId && $request->filled('autor')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->autor . '%');
            });
        }
        
        if ($request->filled('tag')) {
            foreach ((array)$request->tag as $tag) {
                $query->whereHas('tags', function($q) use ($tag) {
                    $q->where('name', $tag);
                });
            }
        }
        
        if ($request->orden === 'votados') {
            $query->orderByRaw('COALESCE(score_sum, 0) DESC');
        } else {
            $query->orderBy('builds.created_at', 'desc');
        }
        
        return $query;
    }

    public function getArmorKind($id)
    {
        $armors = json_decode(Storage::get('data/armors.json'), true) ?: [];
        foreach($armors as $a) {
            if($a['id'] == $id) return $a['kind'];
        }
        return null;
    }

    public function saveBuildEquipment($buildId, array $buildData, array $decoData)
    {
        $categoryMap = [
            'weapon1' => 1, 'weapon2' => 1,
            'head'    => 2, 'chest'   => 2, 'arms' => 2, 'waist' => 2, 'legs' => 2,
            'charm'   => 3
        ];

        foreach ($buildData as $slot => $item) {
            if (!$item || !isset($item['id'])) continue;
            $tipo = isset($categoryMap[$slot]) ? $categoryMap[$slot] : 0;

            $buildEquipmentId = DB::table('builds_equipments')->insertGetId([
                'build_id'     => $buildId,
                'equipment_id' => $item['id'],
                'tipo'         => $tipo,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            if (isset($decoData[$slot]) && is_array($decoData[$slot])) {
                foreach ($decoData[$slot] as $deco) {
                    if ($deco && isset($deco['id'])) {
                        DB::table('builds_equipments_decorations')->insert([
                            'build_equipment_id' => $buildEquipmentId,
                            'decoration_id'      => $deco['id'],
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);
                    }
                }
            }
        }
    }
}