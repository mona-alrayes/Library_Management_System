<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\RatingResource;
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
            'author' => $this->author,
            'published_at' => $this->published_at,
            'description' => $this->description,
            'category_name'=>$this->category->name,
            'ratings' => RatingResource::collection($this->ratings ?: collect([])), // Ensure empty array if no ratings
            'average_rating' => $this->averageRating(),
        ];
    }
}
