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
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'author' => $this->author,
            'read_time' => $this->read_time,
            'cover' => $this->cover,
            'content_audio' => $this->content_audio,
            'total_views' => $this->total_views,
            'week_views' => $this->week_views,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d'),
            'is_favorite' => $request->user()->favorites()->where('book_id', $this->id)->get()->isNotEmpty(),
            'categories' => $this->categories()->pluck('name'),
        ];
    }
}
