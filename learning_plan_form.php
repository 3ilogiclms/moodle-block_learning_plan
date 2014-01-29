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

require_once("{$CFG->libdir}/formslib.php");
// Add Learning Plans.
class learningplan_form extends moodleform {
    public function definition() {
        $mform =& $this->_form;
        $errors= array();
        $mform->addElement('header', 'displayinfo', get_string('learningpath', 'block_learning_plan'));
        $mform->addElement('text', 'learning_plan', get_string('learningplan', 'block_learning_plan'));
        $mform->addRule('learning_plan', get_string('plan_format', 'block_learning_plan'), 'regex', '#^[A-Z0-9 ]+$#i', 'client');
        $mform->addRule('learning_plan', $errors, 'required', null, 'server');
        $mform->setType('learning_plan', PARAM_RAW);
        $attributes = array('rows' => '8', 'cols' => '40');
        $mform->addElement('textarea', 'description', get_string('desc', 'block_learning_plan'), $attributes);
        $mform->setType('description', PARAM_TEXT);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // $this->add_action_buttons();
        $this->add_action_buttons($cancel = false);
    }
    public function validation($data, $files) {
        global $DB;
        $errors= array();
        if($data['id']) {
            return true;
        } else if ($DB->record_exists('learning_learningplan', array('learning_plan'=>$data['learning_plan']))) {
           $errors['learning_plan'] = get_string('plan_exist', 'block_learning_plan');
            return $errors;
        }
    }
    public function display_list() {
        global $DB, $OUTPUT, $CFG;
        // Page parameters.
        $page    = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page
        $sort    = optional_param('sort', 'learning_plan', PARAM_ALPHA);
        $dir     = optional_param('dir', 'DESC', PARAM_ALPHA);

        $changescount = $DB->count_records('learning_learningplan');
        $columns = array('learning_plan'    => get_string('learning_plan', 'block_learning_plan'),
                         'description'     => get_string('desc', 'block_learning_plan'), );
        $hcolumns = array();
        if (!isset($columns[$sort])) {
            $sort = 'learning_plan';
        }
        foreach ($columns as $column=>$strcolumn) {
            if ($sort != $column) {
                $columnicon = '';
                if ($column == 'learning_plan') {
                    $columndir = 'DESC';
                } else {
                    $columndir = 'ASC';
                }
            } else {
                $columndir = $dir == 'ASC' ? 'DESC':'ASC';
                if ($column == 'learning_plan') {
                    $columnicon = $dir == 'ASC' ? 'up':'down';
                } else {
                    $columnicon = $dir == 'ASC' ? 'down':'up';
                }
                $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

            }
            $hcolumns[$column] = "<a href=\"view.php?viewpage=1&sort=$column&amp;dir=$columndir&amp;page=$page&amp;perpage=$perpage\">".$strcolumn."</a>$columnicon";

        }

        $baseurl = new moodle_url('view.php?viewpage=1', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
        echo $OUTPUT->paging_bar($changescount, $page, $perpage, $baseurl);
        $table = new html_table();
        $table->head  = array(get_string('s_no', 'block_learning_plan'), $hcolumns['learning_plan'], $hcolumns['description'], 'Edit', 'Remove');
        $table->size  = array('10%', '35%', '35%');
        $table->align = array('center', 'left', 'left', 'center' , 'center', 'center');
        $table->width = '100%';
        $orderby = "$sort $dir";
        $sql = "SELECT id, learning_plan, description from {learning_learningplan}  ORDER BY $orderby ";
        $inc= 1;
        $rs = $DB->get_recordset_sql($sql, array(),  $page*$perpage, $perpage);
        foreach ($rs as $log) {
            $row = array();
            $row[] = $inc++;
            $row[] = $log->learning_plan;
            $row[] = $log->description;
            $row[] = '<center><center><a  title="Edit" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=1&edit=edit&id='.$log->id.'"/>
                     <img src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" /></a></center>';
            $row[] = '<center><center><a  title="Remove" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=1&rem=remove&id='.$log->id.'"/>
                     <img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
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
        $mform =& $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('add_training', 'block_learning_plan'));
        $radioarray=array();
        if (!isset($attributes)) $attributes = "";
        if (!isset($errors)) $errors = array();
        $radioarray[] =& $mform->createElement('radio', 'type_id', '', get_string('elearning', 'block_learning_plan'), 1 );
        $radioarray[] =& $mform->createElement('radio', 'type_id', '', get_string('classroom', 'block_learning_plan'), 2, $attributes);
        $radioarray[] =& $mform->createElement('radio', 'type_id', '', get_string('onthejob', 'block_learning_plan'), 3, $attributes);
        $mform->addGroup($radioarray, 'type_id', get_string('training_method', 'block_learning_plan'), array('<br>'), false);
        $mform->addRule('type_id', $errors, 'required', null, 'server');
        $mform->addElement('text', 'training_name', get_string('training_name', 'block_learning_plan'));
        $mform->addRule('training_name', get_string('training_format', 'block_learning_plan'), 'regex', '#^[A-Z0-9 ]+$#i', 'client');
        $mform->addRule('training_name', $errors, 'required', null, 'server');
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
        // $this->add_action_buttons();
        $this->add_action_buttons($cancel = false);
    }
    public function validation($data, $files) {
        global $DB;
        $errors= array();
        if($data['id']) {
            return true;
        }
        else if ($DB->record_exists('learning_training', array('training_name'=>$data['training_name']))) {
           $errors['training_name'] = get_string('training_exist', 'block_learning_plan');
        }
         if($data['start_date'] >= $data['end_date']) {
            $errors['errormsg'] = get_string('date_val', 'block_learning_plan');
            return $errors;
         }
    }

    public function display_list() {
        global $DB, $OUTPUT, $CFG;
        // page parameters.
        $page    = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page
        $sort    = optional_param('sort', 'training_name', PARAM_ALPHA);
        $dir     = optional_param('dir', 'DESC', PARAM_ALPHA);
        $changescount = $DB->count_records('learning_training');
        $columns = array('training_name'     => get_string('training_name', 'block_learning_plan'),
                         'type_id'     => get_string('training_method', 'block_learning_plan'),
                         'start_date'     => get_string('start_date', 'block_learning_plan'),
                         'end_date'     => get_string('end_date', 'block_learning_plan'));
        $hcolumns = array();
        if (!isset($columns[$sort])) {
            $sort = 'training_name';
        }
        foreach ($columns as $column=>$strcolumn) {
            if ($sort != $column) {
                $columnicon = '';
                if ($column == 'training_name') {
                    $columndir = 'DESC';
                } else {
                    $columndir = 'ASC';
                }
            } else {
                $columndir = $dir == 'ASC' ? 'DESC':'ASC';
                if ($column == 'training_name') {
                    $columnicon = $dir == 'ASC' ? 'up':'down';
                } else {
                    $columnicon = $dir == 'ASC' ? 'down':'up';
                }
                $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

            }
            $hcolumns[$column] = "<a href=\"view.php?viewpage=2&sort=$column&amp;dir=$columndir&amp;page=$page&amp;perpage=$perpage\">".$strcolumn."</a>$columnicon";

        }
        $baseurl = new moodle_url('view.php?viewpage=2', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
        echo $OUTPUT->paging_bar($changescount, $page, $perpage, $baseurl);
        $table = new html_table();
        $table->head  = array(get_string('s_no', 'block_learning_plan'), $hcolumns['training_name'], $hcolumns['type_id'], $hcolumns['start_date'], $hcolumns['end_date'], 'Edit', 'Remove');
        $table->size  = array('10%', '15%', '15%', '15%', '15%', '15%', '15%', '15%');
        $table->align = array('center', 'left', 'left', 'center', 'center', 'center', 'center');
        $table->width = '100%';
        $orderby = "$sort $dir";
        $sql = "SELECT id, training_name, type_id, start_date, end_date, url  from {learning_training}  ORDER BY $orderby ";
        $inc= 1;
        $rs = $DB->get_recordset_sql($sql, array(),  $page*$perpage, $perpage);
        foreach ($rs as $log) {
            $row = array();
            $row[] = $inc++;
            if(strlen($log->url)>0) {
                $row[] = '<a  title="Training" href="'.$log->url.'">'.$log->training_name.'</a>';
            } else {
                $row[] = $log->training_name;
            }
            $training_method;
            if ($log->type_id == 1) {
                $training_method= get_string('elearning', 'block_learning_plan');
            } else if ($log->type_id == 2) {
                $training_method= get_string('classroom', 'block_learning_plan');
            } else if ($log->type_id == 3) {
                $training_method= get_string('onthejob', 'block_learning_plan');
            }
            $row[] = $training_method;
            // $row[] = date('d-m-Y', $log->start_date);
            $row[] = date('M j, Y, g:i a', $log->start_date);
            $row[] = date('M j, Y, g:i a', $log->end_date);
            // $row[] = date('d-m-Y', $log->end_date);
            // $row[] = "<a href=".$log->url."/>Link</a>";
            $row[] = '<center><center><a  title="Edit" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=2&edit=edit&id='.$log->id.'"/>
                     <img src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" /></a></center>';
            $row[] = '<center><center><a  title="Remove" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=2&rem=remove&id='.$log->id.'"/>
                     <img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
            $table->data[] = $row;
        }
            //$rs->close();
        return $table;
    }
}
// Add Training Method.
class trainingmethod_form extends moodleform {
    public function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('add_training_method', 'block_learning_plan'));
        $mform->addElement('text', 'training_method', get_string('training_method', 'block_learning_plan'));
        $attributes = array('rows' => '8', 'cols' => '40');
        $mform->addElement('textarea', 'description', get_string('desc', 'block_learning_plan'), $attributes);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // $this->add_action_buttons();
        $this->add_action_buttons($cancel = false);
    }

    public function display_list(){
        $table = new html_table();
        $table->head  = array(get_string('s_no', 'block_learning_plan'), get_string('training_m', 'block_learning_plan'), get_string('desc', 'block_learning_plan'));
        $table->size  = array('30%', '35%', '35%');
        $table->align = array('center', 'left', 'left');
        $table->width = '100%';
        $table->data  = array();
        $row = array();
        $table->data[] = $row;
        return $table;
    }
}
// Assign Training into Learning Plan.
class assigntraining_learningplan__form extends moodleform {
    public function definition() {
        global $DB, $CFG;
        $mform =& $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('assign_training_learningplan', 'block_learning_plan'));
        $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom=0, $limitnum=0);
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null,
                            array('link' => $CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=1', 'label' => get_string('add_learningplan', 'block_learning_plan')));
        $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training}', null, $limitfrom=0, $limitnum=0);
        $select = $mform->addElement('selectwithlink', 't_id', get_string('training', 'block_learning_plan'), $attributes, null,
                                     array('link' => $CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=2', 'label' => get_string('add_training', 'block_learning_plan')));
        $select->setmultiple(true);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // $this->add_action_buttons();
        $this->add_action_buttons($cancel = false);
    }

    public function display_list() {
        global $DB, $OUTPUT, $CFG;
        // Page parameters.
        $page    = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 10, PARAM_INT);    // how many per page
        $sort    = optional_param('sort', 'learning_plan', PARAM_ALPHA);
        $dir     = optional_param('dir', 'DESC', PARAM_ALPHA);

        $changescount = $DB->count_records('learning_learningplan');
        $columns = array('learning_plan'    => get_string('learning_plan', 'block_learning_plan'),
                         'training_name'     => get_string('training_name', 'block_learning_plan'),
                        );
        $hcolumns = array();
        if (!isset($columns[$sort])) {
            $sort = 'learning_plan';
        }
        foreach ($columns as $column=>$strcolumn) {
            if ($sort != $column) {
                $columnicon = '';
                if ($column == 'learning_plan') {
                    $columndir = 'DESC';
                } else {
                    $columndir = 'ASC';
                }
            } else {
                $columndir = $dir == 'ASC' ? 'DESC':'ASC';
                if ($column == 'learning_plan') {
                    $columnicon = $dir == 'ASC' ? 'up':'down';
                } else {
                    $columnicon = $dir == 'ASC' ? 'down':'up';
                }
                $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";

            }
            $hcolumns[$column] = "<a href=\"view.php?viewpage=4&sort=$column&amp;dir=$columndir&amp;page=$page&amp;perpage=$perpage\">".$strcolumn."</a>$columnicon";

        }

        $baseurl = new moodle_url('view.php?viewpage=4', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
        echo $OUTPUT->paging_bar($changescount, $page, $perpage, $baseurl);
        $table = new html_table();
        $table->head  = array(get_string('s_no', 'block_learning_plan'), $hcolumns['learning_plan'], $hcolumns['training_name'], 'Remove');
        $table->size  = array('10%', '35%', '35%', '35%');
        $table->align = array('center', 'left', 'left', 'center');
        $table->width = '100%';
        $orderby = "$sort $dir";
        $sql = "SELECT id, (select learning_plan from {learning_learningplan}  where id=lp_id) as learning_plan, (select training_name from {learning_training} where id=t_id)
                as training_name from {learning_plan_training}  ORDER BY $orderby ";
        $inc= 1;
        $rs = $DB->get_recordset_sql($sql, array(),  $page*$perpage, $perpage);
        foreach ($rs as $log) {
            $row = array();
            $row[] = $inc++;
            $row[] = $log->learning_plan;
            $row[] = $log->training_name;
            $row[] = '<center><center><a  title="Remove" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=4&rem=remove&id='.$log->id.'"/>
                     <img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
            $table->data[] = $row;
        }
            // $rs->close();
        return $table;
    }
}
// Assign Learning plan to User.
class assignlerningplan_user_form extends moodleform {
    public function definition() {
        global $DB, $CFG, $USER;
        $mform =& $this->_form;
        if (!isset($attributes1)) $attributes1 = "";
        $mform->addElement('header', 'displayinfo', get_string('assign_learningplan_user', 'block_learning_plan'));
        $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom=0, $limitnum=0);
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null,
                           array('link' => $CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=1', 'label' => get_string('add_learningplan', 'block_learning_plan')));
        $attributes =  $DB->get_records_sql_menu('SELECT id, CONCAT(firstname," ", lastname)FROM {user} where username!="guest"', array ($params=null), $limitfrom=0, $limitnum=0);
        $select = $mform->addElement('select', 'u_id', get_string('users', 'block_learning_plan'), $attributes, null,
                                     array('link' => $CFG->wwwroot.'/user/editadvanced.php?id=-1', 'label' => get_string('addusers', 'block_learning_plan'), $attributes1));
        $select->setMultiple(true);
        // $mform->addElement('hidden', 'assignee', $USER->id);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // $this->add_action_buttons();
        $this->add_action_buttons($cancel = false);
    }

    public function display_list() {
        global $DB, $OUTPUT, $CFG;
        $table = new html_table();
        $table->head  = array(get_string('s_no', 'block_learning_plan'), get_string('learning_plan', 'block_learning_plan'), get_string('users', 'block_learning_plan'),
                              get_string('assignee', 'block_learning_plan'), get_string('remove', 'block_learning_plan'));
        $table->size  = array('10%', '25%', '25%', '25%', '15%');
        $table->align = array('center', 'left', 'left', 'left');
        $table->width = '100%';
        $table->data  = array();
        $sql = 'SELECT id, (SELECT concat(firstname," ", lastname) FROM {user} WHERE id = u_id) as fullname, (SELECT learning_plan
                FROM {learning_learningplan} WHERE id = lp_id) as learning_plan, (SELECT concat(firstname," ", lastname) FROM 
                {user} WHERE id = assignee_id) as assignee FROM {learning_user_learningplan}'; // ORDER BY $orderby';
        $inc= 0;
        $rs = $DB->get_recordset_sql($sql, array());
        foreach ($rs as $log) {
            $row = array();
            $row[] = ++$inc;
            $row[] = $log->learning_plan;
            $row[] = $log->fullname;
            $row[] = $log->assignee;
            $row[] = '<center><center><a title="Remove" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=5&rem=remove&id='.$log->id.'"/>
                     <img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
            $table->data[] = $row;
        }
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
        $mform =& $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('trainingstatus', 'block_learning_plan'));
        if(isset($l_id)) {
            $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan} where id=?', array($l_id), $limitfrom=0, $limitnum=0);
        }
        else {
            $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom=0, $limitnum=0);
        }
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null,
                           array('link' => $CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=1', 'label' => get_string('add_learningplan', 'block_learning_plan')));
        if(isset($u_id)) {
            $attributes =  $DB->get_records_sql_menu('SELECT id, CONCAT(firstname," ", lastname)FROM {user} where username!="guest" AND id=?', 
                                                    array ($u_id), $limitfrom=0, $limitnum=0);
        }
        else {
            $attributes =  $DB->get_records_sql_menu('SELECT id, CONCAT(firstname," ", lastname)FROM {user} where username!="guest"', 
                                                     array ($params=null), $limitfrom=0, $limitnum=0);
        }
        $mform->addElement('selectwithlink', 'u_id', get_string('users', 'block_learning_plan'), $attributes, null,
                           array('link' => $CFG->wwwroot."/user/editadvanced.php?id=-1", 'label' => get_string('addusers', 'block_learning_plan')));
        if(isset($t_id)) {
            $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training} where id=?', array($t_id), $limitfrom=0, $limitnum=0);
        }
        else {
            $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training}', null, $limitfrom=0, $limitnum=0);
        }
        $mform->addElement('selectwithlink', 't_id', get_string('training', 'block_learning_plan'), $attributes, null,
                           array('link' => $CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=2', 'label' => get_string('add_training', 'block_learning_plan')));
        $attributes = array('In-progress', 'Not Yet Started', 'Complete');
        $mform->addElement('select', 'status', get_string('status', 'block_learning_plan'), $attributes);
        $attributes = array('size' => '50', 'maxlength' => '1000');
        $mform->addElement('text', 'remarks', get_string('remarks', 'block_learning_plan'), $attributes);
        $mform->setType('remarks', PARAM_TEXT);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        // $this->add_action_buttons();
        $this->add_action_buttons($cancel = false);
    }

    public function display_list() {
        return false;
    }

}
class search extends moodleform {
    public function definition() {
        global $DB, $CFG;
        $mform =& $this->_form;
        $mform->addElement('header', 'displayinfo', get_string('searchusers', 'block_learning_plan'));
        $attributes = $DB->get_records_sql_menu('SELECT id, learning_plan FROM {learning_learningplan}', null, $limitfrom=0, $limitnum=0);
        $mform->addElement('selectwithlink', 'l_id', get_string('learningplan', 'block_learning_plan'), $attributes, null,
                           array('link' => $CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=1', 'label' => get_string('add_learningplan', 'block_learning_plan')));
        $attributes = $DB->get_records_sql_menu('SELECT id, training_name FROM {learning_training}', null, $limitfrom=0, $limitnum=0);
        $mform->addElement('selectwithlink', 't_id', get_string('training', 'block_learning_plan'), $attributes, null,
                           array('link' => $CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=2', 'label' => get_string('add_training', 'block_learning_plan')));
        $attributes = array('In-progress', 'Not Yet Started', 'Complete', 'All Status');
        $mform->addElement('select', 'status', get_string('status', 'block_learning_plan'), $attributes);
        $mform->addElement('hidden', 'viewpage');
        $mform->setType('viewpage', PARAM_INT);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('button', 'showuser', 'Search', array("id" => "btnajax"));
    }
    public function display_list($lp_id = "", $t_id = "", $status = "") {
        // if ($lp_id && $t_id) {
        global $DB, $OUTPUT, $CFG;
        $table = new html_table();
        $table->id = 'statuslist';
        $table->head  = array(get_string('s_no', 'block_learning_plan'), get_string('training_name', 'block_learning_plan'), get_string('users', 'block_learning_plan'),
                              get_string('start_date', 'block_learning_plan'), get_string('end_date', 'block_learning_plan'), get_string('status', 'block_learning_plan'),
                              get_string('remarks', 'block_learning_plan'), get_string('setting', 'block_learning_plan'));
        $table->size  = array('5%', '30%', '20%', '10%', '10%', '10%', '25%', '10%');
        $table->align = array('center', 'left', 'left', 'center', 'center', 'center', 'left', 'left', 'center');
        $table->width = '100%';
        $table->data  = array();
        if($status == '3') {
        $sql = 'select  t_id, lp_id, lut.status, lut.remarks, `u_id` as id,(select training_name from  {learning_training} where id =t_id)
                as training,(select learning_plan from   {learning_learningplan} where id =lp_id) as learning_plan, (SELECT
                CONCAT(firstname," ", lastname)FROM {user} where username!="guest" AND id = u_id) as name,(select
                start_date from  {learning_training} where id =t_id)as date1,(select end_date from  {learning_training}
                where id =t_id)as date2 from {learning_plan_training} lpt inner join {learning_user_trainingplan} lut
                on lut.lpt_id=lpt.id  where lpt.lp_id=?  AND lpt.t_id= ?'; // ORDER BY $orderby';
        } else {
        $sql = 'select  t_id, lp_id, lut.status, lut.remarks, `u_id` as id,(select training_name from  {learning_training} where id =t_id)
                as training,(select learning_plan from   {learning_learningplan} where id =lp_id) as learning_plan, (SELECT
                CONCAT(firstname," ", lastname)FROM {user} where username!="guest" AND id = u_id) as name,(select
                start_date from  {learning_training} where id =t_id)as date1,(select end_date from  {learning_training}
                where id =t_id)as date2 from {learning_plan_training} lpt inner join {learning_user_trainingplan} lut
                on lut.lpt_id=lpt.id  where lpt.lp_id=?  AND lpt.t_id= ? AND lut.status = ?'; // ORDER BY $orderby';
        }
        $inc = 0;
        $rs = $DB->get_recordset_sql($sql, array($lp_id, $t_id, $status));
        foreach ($rs as $log) {
            $row = array();
            $row[] = ++$inc;
            $row[] = $log->training;
            $row[] = $log->name;
            $row[] = date('d-m-Y', $log->date1);
            $row[] = date('d-m-Y', $log->date2);
            $row[] = status_value($log->status) ;
            $row[] = $log->remarks;
            $row[] = '<a href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=6&l_id='.$log->lp_id.'&u_id='.$log->id.'&t_id='.$log->t_id.'&setting=1">Setting</a>';
            // $row[] = '<center><center><a title="Remove" href="'.$CFG->wwwroot.'/blocks/learning_plan/view.php?viewpage=5&rem=remove&id='.$log->id.'"/>
            // <img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall"/></a></center>';
            $table->data[] = $row;
        }
        return $table;
    }
}
