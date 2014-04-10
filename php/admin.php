<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

function OpenACalendar_admin_menu() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	require_once __DIR__.DIRECTORY_SEPARATOR."database.php";
	
	// ##################################################### Normal Page
	$pools = OpenACalendar_db_getCurrentPools();
	echo '<div class="wrap">';
	if ($pools) {
		foreach($pools as $pool) {
			print "<p>Event Pool ".$pool['id'].": ".htmlspecialchars($pool['title'])."</p>";
			$sources = OpenACalendar_db_getCurrentSourcesForPool($pool['id']);
			if ($sources) {
				foreach ($sources as $source) {
					print "<p>Source: ".htmlspecialchars($source['baseurl']);
					print "</p>";
				}
			} else {
				echo '<p>No sources</p>';
			}
		}
	} else {
		echo '<p>No pools</p>';
	}
	echo '</div>';
}
