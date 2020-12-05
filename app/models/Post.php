<?php
/**
 * Post Model
 */

namespace App\Model;

class Post extends Model
{
    protected static $table = "posts";

    protected $id;
    public $title;
    public $body;
    public $author;
    protected $created_at;
    protected $updated_at;

    public function bodyExpansion()
    {
        $this->body .= "lorem50";
    }

}
