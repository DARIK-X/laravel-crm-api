<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    //
    public function getUser(Request $request){
        $user = $request->user();
        return response()->json([
            "id" => $user->id,
            "name"=>$user->name,
            "email"=>$user->email,
            "phone"=>$user->phone,
            "role"=>$user->role,
            "created_at"=>$user->created_at,
            "updated_at"=>$user->updated_at,
        ]);
    }

    public function updateUser(Request $request){
        $data = $request->validate([
            "name"=>"sometimes|string|max:255",
            "email"=>[
                "sometimes","string","email","max:255",
                Rule::unique('users')->ignore($request->user()->id),
            ],
            "phone"=>[
                "sometimes", "string", "max:255",
                Rule::unique("users")->ignore($request->user()->id),
            ],
        ]);
        $request->user()->update($data);
        $user = $request->user();
        return response()->json([
            "id" => $user->id,
            "name"=>$user->name,
            "email"=>$user->email,
            "phone"=>$user->phone,
            "role"=>$user->role,
            "created_at"=>$user->created_at,
            "updated_at"=>$user->updated_at,
        ]);
    }

    public function getAdverts(Request $request){
        $query = $request->user()->adverts();
        $status = $request->query('status', 'published');
        $draftStatus = ['draft', 'moderation', 'declined', 'published', 'archived'];

        if(!in_array($status, $draftStatus)){
            $status = 'published';
        }

        $adverts = $query->where('status', $status)
            ->with(['user', 'category'])
            ->withCount('views')
            ->latest()
            ->get();
        $adverts = AdvertResource::collection($adverts);


        return response()->json(
            $adverts
        , 200);
    }
}
