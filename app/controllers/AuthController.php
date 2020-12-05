<?php


namespace App\Controller;


use App\Core\App;
use App\Model\Auth;
use App\Model\User;
use App\Traits\Parsley;

class AuthController extends Controller
{
    use Parsley;

    public function login()
    {
        if( ! empty($this->validation()) ) {
            return $this->validation();
        }

        $user = User::find($this->post("email"), "email=:email");

        if( ! $user) {
            return $this->e400("Email/Password is wrong!");
        }

        if( ! password_verify($this->post("password"), $user->password)) {
            return $this->e400("Email/Password is wrong!");
        }

        $remember = !! $this->post("remember_me");

        App::get('session')->set('user_id', $user->getIdentifier());
        App::get('session')->set('logged_in', true);

        $userIdentifier = $user->getIdentifier();

        // Remove old Auth(s) if exists
        Auth::deleteALl($userIdentifier, 'user_id=:user_id');

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

        return $this->s200($user);
    }

    public function logout()
    {
        App::get('session')->unset('user_id');
        App::get('session')->unset('logged_in');
        App::get('session')->restart();
    }

//    public static function valid()
//    {
//        return App::get('session')->get('logged_in') === true;
//    }

    protected static function rules_for_login()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean'
        ];
    }
}