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

	require_once dirname(__FILE__).DIRECTORY_SEPARATOR."database.php";
	
	echo '<div class="wrap"><h2>Open A Calendar</h2>';
	
	if (isset($_POST['action']) && $_POST['action'] == 'getevents' && isset($_POST['poolid']) && intval($_POST['poolid'])) {
		// ##################################################### Fetch events for Pool

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR."fetch.php";

		$pool = OpenACalendar_db_getCurrentPool(intval($_POST['poolid']));
		if ($pool) {
			$sources = OpenACalendar_db_getCurrentSourcesForPool($pool['id']);
			foreach ($sources as $source) {
				$count = OpenACalendar_getAndStoreEventsForSource($source);
				print "<p>Source: ".htmlspecialchars($source->getBaseurl());
				
				print " got ".$count." events.";
				print "</p>";
			}
		}
		print OpenACalendar_admin_returnToMenuHTML();
		
	} else if (isset($_POST['action']) && $_POST['action'] == 'getevents' && isset($_POST['sourceid']) && intval($_POST['sourceid'])) {
		// ##################################################### Fetch events for Source

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR."fetch.php";

		$source = OpenACalendar_db_getCurrentSource(intval($_POST['sourceid']));
		if ($source) {
			$count = OpenACalendar_getAndStoreEventsForSource($source);
			print "<p>Source: ".htmlspecialchars($source->getBaseurl());
			
			print " got ".$count." events.";
			print "</p>";
		}
		
		print OpenACalendar_admin_returnToMenuHTML();
		
		
	} else if (isset($_POST['action']) && $_POST['action'] == 'deletesource' && isset($_POST['sourceid']) && intval($_POST['sourceid'])) {
		// ##################################################### Delete Source

		$source = OpenACalendar_db_getCurrentSource(intval($_POST['sourceid']));
		if ($source) {
			OpenACalendar_db_deleteSource($source);
			print "<p>Removed Source: ".htmlspecialchars($source->getBaseurl());
			print "</p>";
		}
		
		print OpenACalendar_admin_returnToMenuHTML();
		
		
	} else 	if (isset($_POST['action']) && $_POST['action'] == 'newsource' && isset($_POST['poolid']) && intval($_POST['poolid'])) {
		
		$source = new OpenACalendarModelSource();
		$source->setPoolID($_POST['poolid']);
		if (isset($_POST['filterKey']) && $_POST['filterKey'] == 'group') {
			$source->setGroupSlug($_POST['filterValue']);
		} else if (isset($_POST['filterKey']) && $_POST['filterKey'] == 'area') {
			$source->setAreaSlug($_POST['filterValue']);
		} else if (isset($_POST['filterKey']) && $_POST['filterKey'] == 'curatedlist') {
			$source->setCuratedListSlug($_POST['filterValue']);
		} else if (isset($_POST['filterKey']) && $_POST['filterKey'] == 'country') {
			$source->setCountryCode($_POST['filterValue']);
		} else if (isset($_POST['filterKey']) && $_POST['filterKey'] == 'venue') {
			$source->setVenueSlug($_POST['filterValue']);
		}
		$source->setBaseurl($_POST['baseurl']);
		$id = OpenACalendar_db_newSource($source);
		print '<p>Done</p>';
		
		print '<form action="" method="post">';
		print '<input type="hidden" name="sourceid" value="'.$source->getId().'">';
		print '<input type="hidden" name="action" value="getevents">';
		print '<input type="submit" value="Get events from this source now">';
		print '</form>';
		
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
				print "<h3>Event Pool: ".htmlspecialchars($pool['title'])." (ID=".$pool['id'].")</h3>";
				$sources = OpenACalendar_db_getCurrentSourcesForPool($pool['id']);
				
				if ($sources) {
					print '<table class="wp-list-table fixed widefat">';
					print '<thead>';
					print '<tr>';
					print '<th>Source URL</th>';
					print '<th>Country</th>';
					print '<th>Area</th>';
					print '<th>Venue</th>';
					print '<th>Group</th>';
					print '<th>Curated List</th>';
					print '<th>Actions</th>';
					print '</tr>';
					print '</thead>';
					print '<tbody>';
					foreach ($sources as $source) {
						print '<tr>';
						print "<td>".htmlspecialchars($source->getBaseurl()).'</td>';
						print "<td>".htmlspecialchars($source->getCountryCode()).'</td>';
						print "<td>".htmlspecialchars($source->getAreaSlug()).'</td>';
						print "<td>".htmlspecialchars($source->getVenueSlug()).'</td>';
						print "<td>".htmlspecialchars($source->getGroupSlug()).'</td>';
						print "<td>".htmlspecialchars($source->getCuratedListSlug()).'</td>';
						print "<td>";
						
						print '<form action="" method="post" onsubmit="return confirm(\'Are you sure you want to remove this?\');">';
						print '<input type="hidden" name="sourceid" value="'.$source->getId().'">';
						print '<input type="hidden" name="action" value="deletesource">';
						print '<input type="submit" value="Remove Source">';
						print '</form>';
						
						print "</td>";
						print "</tr>";
					}
					print '</tbody>';
					print '</table>';
					
					
					print '<form action="" method="post">';
					print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
					print '<input type="hidden" name="action" value="getevents">';
					print '<input type="submit" value="Get events now">';
					print '</form>';
				}
				
				print '';
				print '<form action="" method="post">';
				print '<input type="hidden" name="action" value="newsource">';	
				print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
				print '<td>New Source URL: <input type="text" name="baseurl"></td>';
				print '<td colspan="5"><select name="filterKey">';
				print '<option value="">filter by?</option><option value="group">group</option><option value="area">area</option>';
				print '<option value="curatedlist">curatedlist</option><option value="country">country</option><option value="venue">venue</option>';
				print '</select>: <input type="text" name="filterValue"></td>';
				print '<td><input type="submit" value="Create New Source"></td>';
				print '</form>';

			}
		} else {
			echo '<p>No pools</p>';
		}
		
		print '<h3>New Event Pool</h3>';
		print '<form action="" method="post">';
		print '<label>Title: <input type="text" name="title"></label>';
		print '<input type="hidden" name="action" value="newpool">';
		print '<input type="submit" value="Create">';
		print '</form>';
	}
	
	
	echo '</div>';
}
