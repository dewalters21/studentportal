<?php

namespace App\Models;

use App\Config;
use PDOException;
use Core\Model;

class Course extends Model
{

    /**
     * Error arrays
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