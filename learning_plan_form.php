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
require_once("{$CFG->libdir}/formslib.php");

// Add Learning Plans.
class learningplan_form extends moodleform {

    public function definition() {
        $mform = & $this->_form;
        if (!isset($errors))
            $errors = array();
        $mform->addElement('header', 'displayinfo', get_string('learningpath', 'block_learning_plan'));
        $mform->addElement('text', 'learning_plan', get_string('learningplan', 'block_learning_plan'));
        // $mform->addRule('learning_plan', get_string('plan_format', 'block_learning_plan'), 'regex', '#^[A-Z0-9 ]+$#i', 'client');
        $mform->addRule('learning_plan', null, 'required', null, 'server');
        $mform->setType('learning_plan', PARAM_TEXT);
        $attributes = array('rows' => '8', 'cols' => '40');
        $mform->addElement('textarea', 'description', get_string('desc', 'block_learning_plan'), $attributes);
        $mform->setType('description', PARAM_TEXT);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons($cancel = false);
    }

    public function validation($data, $files) {
        global $DB;
        $errors = array();
        if ($data['id']) {
            return true;
        } else if ($DB->record_exists('learning_learningplan', array('learning_plan' => $data['learning_plan']))) {
            $errors['learning_plan'] = get_string('plan_exist', 'block_learning_plan');
            return $errors;
        }
    }

    public function display_list() {
        global $DB, $OUTPUT, $CFG;
        // Page parameters.
        
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_learning_plan'), get_string('learning_plan', 'block_learning_plan' ), get_string('desc', 'block_learning_plan' ), get_string('edit'), get_string('remove'));
        $table->size = array('10%', '30', '45%', '10%', '10%', '10%');
				$table->attributes = array('class' => 'display');
        $table->align = array('center', 'left', 'left', 'center', 'center', 'center');
        $table->width = '100%';
        $sql = "SELECT id, learning_plan, description from {learning_learningplan}";
        $inc = 1;
        //$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
        $rs = $DB->get_recordset_sql($sql, array(), null, null);
        foreach ($rs as $log) {
            $row = array();
            $row[] = $inc++;
            $row[] = format_string($log->learning_plan, false);
            $row[] = format_string($log->description, false);
            $row[] = '<center><center><a title="' . get_string('edit') . '" href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=1&edit=edit&id=' . $log->id . '"/>
                      <img alt="" src="' . $OUTPUT->pix_url('t/edit') . '" class="iconsmall" /></a></center>';
            $row[] = '<center><center><a title="' . get_string('delete') . '" href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=1&rem=remove&id=' . $log->id . '"/>
                       <img alt="" src="' . $OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
            $table->data[] = $row;
        }
        // $rs->close();
        return $table;
        // echo html_writer::table($table);
    }

}

// Add Training Types.
class training_form extends moodleform {

    public function definition() {
        $mform = & $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('add_training', 'block_learning_plan'));
        $radioarray = array();
        if (!isset($attributes))
            $attributes = "";
        if (!isset($errors))
            $errors = array();
        $radioarray[] = & $mform->createElement('radio', 'type_id', '', get_string('elearning', 'block_learning_plan'), 1);
        $radioarray[] = & $mform->createElement('radio', 'type_id', '', get_string('classroom', 'block_learning_plan'), 2, $attributes);
        $radioarray[] = & $mform->createElement('radio', 'type_id', '', get_string('onthejob', 'block_learning_plan'), 3, $attributes);
        $mform->addGroup($radioarray, 'type_id', get_string('training_method', 'block_learning_plan'), array('<br>'), false);
        $mform->setDefault('type_id', 1);
        $mform->addRule('type_id', $errors, 'required', null, 'server');
        $mform->addElement('text', 'training_name', get_string('training_name', 'block_learning_plan'));
        // $mform->addRule('training_name', get_string('training_format', 'block_learning_plan'), 'regex', '#^[A-Z0-9 ]+$#i', 'client');
        $mform->addRule('training_name', null, 'required', null, 'server');
        $mform->setType('training_name', PARAM_TEXT);
        // $attributes = array('maxbytes' => '4194304', 'accepted_types' => "*");
        // $mform->addElement('file', 'attachment', get_string('attachment', 'block_learning_plan'), $attributes );
        // $mform->addElement('filepicker', 'attachment', get_string('attachment', 'block_learning_plan'), null, $attributes);
        $mform->addElement('text', 'url', get_string('url', 'block_learning_plan'));
        $mform->addRule('url', get_string('wrong_url', 'block_learning_plan'), 'regex', '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', 'client');
        $mform->setType('url', PARAM_LOCALURL);
        $mform->addElement('date_time_selector', 'start_date', get_string('start_date', 'block_learning_plan'));
        $mform->addElement('date_time_selector', 'end_date', get_string('end_date', 'block_learning_plan'));
        $mform->addElement('static', 'errormsg');
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons($cancel = false);
    }

    public function validation($data, $files) {
        global $DB;
        $errors = array();
        if ($data['id']) {
            return true;
        } else if ($DB->record_exists('learning_training', array('training_name' => $data['training_name']))) {
            $errors['training_name'] = get_string('training_exist', 'block_learning_plan');
            return $errors;
        }
        if ($data['start_date'] >= $data['end_date']) {
            $errors['errormsg'] = get_string('date_val', 'block_learning_plan');
            return $errors;
        }
    }

    public function display_list() {
        global $DB, $OUTPUT, $CFG;
        // page parameters.
        
        $columns = array('training_name' => get_string('training_name', 'block_learning_plan'),
            'type_id' => get_string('training_method', 'block_learning_plan'),
            'start_date' => get_string('start_date', 'block_learning_plan'),
            'end_date' => get_string('end_date', 'block_learning_plan'),);
      
        
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_learning_plan'), $columns['training_name'], $columns['type_id'], $columns['start_date'], $columns['end_date'], get_string('edit'), get_string('remove'));
        $table->size = array('10%', '15%', '15%', '15%', '15%', '15%', '15%');
        $table->attributes = array('class' => 'display');
        $table->align = array('center', 'left', 'left', 'center', 'center', 'center');
        $table->width = '100%';
        $sql = "SELECT id, training_name, type_id, start_date, end_date, url  from {learning_training} ";
        $inc = 1;
       // $rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
         $rs = $DB->get_recordset_sql($sql, array(), null, null);
        foreach ($rs as $log) {
            $row = array();
            $row[] = $inc++;
            if (strlen($log->url) > 0) {
                $row[] = '<a title="' . get_string('training', 'block_learning_plan') . '" href="' . $log->url . '">' . format_string($log->training_name, false) . '</a>';
            } else {
                $row[] = format_string($log->training_name, false);
            }
            $training_method;
            if ($log->type_id == 1) {
                $training_method = get_string('elearning', 'block_learning_plan');
            } else if ($log->type_id == 2) {
                $training_method = get_string('classroom', 'block_learning_plan');
            } else if ($log->type_id == 3) {
                $training_method = get_string('onthejob', 'block_learning_plan');
            }
            $row[] = format_string($training_method, false);
            // $row[] = date('d-m-Y', $log->start_date);
            $row[] = userdate($log->start_date, get_string('strftimedatetime', 'core_langconfig')); // date('M j, Y, g:i a', $log->start_date);
            $row[] = userdate($log->end_date, get_string('strftimedatetime', 'core_langconfig')); // date('M j, Y, g:i a', $log->end_date);
            // $row[] = date('d-m-Y', $log->end_date);
            // $row[] = "<a href=".$log->url."/>Link</a>";
            $row[] = '<center><center><a title="' . get_string('edit') . '" href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=2&edit=edit&id=' . $log->id . '"/>
                     <img alt="" src="' . $OUTPUT->pix_url('t/edit') . '" class="iconsmall" /></a></center>';
            $row[] = '<center><center><a title="' . get_string('remove') . '" href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=2&rem=remove&id=' . $log->id . '"/>
                     <img alt="" src="' . $OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
            $table->data[] = $row;
        }
        //$rs->close();
        return $table;
    }

}

// Add Training Method.
class trainingmethod_form extends moodleform {

    public function definition() {
        $mform = & $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('add_training_method', 'block_learning_plan'));
        $mform->addElement('text', 'training_method', get_string('training_method', 'block_learning_plan'));
        $attributes = array('rows' => '8', 'cols' => '40');
        $mform->addElement('textarea', 'description', get_string('desc', 'block_learning_plan'), $attributes);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons($cancel = false);
    }

    public function display_list() {
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_learning_plan'), get_string('training_m', 'block_learning_plan'), get_string('desc', 'block_learning_plan'));
        $table->size = array('30%', '35%', '35%');
        $table->attributes = array('class' => 'display');
        $table->align = array('center', 'left', 'left');
        $table->width = '100%';
        $table->data = array();
        $row = array();
        $table->data[] = $row;
        return $table;
    }

}

// Assign Training into Learning Plan.
class assigntraining_learningplan__form extends moodleform {

    public function definition() {
        global $DB, $CFG;
        $mform = & $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('assign_training_learningplan', 'block_learning_plan'));
        $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom = 0, $limitnum = 0);
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null, null);
        $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training}', null, $limitfrom = 0, $limitnum = 0);
        $training_types = array('1' => get_string('elearning', 'block_learning_plan'), '2' => get_string('classroom', 'block_learning_plan'), '3' => get_string('onthejob', 'block_learning_plan'));
        $mform->addElement('select', 'training_type', get_string('training_method', 'block_learning_plan'), $training_types);
        $select = $mform->addElement('selectwithlink', 't_id', get_string('training', 'block_learning_plan'), $attributes, null, null);
        $select->setmultiple(true);
        $mform->addRule('t_id', get_string('select_training', 'block_learning_plan'), 'required', null, 'client');
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons($cancel = false);
    }

    public function display_list() {
        global $DB, $OUTPUT, $CFG;
        // Page parameters.
      
        $columns = array('learning_plan' => get_string('learning_plan', 'block_learning_plan'),
            'training_name' => get_string('training_name', 'block_learning_plan'),
            'type_id' => get_string('training_method', 'block_learning_plan'),
        );
      
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_learning_plan'), $columns['learning_plan'], $columns['training_name'], $columns['type_id'], get_string('remove'));
        $table->size = array('10%', '30%', '30%', '45%', '35%');
        $table->attributes = array('class' => 'display');
        $table->align = array('center', 'left', 'left', 'left', 'center');
        $table->width = '100%';
        $sql = "SELECT id, (select learning_plan from {learning_learningplan}  where id=lp_id) as learning_plan,
               (select training_name from {learning_training} where id=t_id) as training_name,
               (select type_id from {learning_training} where id=t_id) as type_id from {learning_plan_training}  ";
        $inc = 1;
        //$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
        $rs = $DB->get_recordset_sql($sql, array(), null, null);
        foreach ($rs as $log) {
            $row = array();
            $row[] = $inc++;
            $row[] = format_string($log->learning_plan, false);
            $row[] = format_string($log->training_name, false);
            $row[] = training_type($log->type_id);
            $row[] = '<center><center><a  title="' . get_string('remove') . '" href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=4&rem=remove&id=' . $log->id . '"/>
                     <img src="' . $OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
            $table->data[] = $row;
        }
        // $rs->close();
        //echo $OUTPUT->paging_bar($inc + 1, $page, $perpage, $baseurl);
        return $table;
    }

}

// Assign Learning plan to User.
class assignlerningplan_user_form extends moodleform {

    public function definition() {
        global $DB, $CFG, $USER;
        $mform = & $this->_form;
        if (!isset($attributes1))
            $attributes1 = "";
        $mform->addElement('header', 'displayinfo', get_string('assign_learningplan_user', 'block_learning_plan'));
        $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom = 0, $limitnum = 0);
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null, null);
        if (!isGroup_null()) {
            $attributes = array('1' => 'No Group');
        } else
            $attributes = array('1' => 'No Group', '2' => 'Group');
        $select = $mform->addElement('select', 'g_selection', get_string('group_selection', 'block_learning_plan'), $attributes, null, array(null));
        $mform->disabledIf('g_id', 'g_selection', 'eq', 1);
        $attributes = $DB->get_records_sql_menu('SELECT id, name FROM {groups}', null, $limitfrom = 0, $limitnum = 0);
        $select = $mform->addElement('select', 'g_id', get_string('department', 'block_learning_plan'), $attributes, null, array(null));
        $attributes = $DB->get_records_sql_menu("SELECT id, CONCAT(firstname,' ', lastname)FROM {user} where username!='guest'", array($params = null), $limitfrom = 0, $limitnum = 0);
        $select = $mform->addElement('select', 'u_id', get_string('users', 'block_learning_plan'), $attributes, null, array('link' => $CFG->wwwroot . '/user/editadvanced.php?id=-1', 'label' => get_string('addusers', 'block_learning_plan'), $attributes1));
        $select->setMultiple(true);
        $mform->addRule('u_id', get_string('select_user', 'block_learning_plan'), 'required', null, 'client');
        // $mform->addElement('hidden', 'assignee', $USER->id);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons($cancel = false);
    }

    public function display_list() {
        global $DB, $OUTPUT, $CFG;
       
        $columns = array('learning_plan' => get_string('learning_plan', 'block_learning_plan'),
            'fullname' => get_string('users', 'block_learning_plan'),);
        
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_learning_plan'), $columns['learning_plan'], $columns['fullname'], get_string('remove', 'block_learning_plan'));
        $table->size = array('10%', '35%', '25%', '15%');
        $table->attributes = array('class' => 'display');
        $table->align = array('center', 'left', 'left', 'center');
        $table->width = '100%';
        $table->data = array();
        $sql = "SELECT id, u_id, lp_id, (SELECT concat(firstname,' ', lastname)  FROM {user} WHERE id = u_id) as fullname,
               (SELECT learning_plan FROM {learning_learningplan} WHERE id = lp_id) as learning_plan,
               (SELECT concat(firstname,' ', lastname) FROM {user} WHERE id = assignee_id) as assignee
               FROM {learning_user_learningplan}";
        $inc = 0;
        //$rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
        $rs = $DB->get_recordset_sql($sql, array(), null, null);
        foreach ($rs as $log) {
            $row = array();
            $row[] = ++$inc;
            $row[] = format_string($log->learning_plan, false);
            $row[] = format_string($log->fullname);
            // $row[] = $log->assignee;
            $row[] = '<a title="' . get_string('remove') . '"  href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=5&rem=remove&id=' . $log->u_id . '&lp=' . $log->lp_id . '"/>'
                    . ' <img src="' . $OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a>';
            $table->data[] = $row;
        }
        //echo $OUTPUT->paging_bar($inc + 1, $page, $perpage, $baseurl);
        return $table;
    }

}

// Set Training Status.
class trainingstatus_form extends moodleform {

    public function definition() {
        global $DB, $CFG;
        $l_id = optional_param('l_id', null, PARAM_INT);
        $u_id = optional_param('u_id', null, PARAM_INT);
        $t_id = optional_param('t_id', null, PARAM_INT);
        $mform = & $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('trainingstatus', 'block_learning_plan'));
        if (isset($l_id)) {
            $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan} where id=?', array($l_id), $limitfrom = 0, $limitnum = 0);
        } else {
            $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom = 0, $limitnum = 0);
        }
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null, null);
        if (isset($u_id)) {
            $attributes = $DB->get_records_sql_menu("SELECT id, CONCAT(firstname,' ', lastname)FROM {user} where username!='guest' AND id=?", array($u_id), $limitfrom = 0, $limitnum = 0);
        } else {
            $attributes = $DB->get_records_sql_menu("SELECT id, CONCAT(firstname,' ', lastname)FROM {user} where username!='guest'", array($params = null), $limitfrom = 0, $limitnum = 0);
        }
        $mform->addElement('selectwithlink', 'u_id', get_string('users', 'block_learning_plan'), $attributes, null, null);
        if (isset($t_id)) {
            $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training} where id=?', array($t_id), $limitfrom = 0, $limitnum = 0);
        } else {
            $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training}', null, $limitfrom = 0, $limitnum = 0);
        }
        $mform->addElement('selectwithlink', 't_id', get_string('training', 'block_learning_plan'), $attributes, null, null);
        $attributes = array(get_string('status_in_progress', 'block_learning_plan'), get_string('status_not_started', 'block_learning_plan'), get_string('status_completed', 'block_learning_plan'));
        $mform->addElement('select', 'status', get_string('status', 'block_learning_plan'), $attributes);
        $attributes = array('size' => '50', 'maxlength' => '1000');
        $mform->addElement('text', 'remarks', get_string('remarks', 'block_learning_plan'), $attributes);
        $mform->setType('remarks', PARAM_TEXT);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
                $mform->addRule('l_id', get_string('select_learningplan', 'block_learning_plan'), 'required', null, 'client');
        $mform->addRule('u_id', get_string('selectuser', 'block_learning_plan'), 'required', null, 'client');
        $mform->addRule('t_id', get_string('user_training', 'block_learning_plan'), 'required', null, 'client');

        
        $this->add_action_buttons($cancel = false);
    }

    public function display_list() {
        return false;
    }

}

class search extends moodleform {

    public function definition() {
        global $DB, $CFG;
        $mform = & $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('searchusers', 'block_learning_plan'));
        $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom = 0, $limitnum = 0);
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null, null);
        $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training}', null, $limitfrom = 0, $limitnum = 0);
        $mform->addElement('selectwithlink', 't_id', get_string('training', 'block_learning_plan'), $attributes, null, null);
        $attributes = array(get_string('status_in_progress', 'block_learning_plan'), get_string('status_not_started', 'block_learning_plan'), get_string('status_completed', 'block_learning_plan'), get_string('status_all', 'block_learning_plan'));
        $mform->addElement('select', 'status', get_string('status', 'block_learning_plan'), $attributes);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('button', 'showuser', get_string('search'), array("id" => "btnajax"));
    }

    public function display_list($lp_id = "", $t_id = "", $status = "") {
        // if ($lp_id && $t_id) {
        global $DB, $OUTPUT, $CFG;
        $table = new html_table();
        $table->id = 'statuslist';
        $table->head = array(get_string('s_no', 'block_learning_plan'), get_string('training_name', 'block_learning_plan'), get_string('users', 'block_learning_plan'),
            get_string('start_date', 'block_learning_plan'), get_string('end_date', 'block_learning_plan'), get_string('status', 'block_learning_plan'),
            get_string('remarks', 'block_learning_plan'), get_string('setting', 'block_learning_plan'));
        $table->size = array('10%', '20%', '15%', '10%', '10%', '10%', '25%', '10%');
        $table->align = array('center', 'left', 'left', 'center', 'center', 'center', 'left', 'left', 'center');
        $table->width = '100%';
        $table->data = array();
        if ($status == '3') {
            $sql = "select  t_id, lp_id, lut.status, lut.remarks, `u_id` as id,
               (select training_name from  {learning_training} where id =t_id) as training,
               (select learning_plan from   {learning_learningplan} where id =lp_id) as learning_plan,
                (SELECT CONCAT(firstname,' ', lastname)FROM {user} where username!='guest' AND id = u_id) as name,
                (select start_date from  {learning_training} where id =t_id)as date1,
                (select end_date from  {learning_training} where id =t_id)as date2
                from {learning_plan_training}
                lpt inner join {learning_user_trainingplan} lut on lut.lpt_id=lpt.id  where lpt.lp_id=?  AND lpt.t_id= ?"; // ORDER BY $orderby';
        } else {
            $sql = "select  t_id, lp_id, lut.status, lut.remarks, `u_id` as id,
               (select training_name from  {learning_training} where id =t_id)as training,
               (select learning_plan from   {learning_learningplan} where id =lp_id) as learning_plan,
               (SELECT CONCAT(firstname,' ', lastname)FROM {user} where username!='guest' AND id = u_id) as name,
               (select start_date from  {learning_training} where id =t_id)as date1,
               (select end_date from  {learning_training} where id =t_id)as date2
               from {learning_plan_training}
               lpt inner join {learning_user_trainingplan} lut
                on lut.lpt_id=lpt.id  where lpt.lp_id=?  AND lpt.t_id= ? AND lut.status = ?";
        }
        $inc = 0;
        $rs = $DB->get_recordset_sql($sql, array($lp_id, $t_id, $status));
        if (count($rs) > 0) {
            foreach ($rs as $log) {
                $row = array();
                $row[] = ++$inc;
                $row[] = format_string($log->training);
                $row[] = format_string($log->name);
                $row[] = date('d-m-Y', $log->date1);
                $row[] = date('d-m-Y', $log->date2);
                $row[] = status_value($log->status);
                $row[] = format_string($log->remarks);
                $row[] = '<a href="' . $CFG->wwwroot . '/blocks/learning_plan/view.php?viewpage=6&l_id=' . $log->lp_id . '&u_id=' . $log->id . '&t_id=' . $log->t_id . '&setting=1">Setting</a>';
                // $row[] = '<center><center><a title="Remove" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=5&rem=remove&id='.$log->id.'"/>
                // <img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
                $table->data[] = $row;
            }
        } else {
            $row = array('None');
        }
        return $table;
    }

}
