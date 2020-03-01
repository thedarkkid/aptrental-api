<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\User;

class Apartment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::find($this->user_id);
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'floor_area_size' => $this->floor_area_size,
            'price_per_month' => $this->price_per_month,
            'number_of_rooms' => $this->number_of_rooms,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'address' => $this->address,
            'user_id' => $this->user_id,
            'author' => $user->name,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
