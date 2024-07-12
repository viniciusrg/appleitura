<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isCategory = $this->categories()->first();
        return [
            'id' => $this->id,
            'email' => $this->email,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d'),
            'category' => $isCategory ?  $isCategory->name : 'Nenhuma',
        ];
    }
}
