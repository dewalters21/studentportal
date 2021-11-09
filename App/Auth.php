<?php

namespace App;

use App\Models\User;

/**
 * Authentication
 *
 * PHP version 7.4
 */
class Auth
{
    /**
     * Login the user
     *
     * @param array $user The user model
     * @param boolean $remember_me Remember the login if true
     *
     * @return void
     */
    public static function login(array $user)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['firstName'];
        $_SESSION['last_action'] = time();
        User::sendToLog("User " . $user['email'] . " logged in.");
    }

    /**
     * Logout the user
     *
     * @return void
     */
    public static function logout()
    {
        $thisuser = User::findByID($_SESSION['user_id']);
        // Unset all of the session variables
        $_SESSION = [];
        // Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        // Finally destroy the session
        session_destroy();
        User::sendToLog("User " . $thisuser[0]['email'] . " logged out." . PHP_EOL . "=========================");
    }

    /**
     * Get the current logged-in user, from the session or the remember-me cookie
     *
     * @return mixed The user model or null if not logged in
     */
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            return User::findByID($_SESSION['user_id']);
        } else {
            return false;
        }
    }

}
