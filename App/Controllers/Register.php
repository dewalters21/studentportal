<?php

namespace App\Controllers;

use Core\View;
use Core\Controller;
use App\Models\User;

/**
 * Register controller
 *
 * PHP version 7.4
 */
class Register extends Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('/users/register.html');
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function createAction()
    {
        $curr_user = $_POST;
        $user = new User($curr_user);
        $result = $user->create();
        if ($result === true) {
            $success = ['Registration successful!'];
            User::sendToLog(date('G:i:s').": Registration successful for ".$_POST['email'].".".PHP_EOL);
            View::renderTemplate('/home/login.html', [
                'success' => $success
            ]);
        } else {
            User::sendToLog(date('G:i:s').": Registration failed for ".$_POST['email'].": ".$result[0].PHP_EOL);
            View::renderTemplate('/users/register.html', [
                'errors' => $result,
                'user' => $curr_user
            ]);
        }
    }

}