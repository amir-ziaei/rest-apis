<?php


namespace App\Controller;


use App\Core\App;
use App\Model\Auth;
use App\Model\User;

class AuthController extends Controller
{

    public function register()
    {
        if( ! empty($this->validation(false)) ) {
            return $this->validation(false);
        }

        $user = new User;

        $user->email = $this->post("email");
        $user->password = password_hash($this->post("password"), PASSWORD_BCRYPT);
        $user->name = $this->post("name");

        try {
            $user->save();
        } catch(\PDOException $e) {
            if($e->getCode() == 23000)
                return $this->e409("Email address is already taken.");
            else
                throw $e;
        }

        return $this->s201("Register successful.");
    }

    public function login()
    {
        if( ! empty($this->validation(false)) ) {
            return $this->validation(false);
        }

        $user = User::find($this->post("email"), "email=:email");

        if( ! $user) {
            return $this->e401("Email/Password is wrong!");
        }

        if( ! password_verify($this->post("password"), $user->password)) {
            return $this->e401("Email/Password is wrong!");
        }

        $remember = !! $this->post("remember");

        $csrf_token = bin2hex(openssl_random_pseudo_bytes(24));

        App::get('session')->set('user_id', $user->getIdentifier());
        App::get('session')->set('logged_in', true);
        App::get('session')->set('csrf_token', $csrf_token);

        $userIdentifier = $user->getIdentifier();

        // Remove old Auth(s) if exists
        Auth::deleteAll($userIdentifier, 'user_id=:user_id');

        if($remember) {
            // Generate a fresh Auth
            $token = password_hash(random_hash(), PASSWORD_DEFAULT);
            $auth = new Auth;
            $auth->token = $token;
            $auth->user_id = $userIdentifier;
            $expTime = App::get('config')['cookies']['expiry'][App::get('cookie')::DEFAULT_EXP_TIME];
            $auth->setExpiration($expTime);
            $auth->save();

            App::get('cookie')::set('user_id', $userIdentifier);
            App::get('cookie')::set('token', $token);
        }

        $user = array_filter($user->get_object_vars(), function ($key) {
            return  ! in_array($key,['password','db']);
        }, ARRAY_FILTER_USE_KEY);

        return $this->s200(["csrf_token" => $csrf_token, "user" => $user]);
    }

    public function logout()
    {
        App::get('session')->unset('user_id');
        App::get('session')->unset('logged_in');
        App::get('session')->unset('token');
        App::get('session')->restart();

        App::get('cookie')::delete('user_id');
        App::get('cookie')::delete('token');

        return $this->s200();
    }

    protected static function rules_for_register()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'name' => 'required|alpha_spaces'
        ];
    }

    protected static function rules_for_login()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'boolean'
        ];
    }
}