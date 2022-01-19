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

/**
 * Class for retake history object
 */
class Retake
{  
    private $courseId;
    private $retakeAllowTable = 'local_retake_allow';
    private $retakeHistoryTable = 'local_retake_history';

    public function __construct($courseId){
        $this->courseId = $courseId;
    }

    /**
     * Fungsi untuk mendapatkan data history
     * @param int $courseId ID Course yang ingin ditarik datanya
     */
    public function getHistory() {
        global $DB;

        return $DB->get_records_sql(
            'SELECT A.*,
                B.firstname,
                B.username,
                FROM_UNIXTIME(A.timecreated) datecreated
            FROM {local_retake_history} A
            JOIN {user} B ON A.user = B.id
            WHERE course = ?',
            [$this->courseId]
        );
    }

    /**
     * fungsi untuk enable retake pada sebuah course
     * @param int $userId User ID yang melakukan action enable
     */
    public function enable($userId) {
        global $DB;
        $data = array(
            "course" => $this->courseId,
            "user" => $userId,
            "timecreated" => time()
        );

        return $DB->insert_record($this->retakeAllowTable, $data);
    }

    /**
     * fungsi untuk disable retake pada sebuah course
     */
    public function disable(){
        global $DB;
        return $DB->delete_records($this->retakeAllowTable, array("course" => $this->courseId));
    }

    /**
     * fungsi untuk mencari status retake course
     */
    public function isEnabled(){
        global $DB;
        return $DB->record_exists($this->retakeAllowTable, array("course" => $this->courseId));
    }
}