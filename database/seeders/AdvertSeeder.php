<?php

namespace Database\Seeders;

use App\Http\Helpers\MainHelper;
use App\Models\Advert;
use App\Models\Top;
use App\Models\User;
use App\Models\View;
use App\Models\Vip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdvertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $MainHelper = new MainHelper();
        $data = $MainHelper->csv_to_array('adverts.csv');

        foreach ($data as $csvLine) { // Используем $csvLine, чтобы не путать с моделью
            $user = User::where('email', $csvLine['author'])->first();
            if (!$user) continue;

            $categoryId = str_replace(['C', 'c', 'С', 'с'], '', $csvLine['category']);
            $photoRaw = stripcslashes($csvLine['photos'] ?? '[]');
            $photosArray = json_decode($photoRaw, true) ?: [];

            // Создаем объявление
            $newAdvert = Advert::create([
                'title'       => $csvLine['title'],
                'text'        => $csvLine['text'], // Проверьте, что в БД именно 'text'
                'photos'      => $photosArray, // Если в модели есть casts array, json_encode не нужен
                'category_id' => $categoryId,
                'status'      => $csvLine['status'],
                'price'       => $csvLine['price'],
                'user_id'     => $user->id,
            ]);

            // Платные услуги
            $services = stripcslashes($csvLine['paid_services']) ?:[];
            if (is_array($services)) {
                foreach ($services as $service) {
                    if ($service === 'top') Top::create(['advert_id' => $newAdvert->id]);
                    if ($service === 'vip') Vip::create(['advert_id' => $newAdvert->id]);
                }
            }

            // Просмотры - берем значение прямо из текущей строки CSV
            $viewsCount = (int)($csvLine['views_count'] ?? 0);
            if ($viewsCount > 0) {
                $viewsData = array_fill(0, $viewsCount, [
                    'advert_id'  => $newAdvert->id,
                    'user_id'    => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach (array_chunk($viewsData, 500) as $chunk) {
                    View::insert($chunk);
                }
            }
        }
    }

}
