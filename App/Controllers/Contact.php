<?php


namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

/**
 * Contact controller
 *
 * PHP version 7.4
 */
class Contact extends Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findByID($_SESSION['user_id']);
            View::renderTemplate('/home/contact.html', [
                'current_user' => $user['firstName']
            ]);
        } else {
            View::renderTemplate('/home/contact.html');
        }
    }

}