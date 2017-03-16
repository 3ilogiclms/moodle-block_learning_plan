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
 * This plugin serves as a database and plan for all learning activities in the organization,
 * where such activities are organized for a more structured learning program.
 * @package    block_learning_plan
 * @copyright  3i Logic<lms@3ilogic.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @author     Azmat Ullah <azmat@3ilogic.com>
 */
// Ref: http://docs.moodle.org/dev/Upgrade_API.

defined('MOODLE_INTERNAL') || die();


/**
 * Checking previous versions allow us to upgrade only the settings that need to be changed
 * and avoid changes that might have since been customized by the site administrator.
 * @param float old version information
 * @return boolean.
 */
function xmldb_block_learning_plan_upgrade($oldversion) {
    global $DB;
    $plugin = new stdClass();
    require(realpath(dirname(__FILE__) . '/..') . '/version.php'); // defines $plugin->version
    // Checking previous versions allow us to upgrade only the settings that need to be changed
    // and avoid changes that might have since been customized by the site administrator.
    if ($oldversion < 2011033018) { // YYYYMMDD00
        // Updates for older version of plugin go here
    }
    if ($oldversion < $plugin->version) {
        // Updates for current version of plugin go here. Move content into a new IF statement like above for next version.
        // Fixes instances where text strings were stored in table instead of numeric values
        // Numeric values enable the use strings from language file to report status.
        $DB->execute("UPDATE {learning_user_trainingplan} SET status = '0' WHERE status = 'In-progress'");
        $DB->execute("UPDATE {learning_user_trainingplan} SET status = '1' WHERE status = 'Not Yet Started'");
        $DB->execute("UPDATE {learning_user_trainingplan} SET status = '2' WHERE status = 'Complete'");
        // Clean-up unused records in learning_user_trainingplan that were left behind when training plans were deleted (this won't happen anymore)
        $DB->execute("DELETE FROM {learning_user_trainingplan} WHERE (SELECT count(1) FROM {learning_plan_training} WHERE id = {learning_user_trainingplan}.lpt_id) < 1");
    }
    return true;
}

?>