<?php
/**
 * Users Controller
 */

namespace App\Controller;

use App\Model\User;

class UsersController extends Controller
{

    public function index()
    {
        $users = User::all();

        return $this->s200($users);
    }

    public function show(int $id)
    {
        $user = User::find($id);

        if(! $user) {
            return $this->e404("A user with the id of ${id} was not found.");
        }

        return $this->s200($user);
    }

    public function update(int $id)
    {
        $user = User::find($id);

        if(! $user) {
            return $this->e404("A user with the id of ${id} was not found.");
        }

        if( ! empty($this->validation()) ) {
            return $this->validation();
        }

        $user->email = $this->post("email");
        if($this->post("password")) {
            $user->password = password_hash($this->post("password"), PASSWORD_BCRYPT);
        }
        $user->name = $this->post("name");
        $user->save();

        return $this->s200($user);
    }

    public function delete(int $id)
    {
        $user = User::find($id);

        if( ! $user) {
            return $this->e404("A post user the id of ${id} was not found.");
        }

        $user->delete();

        (new AuthController())->logout();

        return $this->s200();
    }

    public function create()
    {
        if( ! empty($this->validation()) ) {
            return $this->validation();
        }

        $user = new User;

        $user->email = $this->post("email");
        $user->password = password_hash($this->post("password"), PASSWORD_BCRYPT);
        $user->name = $this->post("name");

        try {
            $user->save();
        } catch(\PDOException $e) {
            if($e->getCode() == 23000)
                return $this->e400("Email address is not available.");
            else
                throw $e;
        }

        return $this->s201();
    }

    protected static function rules_for_create()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'name' => 'required|alpha_spaces'
        ];
    }

    protected static function rules_for_update()
    {
        return [
            'email' => 'required|email',
            'password' => 'min:6',
            'name' => 'required|alpha_spaces'
        ];
    }
}