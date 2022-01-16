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
}
