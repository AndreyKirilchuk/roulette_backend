<?php

namespace App\Services;

use App\Repositories\MemeRepository;
use App\Repositories\UserMemeRepository;
use Illuminate\Support\Facades\DB;

class MemeService
{
    public function __construct(
        private readonly MemeRepository $memeRepository
    ) {}

    public function spin()
    {
        $rarityChances = [
            'common'      => 0.5,        // 50%
            'uncommon'    => 0.3,        // 30%
            'rare'        => 0.15,       // 15%
            'epic'        => 0.04,       // 4%
            'legendary'   => 0.00999,    // 0.999%
            'mythical'    => 0.00001     // 0.001%
        ];

        $random = mt_rand() / mt_getrandmax(); // 0..1
        $cumulative = 0.0;
        $randomRarity = '';

        foreach ($rarityChances as $rarity => $chance) {
            $cumulative += $chance;
            if ($random <= $cumulative) {
                $randomRarity = $rarity;
                break;
            }
        }

        $memesForRandomRarity = $this->memeRepository->whereGet('rarity', $randomRarity);

        $randomIndex = random_int(0, count($memesForRandomRarity) - 1);
        $randomMeme = $memesForRandomRarity[$randomIndex];

//        $this->userMemeRepository->incrementOrCreate(auth()->id(), $randomMeme->id);

        return $randomMeme;
    }

    public function getUserMemes($user)
    {
        $allMemes = $this->memeRepository->get();

        $userMemesWithPivot = $user->memes()->withPivot('count')->get();

        $userMemeCounts = $userMemesWithPivot->pluck('pivot.count', 'id')->toArray();

        // Теперь, преобразуем все мемы, добавляя информацию о владении пользователем
        $enrichedMemes = $allMemes->map(function ($meme) use ($userMemeCounts) {
            $memeId = $meme->id;

            if (array_key_exists($memeId, $userMemeCounts)) {
                $meme->user_have = true;
                $meme->count = $userMemeCounts[$memeId];
            } else {
                $meme->user_have = false;
            }
            return $meme;
        });

        return $enrichedMemes;
    }
}
