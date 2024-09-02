<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rating' => $this->rating,
            'review' => $this->review,
            'book_title' => $this->whenLoaded('book', function() {
                return $this->book->title; 
            }),
            'user_name' => $this->whenLoaded('user', function() {
                return $this->user->name;     
            }),
        ];
    }
}