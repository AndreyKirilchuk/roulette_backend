<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Meme;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $memes = [
            ['name' => 'Funny Cat', 'rarity' => 'common'],
            ['name' => 'Weird Dog', 'rarity' => 'uncommon'],
            ['name' => 'Dancing Baby', 'rarity' => 'rare'],
            ['name' => 'Epic Fail', 'rarity' => 'epic'],
            ['name' => 'Golden Pepe', 'rarity' => 'legendary'],
            ['name' => 'Galaxy Brain', 'rarity' => 'mythical'],
        ];

        foreach ($memes as $meme) {
            Meme::create([
                'name' => $meme['name'],
                'rarity' => $meme['rarity'],
                'description' => null,
                'image_url' => null,
            ]);
        }
    }
}
