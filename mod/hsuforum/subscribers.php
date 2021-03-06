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
 * This file is used to display and organise forum subscribers
 *
 * @package   mod_hsuforum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @author Mark Nielsen
 */

require_once("../../config.php");
require_once("lib.php");

$id    = required_param('id',PARAM_INT);           // forum
$group = optional_param('group',0,PARAM_INT);      // change of group
$edit  = optional_param('edit',-1,PARAM_BOOL);     // Turn editing on and off

$url = new moodle_url('/mod/hsuforum/subscribers.php', array('id'=>$id));
if ($group !== 0) {
    $url->param('group', $group);
}
if ($edit !== 0) {
    $url->param('edit', $edit);
}
$PAGE->set_url($url);

$forum = $DB->get_record('hsuforum', array('id'=>$id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$forum->course), '*', MUST_EXIST);
if (! $cm = get_coursemodule_from_instance('hsuforum', $forum->id, $course->id)) {
    $cm->id = 0;
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
if (!has_capability('mod/hsuforum:viewsubscribers', $context)) {
    print_error('nopermissiontosubscribe', 'hsuforum');
}

unset($SESSION->fromdiscussion);

$params = array(
    'context' => $context,
    'other' => array('forumid' => $forum->id),
);
$event = \mod_hsuforum\event\subscribers_viewed::create($params);
$event->trigger();

$forumoutput = $PAGE->get_renderer('mod_hsuforum');
$currentgroup = groups_get_activity_group($cm);
$options = array('forumid'=>$forum->id, 'currentgroup'=>$currentgroup, 'context'=>$context);
$existingselector = new hsuforum_existing_subscriber_selector('existingsubscribers', $options);
$subscriberselector = new hsuforum_potential_subscriber_selector('potentialsubscribers', $options);
$subscriberselector->set_existing_subscribers($existingselector->find_users(''));

if (data_submitted()) {
    require_sesskey();
    $subscribe = (bool)optional_param('subscribe', false, PARAM_RAW);
    $unsubscribe = (bool)optional_param('unsubscribe', false, PARAM_RAW);
    /** It has to be one or the other, not both or neither */
    if (!($subscribe xor $unsubscribe)) {
        print_error('invalidaction');
    }
    if ($subscribe) {
        $users = $subscriberselector->get_selected_users();
        foreach ($users as $user) {
            if (!hsuforum_subscribe($user->id, $id)) {
                print_error('cannotaddsubscriber', 'hsuforum', '', $user->id);
            }
        }
    } else if ($unsubscribe) {
        $users = $existingselector->get_selected_users();
        foreach ($users as $user) {
            if (!hsuforum_unsubscribe($user->id, $id)) {
                print_error('cannotremovesubscriber', 'hsuforum', '', $user->id);
            }
        }
    }
    $subscriberselector->invalidate_selected_users();
    $existingselector->invalidate_selected_users();
    $subscriberselector->set_existing_subscribers($existingselector->find_users(''));
}

$strsubscribers = get_string("subscribers", "hsuforum");
$PAGE->navbar->add($strsubscribers);
$PAGE->set_title($strsubscribers);
$PAGE->set_heading($COURSE->fullname);
if (has_capability('mod/hsuforum:managesubscriptions', $context) && hsuforum_is_forcesubscribed($forum) === false) {
    if ($edit != -1) {
        $USER->subscriptionsediting = $edit;
    }
    $PAGE->set_button(hsuforum_update_subscriptions_button($course->id, $id));
} else {
    unset($USER->subscriptionsediting);
}
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('forum', 'hsuforum').' '.$strsubscribers);
if (empty($USER->subscriptionsediting)) {
    $subscribers = hsuforum_subscribed_users($course, $forum, $currentgroup, $context);
    if (hsuforum_is_forcesubscribed($forum)) {
        $subscribers = mod_hsuforum_filter_hidden_users($cm, $context, $subscribers);
    }
    echo $forumoutput->subscriber_overview($subscribers, $forum->name, $forum, $course);
} else if (hsuforum_is_forcesubscribed($forum)) {
    $subscriberselector->set_force_subscribed(true);
    echo $forumoutput->subscribed_users($subscriberselector);
} else {
    echo $forumoutput->subscriber_selection_form($existingselector, $subscriberselector);
}
echo '<hr>';
echo '<div class="col-md-12" style="margin-top:50px">';
echo '<h2>Subscription requests</h2>';
$i = 1;
$users_requested = $DB->get_records('hsuforum_requests',array('forumid'=>$id));
$table = new html_table();
$table->head = (array) get_strings(array('slno','user','forumname','action'),'mod_hsuforum');
if($users_requested){
    foreach ($users_requested as $key => $user) {
        $stat = $DB->get_record('hsuforum_requests',array('forumid'=>$id,'userid'=>$user->userid));
        if($stat->status ==1){
            $flag = 1;
            $usern = $DB->get_record('user',array('id'=>$user->userid));
            $userlink = new \moodle_url('/user/profile.php', ['id' => $user->userid]);
            $username = '<a href="'.$userlink.'">'.$usern->firstname.' '.$usern->lastname.'</a>';
            $sub_form = '<form method="get" action="'.$CFG->wwwroot.'/mod/hsuforum/request.php">
                                            <input name="backtoindex" value="1" type="hidden">
                                            <input name="requestid" value="2" type="hidden">
                                            <input name="user" value="'.$user->userid.'" type="hidden">
                                            <input name="id" value="'.$forum->id.'" type="hidden">
                                            <input name="sesskey" value="'.sesskey().'" type="hidden">
                                        <button type="submit" class="btn btn-primary" style="width:45%" title="">Enable subscription</button>
                                    </form>';
            $table->data[] = array(
                            $i,
                            $username,
                            $forum->name,
                            $sub_form,
                            ); 
             $i++;
        }else if($stat->status ==2){
            $flag = 1;
            $usern = $DB->get_record('user',array('id'=>$user->userid));
            $userlink = new \moodle_url('/user/profile.php', ['id' => $user->userid]);
            $username = '<a href="'.$userlink.'">'.$usern->firstname.' '.$usern->lastname.'</a>';
            $sub_form = '<form method="get" action="'.$CFG->wwwroot.'/mod/hsuforum/request.php">
                                            <input name="backtoindex" value="1" type="hidden">
                                            <input name="requestid" value="3" type="hidden">
                                            <input name="user" value="'.$user->userid.'" type="hidden">
                                            <input name="id" value="'.$forum->id.'" type="hidden">
                                            <input name="sesskey" value="'.sesskey().'" type="hidden">
                                        <button type="submit" class="btn btn-primary" style="width:45%" title="">Disable subscription</button>
                                    </form>';
            $table->data[] = array(
                            $i,
                            $username,
                            $forum->name,
                            $sub_form,
                            ); 
             $i++;
        }else{
            $flag = 0;
        }
    }
}else{
    $flag = 2;
}
if($flag == 0){
    $table->data[] = array(
                        '','','No user to subscribe',''
                        );
}else if($flag == 2){
    $table->data[] = array(
                        '','','No record found',''
                        );
}
echo html_writer::table($table);
echo '</div>';
echo $OUTPUT->footer();


/**
 * Filters a list of users for whether they can see a given activity.
 * If the course module is hidden (closed-eye icon), then only users who have
 * the permission to view hidden activities will appear in the output list.
 *
 * @todo MDL-48625 This filtering should be handled in core libraries instead.
 *
 * @param stdClass $cm the course module record of the activity.
 * @param context_module $context the activity context, to save re-fetching it.
 * @param array $users the list of users to filter.
 * @return array the filtered list of users.
 */
function mod_hsuforum_filter_hidden_users(stdClass $cm, context_module $context, array $users) {
    if ($cm->visible) {
        return $users;
    } else {
        // Filter for users that can view hidden activities.
        $filteredusers = array();
        $hiddenviewers = get_users_by_capability($context, 'moodle/course:viewhiddenactivities');
        foreach ($hiddenviewers as $hiddenviewer) {
            if (array_key_exists($hiddenviewer->id, $users)) {
                $filteredusers[$hiddenviewer->id] = $users[$hiddenviewer->id];
            }
        }
        return $filteredusers;
    }
}
