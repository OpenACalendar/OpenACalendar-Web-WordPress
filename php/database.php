<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

function OpenACalendar_db_getCurrentPools() {
	global $wpdb;
	
	return $wpdb->get_results(
			"SELECT * FROM ".$wpdb->prefix."openacalendar_pool WHERE deleted=0"
			,ARRAY_A);
	
}

function OpenACalendar_db_getCurrentSourcesForPool($poolid) {
	global $wpdb;
	
	return $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."openacalendar_source WHERE deleted=0 AND poolid=%d", $poolid)
			,ARRAY_A);
}

