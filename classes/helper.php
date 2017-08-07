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
 * Helper functions.
 *
 * @package   local_instructor_files
 * @copyright 2016 UMass Amherst
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_instructor_files;

defined('MOODLE_INTERNAL') || die();

class helper {
    public static function get_files($courseid, $contextid) {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');
        $fileids = self::get_file_ids($courseid, $contextid);
        if (!$fileids) {
            return false;
        }

        $fs = get_file_storage();
        $filenames = array();

        foreach ($fileids as $id => $fileid) {
            $file = $fs->get_file_by_id($fileid->id);
            if (in_array($file->get_filename(), $filenames)) {
                $newfilename = $fileid->id .'-'. $file->get_filename();
            } else {
                $newfilename = $file->get_filename();
            }
            array_push($filenames, $newfilename);
            $filesforzipping[$newfilename] = $file;
        }

        if (count($filesforzipping) == 0) {
            return false;
        }
        return self::pack_files($filesforzipping);
    }

    private static function pack_files($files) {
        global $CFG;
        $tempzip = tempnam($CFG->tempdir . '/', 'local_instructor_files_');
        $zipper = new \zip_packer();
        if ($zipper->archive_to_pathname($files, $tempzip)) {
            return $tempzip;
        }
        return false;
    }

    /**
     * Get the file ids to be archived.
     *
     * @param int $courseid The course in question.
     * @param int $contextid The course context.
     *
     * @return array
     */
    public static function get_file_ids($courseid, $contextid) {
        global $DB;

        $roles = get_config('local_instructor_files', 'roles');

        $query = "SELECT id FROM {files} f
            WHERE f.filesize <> 0
	          AND (
	             userid IN (
			              SELECT distinct u.id FROM {course} c, {role_assignments} ra, {user} u, {context}  ct
			              WHERE c.id = ct.instanceid AND ra.userid = u.id AND ct.id = ra.contextid
			              AND ra.roleid IN ($roles) AND c.id = $courseid
		           ) OR (
			              f.component='mod_resource' AND f.userid IS NULL AND f.author IS NULL AND f.license IS NULL
			              AND f.source like '%/%'
		           )
	         )
	         AND f.contextid IN (
		           SELECT id from {context} ctx WHERE ctx.path LIKE '%/$contextid/%' OR
		           (ctx.path LIKE '%/$contextid' AND ctx.path LIKE '%$contextid'
	         )
       )
       AND (f.component != 'assignfeedback_editpdf' AND f.component != 'backup' AND f.filearea != 'stamps')";
        $fileids = $DB->get_records_sql($query);
        return $fileids;
    }

    /**
     * Get the roles suitable for a selector.
     *
     * @return array
     */
    public static function get_roles() {
        global $DB;

        $roles = $DB->get_records_menu('role', null, '', 'id, shortname');
        return $roles;
    }

    /**
     * Get the default roles.
     *
     * @param array $roles The system roles.
     * @return array
     */
    public static function get_default_roles($roles) {
        $defaultrolenames = array('editingteacher', 'teacher');
        return array_keys(array_intersect($roles, $defaultrolenames));
    }
}
