<?php

namespace App\Controllers;

use Core\View;
use Core\Controller;
use App\Models\User;
use App\Models\Security;

/**
 * Profiles controller
 *
 * PHP version 7.4
 */
class Profiles extends Controller
{

    /**
     * Show the student profile page
     *
     * @return void
     */
    public function indexAction()
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findByID($_SESSION['user_id']);
            $homephone = $user['homephone'];
            $cellphone = $user['cellphone'];
            $user['homephone'] = "(".substr($homephone, 0, 3).') '.substr($homephone, 3, 3).'-'.substr($homephone,6);
            $user['cellphone'] = "(".substr($cellphone, 0, 3).') '.substr($cellphone, 3, 3).'-'.substr($cellphone,6);
            $ssn = Security::decrypt($user['ssn']);
            $user['ssn'] = substr($ssn, 0, 3).'-'.substr($ssn, 3, 2).'-'.substr($ssn,5);
            $user['password'] = Security::decrypt($user['password']);
            View::renderTemplate('profiles/index.html', [
                'user' => $user,
                'current_user' => $user['firstName']
            ]);
        } else {
            $errors = ['You are not logged in!'];
            User::sendToLog('Unauthorized access to student profile page!  User not logged in!');
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Edit student profile page
     *
     * @return void
     */
    public function editAction()
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findByID($_SESSION['user_id']);
            $user['ssn'] = Security::decrypt($user['ssn']);
            View::renderTemplate('/profiles/editprofile.html', [
                'user' => $user,
                'current_user' => $user['firstName']
            ]);
        } else {
            $errors = ['You are not logged in!'];
            User::sendToLog('Unauthorized attempt to edit student profile!  User not logged in!');
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Update student profile
     *
     * @return void
     */
    public function updateAction()
    {
        if ((isset($_SESSION['user_id'])) && ($_SESSION['user_id'] == $_POST['id'])) {
            $data = ['id' => $_POST['id'],
                     'first_name' => $_POST['first_name'],
                     'last_name' => $_POST['last_name'],
                     'address' => $_POST['address'],
                     'city' => $_POST['city'],
                     'state' => $_POST['state'],
                     'zipcode' => $_POST['zipcode'],
                     'homephone' => $_POST['homephone'],
                     'cellphone' => $_POST['cellphone'],
                     'ssn' => $_POST['ssn'],
                     'email' => $_POST['email']
            ];
            $updateResult = User::updateProfile($data);
            if ($updateResult === true) {
                $newUser = User::findByID($data['id']);
                $newUser['ssn'] = Security::decrypt($newUser['ssn']);
                $newUser['password'] = Security::decrypt($newUser['password']);
                $success = ['Profile successfully updated!'];
                User::sendToLog('Profile successfully updated for user '.$newUser['email'].'! ');
                View::renderTemplate('/profiles/index.html', [
                    'success' => $success,
                    'user' => $newUser,
                    'current_user' => $newUser['firstName']
                ]);
            } else {
                $errors = $updateResult;
                array_push($errors,'Profile update failed for user '.$data['email'].'! ');
                User::sendToLog(implode(" ", $errors));
                View::renderTemplate('/profiles/editprofile.html', [
                    'errors' => $errors,
                    'user' => $data,
                    'current_user' => $data['first_name']
                ]);
            }
        } else {
            $errors = ['You are not logged in!'];
            User::sendToLog('Unauthorized attempt to update student profile! User not logged in!');
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Show change student password page
     *
     * @return void
     */
    public function changepwAction()
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findByID($_SESSION['user_id']);
            View::renderTemplate('/profiles/changepwd.html', [
				'id' => $user['id'],
                'firstName' => $user['firstName'],
				'lastName' => $user['lastName'],
                'current_user' => $user['firstName']
            ]);
        } else {
            $errors = ['You are not logged in!'];
            User::sendToLog('Unauthorized access to change student password page! User not logged in!');
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Change student password
     *
     * @return void
     */
    public function chgpwdAction()
    {
        if ((isset($_SESSION['user_id'])) && ($_SESSION['user_id'] == $_POST['id'])) {
            $data = ['id' => $_POST['id'],
                     'password' => $_POST['password'],
                     'password_confirmation' => $_POST['password_confirmation']
                    ];
            if (User::updatePassword($data)) {
                $user = User::findByID($_POST['id']);
                $homephone = $user['homephone'];
                $cellphone = $user['cellphone'];
                $user['homephone'] = "(".substr($homephone, 0, 3).') '.substr($homephone, 3, 3).'-'.substr($homephone,6);
                $user['cellphone'] = "(".substr($homephone, 0, 3).') '.substr($cellphone, 3, 3).'-'.substr($cellphone,6);
                $ssn = Security::decrypt($user['ssn']);
                $user['ssn'] = substr($ssn, 0, 3).'-'.substr($ssn, 3, 2).'-'.substr($ssn,5);
                $user['password'] = Security::decrypt($user['password']);
                $success = ['Password successfully updated!'];
                User::sendToLog('Password successfully updated for user '.$user['email'].'!');
                View::renderTemplate('/profiles/index.html', [
                    'success' => $success,
                    'user' => $user,
                    'current_user' => $user['firstName']
                ]);
            } else {
                $errors = ['Password update failed for user '.$data['id'].'!'];
                User::sendToLog(implode(" ", $errors));
                View::renderTemplate('/profiles/changepwd.html', [
                    'errors' => $errors,
                    'user' => $data
                ]);
            }
        } else {
            $errors = ['You are not logged in!'];
            User::sendToLog(implode(" ", 'Unauthorized attempt to change student password!  User not logged in!'));
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

}