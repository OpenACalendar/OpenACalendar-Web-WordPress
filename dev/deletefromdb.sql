delete from wp_options where option_name='openacalendar_db_version';

drop table wp_openacalendar_event_in_pool;
drop table wp_openacalendar_event;
drop table wp_openacalendar_source;
drop table wp_openacalendar_pool;

