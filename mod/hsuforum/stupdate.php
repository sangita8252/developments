<!DOCTYPE html>
<html>


<?php
    
    //global $OUTPUT;
	
	require_once('../../config.php');

	$discussionid   = optional_param('id', 0, PARAM_INT);

 $newpost = new stdClass();
        $newpost->id      = $discussionid;
        $newpost->status  = 2;
        $val= $DB->update_record("hsuforum_discussions", $newpost);

      

        if($val)
        {
        	echo 1;
        }
  ?>
  </html>