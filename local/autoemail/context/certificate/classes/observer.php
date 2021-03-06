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
 * This plugin sends users a welcome message after logging in
 * and notify a moderator a new user has been added
 * it has a settings page that allow you to configure the messages
 * send.
 *
 * @package    local
 * @subpackage welcome
 * @copyright  2017 Bas Brands, basbrands.nl, bas@sonsbeekmedia.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//namespace local_autoemail;
namespace autoemailcontext_certificate;

defined('MOODLE_INTERNAL') || die();

class observer {

    public static function send_autoemail(\core\event\course_module_viewed $event) {
      global $CFG, $SITE,$DB;

     
       $eventdata = $event->get_data();
    
       $adminvalue = $eventdata['userid'];
       $cid = $eventdata['courseid'];
       $cerid = $eventdata['objectid'];
     

        $user = \core_user::get_user($eventdata ['userid']);
        
        $uid = $eventdata ['relateduserid'];
     
        $sender = get_admin();
       

        if (!empty($user->email)) {

            

            $config = get_config('autoemailcontext_certificate');
            $config1 = get_config('local_autoemail');
           
            $moderator = clone($sender);

            if (!empty($config->auth_plugins)) {
                $auths = explode(',', $config->auth_plugins);
                if (!in_array($user->auth, $auths)) {
                    return '';
                }
            } 

            

         

            $sender->email = $config->sender_email;
            $sender->firstname = $config->sender_firstname;
            $sender->lastname = $config->sender_lastname;
            
            $messageuserenabled = $config->message_user_enabled;
            
            $messageuser = $config->message_user. $config1->signature_subject;

            $messageusersubject = $config->message_user_subject;
                      

            $welcome = new \autoemailcontext_certificate\message($cid,$cerid);
            $messageuser = $welcome->replace_values($user, $messageuser);
            $messageusersubject = $welcome->replace_values($user, $messageusersubject);
           
            if (!empty($messageuser) && !empty($sender->email) && $messageuserenabled) {
              
                email_to_user($user, $sender, $messageusersubject, html_to_text($messageuser), $messageuser);
               
            }

            
         }    
    }


  

}       