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
     * @param array $data Initial property values (optional)
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get all terms
     * @return array $terms  Terms object if found, false otherwise
     */
    public static function getTerms()
    {
        global $errors;
        $sql = "SELECT id, term FROM tblterm";
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get termId by term
     * @return array $termId  Term ID if found, false otherwise
     */
    public static function getTermId($term)
    {
        global $errors;
        $sql = "SELECT id FROM tblterm WHERE term = :term";
        $params = [':term' => $term];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get all years
     * @return array $years  Years object if found, false otherwise
     */
    public static function getYears()
    {
        global $errors;
        $sql = "SELECT id, year FROM tblyear";
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get yearId by year
     * @return array $yearId  Year ID if found, false otherwise
     */
    public static function getYearId($year)
    {
        global $errors;
        $sql = "SELECT id FROM tblyear WHERE year = :year";
        $params = [':year' => $year];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get all courses
     * @return array $courses  Course object if found, false otherwise
     */
    public static function getCourses()
    {
        global $errors;
        $sql = "SELECT id, courseName, courseDesc, courseNomenclature, courseMinSize, courseMaxSize, credits FROM tblcourse";
        $params = [];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get all available courses for user
     * @return array $courses  Course object if found, false otherwise
     */
    public static function getAvailableCourses($termId = 0, $yearId = 0)
    {
        global $errors;
        $sql = "SELECT c.id, c.courseName, c.courseDesc, c.courseNomenclature, c.courseMinSize, c.courseMaxSize, c.credits, t.id AS termId, t.term, y.id AS yearId, y.year FROM tblcourse c LEFT JOIN tblavailablecourses a ON c.id = a.courseId LEFT JOIN tblterm t ON t.id = a.termId LEFT JOIN tblyear y ON y.id = a.yearId WHERE";
        if ($termId != 0) {
            $sql .= " a.termId = :termId";
        }
        if ($termId != 0 && $yearId != 0) {
            $sql .= " AND";
        }
        if ($yearId != 0) {
            $sql .= " a.yearId = :yearId";
        }
        if ($termId != 0 || $yearId != 0) {
            $sql .= " AND";
        }
        $sql .= " c.id NOT IN (SELECT e.courseId FROM tblenrolled e WHERE e.userId = :userId)";
        $sql .= " ORDER BY a.yearId, a.termId";
        $params = [':userId' => $_SESSION['user_id']];
        if ($termId != 0) {
            $params = array_merge($params, array(':termId' => $termId));
        }
        if ($yearId != 0) {
            $params = array_merge($params, array(':yearId' => $yearId));
        }
        try {
            $db = static::connectToPdo();
            $results = static::executeSelectQuery($db, $sql, $params);
            for ($i = 0; $i < count($results); $i++) {
                if (static::checkFullCourse($results[$i]['id'], $results[$i]['termId'], $results[$i]['yearId']) === TRUE) { // If full add fullCourse flag
                    $results[$i]['fullCourse'] = 1;
                } else {
                    $results[$i]['fullCourse'] = 0;
                }
            }
            return $results;
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get course by Id
     * @param integer $id
     * @return array $course  Course object if found, false otherwise
     */
    public static function getCourseById($id)
    {
        global $errors;
        $sql = "SELECT id, courseName, courseDesc, courseNomenclature, courseMinSize, courseMaxSize, credits FROM tblcourse WHERE id = :id";
        $params = [':id' => $id];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get available course by Id
     * @param integer $id
     * @return array $availCourse  Course object if found, false otherwise
     */
    public static function getAvailableCourseById($id)
    {
        global $errors;
        $sql = "SELECT id, courseId, termId, yearId FROM tblavailablecourses WHERE id = :id";
        $params = [':id' => $id];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get enrolled courses by userId.
     * @param integer $id
     * @return array  Enrolled object if successful, false otherwise
     */
    public static function getEnrolledCoursesByUserId($id)
    {
        global $errors;
        $sql = "SELECT c.id, c.courseName, c.courseDesc, c.courseNomenclature, c.credits, e.id AS enrolledId, t.term, y.year FROM tblcourse c LEFT JOIN tblenrolled e ON c.id = e.courseId LEFT JOIN tblterm t ON e.termId = t.id LEFT JOIN tblyear y ON e.yearId = y.id WHERE e.userId = :id ORDER BY e.yearId, e.termId";
        $params = [':id' => $id];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get enrolled course by enrolledId.
     * @param integer $id
     * @return array  Enrolled object if successful, false otherwise
     */
    public static function getEnrolledCourseById($id)
    {
        global $errors;
        $sql = "SELECT id, userId, courseId, termId, yearId FROM tblenrolled WHERE id = :id";
        $params = [':id' => $id];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Enroll user in course based on course id.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function enrollCourse($userId, $courseId)
    {
        global $errors;
        // Check to see if user is already enrolled.
        $availCourse = static::getAvailableCourseById($courseId);
        if (static::checkEnrolled($userId, $courseId, $availCourse[0]['termId'], $availCourse[0]['yearId']) === TRUE) { // User is already enrolled
            $errors[] = 'You are already enrolled in that class.';
            return TRUE;
        } else {
            $sql = "INSERT INTO tblenrolled (userId, courseId, termId, yearId) VALUES (:userId, :courseId, :termId, :yearId)";
            $params = [
                ':userId' => $userId,
                ':courseId' => $courseId,
                ':termId' => $availCourse[0]['termId'],
                ':yearId' => $availCourse[0]['yearId']
            ];
            try {
                $db = static::connectToPdo();
                return static::executeQuery($db, $sql, $params);
            } catch (PDOException $e) {
                $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
                return false;
            }
        }
    }

    /**
     * Check to see if user is already enrolled in course.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public
    static function checkEnrolled($userId, $courseId, $termId, $yearId)
    {
        global $errors;
        $sql = "SELECT * FROM tblenrolled WHERE userId = :userId AND courseId = :courseId AND termId = :termId AND yearId = :yearId";
        $params = [':userId' => $userId,
            ':courseId' => $courseId,
            ':termId' => $termId,
            ':yearId' => $yearId
        ];
        try {
            $db = static::connectToPdo();
            $result = static::executeSelectQuery($db, $sql, $params);
            if (sizeof($result) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Check to see if the course is already full.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function checkFullCourse($courseId, $termId, $yearId)
    {
        global $errors;
        $course = Enrollment::getCourseById($courseId);
        $courseMaxSize = $course[0]['courseMaxSize'];
        $sql = "SELECT * FROM tblenrolled WHERE courseId = :courseId AND termId = :termId AND yearId = :yearId";
        $params = [
            ':courseId' => $courseId,
            ':termId' => $termId,
            ':yearId' => $yearId
        ];
        try {
            $db = static::connectToPdo();
            $result = static::executeSelectQuery($db, $sql, $params);
            if (sizeof($result) >= $courseMaxSize) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Check to see if user is already enrolled in course.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function checkWaitlist($userId, $courseId, $termId, $yearId)
    {
        global $errors;
        $sql = "SELECT * FROM tblwaitlist WHERE userId = :userId AND availCourseId = :courseId AND termId = :termId AND yearId = :yearId";
        $params = [':userId' => $userId,
            ':courseId' => $courseId,
            ':termId' => $termId,
            ':yearId' => $yearId
        ];
        try {
            $db = static::connectToPdo();
            $result = static::executeSelectQuery($db, $sql, $params);
            if (!empty($result) && count($result) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Drop course from user's enrolled list.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public
    static function dropCourse($id)
    {
        global $errors;
        $enrolledCourse = static::getEnrolledCourseById($id);
        if (!empty($enrolledCourse)) {
            // Check to see if user is already enrolled.
            if (static::checkEnrolled($_SESSION['user_id'], $enrolledCourse[0]['courseId'], $enrolledCourse[0]['termId'], $enrolledCourse[0]['yearId']) === TRUE) { // User is already enrolled
                $sql = "DELETE FROM tblenrolled WHERE id = :enrolledId";
                $params = [':enrolledId' => $id];
                try {
                    $db = static::connectToPdo();
                    $result = static::executeQuery($db, $sql, $params);
                    if ($result === TRUE) {
                        // Check to see if anyone is on the wait list for this class.
                        $waitlist = static::getWaitlistCoursesByCourseId($enrolledCourse[0]['courseId']);
                        if (!empty($waitlist)) {
                            $firstItem = count($waitlist) - 1;
                            if (static::checkWaitlist($waitlist[$firstItem]['userId'], $enrolledCourse[0]['courseId'], $enrolledCourse[0]['termId'], $enrolledCourse[0]['yearId']) === TRUE) {
                                $courseInfo = static::getCourseById($enrolledCourse[0]['courseId']);
                                if (static::enrollCourse($waitlist[$firstItem]['userId'], $enrolledCourse[0]['courseId']) === FALSE) {
                                    $errors[] = "Failed to enroll in " . $courseInfo[0]['courseNomenclature'] . " " . $courseInfo[0]['courseName'] . ". Contact registrar office.";
                                    return FALSE;
                                } else {
                                    $success[] = "Successfully enrolled in " . $courseInfo[0]['courseNomenclature'] . " " . $courseInfo[0]['courseName'] . ".";
                                    if (static::removeFromWaitlist($waitlist[$firstItem]['id']) === FALSE) {
                                        $errors[] = "Failed to remove from wait list for " . $courseInfo[0]['courseNomenclature'] . " " . $courseInfo[0]['courseName'] . ". Contact registrar office.";
                                        return FALSE;
                                    } else {
                                        if (static::notifyWaitlist($waitlist[$firstItem]['userId'], $enrolledCourse[0]['courseId']) === FALSE) {
                                            $errors[] = "Failed to send notification to wait list student #" . $waitlist[$firstItem]['userId'] . ".";
                                            return FALSE;
                                        } else {
                                            return static::setNotifyFlag($waitlist[$firstItem]['userId']);
                                        }
                                    }
                                }
                            } else {
                                return FALSE;
                            }
                        } else {
                            return TRUE;
                        }
                    } else {
                        return FALSE;
                    }
                } catch (PDOException $e) {
                    $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
                    return false;
                }
            } else {
                $errors[] = 'You are not enrolled in that class.';
                return FALSE;
            }
        } else {
            $errors[] = 'You are not enrolled in that class.';
            return FALSE;
        }
    }

    /**
     * Get waitlist courses by userId.
     * @param integer $id
     * @return array  True if successful, false otherwise
     */
    public static function getWaitlistCoursesByUserId($id)
    {
        global $errors;
        $sql = "SELECT c.id, c.courseName, c.courseDesc, c.courseNomenclature, c.credits, w.id AS waitlistId, w.waitlistOrder, t.term, y.year 
                  FROM tblcourse c
                  LEFT JOIN tblwaitlist w ON c.id = w.availCourseId
                  LEFT JOIN tblavailablecourses a ON c.id = a.courseId
                  LEFT JOIN tblterm t ON a.termId = t.id 
                  LEFT JOIN tblyear y ON a.yearId = y.id 
                  WHERE w.userId = :id";
        $params = [':id' => $id];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Get waitlist courses by courseId.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public static function getWaitlistCoursesByCourseId($id)
    {
        global $errors;
        $sql = "SELECT c.id, c.courseName, c.courseDesc, c.courseNomenclature, w.id AS waitlistId, w.userId, t.id AS termId, t.term, y.id AS yearId, y.year 
                  FROM tblcourse c
                  LEFT JOIN tblwaitlist w ON c.id = w.availCourseId
                  LEFT JOIN tblavailablecourses a ON c.id = a.courseId
                  LEFT JOIN tblterm t ON a.termId = t.id 
                  LEFT JOIN tblyear y ON a.yearId = y.id 
                  WHERE w.availCourseId = :id
                  ORDER BY waitlistOrder DESC";
        $params = [':id' => $id];
        try {
            $db = static::connectToPdo();
            return static::executeSelectQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Add user to course waitlist.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public
    static function addToWaitlist($id)
    {
        global $errors;
        $userId = $_SESSION['user_id'];
        $availCourse = static::getAvailableCourseById($id);
        $waitlist = static::getWaitlistCoursesByCourseId($id);
        if (!empty($waitlist)) {
            $waitlistOrder = $waitlist[0]['waitlistOrder'] + 1;
        } else {
            $waitlistOrder = 1;
        }
        // Check to see if user is already enrolled.
        if (static::checkWaitList($userId, $id, $availCourse[0]['termId'], $availCourse[0]['yearId']) === TRUE) { // User is already on the waitlist
            $errors[] = 'You are already on the waitlist for that class.';
            return TRUE;
        } else {
            $sql = "INSERT INTO tblwaitlist (userId, availCourseId, termId, yearId, waitlistOrder) VALUES (:userId, :courseId, :termId, :yearId, :waitlistOrder)";
            $params = [
                ':userId' => $userId,
                ':courseId' => $id,
                ':termId' => $availCourse[0]['termId'],
                ':yearId' => $availCourse[0]['yearId'],
                ':waitlistOrder' => $waitlistOrder
            ];
            try {
                $db = static::connectToPdo();
                return static::executeQuery($db, $sql, $params);
            } catch (PDOException $e) {
                $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
                return false;
            }
        }
    }

    /**
     * Remove user from course waitlist.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public
    static function removeFromWaitlist($id)
    {
        global $errors;
        $waitlistCourse = static::getWaitlistCoursesByCourseId($id);
        if (!empty($waitlistCourse)) {
            // Check to see if user is already on the wait list.
            if (static::checkWaitlist($waitlistCourse[0]['userId'], $waitlistCourse[0]['id'], $waitlistCourse[0]['termId'], $waitlistCourse[0]['yearId']) === TRUE) { // User is on the wait list
                $sql = "DELETE FROM tblwaitlist WHERE id = :waitlistId";
                $params = [':waitlistId' => $waitlistCourse[0]['waitlistId']];
                try {
                    $db = static::connectToPdo();
                    return static::executeQuery($db, $sql, $params);
                } catch (PDOException $e) {
                    $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
                    return false;
                }
            } else {
                $errors[] = 'You are not on the wait list for that class.';
                return FALSE;
            }
        } else {
            $errors[] = 'You are not on the wait list for that class.';
            return FALSE;
        }
    }

    /**
     * Notify waitlist for given course.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public
    static function notifyWaitlist($userId, $courseId)
    {
        global $errors;
        $course = static::getCourseById($courseId);
        $message = "You have been moved from the wait list to enrolled in ".$course[0]['courseNomenclature']." ".$course[0]['courseName']."!";
        $sql = "INSERT INTO tblnotifications (userId, message) VALUES (:userId, :message)";
        $params = [':userId' => $userId,
                   ':message' => $message
                  ];
        try {
            $db = static::connectToPdo();
            return static::executeQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

    /**
     * Set notify flag for given user ID.
     * @param integer $id
     * @return boolean  True if successful, false otherwise
     */
    public
    static function setNotifyFlag($userId)
    {
        global $errors;
        $sql = "UPDATE tbluser SET notifyFlag = 1 WHERE id = :userId";
        $params = [':userId' => $userId ];
        try {
            $db = static::connectToPdo();
            return static::executeQuery($db, $sql, $params);
        } catch (PDOException $e) {
            $errors[] = "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")";
            return false;
        }
    }

}