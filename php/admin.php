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
	
	echo '<div class="wrap">';
	
	if (isset($_POST['action']) && $_POST['action'] == 'getevents' && isset($_POST['poolid']) && intval($_POST['poolid'])) {
		// ##################################################### Fetch events for Pool

		require_once __DIR__.DIRECTORY_SEPARATOR."fetch.php";

		$pool = OpenACalendar_db_getCurrentPool(intval($_POST['poolid']));
		if ($pool) {
			$sources = OpenACalendar_db_getCurrentSourcesForPool($pool['id']);
			foreach ($sources as $source) {
				$count = OpenACalendar_getAndStoreEventsForSource($source);
				print "<p>Source: ".htmlspecialchars($source['baseurl']);
				
				print " got ".$count." events.";
				print "</p>";
			}
		}
		
	} else {
	
	
		// ##################################################### Normal Page
		$pools = OpenACalendar_db_getCurrentPools();
		
		if ($pools) {
			foreach($pools as $pool) {
				print "<p>Event Pool ".$pool['id'].": ".htmlspecialchars($pool['title'])."</p>";
				$sources = OpenACalendar_db_getCurrentSourcesForPool($pool['id']);
				if ($sources) {
					foreach ($sources as $source) {
						print "<p>Source: ".htmlspecialchars($source['baseurl']);
						print "</p>";
					}
					print '<form action="" method="post">';
					print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
					print '<input type="hidden" name="action" value="getevents">';
					print '<input type="submit" value="Get events now">';
					print '</form>';
				} else {
					echo '<p>No sources</p>';
				}
			}
		} else {
			echo '<p>No pools</p>';
		}
	}
	
	echo '</div>';
}