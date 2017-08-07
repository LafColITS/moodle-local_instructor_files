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

/**
 * Unit tests for tasks.
 *
 * @package    local_instructor_files
 * @category   test
 * @copyright  2017 Lafayette College ITS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class local_instructor_files_download_testcase extends advanced_testcase {
    protected $filecount = 0;

    public function test_download() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create test data.
        $teacher1 = $this->getDataGenerator()->create_user();
        $manager1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course1->id);
        $this->getDataGenerator()->enrol_user($teacher1->id, $course1->id, 'editingteacher');

        // Add some files.
        $this->create_file(array('userid' => $teacher1->id, 'contextid' => $context->id));
        $this->create_file(array('userid' => $manager1->id, 'contextid' => $context->id));
        $this->create_file(array('userid' => $teacher1->id, 'contextid' => $context->id));
        $this->create_file(array('userid' => $teacher1->id, 'contextid' => $context->id, 'component' => 'backup'));

        // Verify returned files.
        $fileids = local_instructor_files\helper::get_file_ids($course1->id, $context->id);
        $this->assertEquals(2, count($fileids));
    }

    /**
     * Create a new file.
     *
     * @param array $options
     * @return object stored_file
     */
    private function create_file($options) {
        global $DB;

        // Increment the file counter.
        $this->filecount++;

        // Set default values.
        if (!isset($options['contenthash'])) {
            $options['contenthash'] = md5(rand());
        }

        if (!isset($options['component'])) {
            $options['component'] = 'local_instructor_files';
        }

        if (!isset($options['filearea'])) {
            $options['filearea'] = 'test';
        }

        if (!isset($options['filename'])) {
            $options['filename'] = "test{$this->filecount}";
        }

        if (!isset($options['itemid'])) {
            $options['itemid'] = 0;
        }

        if (!isset($options['filesize'])) {
            $options['filesize'] = rand();
        }

        if (!isset($options['timecreated'])) {
            $options['timecreated'] = 0;
        }

        if (!isset($options['timemodified'])) {
            $options['timemodified'] = 0;
        }

        if (!isset($options['pathnamehash'])) {
            $options['pathnamehash'] = $options['filename'];
        }

        if (!isset($options['contextid'])) {
            $options['contextid'] = 999;
        }

        if (!isset($options['userid'])) {
            $options['userid'] = null;
        }

        // Add the file and return the stored file object.
        $file = (object)$options;
        $fileid = $DB->insert_record('files', $file);

        $fs = get_file_storage();
        return $fs->get_file_by_id($fileid);
    }
}
