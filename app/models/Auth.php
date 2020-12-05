<?php
/**
 * Auth Model
 */

namespace App\Model;

use App\Core\App;
use DateInterval;
use DateTime;

class Auth extends Model
{
    protected static $table = "auth";

    protected $id;
    public $user_id;
    public $token;
    public $expires_at;
    protected $created_at;
    protected $updated_at;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set(App::get('config')['timezone']);
    }

    public function is_expired()
    {
        $date = new DateTime($this->expires_at);
        $expires_at = $date->getTimestamp();
        return time() >= $expires_at;
    }

    public function setExpiration(string $expires_in)
    {
        $date = new DateTime;
        $date->add(DateInterval::createFromDateString($expires_in));

        $this->expires_at = $date->format('Y-m-d H:i:s');
        return $this;
    }
}
