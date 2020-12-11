<?php
/**
 * User Model
 */

namespace App\Model;

class User extends Model
{
    protected static $table = "users";

    protected $id;
    public $name;
    public $email;
    public $password;
    protected $created_at;
    protected $updated_at;
}
