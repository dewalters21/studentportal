<?php

namespace App\Models;

use Core\Model;

/**
 * Security Model
 *
 * PHP version 7.4
 */
class Security extends Model
{

    /**
     * Securely encrypts $data and returns the encrypted value.
     *
     * @param $data
     * @return string
     */
    public static function encrypt($data) {
        return static::secured_encrypt($data);
    }

    /**
     * Securely decrypts $input and returns the decrypted value.
     *
     * @param $input
     * @return false|string
     */
    public static function decrypt($input) {
        return static::secured_decrypt($input);
    }

}