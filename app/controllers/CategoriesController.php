<?php
/**
 * Categories Controller
 */

namespace App\Controller;

use App\Model\Category;
use App\Traits\Parsley;

class CategoriesController extends Controller
{

    use Parsley;

    public function index()
    {
        $categories = Category::all();

        return $this->s200($categories);
    }

    public function delete(int $id)
    {
        $category = Category::find($id);

        if( ! $category) {
            return $this->e404("A cateogry with the id of ${id} was not found.");
        }

        $category->delete();
        return $this->s200();
    }

    public function create()
    {
        if( ! empty($this->validation()) ) {
            return $this->validation();
        }

        $category = new Category;

        $category->title = $this->post("title");

        $category->save();

        return $this->s201("created", $category);
    }

    protected static function rules_for_create()
    {
        return ['title' => 'required'];
    }

    protected static function rules_for_update()
    {
        return ['title' => 'required'];
    }
}
