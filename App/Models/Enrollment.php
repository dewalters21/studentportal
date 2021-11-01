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

    /**
     * Get all courses
     * @return array $courses  Course object if found, false otherwise
     */
    public static function getCourses()
    {
        $courses = [];
        return $courses;
    }

    /**
     * Get course by Id
     * @param integer $id
     * @return object $course  Course object if found, false otherwise
     */
    public static function getCourseById(id)
    {
        return $course;
    }

    /**
     * Get enrolled courses by userId.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function getEnrolledCoursesByUserId(id)
    {
        return $result;
    }

    /**
     * Get waitlist courses by userId.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function getWaitlistCoursesByUserId(id)
    {
        return $result;
    }

    /**
     * Add user to course waitlist.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function addToWaitlist(id)
    {
        return $result;
    }

    /**
     * Remove user from course waitlist.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function removeFromWaitlist(id)
    {
        return $result;
    }

    /**
     * Notify waitlist for given course.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function notifyWaitlist(id)
    {
        return $result;
    }

    /**
     * Check waitlist for userId, courseId, termId, and yearId.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function dropCourse(id)
    {
        return $result;
    }

}