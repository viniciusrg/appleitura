<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
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
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d'),
        ];
    }
}
