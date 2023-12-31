<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    public function getPaginateByLimit(int $limit_count = 6)
    {
    // updated_atで降順に並べたあと、limitで件数制限をかける
        return $this->orderBy('updated_at', 'DESC')->paginate($limit_count);
    }
    protected $fillable = [
    'user_id',
    'book_title',
    'author',
    'front_cover_image_path',
    'category_id',
    'series_id',
    'blog_title',
    'blog_body'
    ];
    
    public function user()   
    {
        return $this->belogsTo(User::class);  
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function series()
    {
        return $this->belongsTo(Series::class);
    }
    public function blog_likes()
    {
        return $this->hasMany('App\Models\BlogLike');
    }
    // Viewで使う、いいねされているかを判定するメソッド。
    public function isLikedBy($user): bool {
        return BlogLike::where('user_id', $user->id)->where('blog_id', $this->id)->first() !==null;
    }
}
