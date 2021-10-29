<?php

namespace App\Models;

use PDOException;
use Core\Model;

/**
 * User Model
 *
 * PHP version 7.4
 */
class User extends Model
{
    /**
     * Error messages
     * @var array
     */
    public array $errors = [];

    /**
     * Class Constructor
     * @param array $data  Initial property values (optional)
     * @return void
     */
    public function __construct($data = []) {
        foreach($data as $key=>$value) {
            $this->$key = $value;
        }
    }

    /**
     * Function Name:  create()
     * Task:           Create user registration information and save to the database
     * Arguments:
     * Returns:
     * @return mixed
     */
    public static function create()
    {
        global $errors;
        User::validate($_POST);
        if (empty($errors)) {
            $sql = "INSERT INTO tbluser (firstName, lastName, streetAddr, city, state, zipcode, email, homephone, cellphone, ssn, password) VALUES (:first_name, :last_name, :streetAddr, :city, :state, :zipcode, :email, :homephone, :cellphone, :ssn, :password)";
            $params = [
                ':first_name' => $_POST['first_name'],
                ':last_name' => $_POST['last_name'],
                ':streetAddr' => $_POST['address'],
                ':city' => $_POST['city'],
                ':state' => $_POST['state'],
                ':zipcode' => $_POST['zipcode'],
                ':email' => $_POST['email'],
                ':homephone' => preg_replace( '/[\W]/', '', $_POST['homephone']),
                ':cellphone' => preg_replace( '/[\W]/', '', $_POST['cellphone']),
                ':ssn' => Security::encrypt(preg_replace( '/[\W]/', '', $_POST['ssn'])),
                ':password' => Security::encrypt($_POST['password'])
            ];
            try {
                $db = static::connectToPdo();
                return static::executeQuery($db, $sql, $params);
            } catch (PDOException $e) {
                $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
                return false;
            }
        } else {
            return $errors;
        }

    }

    /**
     * Function name:  validate()
     * Task:           Validate user registration data
     * Arguments:
     * Returns:
     * @param array $data
     * @return void
     */
    public static function validate(array $data): void
    {
        global $errors;
        /** Name **/
        if (isset($data['first_name']) && $data['first_name'] == '') {
            $errors[] = 'First name is required';
        }
        if (isset($data['last_name']) && $data['last_name'] == '') {
            $errors[] = 'Last name is required';
        }
        /** Mailing Address **/
        if (isset($data['address']) && $data['address'] == '') {
            $errors[] = 'Mailing address is required';
        }
        /** Phone Number **/
        if (isset($data['phone']) && $data['phone'] == '') {
            $errors[] = 'Phone number is required';
        }
        if (isset($data['phone'])) {
            $nums_only = preg_replace("/[^\d]/", "", $data['phone']);
            if (strlen($nums_only) != 10) {
                $errors[] = 'Invalid US phone number.';
            }
        }
        /** Social Security Number **/
        if (isset($data['ssn']) && $data['ssn'] == '') {
            $errors[] = 'Social security number is required';
        }
        if (isset($data['ssn'])) {
            $numbers_only = preg_replace("/[^\d]/", "", $data['ssn']);
            if (strlen($numbers_only) != 9) {
                $errors[] = 'Invalid social security number';
            }
        }
        /** Salary **/
        if (isset($data['salary']) && $data['salary'] == '') {
            $errors[] = 'Salary is required';
        }
        if (isset($data['salary']) && !is_numeric($data['salary'])) {
            $errors[] = 'Not a valid salary.  Please enter a value in ###.## format.';
        }
        /** email address **/
        if (isset($data['email']) && $data['email'] == '') {
            $errors[] = 'Email address is required';
        }
        if (isset($data['email']) && (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false)) {
            $errors[] = 'Invalid email';
        }
        if (isset($data['email']) && (static::emailExists($data['email'], $data['id'] ?? null))) {
            $errors[] = 'Email address already exists';
        }
        /** Password **/
        if (isset($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors[] = 'Please enter at least 8 characters for the password';
            }
            if (preg_match('/.*[a-z]+.*/i', $data['password']) == 0) {
                $errors[] = 'Password needs at least one lowercase letter';
            }
            if (preg_match('/.*[A-Z]+.*/i', $data['password']) == 0) {
                $errors[] = 'Password needs at least one uppercase letter';
            }
            if (preg_match('/.*\d+.*/i', $data['password']) == 0) {
                $errors[] = 'Password needs at least one number';
            }
            if (preg_match('/.*[!@#$%^&*-]+.*/i', $data['password']) == 0) {
                $errors[] = 'Password needs at least one special character (!@#$%^&*-)';
            }
        }
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     * @param string|null $ignore_id Return false anyway if the record found has this ID
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists(string $email, string $ignore_id = null): bool
    {
        $user = static::findByEmail($email);
        if ($user) {
            if ($user['id'] != $ignore_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Find a user by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail(string $email)
    {
        global $errors;
        $sql = 'SELECT * FROM tbluser WHERE email = :email';
        try {
            $db = static::connectToPdo();
            $params = [':email' => $email];
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * //* Authenticate a user by email and password.
     * Authenticate a user by email and password. User account has to be active.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate(string $email, string $password)
    {
        $user = static::findByEmail($email);
        if ($user) {
            if (Security::decrypt($user['password']) == $password) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID(string $id)
    {
        global $errors;
        $sql = 'SELECT * FROM tbluser WHERE id = :id';
        try {
            $db = static::connectToPdo();
            $params = [':id' => $id];
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Update the user's profile
     *
     * @param array $data Data from the edit profile form
     *
     * @return mixed  True if the data was updated, $errors otherwise
     */
    public static function updateProfile(array $data)
    {
        global $errors;
        User::validate($data);
        if (empty($errors)) {
            $sql = 'UPDATE tbluser SET firstName = :first_name, lastName = :last_name, streetAddr = :address, city = :city, state = :state, zipcode = :zipcode, homephone = :homephone, cellphone = :cellphone, ssn = :ssn, email = :email WHERE id = :id';
            $params = [':first_name' => $data['first_name'],
                       ':last_name' => $data['last_name'],
                       ':address' => $data['address'],
                       ':city' => $data['city'],
                       ':state' => $data['state'],
                       ':zipcode' => $data['zipcode'],
                       ':homephone' => $data['homephone'],
                       ':cellphone' => $data['cellphone'],
                       ':ssn' => Security::encrypt($data['ssn']),
                       ':email' => $data['email'],
                       ':id' => $data['id']
                      ];
            try {
                $db = static::connectToPdo();
                return static::executeQuery($db, $sql, $params);
            } catch (PDOException $e) {
                $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
                return false;
            }
        } else {
            return $errors;
        }
    }

    /**
     * Update the user's password
     *
     * @param array $data Data from the change password form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public static function updatePassword(array $data): bool
    {
        global $errors;
        User::validate($data);
        if (empty($errors)) {
            $sql = 'UPDATE tbluser SET password = :password WHERE id = :id';
            if ((isset($data['password'])) && (isset($data['password_confirmation']))) {
                if ($data['password'] == $data['password_confirmation']) {
                    $password_hash = Security::encrypt($data['password']);
                    $params = [':password' => $password_hash,
                        ':id' => $data['id']
                    ];
                    try {
                        $db = static::connectToPdo();
                        return static::executeQuery($db, $sql, $params);
                    } catch (PDOException $e) {
                        $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Send $data to log file.
     *
     * @param string $data Data to be logged
     *
     * @return boolean  True if the data was logged, false otherwise
     */
    public static function sendToLog(string $data): bool
    {
        $data .= $data . PHP_EOL . "-----------------------" . PHP_EOL;
        if (file_put_contents('../logs/log_'.date("j.n.Y"), $data, FILE_APPEND)) {
            return true;
        } else {
            return false;
        }
    }

}