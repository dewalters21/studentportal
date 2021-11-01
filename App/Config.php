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
    const DB_NAME = 'studentportal';  // Change this before deploying to production.

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'spuser';  // Change this before deploying to production.

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'Password1';  // Change this before deploying to production.

    /**
     * Encryption Keys
     */
    const FIRST_KEY ='0Tj3K3o9pIBEHG5IdyyPQxoUHEKgSea59D7aav3UiFk=';  // Change this before deploying to production.
    const SECOND_KEY = 'saEFWJI44L/7tfeTRZYYFKUSU3FlNf8AwEIB2B0BpyXGmCODo8RLAUiFpx8+LNJUjZoaGohGghiHo0xSDil6Rw==';  // Change this before deploying to production.

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /** Public variables and arrays
     * @var int
     * @var string
     * @var array
     */
    public array $area_codes = [201,202,203,205,206,207,208,209,210,212,213,214,215,216,217,218,219,224,225,228,229,231,234,239,240,248,251,252,253,254,256,260,262,267,269,270,276,278,281,283,301,302,303,304,305,307,308,309,310,312,313,314,315,316,317,318,319,320,321,323,325,330,331,334,336,337,339,340,341,347,351,352,360,361,369,380,385,386,401,402,404,405,406,407,408,409,410,412,413,414,415,417,419,423,424,425,430,432,434,435,440,442,443,464,469,470,475,478,479,480,484,501,502,503,504,505,507,508,509,510,512,513,515,516,517,518,520,530,539,540,541,551,557,559,561,562,563,564,567,570,571,573,574,575,580,585,586,601,602,603,605,606,607,608,609,610,612,614,615,616,617,618,619,620,623,626,627,628,630,631,636,641,646,650,651,657,660,661,662,669,670,671,678,679,681,682,689,701,702,703,704,706,707,708,712,713,714,715,716,717,718,719,720,724,727,731,732,734,737,740,747,754,757,760,762,763,764,765,769,770,772,773,774,775,779,781,785,786,787,801,802,803,804,805,806,808,810,812,813,814,815,816,817,818,828,830,831,832,835,843,845,847,848,850,856,857,858,859,860,862,863,864,865,870,872,878,901,903,904,906,907,908,909,910,912,913,914,915,916,917,918,919,920,925,927,928,931,935,936,937,939,940,941,947,949,951,952,954,956,957,959,970,971,972,973,975,978,979,980,984,985,989];
    public array $states = [
        ['abbr' => 'AL', 'name' => 'Alabama'],
        ['abbr' => 'AK', 'name' => 'Alaska'],
        ['abbr' => 'AZ', 'name' => 'Arizona'],
        ['abbr' => 'AR', 'name' => 'Arkansas'],
        ['abbr' => 'CA', 'name' => 'California'],
        ['abbr' => 'CO', 'name' => 'Colorado'],
        ['abbr' => 'CT', 'name' => 'Connecticut'],
        ['abbr' => 'DE', 'name' => 'Delaware'],
        ['abbr' => 'FL', 'name' => 'Florida'],
        ['abbr' => 'GA', 'name' => 'Georgia'],
        ['abbr' => 'HI', 'name' => 'Hawaii'],
        ['abbr' => 'ID', 'name' => 'Idaho'],
        ['abbr' => 'IL', 'name' => 'Illinois'],
        ['abbr' => 'IN', 'name' => 'Indiana'],
        ['abbr' => 'IA', 'name' => 'Iowa'],
        ['abbr' => 'KS', 'name' => 'Kansas'],
        ['abbr' => 'KY', 'name' => 'Kentucky'],
        ['abbr' => 'LA', 'name' => 'Louisiana'],
        ['abbr' => 'ME', 'name' => 'Maine'],
        ['abbr' => 'MD', 'name' => 'Maryland'],
        ['abbr' => 'MA', 'name' => 'Massachusetts'],
        ['abbr' => 'MI', 'name' => 'Michigan'],
        ['abbr' => 'MN', 'name' => 'Minnesota'],
        ['abbr' => 'MS', 'name' => 'Mississippi'],
        ['abbr' => 'MO', 'name' => 'Missouri'],
        ['abbr' => 'MT', 'name' => 'Montana'],
        ['abbr' => 'NE', 'name' => 'Nebraska'],
        ['abbr' => 'NV', 'name' => 'Nevada'],
        ['abbr' => 'NH', 'name' => 'New Hampshire'],
        ['abbr' => 'NJ', 'name' => 'New Jersey'],
        ['abbr' => 'NM', 'name' => 'New Mexico'],
        ['abbr' => 'NY', 'name' => 'New York'],
        ['abbr' => 'NC', 'name' => 'North Carolina'],
        ['abbr' => 'ND', 'name' => 'North Dakota'],
        ['abbr' => 'OH', 'name' => 'Ohio'],
        ['abbr' => 'OK', 'name' => 'Oklahoma'],
        ['abbr' => 'OR', 'name' => 'Oregon'],
        ['abbr' => 'PA', 'name' => 'Pennsylvania'],
        ['abbr' => 'RI', 'name' => 'Rhode Island'],
        ['abbr' => 'SC', 'name' => 'South Carolina'],
        ['abbr' => 'SD', 'name' => 'South Dakota'],
        ['abbr' => 'TN', 'name' => 'Tennessee'],
        ['abbr' => 'TX', 'name' => 'Texas'],
        ['abbr' => 'UT', 'name' => 'Utah'],
        ['abbr' => 'VT', 'name' => 'Vermont'],
        ['abbr' => 'VA', 'name' => 'Virginia'],
        ['abbr' => 'WA', 'name' => 'Washington'],
        ['abbr' => 'WV', 'name' => 'West Virginia'],
        ['abbr' => 'WI', 'name' => 'Wisconsin'],
        ['abbr' => 'WY', 'name' => 'Wyoming'],
        ['abbr' => 'DC', 'name' => 'District of Columbia'],
        ['abbr' => 'AS', 'name' => 'American Samoa'],
        ['abbr' => 'GU', 'name' => 'Guam'],
        ['abbr' => 'MP', 'name' => 'Northern Mariana Islands'],
        ['abbr' => 'PR', 'name' => 'Puerto Rico'],
        ['abbr' => 'VI', 'name' => 'U.S. Virgin Islands']
    ];
}
