<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagsSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            // Weapons
            'Great Sword',
            'Long Sword',
            'Sword and Shield',
            'Dual Blades',
            'Hammer',
            'Hunting Horn',
            'Lance',
            'Gunlance',
            'Switch Axe',
            'Charge Blade',
            'Insect Glaive',
            'Bow',
            'Light Bowgun',
            'Heavy Bowgun',

            // Guide categories
            'Advanced Tips',
            'General Gameplay',
            'Beginner Guide',
            'Meta Builds',
            'Armor Sets',
            'Weapon Combos',
            'Endgame Progression',
            'Skill Synergy',
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => $tag]);
        }
    }
}
