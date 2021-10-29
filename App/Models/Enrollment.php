<?php

namespace App\Models;

use PDOException;

use Core\Model;

/**
 * Enrollment Model
 *
 * PHP version 7.4
 */
class Enrollment extends Model
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


}