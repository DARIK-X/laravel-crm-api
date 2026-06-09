<?php

namespace App\Http\Controllers\api\Advert;

use App\Http\Controllers\Controller;
use App\Models\Advert;
use Illuminate\Http\Request;

class UpdateStatusController extends Controller
{
    //
    public function __invoke(Request $request, $id)
    {
        $advert = Advert::find($id);

        if (!$advert) {
            return response()->json([
                "error" => "The requested resource was not found"
            ], 404);
        }

        $data = $request->validate([
            'status' => 'required|string|in:draft,moderation,published,declined,archived'
        ]);

        $newStatus = $data['status'];
        $user = $request->user();

        // Проверка прав
        $isAuthor = $advert->user_id === $user->id;
        $isModerator = $user->role === 'moderator';

        if (!$isAuthor && !$isModerator) {
            return response()->json([
                "error" => "You do not have permission to perform this request"
            ], 403);
        }

        // Логика переходов
        $currentStatus = $advert->status;
        $allowed = false;

        if ($isAuthor) {
            if ($currentStatus === 'draft' && $newStatus === 'moderation') {
                $allowed = true;
            }

            if ($currentStatus === 'published' && $newStatus === 'archived') {
                $allowed = true;
            }
        }

        if ($isModerator) {
            if ($currentStatus === 'moderation' && in_array($newStatus, ['published', 'declined'])) {
                $allowed = true;
            }

            if ($currentStatus === 'published' && $newStatus === 'archived') {
                $allowed = true;
            }
        }

        if (!$allowed) {
            return response()->json([
                "error" => "The request body is not valid"
            ], 422);
        }

        // Обновление
        $advert->status = $newStatus;
        $advert->save();

        return response()->json($advert, 200);
    }
}
