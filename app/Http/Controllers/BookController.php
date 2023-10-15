<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; //\BookpsRequest;
use App\Models\Book;
use App\Models\Bookshelf;
use App\Models\Category;
use App\Models\Series;
use App\Models\User;
use App\Http\Requests\BookpsRequest;
use Auth;
use Cloudinary;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function home (Request $request)
    {
        $user=Auth::user()->id;
         //検索フォームに入力された値を取得
        $series = $request->input('series');
        $category = $request->input('category');
        $keyword = $request->input('keyword');
        // dd($category,$series);
        $query = Book::query();
        //テーブル結合
        $query->join('series', function ($query) use ($request) {
            $query->on('books.series_id', '=', 'series.id');
            })->join('categories', function ($query) use ($request) {
            $query->on('books.category_id', '=', 'categories.id');
            });
            
        // dd($query->get());
        if(!empty($series)) {
            
            $query->where('series.name', 'LIKE', $series);
        }

        if(!empty($category)) {
            $query->where('categories.name', 'LIKE', $category);
        }

        if(!empty($keyword)) {
            $query->where('title', 'LIKE', "%{$keyword}%");
        }

        $books = $query->where('user_id', $user)->get();
        //dd($books);
        $series_list = Series::all();
        $categories_list = Category::all();

        return view('home', compact('books', 'keyword', 'series', 'category', 'series_list', 'categories_list'));
       
    }

    public function bookps(Book $book)//インポートしたBookをインスタンス化して$bookとして使用。
    {
        $user_id=Auth::user()->id;
        return view('books.bookps')->with(['books' => $book->getPaginateByLimit($user_id)]);//$bookの中身を戻り値にする。
    }//
     
    public function bookshow(Book $book)
    {
        $this->authorize('view', $book);
        return view('books.bookshow')->with(['book' => $book]);    
    }
    
    public function bookct(Book $book, Bookshelf $bookshelf,Category $category,Series $series)
    {
        // Auth::user()->categories()->get();
       // $user = auth()->user();
        $user_id=Auth::id();
        $books=Book::where('user_id', $user_id)->get();
        $bookshelves=collect([]);
        foreach($books as $book) 
        {
            $bookshelves->add($book->bookshelf);
        }
        $unique_bookshelves=$bookshelves->unique('id');
       
        $categories=Auth::user()->categories()->get();
        
        $series=Auth::user()->series()->get();
        
        return view('books.bookct')->with(['bookshelves' => $unique_bookshelves,'series_list' => $series,'categories' =>$categories]);
    }
    public function bookstore(BookpsRequest $request, Book $book,Bookshelf $bookshelf,Category $category,Series $series, User $user)
    {
        
        // dd($request);
        $input = $request['bookshelf'];
        if($request['bookshelf']['bookshelf_input_name'] == null ) { 
            $bookshelf_select_name = $request['bookshelf']['bookshelf_select_name'];
            $bookshelf = Bookshelf::where('name', $bookshelf_select_name)->first();
            $bookshelf_id = $bookshelf ->id;
        } else {
            $bookshelf_input_name = $request['bookshelf']['bookshelf_input_name'];
            if(DB::table('bookshelves')->where('name', $bookshelf_input_name)->doesntExist() ) {
                $input += ['name' => $bookshelf_input_name];
               $bookshelf_image_path = Cloudinary::upload($request->file('bookshelf_image_path')->getRealPath())->getSecurePath();
                $input += ['bookshelf_image_path' => $bookshelf_image_path];
                $bookshelf->fill($input)->save(); 
                // $bookshelf->save();
                
            } else {
                $bookshelf = Bookshelf::where('name', $bookshelf_input_name)->first();
            }
            $bookshelf_id = $bookshelf -> latest('id')->first()->id;
        } 
        // if($request['bookshelf']['bookshelf_input_name'] == null ) { 
        //     $bookshelf_select_name = $request['bookshelf']['bookshelf_select_name'];
        //     $bookshelf = Bookshelf::where('name', $bookshelf_select_name)->first();
            
        //     $bookshelf_id = $bookshelf ->id;
            
        // } else {
        //     $bookshelf_input_name = $request['bookshelf']['bookshelf_input_name'];
        //   // $bookshelf->name =$bookshelf_input_name;
        //     $input += ['name' => $bookshelf_input_name];
        //     $bookshelf_image_path = Cloudinary::upload($request->file('bookshelf_image_path')->getRealPath())->getSecurePath();
        //     $input += ['bookshelf_image_path' => $bookshelf_image_path];
        //     $bookshelf->fill($input)->save();
        //     $bookshelf_id = $bookshelf -> latest('id')->first()->id;
        // } 
        // dd($bookshelf);
        
        $input = $request['category'];
        if($request['category_input_name'] == null ) 
        { 
            $category_select_name = $request['category_select_name'];
            $category = Category::where('name', $category_select_name)->first();
        } else 
        {
            $category_input_name = $request['category_input_name'];
            // 
            if(DB::table('categories')->where('name', $category_input_name)->doesntExist() ) {
                $category->name =$category_input_name;
                $category->save();
                $user=Auth::user();
                $category->users()->attach($user->id);
                
            } else {
                $category = Category::where('name', $category_input_name)->first();
            }
        } 
        
        
        $input = $request['series'];
        if($request['series_input_name'] == null ) { 
            $series_select_name = $request['series_select_name'];
            $series = Series::where('name', $series_select_name)->first();
        } else {
            $series_input_name = $request['series_input_name'];
            if(DB::table('series')->where('name', $series_input_name)->doesntExist() ) {
                $series->name =$series_input_name;
                $series->save();
                $user=Auth::user();
                $series->users()->attach($user->id);
            } else {
                $series = Series::where('name', $series_input_name)->first();
            }
        } 
        
        $input = $request['book'];
        $front_cover_image_path = Cloudinary::upload($request->file('front_cover_image_path')->getRealPath())->getSecurePath();
        $input += ['front_cover_image_path' => $front_cover_image_path];
        $input['user_id'] = Auth::id();
        $input += ['bookshelf_id' => $bookshelf_id];
        $input['category_id'] = $category->id;
        $input['series_id'] = $series->id;
        $book->fill($input)->save();
        return redirect('/books/' . $book->id);    
        
    }
    public function bookedit(book $book,Bookshelf $bookshelf,Category $category,Series $series)
    {
        $categories=Auth::user()->categories()->get();
        
        $series=Auth::user()->series()->get();
        
        return view('books.bookedit')->with(['book' => $book,'bookshelves' => $bookshelf->get(),'categories' => $categories,'series_list' => $series]);
    }
    public function bookupdate(BookpsRequest $request, Book $book,Bookshelf $bookshelf,Category $category,Series $series, User $user)
    {
        // dd($request);
        $input = $request['bookshelf'];
        if($request['bookshelf']['bookshelf_input_name'] == null ) { 
            $bookshelf_select_name = $request['bookshelf']['bookshelf_select_name'];
            $bookshelf = Bookshelf::where('name', $bookshelf_select_name)->first();
            $bookshelf_id = $bookshelf ->id;
            
        } else {
            $bookshelf_input_name = $request['bookshelf']['bookshelf_input_name'];
           // $bookshelf->name =$bookshelf_input_name;
           
            if(DB::table('bookshelves')->where('name', $bookshelf_input_name)->doesntExist() ) {
                $input += ['name' => $bookshelf_input_name];
               $bookshelf_image_path = Cloudinary::upload($request->file('bookshelf_image_path')->getRealPath())->getSecurePath();
                $input += ['bookshelf_image_path' => $bookshelf_image_path];
                $bookshelf->fill($input)->save(); 
                // $bookshelf->save();
                
            } else {
                $bookshelf = Bookshelf::where('name', $bookshelf_input_name)->first();
            }
            // $bookshelf_image_path = Cloudinary::upload($request->file('bookshelf_image_path')->getRealPath())->getSecurePath();
            // $input += ['bookshelf_image_path' => $bookshelf_image_path];
            // $bookshelf->fill($input)->save();
            $bookshelf_id = $bookshelf -> latest('id')->first()->id;
        } 
        
        $input = $request['category'];
        if($request['category_input_name'] == null ) { 
            $category_select_name = $request['category_select_name'];
            $category = Category::where('name', $category_select_name)->first();
        } else {
            $category_input_name = $request['category_input_name'];
            
            $category_serach_id = Category::where('name',$category_input_name)->first(['id']);
            //dd($category_serach_id);
            if(DB::table('category_user')->where('category_id', $category_serach_id)->where('user_id',Auth::id())->doesntExist() ) {
                if(DB::table('categories')->where('name', $category_input_name)->doesntExist() ) {
                    $category->name =$category_input_name;
                    $category->save();
                    $user=Auth::user();
                    $category->users()->attach($user->id);
                } else {
                    $user=Auth::user();
                    $category = Category::where('name',$category_input_name)->first();
                    //dd($category);
                    $category->users()->attach($user->id);
                }
            } else {
                $category = Category::where('name', $category_input_name)->first();
            }
        } 
        
        $input = $request['series'];
        if($request['series_input_name'] == null ) { 
            $series_select_name = $request['series_select_name'];
            $series = Series::where('name', $series_select_name)->first();
        } else {
            $series_input_name = $request['series_input_name'];
            $series_serach_id = Series::where('name',$series_input_name)->first(['id']);
            
            if(DB::table('series_user')->where('series_id', $series_serach_id)->where('user_id',Auth::id())->doesntExist()) {
                if(DB::table('series')->where('name', $series_input_name)->doesntExist() ) {
                    $series->name =$series_input_name;
                    $series->save();
                    $user=Auth::user();
                    $series->users()->attach($user->id);
                } else {
                    $user=Auth::user();
                    $series = Series::where('name',$series_input_name)->first();
                    $series->users()->attach($user->id);
                }
            } else {
                $series = Series::where('name', $series_input_name)->first();
            }
        } 
        
        // $book->bookshelf_id = $bookshelf->id;
        // $book->category_id = $category->id;
        // $book->series_id = $series->id;
        // $input = $request['book'];
        // $book->fill($input)->save();
        // return redirect('/books/' . $book->id);
        
        $input = $request['book'];
        $front_cover_image_path = Cloudinary::upload($request->file('front_cover_image_path')->getRealPath())->getSecurePath();
        $input += ['front_cover_image_path' => $front_cover_image_path];
        $input['user_id'] = Auth::id();
        $input += ['bookshelf_id' => $bookshelf_id];
        $input['category_id'] = $category->id;
        $input['series_id'] = $series->id;
        $book->fill($input)->save();
        return redirect('/books/' . $book->id); 
    }
    public function bookdelete(Book $book)
    {
        $book->delete();
        return redirect('/');
    }

}