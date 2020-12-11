<?php
/**
 * Posts Controller
 */

namespace App\Controller;

use App\Model\Auth;
use App\Model\Post;
use App\Model\User;
use App\Traits\Parsley;

class PostsController extends Controller
{

    use Parsley;

    public function index()
    {
        if(in_array(@$_GET['order'], ["asc", "desc"]))
            $order = $_GET['order'];
        else $order = "desc";

        $year = (int) @$_GET['year'];
        $cats = safe(str_replace(" ", "", @$_GET['cat']));
        $search = safe(trim(@$_GET['search']));

        $posts = Post::allWithFilters($search, $order, $year, $cats);

        foreach ($posts as $post) {
            $post->author = User::find($post->author);
            $post->categories = $post->getCategories();
        }

        return $this->s200($posts);
    }

    public function show(string $slug)
    {
        $post = Post::find(safe($slug), "slug=:slug");

        if(! $post) {
            return $this->e404("Not post associated with this slug was found.");
        }

        $post->author = User::find($post->author);
        $post->categories = $post->getCategories();

        return $this->s200($post);
    }

    public function update(int $id)
    {
        $post = Post::find($id);

        if(! $post) {
            return $this->e404("A post with the id of ${id} was not found.");
        }

        if( ! empty($this->validation()) ) {
            return $this->validation();
        }

        $post->title = $this->post("title");
        $post->body = $this->post("body");
        $post->slug = $this->post("slug");
        try {
            $post->save();
        } catch(\PDOException $e) {
            if($e->getCode() == 23000) {
                return $this->e422("The slug is not available.");
            } else
                throw $e;
        }

        $cats = explode(",", $this->post("categories"));
        $rows = [];
        foreach ($cats as $cat) {
            $rows[] = array($id, $cat);
        }

        $post->setCategories($id, $rows);

        return $this->s200($post);
    }

    public function delete(int $id)
    {
        $post = Post::find($id);

        if( ! $post) {
            return $this->e404("A post with the id of ${id} was not found.");
        }

        $post->delete();
        return $this->s200();
    }

    public function create()
    {
        if( ! empty($this->validation()) ) {
            return $this->validation();
        }


        $post = new Post;

        $post->title = $this->post("title");
        $post->body = $this->post("body");
        $post->author = $this->post("author");
        $post->slug = $this->post("slug");

        try {
            $post->save();
        } catch(\PDOException $e) {
            if($e->getCode() == 23000) {
                if(strpos( $e->getMessage(),"slug") !== false)
                    return $this->e422("The slug is not available.");
                else
                    return $this->e422("The author does not exist.");
            } else
                throw $e;
        }

        $cats = explode(",", $this->post("categories"));
        $rows = [];
        foreach ($cats as $cat) {
            $rows[] = array($post->getIdentifier(), $cat);
        }

        $post->setCategories($post->getIdentifier(), $rows);

        return $this->s201("created", $post);
    }

    public function generate()
    {
        $txtToWrite = "";
        $posts = Post::all();

        $now = time();
        $dir = "public/reports";
        $fileName = "posts_{$now}.txt";
        $path = "{$dir}/{$fileName}";
        $file = fopen($path, 'w');

        $txtToWrite .= "||----------------------------------- Posts -----------------------------------||\n";
        $txtToWrite .= "Reported to: ". @Auth::getName()."\n";
        $txtToWrite .= "At: ". date("Y/m/d H:i:s")."\n";
        $txtToWrite .= "||-----------------------------------------------------------------------------||\n";

        foreach ($posts as $post) {
            $post->author = User::find($post->author);
            $txtToWrite .= "Title: " . $post->title . "\n";
            $txtToWrite .= "Body: " . wordwrap($post->body) . "\n";
            $txtToWrite .= "Author: " . $post->author->name . "\n";
            $txtToWrite .= "---------------------------------------------------------------------------------\n";
        }

        fwrite($file, $txtToWrite);
        fclose($file);

        return $this->s200($fileName);
    }

    protected static function rules_for_create()
    {
        return [
            'title' => 'required',
            'body' => 'required',
            'author' => 'required|numeric',
            'slug' => 'required|alpha_dash'
        ];
    }

    protected static function rules_for_update()
    {
        return [
            'title' => 'required',
            'body' => 'required',
            'slug' => 'required|alpha_dash'
        ];
    }
}
