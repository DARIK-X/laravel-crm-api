<?php

namespace App\Http\Controllers\api\Advert;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    //
    public function __invoke(Request $request, $id){
        $advert = Advert::query()->where('id', $id)->
        with('user', 'category')->withCount('views')->first();
        if(!$advert){
            return response()->json([
                "error"=> "The requested resource was not found"
            ],404);
        }
        $collection = AdvertResource::make($advert);
        return response()->json($collection, 200);
    }
}
