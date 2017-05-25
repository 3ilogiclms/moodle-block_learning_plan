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
require_once('../../../config.php');
require_once('../learning_plan_form.php');
require_once('../lib.php');
require_once("{$CFG->libdir}/formslib.php");
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

$title = '<h2 align="center">' . get_string('status_report', 'block_learning_plan') . '</h2>
		  <table align="center">
          <tr><td align="center">' .$USER->firstname . ' ' . $USER->lastname.'
		  <h4>' . get_learningplan_name($lp_id) .'</h4>
		  '.get_string('report_at', 'block_learning_plan') . ' ' . (Date("d M Y")).'
		  </td>
		  </tr>
          </table>';
echo $title;

echo "<br/>".html_writer::table($student_status);
echo $OUTPUT->footer();
// End Form Display.