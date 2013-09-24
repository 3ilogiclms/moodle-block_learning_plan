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
 * @date: 20-Sep-2013
 * @copyright  Copyrights © 2012 - 2013 | 3i Logic (Pvt) Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once('../learning_plan_form.php');
require_once('../lib.php');
require_once("{$CFG->libdir}/formslib.php");
// Variable
global $DB, $USER, $OUTPUT, $PAGE, $CFG;
$PAGE->set_url('/blocks/learning_plan/view.php');
$PAGE->set_pagelayout('standard');
// $lp_id = optional_param('id',null ,PARAM_INT);
// $viewpage = required_param('viewpage', PARAM_INT);
$lp_id= required_param('id', PARAM_INT);
$PAGE->set_heading('Learning Plan');
$PAGE->set_title('Learning Plan');
// $pageurl = new moodle_url('/blocks/learning_plan/student/view.php?viewpage='.$viewpage);
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", 'block_learning_plan'));

echo $OUTPUT->header();
$student_status = display_list($lp_id, $USER->id) ;
$title = '<center><table width="100%" style="background-color:#EEE;"><tr><td><center><h2>'.get_string('status_report', 'block_learning_plan').
         '</h2><h2>'.get_learningplan_name($lp_id).'</h2><p>'. get_string('report_at', 'block_learning_plan').' '.(Date("d M Y")).'</p></center></td></tr><table></center>';
// Set viewpage with form.
// $toform['viewpage'] = $viewpage;
// $form->set_data($toform);

// Display Form.
// $form->display();
// Display List.
// if($table=$form->display_list()) {
// echo '<div id="prints">';
        echo $title;
        echo html_writer::table($student_status);
        //echo '</div>';
    //}
    $PAGE->requires->js_init_call('M.block_learning_plan.init', array($viewpage, $setting));
    echo $OUTPUT->footer();
    //End Form Display