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


require_once('../../config.php');
require_once('learning_plan_form.php');
require_once("lib.php");
global $DB;
$attributes = array();
$lp_id = required_param('id', PARAM_INT); // $_GET['id'];
$u_id = optional_param('u_id', null, PARAM_INT);// $_GET['u_id'];
$t = optional_param('t', null, PARAM_INT); // $_GET['t'];
$t_id = optional_param('t_id', null, PARAM_INT); // $_GET['t_id'];
$status = optional_param('status', null, PARAM_INT);  // $_GET['status'];
$hidetraining = optional_param('hidetraining', null, PARAM_INT); // $_GET['hidetraining'];
$hideusers = optional_param('hideusers', null, PARAM_INT); //$_GET['hideusers'];

if (!isloggedin()) {
    die(get_string('loggedinnot'));
}
require_login(NULL, false);
$context = context_system::instance();
if (!has_capability('block/learning_plan:managepages', $context)) {
    die(get_string('sitepartlist'));
}
$PAGE->set_context($context);
$PAGE->set_url('/blocks/learning_plan/ajax_bridge.php');

if($lp_id && $t_id) {
    $form = new search();
    $table = $form->display_list($lp_id, $t_id, $status);
    $a =   html_writer::table($table);
    echo $a;
} else {
    // Change training on the base of learning plan and user.
    if($lp_id && $u_id) {
        $attributes =  $DB->get_records_sql_menu('select lpt.t_id as id, (select training_name from  {learning_training} where id =lpt.t_id) as name from {learning_plan_training}
                                                 lpt inner join {learning_user_trainingplan} lut on lut.lpt_id=lpt.id  where lpt.lp_id=? AND lut.u_id=?',
                                                 array ($lp_id, $u_id), $limitfrom=0, $limitnum=0);
    }
    // Find training on the base of lp Id.
    else if($lp_id && $t) {
        $attributes =  $DB->get_records_sql_menu('SELECT t_id as id,(select training_name from  {learning_training} where id =t_id) as name FROM {learning_plan_training}
                                                 where lp_id= ?', array ($lp_id), $limitfrom=0, $limitnum=0);
    }

    // Change users on the base of Learning plan
    else if($lp_id && $hidetraining) {
        $attributes1 =  $DB->get_records_sql_menu('SELECT id, training_name as name from {learning_training}  ', null, $limitfrom=0, $limitnum=0);
        $attributes2 =  $DB->get_records_sql_menu('SELECT  t_id as id, (SELECT training_name from {learning_training} where id =t_id) as name from {learning_plan_training}
                                                  where lp_id=?', array($lp_id), $limitfrom=0, $limitnum=0);
        $attributes = array_diff($attributes1, $attributes2);
    }
    else if( $lp_id && $hideusers) {
        $attributes3 =  $DB->get_records_sql_menu('SELECT id, CONCAT(firstname," ", lastname) as name FROM {user} where firstname != "Guest User"', null, $limitfrom=0, $limitnum=0);
        $attributes4 =  $DB->get_records_sql_menu('SELECT u_id as id, (SELECT concat(firstname," ", lastname) FROM {user} WHERE id = u_id) as name FROM {learning_user_learningplan}
                                                   where lp_id = ?', array($lp_id), $limitfrom=0, $limitnum=0);
        $attributes = array_diff($attributes3, $attributes4);
    }
    else if($lp_id) {
        $attributes =  $DB->get_records_sql_menu('SELECT u.u_id as id, (SELECT  CONCAT(firstname," ", lastname)FROM {user} where username!="guest" AND id = u. u_id) as name FROM
                                                 {learning_user_learningplan} as u where lp_id= ?', array ($lp_id), $limitfrom=0, $limitnum=0);
    }
    $data = "";
    foreach ($attributes as $key => $attrib) {
        $data .= $key.'~'.format_string($attrib, false).'^';
    }
    return print_r($data);
}