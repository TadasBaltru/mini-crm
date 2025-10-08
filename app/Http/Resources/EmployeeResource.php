<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_id' => $this->company_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Include company relationship if loaded
            'company' => $this->when(
                $this->relationLoaded('company'),
                fn() => [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'email' => $this->company->email,
                    'website' => $this->company->website,
                ]
            ),
        ];
    }
}

