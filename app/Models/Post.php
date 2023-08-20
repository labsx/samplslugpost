<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Post 
{
    public $title;
    public $excerpt;
    public $date;
    public $body;
    public $slug;
    public $author;
     public function __construct($title,$excerpt,$date,$body,$slug, $author)
     {
        $this->title = $title;
        $this->excerpt = $excerpt;
        $this->date = $date;
        $this->body = $body;
        $this->slug = $slug;
        $this->author = $author;
     }


    public static function all(){

        return cache()->rememberForever('posts.all',  function (){
            return collect(File::files(resource_path("posts")))
            ->map(fn($file) => YamlFrontMatter::parseFile($file))
            ->map(fn($document) => new Post(
                $document->title,
                $document->excerpt,
                $document->date,
                $document->body(), //get posts resource using yamlfrontmatter
                $document->slug,
                $document->author
            ))
                ->sortByDesc('date');

        });
}

public static function find($slug){
        
    return static::all()->firstWhere('slug', $slug);
  
}

public static function findorFail($slug){
    
    $post = static::all()->firstWhere('slug', $slug);

    if(! $post){
        abort (404);
    }
    return  $post;

}
}
