<?php

defined('MOODLE_INTERNAL') || die;

// Required for non-standard context constants definition.
//require_once($CFG->dirroot.'/local/metadata/lib.php');

if ($hassiteconfig) {

    $moderator = get_admin();
    $site = get_site();

    $settings = new admin_settingpage('local_userquiz', get_string('usersettings','autoemailcontext_quiz'));
    $ADMIN->add('localplugins', $settings);

    $availablefields = new moodle_url('/local/autoemail/context/quiz/index.php');
   

    $name = 'autoemailcontext_quiz/message_user_enabled';
    $title = get_string('message_user_enabled', 'autoemailcontext_quiz'); 
    $description = get_string('message_user_enabled_desc', 'autoemailcontext_quiz', $availablefields->out());
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $settings->add($setting);

   

    $name = 'autoemailcontext_quiz/message_user_subject';
    $default = get_string('default_user_email_subject', 'autoemailcontext_quiz', $site->fullname);
    $title = get_string('message_user_subject', 'autoemailcontext_quiz');
    $description = get_string('message_user_subject_desc', 'autoemailcontext_quiz');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    
    $name = 'autoemailcontext_quiz/message_user';
    $title = get_string('message_user', 'autoemailcontext_quiz');
    $description = get_string('message_user_desc', 'autoemailcontext_quiz');
    $setting = new admin_setting_confightmleditor($name, $title, $description, '');
    $settings->add($setting);

    //self registration
    
    $name = 'autoemailcontext_quiz/sender_email';
    $title = get_string('sender_email', 'autoemailcontext_quiz');
    $description = get_string('sender_email_desc', 'autoemailcontext_quiz');
    $setting = new admin_setting_configtext($name, $title, $description, $moderator->email);
    $settings->add($setting);

    $name = 'autoemailcontext_quiz/sender_firstname';
    $title = get_string('sender_firstname', 'autoemailcontext_quiz');
    $description = get_string('sender_firstname_desc', 'autoemailcontext_quiz');
    $setting = new admin_setting_configtext($name, $title, $description, $moderator->firstname);
    $settings->add($setting);

    $name = 'autoemailcontext_quiz/sender_lastname';
    $title = get_string('sender_lastname', 'autoemailcontext_quiz');
    $description = get_string('sender_lastname_desc', 'autoemailcontext_quiz');
    $setting = new admin_setting_configtext($name, $title, $description, $moderator->lastname);
    $settings->add($setting);

   } 