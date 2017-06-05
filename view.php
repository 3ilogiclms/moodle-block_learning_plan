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
require_once('../../config.php');
$setting = null;
$row = array();
require_once('learning_plan_form.php');
require_once('lib.php');
require_once("{$CFG->libdir}/formslib.php");
global $DB, $USER, $OUTPUT, $PAGE, $CFG;

$viewpage = required_param('viewpage', PARAM_INT);
$rem = optional_param('rem', null, PARAM_RAW);
$edit = optional_param('edit', null, PARAM_RAW);
$delete = optional_param('delete', null, PARAM_RAW);
$id = optional_param('id', null, PARAM_INT);
$u_id = optional_param('id', null, PARAM_INT);
$lp = optional_param('lp', null, PARAM_INT);
$pageurl = new moodle_url('/blocks/learning_plan/view.php', array('viewpage' => $viewpage));
$learningplan_url = new moodle_url('/blocks/learning_plan/view.php?viewpage=1');
$nav_title = nav_title($viewpage);
?>

<!-- DataTables code starts-->
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/public/datatable/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/public/datatable/dataTables.tableTools.css">
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/public/datatable/jquery.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/public/datatable/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo $CFG->wwwroot ?>/blocks/learning_plan/public/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" language="javascript" class="init">
    $(document).ready(function () {
// fn for automatically adjusting table coulmns
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
        });

        $('.display').DataTable({
            dom: 'T<"clear">lfrtip',
            tableTools: {
                "aButtons": [
                    "copy",
                    "print",
                    {
                        "sExtends": "collection",
                        "sButtonText": "Save",
                        "aButtons": ["xls", "pdf"]
                    }
                ],
                "sSwfPath": "<?php echo $CFG->wwwroot ?>/blocks/learning_plan/public/datatable/copy_csv_xls_pdf.swf"
            }
        });
    });
</script>
<!-- DataTables code ends-->
<?php
require_login();
$context = context_system::instance();
if (!has_capability('block/learning_plan:managepages', $context)) {
    redirect($CFG->wwwroot);
}
$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('learning_plan', 'block_learning_plan'));
$PAGE->set_title(get_string('learning_plan', 'block_learning_plan'));
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", 'block_learning_plan'), new moodle_url($learningplan_url));
$PAGE->navbar->add($nav_title);
echo $OUTPUT->header();
$table = new html_table();
$table->head = array('<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=1">' . get_string('learningpath', 'block_learning_plan') . '</a>',
    '<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=2">' . get_string('add_training', 'block_learning_plan') . '</a>',
    '<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=4">' . get_string('assign_training_learningplan', 'block_learning_plan') . '</a>',
    '<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=5">' . get_string('assign_learningplan_user', 'block_learning_plan') . '</a>',
    '<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=6">' . get_string('trainingstatus', 'block_learning_plan') . '</a>',
    '<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=8">' . get_string('send_notification', 'block_learning_plan') . '</a>',
    '<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=7">' . get_string('search', 'block_learning_plan') . '</a>');
$table->size = array('15%', '15%', '20%', '25%', '15%', '10%');
$table->align = array('center', 'center', 'center', 'center', 'center', 'center');
$table->width = '100%';
$table->data[] = $row;
echo html_writer::table($table);
if ($viewpage == 1) { // Add Learning Plans.
    $form = new learningplan_form();
    // Insert or Update data - Save button Click.
    if ($fromform = $form->get_data()) {
        if ($fromform->id) {
            $DB->update_record('learning_learningplan', $fromform);
            redirect($pageurl, get_string('updated', 'block_learning_plan'), 2);
        } else {
            // Insert Record.
            $DB->insert_record('learning_learningplan', $fromform);
            redirect($pageurl, get_string('saved', 'block_learning_plan'), 2);
        }
    }
    // Delete Record.
    if ($rem) {
        echo $OUTPUT->confirm(get_string('plan_delete', 'block_learning_plan'), '/blocks/learning_plan/view.php?viewpage=1&rem=rem&delete=' . $id, '/blocks/learning_plan/view.php?viewpage=1');
        if ($delete) {
            delete_learningplan_record('learning_learningplan', $delete, $pageurl);
        }
    }
    // Edit Record.
    if ($edit) {
        $get_learningplan = $DB->get_record('learning_learningplan', array('id' => $id), '*');
        $form = new learningplan_form(null, array('id' => $get_learningplan->id));
        $form->set_data($get_learningplan);
    }
} else if ($viewpage == 2) { // Add Training Types.
    $form = new training_form();
    if ($fromform = $form->get_data()) {
        if ($fromform->id) {
            // Update Record.
            $DB->update_record('learning_training', $fromform);
            redirect($pageurl, get_string('updated', 'block_learning_plan'), 2);
        } else {
            // Insert Record.
            $DB->insert_record('learning_training', $fromform);
            redirect($pageurl, get_string('saved', 'block_learning_plan'), 2);
        }
    }
    // Delete Record.
    if ($rem) {
        echo $OUTPUT->confirm(get_string('training_delete', 'block_learning_plan'), '/blocks/learning_plan/view.php?viewpage=2&rem=rem&delete=' . $id, '/blocks/learning_plan/view.php?viewpage=2');
        if ($delete) {
            delete_learningplan_record('learning_training', $delete, $pageurl);
        }
    }
    // Edit Record.
    if ($edit) {
        $get_learningplan = $DB->get_record('learning_training', array('id' => $id), '*');
        $form = new training_form(null, array('id' => $get_learningplan->id));
        $form->set_data($get_learningplan);
    }
} else if ($viewpage == 3) { // Add Training Method.
    $form = new trainingmethod_form();
} else if ($viewpage == 4) { // Assign Training into Learning Plan.
    $form = new assigntraining_learningplan__form();
    if ($fromform = $form->get_data()) {
        if ($fromform->id) {
            // Update Record.
            $DB->update_record('learning_plan_training ', $fromform);
        } else {
            // Insert Record.
            $max = sizeof($fromform->t_id);
            $record = new stdClass();
            $record->lp_id = $fromform->l_id;
            foreach ($fromform->t_id as $formtid) {
                $record->t_id = $formtid;
                $DB->insert_record('learning_plan_training', $record);
                // Condtion for already assigned learning plan.
                // Getting lpt_id.
                // Get lp_id and getting user array.
                if (islp_assign_user($record->lp_id)) {
                    $lpt_id = get_lpt_id($record->lp_id, $record->t_id);
                    $users = get_learningplan_user($record->lp_id);
                    // Insert User Training if leraning plan already assgin to user.
                    $record2 = new stdClass();
                    $record2->lpt_id = $lpt_id;
                    foreach ($users as $userid) {
                        $record2->u_id = $userid->u_id;
                        $DB->insert_record('learning_user_trainingplan', $record2);
                    }
                }
            }
        }
        redirect($pageurl, get_string('saved_changes', 'block_learning_plan'), 2);
    }
    // Delete Record.
    if ($rem) {
        echo $OUTPUT->confirm(get_string('record_delete', 'block_learning_plan'), '/blocks/learning_plan/view.php?viewpage=4&rem=rem&delete=' . $id . '&id=' . $lp, '/blocks/learning_plan/view.php?viewpage=4');
        if ($delete) {
            delete_learningplan_record('learning_plan_training', $delete, $pageurl, $id);
        }
    }
} else if ($viewpage == 5) { // Assign Learning plan to User.
    $form = new assignlerningplan_user_form();
    if ($fromform = $form->get_data()) {
        if ($fromform->id) {
            // Update Record.
            $DB->update_record('learning_user_learningplan', $fromform);
        } else {
            // Insert Record.
            $record = new stdClass();
            $record2 = new stdClass();
            $record->lp_id = $fromform->l_id;
            $record->assignee_id = $USER->id;
            foreach ($fromform->u_id as $formtid) {
                $record->u_id = $formtid;
                $training = learningplan_training($fromform->l_id);
                foreach ($training as $train) {
                    $record2->lpt_id = $train->id;
                    $record2->u_id = $record->u_id;
                    // Insert in learning_user_trainingplan.
                    $DB->insert_record('learning_user_trainingplan', $record2);
                }
                // Insert in learning_user_learningplan.
                $DB->insert_record('learning_user_learningplan', $record);
            }
        }
        redirect($pageurl, get_string('saved', 'block_learning_plan'), 2);
    }
    // Delete Record.
    if ($rem) {
        echo $OUTPUT->confirm(get_string('record_delete', 'block_learning_plan'), '/blocks/learning_plan/view.php?viewpage=5&rem=rem&delete=' . $u_id . '&lp=' . $lp, '/blocks/learning_plan/view.php?viewpage=5');
        if ($delete) {
            delete_learningplan_record('learning_user_learningplan', $delete, $pageurl, $lp);
        }
    }
} else if ($viewpage == 6) { // Set Training Status.
    $form = new trainingstatus_form();
    $setting = optional_param('setting', null, PARAM_INT);
    if ($fromform = $form->get_data()) {
        $status_id = status_id($fromform->l_id, $fromform->u_id, $fromform->t_id);
        $fromform->id = $status_id;
        $DB->update_record('learning_user_trainingplan', $fromform);
        redirect($pageurl, get_string('saved_changes', 'block_learning_plan'), 2);
    }
} else if ($viewpage == 7) {
    $form = new search();
} else if ($viewpage == 8) {

    $form = new send_notification();
    $form->display();

    if ($fromform = $form->get_data()) {
        $learning_plan = $fromform->learning_plan;
        $message = $fromform->message;

        $training_list = get_lp_training($learning_plan);
        $training_list = html_writer::table($training_list);

        $users_list = lp_get_users($learning_plan, $message, $training_list);
        redirect($pageurl, get_string('notification_sent', 'block_learning_plan'), 2);
    }
}
// Set viewpage with form.
if ($viewpage != 8) {
    $toform['viewpage'] = $viewpage;
    $form->set_data($toform);
// Display Form.
    $form->display();
// Form Cancel.
    if ($fromform = $form->is_cancelled()) {
        redirect("{$CFG->wwwroot}" . "/blocks/learning_plan/view.php?viewpage=" . $viewpage);
    }
// Display List.
    if ($table = $form->display_list()) {
        echo html_writer::table($table);
    }
}
$PAGE->requires->js_init_call('M.block_learning_plan.init', array($viewpage, $setting));
echo $OUTPUT->footer();
// End Form Display.