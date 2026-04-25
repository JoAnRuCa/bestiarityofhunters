<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            TagsSeeder::class,
            GuideSeeder::class,
            BuildSeeder::class,
            GuideTagSeeder::class,
            BuildTagSeeder::class,
            BuildEquipmentSeeder::class,
            BuildEquipmentDecorationSeeder::class,
            BuildCommentSeeder::class,
            GuideCommentSeeder::class,
            BuildsCommentsVotesSeeder::class,
            BuildsVotesSeeder::class,
            GuidesVotesSeeder::class,
            SavedBuildsSeeder::class,
            SavedGuidesSeeder::class,
        ]);
    }
}
