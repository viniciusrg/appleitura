<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
            'read_time' => $this->read_time,
            'content' => $this->content,
            'content_audio' => $this->content_audio,
            'total_views' => $this->total_views,
            'week_views' => $this->week_views,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d'),
        ];
    }
}
