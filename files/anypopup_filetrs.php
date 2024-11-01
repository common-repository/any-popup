<?php
function anypopup_cron_add_minute( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['newsLetterSendEveryMinute'] = array(
	    'interval' => ANYPOPUP_FILTER_REPEAT_INTERVAL*60,
	    'display' => __('Once Every Minute')
    );
    return $schedules;
}
add_filter('cron_schedules', 'anypopup_cron_add_minute');