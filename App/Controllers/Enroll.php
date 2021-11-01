<?php

namespace App\Controllers;

use Core\View;
use Core\Controller;
use App\Models\User;
use App\Models\Security;

/**
 * Enroll controller
 *
 * PHP version 7.4
 */
class Enroll extends Controller
{

    /**
     * Show the student enrollment page
     *
     * @return void
     */
    public function indexAction()
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findByID($_SESSION['user_id']);
            View::renderTemplate('enroll/index.html', [
            'user' => $user,
            'current_user' => $user['firstName']
        ]);
        } else {
            $errors = ['You are not logged in!'];
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

}