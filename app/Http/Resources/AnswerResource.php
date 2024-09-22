<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'q1' => $this->q1,
            'q2' => $this->q2,
            'q3' => $this->q3,
            'user_email' => $this->user->email,
        ];
    }
}
