<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->have_user)
        {
            return [
                "name" => $this->name,
                "description" => $this->description,
                "image_url" => url($this->image_url),
                "rarity" => $this->rarity,
                "count" => $this->count,
                "have_url" => $this->have_user
            ];
        }
        else{
            return [
              "name" => $this->name,
              "rarity" => $this->rarity,
              "have_url" => $this->have_user
            ];
        }
    }
}
