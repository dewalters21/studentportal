<?php


namespace App\Controllers;


use App\Auth;
use App\Models\User;
use Core\Controller;
use Core\View;

/**
 * Login controller
 *
 * PHP version 7.4
 */
class Login extends Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('/home/login.html');
    }

    /**
     * Authenticate user
     *
     * @return void
     */
    public function authAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        if ($user) {
            Auth::login($user);
            View::renderTemplate('/home/index.html', [
                'current_user' => $_SESSION['username']
            ]);
        } else {
            $errors = ['Email address or password incorrect!'];
            View::renderTemplate('/home/login.html', [
                'errors' => $errors,
                'email' => $_POST['email']
            ]);
        }
    }

    /**
     * Log out a user
     *
     * @return void
     */
    public function destroyAction()
    {
        if (isset($_SESSION['user_id'])) {
            Auth::logout();
            $success = ['You have been logged out!'];
            View::renderTemplate('/home/login.html', [
                'success' => $success
            ]);
        } else {
            View::renderTemplate('/home/index.html');
        }
    }

}