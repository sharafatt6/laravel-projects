<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;

    protected $table = 'post_media';

    protected $fillable = ['post_id', 'file', 'file_name', 'file_type', 'file_extension'];

    public function post()  {
        return $this->belongsTo(Posts::class);

    }
}
