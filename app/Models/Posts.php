<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Posts extends Model
{
    use HasFactory;

   protected $fillable = ['title', 'content', 'user_id', 'slug', 'image', 'file_name', 'file_extension'];

   public static function getUserPosts (){
    return self::where('user_id', Auth::id())->get();
   }

   public static function getUserPostsByApi (){
    return self::where('user_id', auth('api')->user()->id)->paginate(5);
   }

   public function user() {
    return $this->belongsTo(User::class);
   }

   public function comments() {
    return $this->hasMany(Comments::class, 'post_id');
   }

   public function media(){
    return $this->hasMany(PostMedia::class, 'post_id');
   }
}
