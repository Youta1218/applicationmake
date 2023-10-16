<x-app-layout>
    <x-slot name="header">
        BLOG
    </x-slot>
     <div class='my-4'>
         <div class='grid grid-cols-3 gap-4 content-stretch place-self-stretch place-items-center'>
            @foreach ($blogs as $blog)
                <div class="p-12 mx-10 bg-white shadow-sm sm:rounded-lg ">
                    <div class='mb-4'>
                        <h2 class='blog_title'><a href="/blogs/{{ $blog->id }}">{{ $blog->blog_title }}</a></h2>
                    </div>
                    <h3><表紙></h3>
                    <p class=''><img class='h-48 border-8 border-indigo-600' src="{{ $blog->front_cover_image_path }}" alt="画像が読み込めません。"/></p>
                    <h4 class='book_title'><題名>{{ $blog->book_title }}</h4>
    
                    <h3></h3> 
                    <p class='author'><作者>{{ $blog->author }}</p>
                </div>    
            @endforeach
        </div>    
    </div>
    {{ $blogs->links() }}
    
</x-app-layout>