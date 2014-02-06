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
 * @author: Azmat Ullah, Talha Noor, Michael Milette (Instrux Media)
 * @date: 20-Sep-2013
 * @copyright  Copyrights © 2012 - 2013 | 3i Logic (Pvt) Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function get_lpt_id($l_id, $t_id) {
    global $DB;
    $lpt_id=$DB->get_record_sql('select id  from {learning_plan_training}  where lp_id=? AND t_id=?',
                                array($l_id, $t_id));
    $lpt_id=$lpt_id->id;
    return $lpt_id;
}
function  islp_assign_user($l_id) {
    global $DB;
    $return;
    $isexist = $DB->record_exists('learning_user_learningplan', array ('lp_id'=>$l_id));
    if($isexist > 0) {
        $return = true;
    }
    else {
        $return = false;
    }
    return $return;
 }
function get_learningplan_user($lp_id) {
    global $DB;
    $users = $DB->get_records('learning_user_learningplan', array('lp_id' => $lp_id));
    return $users;
}
function learningplan_training($lp_id) {
    global $DB;
    $training = $DB->get_records('learning_plan_training', array('lp_id' => $lp_id));
    return $training;
}
function user_learningplan($u_id) {
    global $DB;
    $page    = optional_param('page', 0, PARAM_INT);    // which page (default: page 0 - first)
    $perpage = optional_param('perpage', 0, PARAM_INT); // how many per page (default: unlimited)
    $training=$DB->get_recordset_sql('SELECT lp_id as id, (select  learning_plan  from {learning_learningplan} where id =lp_id) as learningplan
                                     FROM {learning_user_learningplan} where u_id = ?', array($u_id),  $page*$perpage, $perpage);
    return $training;
}
function status_value($status_id) {
    $status_value = '';
    if($status_id == '0') {
        $status_value = get_string('status_in_progress','block_learning_plan');
     } else if ($status_id == '1') {
         $status_value = get_string('status_not_started','block_learning_plan');
     } else if ($status_id == '2') {
         $status_value = get_string('status_completed','block_learning_plan');
     }
    return $status_value;

}
function  status_id($l_id, $u_id, $t_id) {
    global $DB;
    $status=$DB->get_record_sql('select lut.id as id from {learning_plan_training} lpt inner join {learning_user_trainingplan} lut
                                on lut.lpt_id=lpt.id  where lpt.lp_id=? AND lut.u_id=? AND lpt.t_id= ?',
                                array($l_id, $u_id, $t_id));
    $status=$status->id;
    return $status;
    }
// Return Learning Plan as String.
function get_learningplan_name ($id) {
    global $DB;
    $result = $DB->get_record_sql("SELECT learning_plan FROM {learning_learningplan} WHERE id = ?",
                                  array($id));
    return $result->learning_plan;
}
// Return User Full Name as String
function get_user_name($id) {
    global $DB;
    $result = $DB->get_record_sql("SELECT concat (firstname,' ', lastname) as name FROM {user} WHERE id = ?",
                                 array($id));
    return $result->name;
}
function delete_learningplan_record($table, $id, $url, $lp_id = '') {
    global $DB;
    // Delete Department.
    if($table == 'learning_learningplan') {
        $DB->delete_records('learning_learningplan', array('id'=> $id));
        $DB->delete_records('learning_plan_training ', array('lp_id'=> $id));
        $DB->delete_records('learning_user_learningplan ', array('lp_id'=> $id));
    }
    else if($table == 'learning_training') {
        $DB->delete_records('learning_training', array('id'=> $id));
        $DB->delete_records('learning_plan_training', array('t_id'=> $id));
    }
    else if($table == 'learning_plan_training') {
        $DB->delete_records('learning_plan_training', array('id'=> $id));
    }
    else if($table == 'learning_user_learningplan') {
        $DB->delete_records('learning_user_learningplan', array('u_id'=> $id, 'lp_id'=> $lp_id));
        $DB->delete_records('learning_user_trainingplan', array('u_id'=> $id, 'lpt_id'=> $lp_id));
    }
    redirect($url);
}


function display_list($lp_id, $u_id) {
    //  if ($lp_id && $t_id) {
    global $DB, $OUTPUT, $CFG;
    $table = new html_table();
    $table->id = 'statuslist';
    $table->head  = array(get_string('s_no', 'block_learning_plan'),
        get_string('training_name', 'block_learning_plan'),
        get_string('user', 'block_learning_plan'), get_string('start_date', 'block_learning_plan'),
        get_string('end_date', 'block_learning_plan'), get_string('status', 'block_learning_plan'),
        get_string('remarks', 'block_learning_plan'));
    $table->size  = array('5%', '30%', '20%', '10%', '10%', '10%', '25%', '10%' );
    $table->align = array('center', 'left', 'left', 'center', 'center', 'center', 'left', 'left', 'center');
    $table->width = '100%';
    $table->data  = array();
    $sql = 'select  t_id, lp_id, lut.status, lut.remarks, `u_id` as id,
            (select training_name from {learning_training} where id =t_id) as training,
            (select url from {learning_training} where id =t_id) as url,
            (select learning_plan from {learning_learningplan} where id =lp_id) as learning_plan,
            (SELECT CONCAT(firstname," ", lastname)FROM {user} where username!="guest" AND id = u_id) as name,
            (select start_date from  {learning_training} where id =t_id)as date1,
            (select end_date from {learning_training} where id =t_id) as date2
            from {learning_plan_training} lpt
            inner join {learning_user_trainingplan} lut on lut.lpt_id=lpt.id  where lpt.lp_id=? AND lut.u_id=?';
            //ORDER BY $orderby';
    $inc= 0;
    $rs = $DB->get_recordset_sql($sql, array($lp_id, $u_id));
    foreach ($rs as $log) {
        $row = array();
        $row[] = ++$inc;
        if(strlen($log->url)>0) {
            $row[] = '<a title="'.get_string('training','block_learning_plan').'" href="'.$log->url.'">'.format_string($log->training, false).'</a>';
        } else {
            $row[] = format_string($log->training, false);
        }
        $row[] = $log->name;
        $row[] = userdate($log->date1,get_string('strftimedatefullshort','core_langconfig')); //date('d-m-Y', $log->date1);
        $row[] = userdate($log->date2,get_string('strftimedatefullshort','core_langconfig')); //date('d-m-Y', $log->date2);
        $row[] = status_value($log->status) ;
        $row[] = format_string($log->remarks, false);
        $table->data[] = $row;
    }
    return $table;
}
function nav_title($viewpage) {
    $array = array(
        1 => get_string('learningpath', 'block_learning_plan'),
        2 => get_string('add_training', 'block_learning_plan'),
        4 => get_string('assign_training_learningplan', 'block_learning_plan'),
        5 => get_string('assign_learningplan_user', 'block_learning_plan'),
        6 => get_string('trainingstatus', 'block_learning_plan'),
        7 => get_string('search', 'block_learning_plan'),
    );
    return $array[$viewpage];
}

function format_string_array($arr, $striplinks = true, $options = null ) {
    if (!empty($arr)) {
        foreach ($arr as $key => $value) {
            $arr[$key] = format_string($value,$striplinks, $options);
        }
    }
    return $arr;
}
