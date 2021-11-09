<?php

namespace App\Controllers;

use App\Auth;
use App\Config;
use Core\View;
use Core\Controller;
use App\Models\User;
use App\Models\Enrollment;

/**
 * Enroll controller
 *
 * PHP version 7.4
 */
class Enroll extends Controller
{

    /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
        if (isset($_SESSION['last_action'])) { // Test to make sure the "last action" session variable was set.
            $secondsInactive = time() - $_SESSION['last_action']; // How many seconds have passed since user's last action.
            $expireAfterSeconds = Config::EXPIRE_AFTER * 60;
            if ($secondsInactive >= $expireAfterSeconds) {
                if (isset($_SESSION['user_id'])) {
                    Auth::logout();
                    $success = ['You have been logged out!'];
                    View::renderTemplate('/home/login.html', [
                        'success' => $success
                    ]);
                    exit(0);
                } else {
                    View::renderTemplate('/home/index.html');
                    exit(0);
                }
            }
        }
    }

    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        $_SESSION['last_action'] = time();
    }

    /**
     * Show the student enrollment page
     *
     * @return void
     */
    public function indexAction()
    {
        if (isset($_SESSION['user_id'])) {
            $user = User::findByID($_SESSION['user_id']);
            $terms = Enrollment::getTerms();
            $years = Enrollment::getYears();
            $courses = Enrollment::getAvailableCourses();
            $enrolled = Enrollment::getEnrolledCoursesByUserId($_SESSION['user_id']);
            $waitlist = Enrollment::getWaitlistCoursesByUserId($_SESSION['user_id']);
            View::renderTemplate('enroll/index.html', [
                'user' => $user[0],
                'current_user' => $user[0]['firstName'],
                'terms' => $terms,
                'years' => $years,
                'enrolled' => $enrolled,
                'waitlist' => $waitlist,
                'courses' => $courses
            ]);
        } else {
            $errors = ['You are not logged in!'];
            View::renderTemplate('/home/index.html', [
                'errors' => $errors
            ]);
        }
    }

    /**
     * Set Term per selected on Enroll page then return.
     *
     * @return void
     */
    public function settermyrAction()
    {
        global $errors;
        $user = User::findByID($_SESSION['user_id']);
        $terms = Enrollment::getTerms();
        $years = Enrollment::getYears();
        $enrolled = Enrollment::getEnrolledCoursesByUserId($_SESSION['user_id']);
        $waitlist = Enrollment::getWaitlistCoursesByUserId($_SESSION['user_id']);
        if (isset($_POST['term']) || isset($_POST['year'])) {
            $currTerm = (isset($_POST['term'])) ? $_POST['term'] : 0;
            $currYear = (isset($_POST['year'])) ? $_POST['year'] : 0;
            $courses = Enrollment::getAvailableCourses($currTerm, $currYear);
            View::renderTemplate('enroll/index.html', [
                'user' => $user[0],
                'current_user' => $user[0]['firstName'],
                'terms' => $terms,
                'currTerm' => $currTerm,
                'currYear' => $currYear,
                'years' => $years,
                'enrolled' => $enrolled,
                'waitlist' => $waitlist,
                'courses' => $courses
            ]);
        } else {
            $errors = ['No term or year selected!'];
            $courses = Enrollment::getAvailableCourses();
            View::renderTemplate('enroll/index.html', [
                'errors' => $errors,
                'user' => $user[0],
                'current_user' => $user[0]['firstName'],
                'terms' => $terms,
                'years' => $years,
                'enrolled' => $enrolled,
                'waitlist' => $waitlist,
                'courses' => $courses
            ]);
        }
    }

    /**
     * Enroll user in class.
     *
     * @return void
     */
    public function enrollAction()
    {
        global $errors;
        global $success;
        $userId = $_SESSION['user_id'];
        $user = User::findByID($userId);
        $terms = Enrollment::getTerms();
        $years = Enrollment::getYears();
        $courseId = $_POST['courseId'];
        $courseInfo = Enrollment::getCourseById($courseId);
        if ($_POST['fullCourse'] == 1) {
            if (Enrollment::addToWaitlist($_POST['courseId']) === FALSE) {
                $errors[] = "Failed to add to wait list for ".$courseInfo[0]['courseNomenclature']." ".$courseInfo[0]['courseName'].". Contact registrar office.";
            } else {
                $success[] = "Successfully added to wait list for ".$courseInfo[0]['courseNomenclature']." ".$courseInfo[0]['courseName'].".";
            }
        } else {
            if (Enrollment::enrollCourse($userId,$courseId) === FALSE) {
                $errors[] = "Failed to enroll in ".$courseInfo[0]['courseNomenclature']." ".$courseInfo[0]['courseName'].". Contact registrar office.";
            } else {
                $success[] = "Successfully enrolled in ".$courseInfo[0]['courseNomenclature']." ".$courseInfo[0]['courseName'].".";
            }
        }
        $enrolledCourses = Enrollment::getEnrolledCoursesByUserId($userId);
        $waitlist = Enrollment::getWaitlistCoursesByUserId($userId);
        $courses = Enrollment::getAvailableCourses();
        View::renderTemplate('enroll/index.html', [
            'errors' => $errors,
            'success' => $success,
            'user' => $user[0],
            'current_user' => $user[0]['firstName'],
            'terms' => $terms,
            'years' => $years,
            'enrolled' => $enrolledCourses,
            'waitlist' => $waitlist,
            'courses' => $courses
        ]);
    }

    /**
     * Drop user from class.
     *
     * @return void
     */
    public function dropAction()
    {
        global $errors;
        global $success;
        $enrolledId = $_POST['enrolledId'];
        $courseNomen = $_POST['courseNomen'];
        $courseName= $_POST['courseName'];
        $courseTerm = $_POST['courseTerm'];
        $courseYear = $_POST['courseYear'];
        if (Enrollment::dropCourse($enrolledId) === TRUE) {
            $success[] = $courseNomen." ".$courseName." for ".$courseTerm." ".$courseYear." successfully dropped.";
        } else {
            $errors[] = "Unable to drop ".$courseNomen." ".$courseName." for ".$courseTerm." ".$courseYear.".  Contact registrar office.";
        }
        $user = User::findByID($_SESSION['user_id']);
        $terms = Enrollment::getTerms();
        $years = Enrollment::getYears();
        $courses = Enrollment::getAvailableCourses();
        $enrolled = Enrollment::getEnrolledCoursesByUserId($_SESSION['user_id']);
        $waitlist = Enrollment::getWaitlistCoursesByUserId($_SESSION['user_id']);
        View::renderTemplate('enroll/index.html', [
            'errors' => $errors,
            'success' => $success,
            'user' => $user[0],
            'current_user' => $user[0]['firstName'],
            'terms' => $terms,
            'years' => $years,
            'enrolled' => $enrolled,
            'waitlist' => $waitlist,
            'courses' => $courses
        ]);
    }

}