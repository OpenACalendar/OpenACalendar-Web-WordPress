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

function OpenACalendar_admin_newSourceHTML() {
	return 'New Source URL: <input type="text" name="baseurl"> <select name="filterKey">'.
		'<option value="">filter by?</option><option value="group">group</option><option value="area">area</option>'.
		'<option value="curatedlist">curatedlist</option><option value="country">country</option><option value="venue">venue</option>'.
		'<option value="userattending">user attending</option>'.
		'</select>: <input type="text" name="filterValue">';
}


function OpenACalendar_admin_process_new_source($poolid) {
	$source = new OpenACalendarModelSource();
	$source->setPoolID($poolid);
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
	} else if (isset($_POST['filterKey']) && $_POST['filterKey'] == 'userattending') {
		$source->setUserAttendingEvents($_POST['filterValue']);
	}
	$source->setBaseurl($_POST['baseurl']);
	return OpenACalendar_db_newSource($source);
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
				try {
					$count = OpenACalendar_getAndStoreEventsForSource($source);
					print "<p>Source: ".htmlspecialchars($source->getBaseurl());
					print " got ".$count." events.";
					print "</p>";
				} catch (OpenACalendarGetEventsException $error) {
					print "<p>Source: ".htmlspecialchars($source->getBaseurl());
					print " had an error! Message: ".$error->getMessage();
					print "</p>";
				}
			}
		}
		print OpenACalendar_admin_returnToMenuHTML();
		
	} else if (isset($_POST['action']) && $_POST['action'] == 'getevents' && isset($_POST['sourceid']) && intval($_POST['sourceid'])) {
		// ##################################################### Fetch events for Source

		require_once dirname(__FILE__).DIRECTORY_SEPARATOR."fetch.php";

		$source = OpenACalendar_db_getCurrentSource(intval($_POST['sourceid']));
		if ($source) {
			try {
				$count = OpenACalendar_getAndStoreEventsForSource($source);
				print "<p>Source: ".htmlspecialchars($source->getBaseurl());
				print " got ".$count." events.";
				print "</p>";
			} catch (OpenACalendarGetEventsException $error) {
				print "<p>Source: ".htmlspecialchars($source->getBaseurl());
				print " had an error! Message: ".$error->getMessage();
				print "</p>";
			}
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

		try {
			$id = OpenACalendar_admin_process_new_source($_POST['poolid']);
			print '<p>Done</p>';

			print '<form action="" method="post">';
			print '<input type="hidden" name="sourceid" value="'.$id.'">';
			print '<input type="hidden" name="action" value="getevents">';
			print '<input type="submit" value="Get events from this source now">';
			print '</form>';

			print OpenACalendar_admin_returnToMenuHTML();
		} catch (OpenACalendarCountryNotRecognisedError $error) {
			print 'Sorry, that country is not recognised. Use a country code like GB or DE.';
			print OpenACalendar_admin_returnToMenuHTML();
		}
		
	} else if (isset($_POST['action']) && $_POST['action'] == 'newpool' && isset($_POST['title']) && trim($_POST['title'])) {
		
		$poolid = OpenACalendar_db_newPool($_POST['title']);

		try {
			$sourceid = OpenACalendar_admin_process_new_source($poolid);

			print '<p>Done</p>';

			print '<form action="" method="post">';
			print '<input type="hidden" name="sourceid" value="'.$sourceid.'">';
			print '<input type="hidden" name="action" value="getevents">';
			print '<input type="submit" value="Get events from this source now">';
			print '</form>';

			print OpenACalendar_admin_returnToMenuHTML();

		} catch (OpenACalendarCountryNotRecognisedError $error) {
			print 'Sorry, that country is not recognised. Use a country code like GB or DE.';
			print OpenACalendar_admin_returnToMenuHTML();
		}


	} else if (isset($_POST['action']) && $_POST['action'] == 'getlisteventsshortcode' && isset($_POST['poolid']) && intval($_POST['poolid'])) {

		$pool = OpenACalendar_db_getCurrentPool(intval($_POST['poolid']));
		if ($pool) {

			$attributes = OpenACalendar_shortcode_events_getDefaultAttributes();
			$attributes['poolid'] = $pool['id'];
			foreach($attributes as $key=>$value) {
				if ($key != 'poolid' && isset($_POST['attribute_'.$key])) {
					$attributes[$key] = $_POST['attribute_'.$key];
				}
			}

			print "<h3>Options</h3>";


			print '<form action="" method="post">';
			print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
			print '<input type="hidden" name="action" value="getlisteventsshortcode">';

			print '<h4>descriptionmaxlength</h4>';
			print '<div>Maximum number of characters of the description to print.</div>';
			print '<input type="text" name="attribute_descriptionmaxlength" value="'.htmlspecialchars($attributes['descriptionmaxlength']).'">';


			print '<h4>usesummarydisplay</h4>';
			print '<div>Does display title include group or not? "yes" or "no".</div>';
			print '<input type="text" name="attribute_usesummarydisplay" value="'.htmlspecialchars($attributes['usesummarydisplay']).'">';

			print '<h4>startformat</h4>';
			print '<div>Format of start date and time. Should match <a href="http://php.net/date" target="_blank">PHP date formats</a>.</div>';
			print '<input type="text" name="attribute_startformat" value="'.htmlspecialchars($attributes['startformat']).'">';

			print '<h4>endformat</h4>';
			print '<div>Format of end date and time. Optional. Should match <a href="http://php.net/date" target="_blank">PHP date formats</a>.</div>';
			print '<input type="text" name="attribute_endformat" value="'.htmlspecialchars($attributes['endformat']).'">';

			print '<h4>endformatsameday</h4>';
			print '<div>Format of end time if start and end are on the same day. Optional. If not given, endformat will be used. Should match <a href="http://php.net/date" target="_blank">PHP date formats</a>.</div>';
			print '<input type="text" name="attribute_endformatsameday" value="'.htmlspecialchars($attributes['endformatsameday']).'">';

			print '<h4>startandenddivider</h4>';
			print '<div>If endformat is given, this is the phrase that will separate the start and end..</div>';
			print '<input type="text" name="attribute_startandenddivider" value="'.htmlspecialchars($attributes['startandenddivider']).'">';

			print '<h4>eventcount</h4>';
			print '<div>How many events to show.</div>';
			print '<input type="text" name="attribute_eventcount" value="'.htmlspecialchars($attributes['eventcount']).'">';

			print '<h4>url</h4>';
			print '<div>One of "site" or "url". If "site", url will be address of page on OpenACalendar site. If "url", url of event.</div>';
			print '<input type="text" name="attribute_url" value="'.htmlspecialchars($attributes['url']).'">';

			print '<h4>image</h4>';
			print '<div>Whether to show image. "false", "full" for full size, "normal" for normal size or a pixel dimension for a custom size.</div>';
			print '<input type="text" name="attribute_image" value="'.htmlspecialchars($attributes['image']).'">';

			print '<h4>eventshowmorelink</h4>';
			print '<div>Whether to show "more" link under each event. "yes" or "no".</div>';
			print '<input type="text" name="attribute_eventshowmorelink" value="'.htmlspecialchars($attributes['eventshowmorelink']).'">';


			print '<div style="padding-top: 40px;"><input type="submit" value="Update Shortcode with your options"></div>';
			print '</form>';


			print "<h3>Your Shortcode</h3>";

			print "<div>[openacalendar_events ";
			foreach($attributes as $key=>$value) {
				print ' '.$key.'="'.$value.'"';
			}
			print "]</div>";

			print OpenACalendar_admin_returnToMenuHTML();

			print "<h3>Content of Results (style may not match)</h3>";

			$html = OpenACalendar_shortcode_events($attributes);
			print $html;

		}
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
					print '<th>User Attending</th>';
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
						print "<td>".htmlspecialchars($source->getUserAttendingEvents()).'</td>';
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

					print '<form action="" method="post">';
					print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
					print '<input type="hidden" name="action" value="getlisteventsshortcode">';
					print '<input type="submit" value="Get list events shortcode to use">';
					print '</form>';


				}
				
				print '';
				print '<form action="" method="post">';
				print '<input type="hidden" name="action" value="newsource">';	
				print '<input type="hidden" name="poolid" value="'.$pool['id'].'">';
				print '<td colspan="6">'.OpenACalendar_admin_newSourceHTML().'</td>';
				print '<td><input type="submit" value="Create New Source"></td>';
				print '</form>';

			}
		} else {
			echo '<p>No pools</p>';
		}
		
		print '<h3>New Event Pool and source of events</h3>';
		print '<form action="" method="post"><input type="hidden" name="action" value="newpool">';
		print '<div><label>Title: <input type="text" name="title"></label></div>';
		print '<div>'.OpenACalendar_admin_newSourceHTML().'</div>';
		print '<input type="submit" value="Create">';
		print '</form>';
	}
	
	
	echo '</div>';
}
