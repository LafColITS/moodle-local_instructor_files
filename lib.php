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
 * Navigation for the instructor files tool.
 *
 * @package   local_instructor_files
 * @copyright 2017 Lafayette College ITS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function local_instructor_files_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('local/instructor_files:download', $context)) {
        $url = new moodle_url('/local/instructor_files/index.php', array('id' => $course->id));
        $navigation->add(get_string('download', 'local_instructor_files'), $url,
                navigation_node::TYPE_SETTING, null, null, new pix_icon('i/download_files', get_string('download'), 'local_instructor_files'));
    }
}
