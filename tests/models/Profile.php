<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tests\Factories\ProfileFactory;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'test_user_profiles';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function newFactory()
    {
        return new ProfileFactory();
    }
}
