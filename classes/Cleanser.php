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
    public function cleanActivityCompletion($courseId, $userId) {
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
    public function cleanScormData($courseId, $userId){
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
    public function cleanH5PActivityAndAttempts($courseId, $userId){
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
}
