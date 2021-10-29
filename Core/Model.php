<?php

namespace Core;

use App\Config;
use App\Models\User;
use PDO;
use PDOException;

/**
 * Base model
 *
 * PHP version 7.4
 */
abstract class Model
{

    /******************************************************\
     * Function Name: 	connectToPdo()
     * Task: 		    Create connection to database
     *                  using PHP native PDO
     * Globals: 		all defined in config.php
     * Returns: 		PDO database connection
    \******************************************************/
    public static function connectToPdo(): PDO
    {
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_PERSISTENT => true
        ];
        if (extension_loaded('pdo_mysql')) {
            $connStr = Config::DB_TYPE.":host=".Config::DB_HOST.";dbname=".Config::DB_NAME;
            try {
                $dbLink = new PDO($connStr, Config::DB_USER, Config::DB_PASSWORD, $options);
            } catch (PDOException $e) {
                $errors[] = "ERROR : " . $e->getMessage() . " (" . $e->getCode() . ")";
                foreach($errors as $error) {
                    User::sendToLog(date('G:i:s').": ".$error.PHP_EOL);
                }

                View::renderTemplate('500.html', [
                    'errors' => $errors
                ]);
                exit(0);
            }
            return $dbLink;
        } else {
            echo("ERROR: PDO extension for mySQL not loaded!");
            die();
        }
    }

    /*****************************************************\
     * Function Name:  executeQuery($con, $sql)
     * Task:           Executes insert, update, or delete
     *                   queries against the database
     * Arguments:      $con - Database Connection
     *                 $sql - SQL Statement to execute
     * Returns:        boolean
    \*****************************************************/
    public static function executeQuery(PDO $con, string $sql, array $params=[]) {
        $actions = ["insert", "update", "delete"];
        $action = strtolower(substr($sql,0, 6 ));
        if (in_array($action, $actions)) {
            try {
                $stmt = $con->prepare($sql);
                $results = $stmt->execute($params);
            } catch (PDOException $e) {
                echo "ERROR : " . $e->getMessage() . " (" . $e->getCode() . ")<br>";
                return false;
            }
            return $results;
        } else {
            echo("Invalid SQL statement! Must be an insert, update, or delete query.");
            return false;
        }
    }

    /*****************************************************\
     * Function Name:  executeSelectQuery($con, $sql)
     * Task:           Executes select queries against
     *                   the database
     * Arguments:      $con - Database Connection
     *                 $sql - SQL Statement to execute
     * Returns:        Query result set or false
    \*****************************************************/
    public static function executeSelectQuery(PDO $con, string $sql, array $params=[]) {
        $action = strtolower(substr($sql,0, 6 ));
        if ($action == "select") {
            try {
                $stmt = $con->prepare($sql);
                $stmt->execute($params);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "ERROR : " . $e->getMessage() . " (" . $e->getCode() . ")<br>";
                return false;
            }
            if (!empty($result)) { $result = $result[0]; }
            return $result;
        } else {
            echo("Invalid SQL statement!");
            return false;
        }
    }

    /*****************************************************\
     * Function Name:  secured_encrypt($data)
     * Task:           Securely encrypts $data and returns
     *                   the encrypted value.
     * Arguments:      $data - Data to be encrypted
     * Returns:        Encrypted data
    \*****************************************************/
    public static function secured_encrypt($data) {
        $first_key = base64_decode(Config::FIRST_KEY);
        $second_key = base64_decode(Config::SECOND_KEY);

        $method = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);

        $first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

        return base64_encode($iv.$second_encrypted.$first_encrypted);;
    }

    /*****************************************************\
     * Function Name:  secured_decrypt($input)
     * Task:           Securely decrypts $input and returns
     *                   the decrypted value.
     * Arguments:      $input - Data to be decrypted
     * Returns:        Decrypted data
    \*****************************************************/
    public static function secured_decrypt($input) {
        $first_key = base64_decode(Config::FIRST_KEY);
        $second_key = base64_decode(Config::SECOND_KEY);
        $mix = base64_decode($input);

        $method = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($method);

        $iv = substr($mix,0,$iv_length);
        $second_encrypted = substr($mix,$iv_length,64);
        $first_encrypted = substr($mix,$iv_length+64);

        $data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
        $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

        if (hash_equals($second_encrypted,$second_encrypted_new))
            return $data;

        return false;
    }

}
