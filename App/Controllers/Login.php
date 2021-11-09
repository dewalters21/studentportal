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
        if ($_POST) {
            $user = User::authenticate($_POST['email'], $_POST['password']);
            if ($user) {
                Auth::login($user[0]);
                if ($user[0]['notifyFlag'] == 1) {
                    $notification = User::getNotifyMessage($user[0]['id']);
                    foreach($notification AS $notifyMsg) {
                        if ($notifyMsg['viewed'] == 0) {
                            $message .= $notifyMsg['message']."\n";
                            User::setViewedFlag($notifyMsg['id']);
                            User::resetNotifyFlag($user[0]['id']);
                        } else {
                            $message = "";
                        }
                    }
                } else {
                    $message = "";
                }
                View::renderTemplate('/home/index.html', [
                    'user' => $user[0],
                    'current_user' => $user[0]['firstName'],
                    'message' => $message
                ]);
            } else {
                $errors = ['Email address or password incorrect!'];
                View::renderTemplate('/home/login.html', [
                    'errors' => $errors,
                    'email' => $_POST['email']
                ]);
            }
        } else {
            View::renderTemplate('/home/login.html');
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