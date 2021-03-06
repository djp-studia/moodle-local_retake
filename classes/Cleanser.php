<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_retake;

defined('MOODLE_INTERNAL') || die();

use context_user;

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

        $courseCompletinCache = \cache::make('core', 'coursecompletion');
        $courseCompletinCache->delete($userId . '_' . $courseId);
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

        // get badge id data
        $sql = 'SELECT distinct badgeid
                FROM {badge} A
                JOIN {badge_issued} B ON A.id = B.badgeid
                WHERE B.userid = :userid
                AND A.courseid = :courseid';
        
        $badges = $DB->get_records_sql(
            $sql, 
            ['userid' => $userId, 'courseid' => $courseId]
        );

        // remove badge image per user from file system
        $fs = get_file_storage();

        foreach($badges as $badge) {
            $usercontext = context_user::instance($userId);
            $fs->delete_area_files($usercontext->id, 'badges', 'userbadge', $badge->badgeid);
        }
        
        // remove all badge issued data and all its critria met
        $sql = 'DELETE A, C
                FROM {badge_issued} A
                JOIN {badge} B ON A.badgeid = B.id
                JOIN {badge_criteria_met} C ON A.id = C.issuedid
                WHERE A.userid = ?
                AND B.courseid = ?
                AND YEAR(FROM_UNIXTIME(A.dateissued)) = YEAR(NOW())';
        
        $DB->execute(
            $sql,
            [$userId, $courseId]
        );
    }
    
}
