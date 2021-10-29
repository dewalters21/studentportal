<?php


namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

/**
 * About controller
 *
 * PHP version 7.4
 */
class About extends Controller
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
            View::renderTemplate('/home/about.html', [
                'current_user' => $user['firstName']
            ]);
        } else {
            View::renderTemplate('/home/about.html');
        }
    }

}