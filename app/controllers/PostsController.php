<?php
/**
 * Posts Controller
 */

namespace App\Controller;

use App\Model\Post;
use App\Traits\Parsley;

class PostsController extends Controller
{

    use Parsley;

    public function index()
    {
        $posts = Post::all();

        return $this->s200($posts);
    }

    public function show(int $id)
    {
        $post = Post::find($id);

        if(! $post) {
            return $this->e404("A post with the id of ${id} was not found.");
        }

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
        $post->save();

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

        try {
            $post->save();
        } catch(\PDOException $e) {
            if($e->getCode() == 23000)
                return $this->e400("The author does not exist.");
            else
                throw $e;
        }

        return $this->s201($post);
    }

    protected static function rules_for_create()
    {
        return [
            'title' => 'required',
            'body' => 'required',
            'author' => 'required|numeric'
        ];
    }

    protected static function rules_for_update()
    {
        return [
            'title' => 'required',
            'body' => 'required'
        ];
    }
}
