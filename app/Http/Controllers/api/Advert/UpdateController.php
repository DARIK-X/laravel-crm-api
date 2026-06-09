<?php

namespace App\Http\Controllers\api\Advert;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    //
    public function __invoke(Request $request,$id){
        $advert = Advert::find($id);
        if (!$advert)return response()->json(["error" => "The requested resource was not found"],404);

        if ($advert->user_id !== auth()->id()){
            return response()->json(["error" => "You do not have permission to perform this request"],403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'sometimes|required|string|max:100',
            'text'        => 'sometimes|required|string|min:3',
            'price'       => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => "The request body is not valid"], 422);
        }

        $advert->update($validator->validated());
        $advert->load(['category', 'user'])->loadCount('views');
        $advert = AdvertResource::make($advert);

        return response()->json($advert, 200);


    }
}
