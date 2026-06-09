<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    //
    protected $guarded = [];
    protected $casts = [
        'photos' => 'array',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function views(){
        return $this->hasMany(View::class);
    }
}
