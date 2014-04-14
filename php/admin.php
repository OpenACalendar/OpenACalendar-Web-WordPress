<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

function OpenACalendar_admin_returnToMenuHTML() {
	$url = admin_url('options-general.php?page=openacalendar-admin-menu');
	return '<p><a href="'.$url.'">Back to OpenACalendar settings</a></p>';

}

function OpenACalendar_admin_menu() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	require_once __DIR__.DIRECTORY_SEPARATOR."database.php";
	
	echo '<div class="wrap"><h2>Open A Calendar</h2>';
	
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
		print OpenACalendar_admin_returnToMenuHTML();
		
	} else 	if (isset($_POST['action']) && $_POST['action'] == 'newsource' && isset($_POST['poolid']) && intval($_POST['poolid'])) {
		
		$source = new OpenACalendarModelSource();
		$source->setPoolID($_POST['poolid']);
		$source->setBaseurl($_POST['baseurl']);
		$id = OpenACalendar_db_newSource($source);
		print '<p>Done</p>';
		print OpenACalendar_admin_returnToMenuHTML();
		
		
	} else if (isset($_POST['action']) && $_POST['action'] == 'newpool' && isset($_POST['title']) && trim($_POST['title'])) {
		
		$id = OpenACalendar_db_newPool($_POST['title']);
		print '<p>Done</p>';
		print OpenACalendar_admin_returnToMenuHTML();
		
	} else {
	
	
		// ##################################################### Normal Page
		$pools = OpenACalendar_db_getCurrentPools();
		
		if ($pools) {
			foreach($pools as $pool) {
				print "<h3>Event Pool ".$pool['id'].": ".htmlspecialchars($pool['title'])."</h3>";
				$sources = OpenACalendar_db_getCurrentSourcesForPool($pool['id']);
				
				print '<table class="wp-list-table fixed widefat">';
				print '<thead>';
				print '<tr>';
				print '<th>Source URL</th>';
				print '<th>Actions</th>';
				print '</tr>';
				print '</thead>';
				print '<tbody>';
				foreach ($sources as $source) {
					print '<tr>';
					print "<th>".htmlspecialchars($source['baseurl']).'</th>';
					print "<th>&nbsp;</th>";
					print "</tr>";
				}

				print '<tr>';
				print '<form action="" method="post">';
				print '<input type="hidden" name="action" value="newsource">';	
				print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
				print '<th>New Source URL: <input type="text" name="baseurl"></th>';
				print '<th><input type="submit" value="Create"></th>';
				print '</form>';
				print "</tr>";


				print '</tbody>';
				print '</table>';
					
				if ($sources) {
					print '<form action="" method="post">';
					print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
					print '<input type="hidden" name="action" value="getevents">';
					print '<input type="submit" value="Get events now">';
					print '</form>';
				}
				

			}
		} else {
			echo '<p>No pools</p>';
		}
		
		print '<h3>New Event Pool</h3>';
		print '<form action="" method="post">';
		print '<label>New Event Pool: <input type="text" name="title"></label>';
		print '<input type="hidden" name="action" value="newpool">';
		print '<input type="submit" value="Create">';
		print '</form>';
	}
	
	
	echo '</div>';
}
