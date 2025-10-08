<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'website' => $this->website,
            'employees_count' => $this->when(
                $this->relationLoaded('employees') || isset($this->employees_count),
                $this->employees_count ?? $this->employees()->count()
            ),
            'users_count' => $this->when(
                $this->relationLoaded('users'),
                fn() => $this->users->count()
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Include relationships if loaded
            'employees' => EmployeeResource::collection($this->whenLoaded('employees')),
            'users' => UserResource::collection($this->whenLoaded('users')),
        ];
    }
}

