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
defined('MOODLE_INTERNAL') || die();

/**
 * This function return learning plan and training association key.
 *
 * @param int $l_id learning plan id
 * @param int $t_id training id
 * @return int leanring plan and training associatino key.
 */
function get_lpt_id($l_id, $t_id) {
    global $DB;
    $lpt_id = $DB->get_record_sql('select id  from {learning_plan_training}  where lp_id=? AND t_id=?', array($l_id, $t_id));
    $lpt_id = $lpt_id->id;
    return $lpt_id;
}

/**
 * This function check is specific learning plan already exist in a table or not.
 *
 * @param int $l_id learning plan id
 * @return boolean .
 */
function islp_assign_user($l_id) {
    global $DB;
    $return;
    $isexist = $DB->record_exists('learning_user_learningplan', array('lp_id' => $l_id));
    if ($isexist > 0) {
        $return = true;
    } else {
        $return = false;
    }
    return $return;
}

/**
 * This function return user list of particular learning plan.
 *
 * @param int $lp_id learning plan id
 * @return array user list.
 */
function get_learningplan_user($lp_id) {
    global $DB;
    $users = $DB->get_records('learning_user_learningplan', array('lp_id' => $lp_id));
    return $users;
}

/**
 * This function return trainings list assigned to particular learning plan.
 *
 * @param int $lp_id
 * @return array training list.
 */
function learningplan_training($lp_id) {
    global $DB;
    $training = $DB->get_records('learning_plan_training', array('lp_id' => $lp_id));
    return $training;
}

/**
 * This function return learning plan array.
 *
 * @param int $u_id user id
 * @return array learning plan.
 */
function user_learningplan($u_id) {
    global $DB;
    $training = $DB->get_recordset_sql('SELECT lp_id as id, (select  learning_plan  from {learning_learningplan} where id =lp_id) as learningplan
                                     FROM {learning_user_learningplan} where u_id = ?', array($u_id));
    return $training;
}

/**
 * This function return status value.
 *
 * @param int $status_id status id
 * @return string status value.
 */
function status_value($status_id) {
    $status_value = '';
    if ($status_id == '0') {
        $status_value = get_string('status_in_progress', 'block_learning_plan');
    } else if ($status_id == '1') {
        $status_value = get_string('status_not_started', 'block_learning_plan');
    } else if ($status_id == '2') {
        $status_value = get_string('status_completed', 'block_learning_plan');
    }
    return $status_value;
}

/**
 * This function return user stauts id.
 *
 * @param int $l_id learning plan id
 * @param int $u_id user id
 * @param int $t_id training id
 * @return int status id.
 */
function status_id($l_id, $u_id, $t_id) {
    global $DB;
    $status = $DB->get_record_sql('select lut.id as id from {learning_plan_training} lpt inner join {learning_user_trainingplan} lut
                                on lut.lpt_id=lpt.id  where lpt.lp_id=? AND lut.u_id=? AND lpt.t_id= ?', array($l_id, $u_id, $t_id));
    $status = $status->id;
    return $status;
}

/**
 * This function return learning plan name.
 *
 * @param int $id learning plan id
 * @return string leanring plan name.
 */
function get_learningplan_name($id) {
    global $DB;
    $result = $DB->get_record_sql("SELECT learning_plan FROM {learning_learningplan} WHERE id = ?", array($id));
    return $result->learning_plan;
}

/**
 * This function return full name of user.
 *
 * @param int $id user id
 * @return string user name.
 */
function get_user_name($id) {
    global $DB;
    $result = $DB->get_record_sql("SELECT concat (firstname,' ', lastname) as name FROM {user} WHERE id = ?", array($id));
    return $result->name;
}

/**
 * This function perform delete operation on overall plugins.
 *
 * @param string $table plugin table name where the record will be delete
 * @param id $id record id or table primary key
 * @param string $url call back url
 * @param int $lp_id learning plan id
 * @return null redirect to call back url.
 */
function delete_learningplan_record($table, $id, $url, $lp_id = '') {
    global $DB;
    // Delete Department.
    if ($table == 'learning_learningplan') {
        $DB->delete_records('learning_learningplan', array('id' => $id));
        $DB->delete_records('learning_plan_training ', array('lp_id' => $id));
        $DB->delete_records('learning_user_learningplan ', array('lp_id' => $id));
    } else if ($table == 'learning_training') {
        $DB->delete_records('learning_training', array('id' => $id));
        $DB->delete_records('learning_plan_training', array('t_id' => $id));
    } else if ($table == 'learning_plan_training') {
        $DB->delete_records('learning_plan_training', array('id' => $id));
    } else if ($table == 'learning_user_learningplan') {
        // $DB->delete_records('learning_user_learningplan', array('id'=> $id));
        $DB->delete_records('learning_user_learningplan', array('u_id' => $id, 'lp_id' => $lp_id));
        // Remove all training in learning_user_trainingplan
        $sql = "select id from  {learning_plan_training} where lp_id =?";
        $lpts = $DB->get_recordset_sql($sql, array('lp_id' => $lp_id), $limitfrom = 0, $limitnum = 0);
        foreach ($lpts as $lpt) {
            $DB->delete_records('learning_user_trainingplan', array('u_id' => $id, 'lpt_id' => $lpt->id));
        }
    }
    redirect($url, get_string('removed', 'block_learning_plan'), 2);
}

/**
 * This function display the list of assigned trainings on the basis of user id and learning plan id.
 *
 * @param int $lp_id learning plan id
 * @param int $u_id user id
 * @return table.
 */
function display_list($lp_id, $u_id) {
    global $DB, $OUTPUT, $CFG;
    $table = new html_table();
    $table->id = 'statuslist';
    $table->head = array(get_string('s_no', 'block_learning_plan'), get_string('training_name', 'block_learning_plan'), get_string('training_method', 'block_learning_plan'), get_string('start_date', 'block_learning_plan'), get_string('end_date', 'block_learning_plan'), get_string('status', 'block_learning_plan'), get_string('remarks', 'block_learning_plan'));
    $table->size = array('5%', '20%', '14%', '8%', '8%', '10%', '25%');
    $table->align = array('center', 'left', 'left', 'center', 'center', 'center', 'left');
    $table->width = '100%';
    $table->attributes = array('class' => 'display');
    $table->data = array();
    $sql = 'select  t_id, (select type_id from  {learning_training} where id =t_id) as type_id, lp_id, lut.status, lut.remarks, `u_id` as id,(select training_name from  {learning_training} where id =t_id)
            as training, (select url from {learning_training} where id =t_id) as url, (select learning_plan from   {learning_learningplan} where id =lp_id) as learning_plan, (SELECT
            CONCAT(firstname," ", lastname)FROM {user} where username!="guest" AND id = u_id) as name,(select
            start_date from  {learning_training} where id =t_id)as date1,(select end_date from  {learning_training}
            where id =t_id)as date2 from {learning_plan_training} lpt inner join {learning_user_trainingplan} lut
            on lut.lpt_id=lpt.id  where lpt.lp_id=? AND lut.u_id=?';
    $inc = 0;
    $rs = $DB->get_recordset_sql($sql, array($lp_id, $u_id));

    if ($DB->record_exists_sql($sql, array($lp_id, $u_id))) {
        foreach ($rs as $log) {
            $row = array();
            $row[] = ++$inc;
            if (strlen($log->url) > 0) {
                $row[] = '<a title="' . get_string('training', 'block_learning_plan') . '" href="' . $log->url . '">' . format_string($log->training, false) . '</a>';
            } else {
                $row[] = format_string($log->training, false);
            }
            if ($log->type_id == 1) {
                $training_method = get_string('elearning', 'block_learning_plan');
            } else if ($log->type_id == 2) {
                $training_method = get_string('classroom', 'block_learning_plan');
            } else if ($log->type_id == 3) {
                $training_method = get_string('onthejob', 'block_learning_plan');
            }
            $row[] = format_string($training_method, false);
            $row[] = userdate($log->date1, get_string('strftimedatefullshort', 'core_langconfig'));
            $row[] = userdate($log->date2, get_string('strftimedatefullshort', 'core_langconfig'));
            $row[] = status_value($log->status);
            $row[] = format_string($log->remarks, false);
            $table->data[] = $row;
        }
    } else {
        $table->data[] = array('', '', '', '', get_string('notfound', 'block_learning_plan'), '', '', '');
    }
    return $table;
}

/**
 * This function return navigation titles displayed on pluign header.
 *
 * @param int $viewpage page id
 * @return string navigation title.
 */
function nav_title($viewpage) {
    $array = array(
        1 => get_string('learningpath', 'block_learning_plan'),
        2 => get_string('add_training', 'block_learning_plan'),
        4 => get_string('assign_training_learningplan', 'block_learning_plan'),
        5 => get_string('assign_learningplan_user', 'block_learning_plan'),
        6 => get_string('trainingstatus', 'block_learning_plan'),
        7 => get_string('search', 'block_learning_plan'),
        8 => get_string('send_notification', 'block_learning_plan'),
    );
    return $array[$viewpage];
}

/**
 * This function return training type.
 *
 * @param int $type_id type id of training
 * @return string type name.
 */
function training_type($type_id) {
    $training_type = '';
    if ($type_id == '1') {
        $training_type = get_string('elearning', 'block_learning_plan');
    } else if ($type_id == '2') {
        $training_type = get_string('classroom', 'block_learning_plan');
    } else if ($type_id == '3') {
        $training_type = get_string('onthejob', 'block_learning_plan');
    }
    return $training_type;
}

/**
 * This function return custom field name on the base of input paramet custom field ID.
 *
 * @param null
 * @return Array groups list will be return.
 */
function isGroup_null() {
    global $DB;
    $result = $DB->record_exists('groups', array());
    return $result;
}

/**
 * This function return learning plan assignment notification.
 *
 * @param int $user user id
 * @param string $msg message body
 * @param table $table trainings list in table form
 * @param string $subject notification subject
 * @return null.
 */
function lp_send_notification($user, $msg, $table, $subject) {

    $message = new \core\message\message();
    $message->component = 'block_learning_plan';
    $message->name = 'view';
    $message->userfrom = get_admin();
    $message->userto = $user;
    $message->subject = $subject;
    $username = getlpuser($user);
    $message->fullmessagehtml = "<br/>Dear $username,<br/><br/>$msg<br/><br/>$table";
    $message->fullmessageformat = FORMAT_HTML;
    $message->smallmessage = '';
    $message->notification = 1;
    message_send($message);
}

/**
 * This function return user full name.
 *
 * @param int $id user id
 * @return String user name.
 */
function getlpuser($id) {
    global $DB;
    $result = $DB->get_record_sql("SELECT concat (firstname,' ', lastname) as name FROM {user} WHERE id = ?", array($id));
    return $result->name;
}

/**
 * This function return training list table associated with particular learning plan.
 *
 * @param int $id learning plan id
 * @return table training list table.
 */
function get_lp_training($id) {
    global $DB;

    $training_sql = "SELECT lt.training_name, lt.type_id, lt.start_date, lt.end_date
					FROM {learning_plan_training} lpt
					INNER JOIN {learning_training} lt
					ON lpt.t_id = lt.id
					where lpt.lp_id = ?";
    $training_rs = $DB->get_records_sql($training_sql, array($id));
    $table = new html_table();
    $table->head = array('S.No', 'Training name', 'Training method', 'Start date', 'End date');
    $table->align = array('center', 'left', 'left', 'center', 'center');
    $table->data = array();
    if ($DB->record_exists_sql($training_sql, array($id))) {
        $sno = 0;
        foreach ($training_rs as $training_log) {
            $row = array();
            $row[] = ++$sno;
            $row[] = $training_log->training_name;
            if ($training_log->type_id == 1) {
                $training_method = get_string('elearning', 'block_learning_plan');
            } else if ($training_log->type_id == 2) {
                $training_method = get_string('classroom', 'block_learning_plan');
            } else if ($training_log->type_id == 3) {
                $training_method = get_string('onthejob', 'block_learning_plan');
            }
            $row[] = $training_method;
            $row[] = date('d-m-Y', $training_log->start_date);
            $row[] = date('d-m-Y', $training_log->end_date);
            $table->data[] = $row;
        }
    } else {
        $table->data[] = array('', '', get_string('notfound', 'block_advance_learning_plan'), '', '');
    }

    return $table;
}

/**
 * This function reterive users having particular learning plan.
 *
 * @param int $id leanring plan id
 * @param string $message message body
 * @return table learning plan trainings table.
 */
function lp_get_users($id, $message, $training_list) {
    global $DB;

    $user_sql = "SELECT u_id FROM {learning_user_learningplan}
				 where lp_id = ?";
    $user_rs = $DB->get_records_sql($user_sql, array($id));
    if ($DB->record_exists_sql($user_sql, array($id))) {
        foreach ($user_rs as $user_log) {
            lp_send_notification($user_log->u_id, $message, $training_list, 'Learning plan notification');
        }
    }
}
