<?php

namespace Database\Seeders;

use App\Http\Helpers\MainHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $now = Carbon::now();
        $mainHelper = new MainHelper();
        $data = $mainHelper->csv_to_array('users.csv');
        foreach ($data as &$user) {
            $user['password'] = preg_replace('/<hash\((.*?)\)>/', '$1', $user['password']);
            $user['password'] = Hash::make($user['password']);
            $user['created_at'] = $now;
            $user['updated_at'] = $now;
        }
        unset($user);
        User::query()->insert($data);


    }
}
