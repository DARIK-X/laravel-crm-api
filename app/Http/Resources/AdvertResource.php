<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdvertResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return [
            "id"=>$this->id,
            "status" =>$this->status,
            "title"=>$this->title,
            "text"=>$this->text,
            "price"=>$this->price,
            "views"=>$this->views_count,
            "category_id"=>new AdvertCategoryResource($this->category),
            "user_id"=>new AdvertUserResource($this->user),

            "photos"=>collect($this->photos)->map(function($photo){
                return asset('storage/images/'.$photo);
            }),

            "created_at"=>$this->created_at
        ];
    }

}
