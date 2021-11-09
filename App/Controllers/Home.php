<?php

namespace App\Controllers;

use App\Auth;
use App\Config;
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
            $user = User::findById($_SESSION['user_id']);
            $message = "";
            if ($user[0]['notifyFlag'] == 1) {
                $notification = User::getNotifyMessage($user[0]['id']);
                foreach($notification AS $notifyMsg) {
                    if ($notifyMsg['viewed'] == 0) {
                        $message .= $notifyMsg['message']."\n";
                        User::setViewedFlag($notification[0]['id']);
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
            View::renderTemplate('/home/index.html');
        }
    }
}
