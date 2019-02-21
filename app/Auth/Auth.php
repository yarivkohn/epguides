<?php
/**
 * Created by PhpStorm.
 * User: yariv
 * Date: 2/19/18
 * Time: 6:35 PM
 */

namespace Epguides\Auth;

use Epguides\Models\User;

class Auth
{

    public function user()
    {
        if(isset($_SESSION['user'])){
            return User::find($_SESSION['user']);
        }
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }

    public function attempt($email, $password)
    {
        $user = User::where('email', $email)->first();
        if(empty($user)){
            return false;
        }

        if(password_verify($password, $user->password)){
            $_SESSION['user'] = $user->id;
            $user->update([
                'last_login' => Date('Y-m-d H:i:s'),
                'user_ip' => ip2long($_SERVER['REMOTE_ADDR']),
            ]);
            return true;
        }

        return false;
    }

    public function logOut()
    {
        if(isset($_SESSION['user'])){
            unset($_SESSION['user']);
        }
    }
}