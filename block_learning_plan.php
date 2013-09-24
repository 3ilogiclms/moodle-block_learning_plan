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

require_once('lib.php');
class block_learning_plan extends block_base {
    public function init() {
        global $CFG, $USER, $COURSE;
        $context = context_course::instance($COURSE->id);
        if (has_capability('block/learning_plan:managepages', $context)) {
            $this->title = get_string('learning_plan', 'block_learning_plan');
        } else if (has_capability('block/learning_plan:viewpages', $context)) {
            $this->title = get_string('myview', 'block_learning_plan');
        } else {
            $this->title = get_string('learning_plan', 'block_learning_plan');
        }
    }
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }
        global $CFG, $USER, $COURSE, $PAGE;
        $this->content =  new stdClass;
        $context = context_course::instance($COURSE->id);
        if (has_capability('block/learning_plan:managepages', $context)) {
            $pageurl = new moodle_url('/blocks/learning_plan/view.php?viewpage=');
            $this->content->text .= html_writer::start_tag('li');
            $this->content->text .= html_writer::link($pageurl.'1', get_string('learningpath', 'block_learning_plan'));
            $this->content->text .= html_writer::end_tag('li');
            $this->content->text .= html_writer::start_tag('li');
            $this->content->text .= html_writer::link($pageurl.'2', get_string('add_training', 'block_learning_plan'));
            $this->content->text .= html_writer::end_tag('li');
            $this->content->text .= html_writer::start_tag('li');
            $this->content->text .= html_writer::link($pageurl.'4', get_string('assign_training_learningplan', 'block_learning_plan'));
            $this->content->text .= html_writer::end_tag('li');
            $this->content->text .= html_writer::start_tag('li');
            $this->content->text .= html_writer::link($pageurl.'5', get_string('assign_learningplan_user', 'block_learning_plan'));
            $this->content->text .= html_writer::end_tag('li');
            $this->content->text .= html_writer::start_tag('li');
            $this->content->text .= html_writer::link($pageurl.'6', get_string('trainingstatus', 'block_learning_plan'));
            $this->content->text .= html_writer::end_tag('li');
            $this->content->text .= html_writer::start_tag('li');
            $this->content->text .= html_writer::link($pageurl.'7', get_string('search', 'block_learning_plan'));
            $this->content->text .= html_writer::end_tag('li');
        } else  {
            $pageurl = new moodle_url('/blocks/learning_plan/student/view.php?id=');
            $learning_plan=user_learningplan($USER->id);
            foreach($learning_plan as $lp) {
                $this->content->text .= html_writer::start_tag('li');
                $this->content->text .= html_writer::link($pageurl.$lp->id, $lp->learningplan);
                $this->content->text .= html_writer::end_tag('li');
            }
        }
    return $this->content;
    }
}