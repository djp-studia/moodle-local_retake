<?php
/**
* Local Retake course cleanser class
*
* @package    local_retake
* @copyright  2022 Agung Pratama
* @license    MIT
*/

namespace local_retake;

defined('MOODLE_INTERNAL') || die();

class Cleanser 
{
    /**
    * fungsi untuk menghapus seluruh activity completion record dari database berdasarkan ID Course dan User.
    * @param   int $courseId Course ID
    * @param   int $userId   User ID
    * @return  void
    */
    public static function removeActivityCompletion($courseId, $userId) {
        global $DB;

        $sql = 'DELETE B
                FROM {course_modules} A
                JOIN {course_modules_completion} B ON A.id = B.coursemoduleid
                WHERE B.userid = ?
                AND A.course = ?';

        $DB->execute(
            $sql,
            [$userId, $courseId]
        );
    }

    /**
     * fungsi untuk menghapus seluruh tracking yang dihasilkan oleh modul SCORM
      * @param   int $courseId Course ID
      * @param   int $userId   User ID
      * @return  void
     */
    public static function removeScormData($courseId, $userId){
        global $DB;

        $sql = 'DELETE A
                FROM {scorm_scoes_track} A
                JOIN {scorm} B ON A.scormid = B.id
                WHERE B.course = ?
                AND A.userid = ?';
        
        $DB->execute(
            $sql,
            [$courseId, $userId]
        );
    }

    /**
     * fungsi untuk menghapus seluruh activity dan attempts terkait h5p (default moodle)
     * @param int $courseId Course ID
     * @param int $userId User ID
     * @return void
     */
    public static function removeH5PActivityAndAttempts($courseId, $userId){
        global $DB;

        $sql = 'DELETE A, B
                FROM {h5pactivity_attempts_results} A
                JOIN {h5pactivity_attempts} B ON A.attemptid = B.id
                JOIN {h5pactivity} C ON B.h5pactivityid = C.id
                WHERE C.course = ?
                AND B.userid = ?';
        
        $DB->execute(
            $sql,
            [$courseId, $userId]
        );
    }

    /**
     * Fungsi untuk menghapus data enrolment user
     * @param int $courseId
     * @param int $userId
     * @return void
     */
    public static function removeUserEnrollment($courseId, $userId){
        return; # masih error, halaman enroll tidak muncul

        global $DB;

        $sql = 'DELETE A
                FROM {user_enrolments} A
                JOIN {enrol} B ON A.enrolid = B.id
                WHERE B.courseid = ?
                AND A.userid = ?';

        $DB->execute(
            $sql,
            [$courseId, $userId]
        );
    }

    /**
     * Menghapus completion dari cache sehingga direbuild ketika user 
     * membuka halaman course setelah retake
     * @param int $courseId Course ID
     * @param int $userId User ID
     */
    public static function removeCompletionCache($courseId, $userId){
        $completionCache = \cache::make('core', 'completion');
        $completionCache->delete($userId . '_' . $courseId);
    }

    /**
     * Menghapus data grades terakhir (current grade)
     * @param int $courseId Course ID
     * @param int $userId User ID
     */
    public static function removeUserGrades($courseId, $userId){
        global $DB;

        $sql = 'DELETE A
                FROM {grade_grades} A
                JOIN {grade_items} B ON A.itemid = B.id
                WHERE A.userid = ?
                AND B.courseid = ?';
        
        $data = $DB->execute(
            $sql,
            [$userId, $courseId]
        );
    }

    /**
     * Menghapus data grades history
     * @param int $courseId Course ID
     * @param int $userId User ID
     */
    public static function removeUserGradesHistory($courseId, $userId){
        global $DB;

        $sql = 'DELETE A
                FROM {grade_grades_history} A
                JOIN {grade_items} B ON A.itemid = B.id
                WHERE A.userid = ?
                AND B.courseid = ?';
        
        $DB->execute(
            $sql,
            [$userId, $courseId]
        );
    }

    /**
     * Menghapus data course completion
     * @param int $courseId
     * @param int $userId
     */
    public static function removeUserCourseCompletion($courseId, $userId){
        global $DB;

        $sql = 'DELETE FROM {course_completions}
                WHERE userid = ? AND course = ?';

        $DB->execute(
            $sql,
            [$userId, $courseId]
        );
    }

    /**
     * Menghapus data kritera course completion per user
     * @param int $courseId
     * @param int $userId
     */
    public static function removeUserCourseCompletionCriteria($courseId, $userId){
        global $DB;

        $sql = 'DELETE FROM {course_completion_crit_compl}
                WHERE userid = ? AND course = ?';
        
        $DB->execute(
            $sql,
            [$userId, $courseId]
        );
    }

    /**
     * Menghapus data badges yang sudah diterbitkan pada tahun berjalan
     * @param int $courseId
     * @param int $userId
     */
    public static function removeUserIssuedBadges($courseId, $userId){
        global $DB;

        $sql = 'DELETE A, C
                FROM {badge_issued} A
                JOIN {badge} B ON A.badgeid = B.id
                JOIN {badge_criteria_met} C ON A.id = C.issuedid
                WHERE A.userid = ?
                AND B.courseid = ?
                AND YEAR(FROM_UNIXTIME(A.dateissued)) = YEAR(NOW())';
        
        $DB->execute(
            $sql,
            [
                'userId' => $userId,
                'courseId' => $courseId
            ]
            );
    }
    
}
