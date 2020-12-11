<?php
/**
 * Base Controller
 */

namespace App\Controller;

use App\Traits\Parsley;
use App\Traits\Responses;

abstract class Controller
{
    use Responses;
    use Parsley;

    protected function post($property)
    {
        return @$this->validationParams[$property];
    }


    protected function validation($csrf = true)
    {
        if($csrf) {
            if(!isset($_SERVER['HTTP_AUTHORIZATION'])){
                throw new \Exception('No CSRF token found!');
            }

            if(hash_equals(@$_SERVER['HTTP_AUTHORIZATION'], @$_SESSION['csrf_token']) === false){
                throw new \Exception('CSRF token mismatch!');
            }
        }

        $methodName = "rules_for_" . debug_backtrace()[1]['function'];
        $rules = call_user_func_array(array(get_called_class(), $methodName), []);

        $run = $this->ParsleyRun($rules);
        if( ! $run )
            return $this->e422($this->firstOfAllError);
    }
}