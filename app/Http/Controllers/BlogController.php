<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    public function blogps(Blog $blog)//インポートしたBlogをインスタンス化して$blogとして使用。
{
    return $blog->get();//$blogの中身を戻り値にする。
}//
}
