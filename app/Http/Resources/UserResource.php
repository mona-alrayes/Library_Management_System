<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * 
 * Transforms the User model into a JSON format for API responses.
 * This resource is used to provide a consistent structure for user-related data.
 * 
 * @package App\Http\Resources
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * The request instance containing the current HTTP request data.
     * 
     * @return array<string, mixed>
     * An associative array representing the user resource, including user details and roles.
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name, 
            'email' => $this->email, 
            'created_at' => $this->created_at, 
            'updated_at' => $this->updated_at, 
            'roles' => $this->roles ? $this->roles->pluck('name')->toArray() : [], // List of role names assigned to the user, or an empty array if no roles are assigned
        ];
    }
}
