<?php

namespace App\Http\Controllers\api\Advert;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    //

    public function __invoke(Request $request){
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:100',
            'text'        => 'required|string|min:3',
            'price'       => 'required|integer|min:0',
            'category_id' => 'required|integer|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                "error" => "The request body is not valid",
            ], 422);
        }
        $data = $validator->validated();
        $photosName = [];


        if($request->hasFile('photos')){
            foreach ($request->photos as $photo) {
                $name = time() . '_' . $photo->getClientOriginalName();
                $photo->storeAs('images', $name, 'public');
                $photosName[] = $name;
            }
        }else{
            $photosName = ["advert-1.jpg","advert-2.jpg","advert-3.jpg","advert-4.jpg"];
        }


        $advert = Advert::create([
            "title" => $data["title"],
            "text" => $data["text"],
            "price" => $data["price"],
            "category_id" => $data["category_id"],
            "photos" => $photosName,
            "user_id" => auth()->id(),
            "status" => "draft",
        ]);

        $advert->load(['category', 'user'])->loadCount('views');
        $advert = AdvertResource::make($advert);

        return response()->json($advert, 200);
    }
}
