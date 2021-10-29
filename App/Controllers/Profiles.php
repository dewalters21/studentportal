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
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Edit employee profile page
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
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Update employee profile
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
                     'phone' => $_POST['phone'],
                     'ssn' => $_POST['ssn'],
                     'salary' => $_POST['salary'],
                     'email' => $_POST['email']
            ];
            $updateResult = User::updateProfile($data);
            if ($updateResult === true) {
                $newUser = User::findByID($data['id']);
                $newUser['SSN'] = Security::decrypt($newUser['SSN']);
                $newUser['password'] = Security::decrypt($newUser['password']);
                $success = ['Profile successfully updated!'];
                View::renderTemplate('/profiles/index.html', [
                    'success' => $success,
                    'user' => $newUser,
                    'current_user' => $newUser['firstName']
                ]);
            } else {
                $errors = $updateResult;
                array_push($errors,"Profile update failed!");
                View::renderTemplate('/profiles/editprofile.html', [
                    'errors' => $errors,
                    'user' => $data,
                    'current_user' => $data['first_name']
                ]);
            }
        } else {
            $errors = ['You are not logged in!'];
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Show change employee password page
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
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Change employee password
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
                $success = ['Password successfully updated!'];
                $user = User::findByID($_POST['id']);
                $phone = $user['phone'];
                $user['phone'] = "(".substr($phone, 0, 3).') '.substr($phone, 3, 3).'-'.substr($phone,6);
                $ssn = Security::decrypt($user['SSN']);
                $user['SSN'] = substr($ssn, 0, 3).'-'.substr($ssn, 3, 2).'-'.substr($ssn,5);
                $user['salary'] = number_format($user['salary'],2);
                $user['password'] = Security::decrypt($user['password']);
                View::renderTemplate('/profiles/index.html', [
                    'success' => $success,
                    'user' => $user,
                    'current_user' => $user['firstName']
                ]);
            } else {
                $errors = ['Password update failed!'];
                View::renderTemplate('/profiles/changepwd.html', [
                    'errors' => $errors,
                    'user' => $data
                ]);
            }
        } else {
            $errors = ['You are not logged in!'];
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

}