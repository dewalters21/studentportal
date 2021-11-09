<?php


namespace App\Controllers;

use App\Auth;
use App\Config;
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
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        if (isset($_SESSION['last_action'])) { // Test to make sure the "last action" session variable was set.
            $secondsInactive = time() - $_SESSION['last_action']; // How many seconds have passed since user's last action.
            $expireAfterSeconds = Config::EXPIRE_AFTER * 60;
            if ($secondsInactive >= $expireAfterSeconds) {
                if (isset($_SESSION['user_id'])) {
                    Auth::logout();
                    $success = ['You have been logged out!'];
                    View::renderTemplate('/home/login.html', [
                        'success' => $success
                    ]);
                    exit(0);
                } else {
                    View::renderTemplate('/home/index.html');
                    exit(0);
                }
            }
        }
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        $_SESSION['last_action'] = time();
    }

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
                'current_user' => $user[0]['firstName']
            ]);
        } else {
            View::renderTemplate('/home/contact.html');
        }
    }

}