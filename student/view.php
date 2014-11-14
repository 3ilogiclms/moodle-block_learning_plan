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
/* Learning Plan Block
 * This plugin serves as a database and plan for all learning activities in the organziation, 
 * where such activities are organized for a more structured learning program.
 * @package blocks
 * @author: Azmat Ullah, Talha Noor
 * @date: 20-Aug-2014
 * @copyright  Copyrights © 2012 - 2014 | 3i Logic (Pvt) Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once('../learning_plan_form.php');
require_once('../lib.php');
require_once("{$CFG->libdir}/formslib.php");
?>
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/css/jquery.dataTables.css">
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
	$('.display').dataTable();
});
</script>
<?php
// Variable

global $DB, $USER, $OUTPUT, $PAGE, $CFG;
require_login();
$lp_id = required_param('id', PARAM_INT);
$pageurl = '/blocks/learning_plan/student/view.php?id=' . $lp_id;
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('learning_plan', 'block_learning_plan'));
$PAGE->set_title(get_string('learning_plan', 'block_learning_plan'));
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", 'block_learning_plan'), new moodle_url($pageurl));
echo $OUTPUT->header();
$student_status = display_list($lp_id, $USER->id);
$title = '<table width="100%" style="background-color:#EEE;"><tr><td style="text-align:center;"><h3>'.get_string('status_report', 'block_learning_plan').'</h3><h3>'.$USER->firstname . ' ' . $USER->lastname .'</h3><h3>'.get_learningplan_name($lp_id) .'</h3></h3><p>'.get_string('report_at', 'block_learning_plan') . ' ' .(Date("d M Y")).'</p></td></tr></tr><table>';
echo $title;
echo html_writer::table($student_status);
$PAGE->requires->js_init_call('M.block_learning_plan.init', array($viewpage = "", $setting = ""));
echo $OUTPUT->footer();
//End Form Display