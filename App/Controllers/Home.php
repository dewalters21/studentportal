<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

/**
 * Home controller
 *
 * PHP version 7.4
 */
class Home extends Controller
{

    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        //echo "(before) ";
        //return false;
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        //echo " (after)";
    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findById($_SESSION['user_id']);
            View::renderTemplate('/home/index.html', [
                'user' => $user,
                'current_user' => $user['firstName']
            ]);
        } else {
            View::renderTemplate('/home/index.html');
        }
    }
}
