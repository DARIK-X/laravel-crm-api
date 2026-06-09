<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertResource;
use App\Models\Advert;
use Illuminate\Http\Request;

class AdvertsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Начинаем запрос только с опубликованных объявлений
        $query = Advert::query()->where('status', 'published')
            ->with(['category', 'user'])->withCount('views'); // Жадная загрузка для оптимизации

        // 2. Фильтрация по категории
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 3. Фильтрация по цене
        if ($request->filled('price_from')) {
            $query->where('price', '>=', $request->price_from);
        }
        if ($request->filled('price_to')) {
            $query->where('price', '<=', $request->price_to);
        }

        // 4. Поиск по заголовку или описанию
        if ($request->filled('query')) {
            $searchTerm = $request->input('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('text', 'like', "%{$searchTerm}%");
            });
        }

        // 5. Сортировка
        $sortBy = $request->input('sort_by', 'date'); // по умолчанию дата
        $sortOrder = $request->input('sort', 'desc'); // по умолчанию desc

        $column = $sortBy === 'price' ? 'price' : 'created_at';
        $query->orderBy($column, $sortOrder);

        // 6. Пагинация
        $perPage = $request->integer('per_page', 10);
        if (!in_array($perPage, [10, 20, 30])) {
            $perPage = 10;
        }

        $adverts = $query->paginate($perPage);
        $collection = AdvertResource::collection($adverts);

        return response()->json($collection, 200);
    }
}
