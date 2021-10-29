<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.4
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database type
     * @var string
     */
    const DB_TYPE = 'mysql';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'studentportal';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'spuser';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'Password1';

    /**
     * Encryption Keys
     */
    const FIRST_KEY ='0Tj3K3o9pIBEHG5IdyyPQxoUHEKgSea59D7aav3UiFk=';
    const SECOND_KEY = 'saEFWJI44L/7tfeTRZYYFKUSU3FlNf8AwEIB2B0BpyXGmCODo8RLAUiFpx8+LNJUjZoaGohGghiHo0xSDil6Rw==';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

}
