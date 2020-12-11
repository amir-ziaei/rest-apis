<?php
/**
 * Category Model
 */

namespace App\Model;

class Category extends Model
{
    protected static $table = "categories";

    protected $id;
    public $title;
    protected $created_at;
    protected $updated_at;
}
