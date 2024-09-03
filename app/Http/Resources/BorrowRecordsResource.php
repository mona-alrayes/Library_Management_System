<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BorrowRecordsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'book_title' => $this->whenLoaded('book', function() {
                return $this->book->title; 
            }),
            'user_name' => $this->whenLoaded('user', function() {
                return $this->user->name;  }),   
            'borrowed_at'=>$this->borrowed_at->format('Y-m-d H:i:s'),
            'returned_at'=>$this->returned_at ? $this->returned_at->format('Y-m-d H:i:s') : null,
            'due_date' => $this->due_date ? $this->due_date->format('Y-m-d H:i:s') : null,
        ];
    }
}
