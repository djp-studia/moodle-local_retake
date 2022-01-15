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
    * @return  int $data     Database API Status
    */
    public function cleanActivityCompletion($courseId, $userId) {
        global $DB;

        $sql = 'DELETE B
                FROM {course_modules} A
                JOIN {course_modules_completion} B ON A.id = B.coursemoduleid
                WHERE B.userid = ?
                AND A.course = ?';

        $data = $DB->execute(
            $sql,
            [$userId, $courseId]
        );

        return $data;
    }
}
