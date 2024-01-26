<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageActivity extends Model
{
    use HasFactory;

    protected $guarded = ['id'], $table = 'image_activity';
}
