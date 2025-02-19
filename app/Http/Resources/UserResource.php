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
        $subcategories = $this->subcategories()->get();
        $categories = $subcategories->map(function ($subcategory) {
            return $subcategory->category ? $subcategory->category->name : null;
        })->filter();

        return [
            'id' => $this->id,
            'email' => $this->email,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d'),
            'categories' => $categories,
        ];
    }
}
