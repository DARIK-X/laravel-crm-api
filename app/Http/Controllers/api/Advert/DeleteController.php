<?php

namespace App\Http\Controllers\api\Advert;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    //
    public function __invoke(Request $request){
        $advert = Advert::find($request->id);
        if(!$advert){
            return response()->json(["error"=> "The requested resource was not found"], 404);
        }

        if ($advert->user_id !== auth()->id()) {
            if ($advert->status != 'draft') {
                return response()->json(["error"=> "You do not have permission to perform this request"], 403);
            }
        }

        $advert->delete();
        return response()->json(null, 204);
    }
}
