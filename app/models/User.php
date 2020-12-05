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
    public $is_admin = 0;
    protected $created_at;
    protected $updated_at;

    public function promote()
    {
        $this->is_admin = true;
    }
}
