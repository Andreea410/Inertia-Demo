<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'deleted_at' => $this->deleted_at,
            'contacts' => $this->whenLoaded('contacts', function () {
                return ContactResource::collection($this->contacts);
            }),
            'contacts_count' => $this->when(isset($this->contacts_count), $this->contacts_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

