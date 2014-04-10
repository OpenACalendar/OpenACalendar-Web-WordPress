<?php

/**
 *
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class OpenACalendarEventsWidget extends WP_Widget {
	
	function __construct() {
		parent::__construct(
			'openacalendar_events_widget', // Base ID
			__('OpenACalendar Events (DEPRECEATED)', 'text_domain'), // Name
			array( 'description' => __( 'List OpenAcalendar Events (DEPRECEATED)', 'text_domain' ), ) // Args
		);
		wp_enqueue_style( 'openacalendar-events-widget', plugins_url().'/openacalendar/css/listeventswidget.css' );
	}

	protected static $includedAssets = false;
	
	const OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT = 300;
	const OPTION_DEFAULT_EVENT_COUNT = 5;
	const OPTION_DEFAULT_USE_SUMMARY_DISPLAY = 1;
	
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$groupSlug = intval($instance['groupslug']) ? intval($instance['groupslug']) : null;
		$descriptionMaxLength = isset($instance['descriptionmaxlength']) ? 
				intval($instance['descriptionmaxlength']) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT;
		$eventCount= isset($instance['eventcount']) ? 
				intval($instance['eventcount']) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_EVENT_COUNT;
		$eventusesummarydisplay = isset($instance['eventusesummarydisplay']) ? 
				intval($instance['eventusesummarydisplay']) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_USE_SUMMARY_DISPLAY;
		
		
		$jsURL = plugins_url().'/openacalendar/js/listeventswidget.js';
				
		
		$data = array(
				'descriptionMaxLength'=>$descriptionMaxLength,
				'eventCount'=>$eventCount,
				'eventUseSummaryDisplay'=>$eventusesummarydisplay ? true : false,
			);
		if ($groupSlug) {
			$data['groupID'] = $groupSlug;
			$moreURL =  $instance['baseurl'] . '/group/'. $groupSlug;
		} else {
			$moreURL = $instance['baseurl'];
		}
		
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		echo '<div id="OpenACalendarListEvents'.$args['widget_id'].'"><a href="'.
				$moreURL.
				'">'. __( 'Loading events ...', 'text_domain' ).'</a></div>';
		if (!OpenACalendarEventsWidget::$includedAssets) {
			echo '<script src="'.$jsURL.'"></script>';
			OpenACalendarEventsWidget::$includedAssets = true;
		}
		echo '<script>'.
				"OpenACalendarWidgetListEvents.place(".
					"'OpenACalendarListEvents".$args['widget_id']."',".
					"'".esc_attr($instance['baseurl'])."',".
					json_encode($data).
				");".
				'</script>';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] :  __( 'Events', 'text_domain' );
		$baseurl = isset( $instance[ 'baseurl' ] ) ? $instance[ 'baseurl' ] : 'http://demo.hasacalendar.co.uk';
		$groupslug = isset( $instance[ 'groupslug' ] ) ? $instance[ 'groupslug' ] : '';
		$descriptionmaxlength = isset( $instance[ 'descriptionmaxlength' ] ) ? 
				$instance[ 'descriptionmaxlength' ] : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT;
		$eventcount = isset( $instance[ 'eventcount' ] ) ? 
				$instance[ 'eventcount' ] : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_EVENT_COUNT;
		$eventusesummarydisplay = isset( $instance[ 'eventusesummarydisplay' ] ) ? 
				intval($instance[ 'eventusesummarydisplay' ]):
				OpenACalendarEventsWidget::OPTION_DEFAULT_USE_SUMMARY_DISPLAY;
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'baseurl' ); ?>"><?php _e( 'Base URL (use SSL if possible):' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'baseurl' ); ?>" name="<?php echo $this->get_field_name( 'baseurl' ); ?>" type="text" value="<?php echo esc_attr( $baseurl ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'groupslug' ); ?>"><?php _e( 'Group Slug:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'groupslug' ); ?>" name="<?php echo $this->get_field_name( 'groupslug' ); ?>" type="text" value="<?php echo esc_attr( $groupslug ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'descriptionmaxlength' ); ?>"><?php _e( 'Description Max Length:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'descriptionmaxlength' ); ?>" name="<?php echo $this->get_field_name( 'descriptionmaxlength' ); ?>" type="text" value="<?php echo esc_attr( $descriptionmaxlength ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventcount' ); ?>"><?php _e( 'Max Events Shown:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'eventcount' ); ?>" name="<?php echo $this->get_field_name( 'eventcount' ); ?>" type="text" value="<?php echo esc_attr( $eventcount ); ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventusesummarydisplay' ); ?>"><?php _e( 'Use Fuller Event Titles:' ); ?></label> 
		<input id="<?php echo $this->get_field_id( 'eventusesummarydisplay' ); ?>" name="<?php echo $this->get_field_name( 'eventusesummarydisplay' ); ?>" type="checkbox" value="1" <?php if ($eventusesummarydisplay) { echo "checked"; }; ?>>
		</p>
		<?php 
	}

	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['baseurl'] = ( ! empty( $new_instance['baseurl'] ) ) ? strip_tags( $new_instance['baseurl'] ) : 'http://demo.hasacalendar.co.uk';
		if (strtolower(substr($instance['baseurl'],0,7)) != 'http://' && 
				strtolower(substr($instance['baseurl'],0,8)) != 'https://') {
					$instance['baseurl'] = 'http://'.$instance['baseurl'];
		}
		$instance['groupslug'] = ( ! empty( $new_instance['groupslug'] ) ) ? intval( $new_instance['groupslug'] ) : '';
		$instance['descriptionmaxlength'] = ( isset( $new_instance['descriptionmaxlength'] ) ) ? 
				intval( $new_instance['descriptionmaxlength'] ) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_DESCRIPTION_MAX_LENGHT;
		$instance['eventcount'] = ( isset( $new_instance['eventcount'] ) ) ? 
				intval( $new_instance['eventcount'] ) : 
				OpenACalendarEventsWidget::OPTION_DEFAULT_EVENT_COUNT;
		$instance['eventusesummarydisplay'] = ( isset( $new_instance['eventusesummarydisplay'] ) ) ? 1 : 0;
		
		return $instance;
	}
}

