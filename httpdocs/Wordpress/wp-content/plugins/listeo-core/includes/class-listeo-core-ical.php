<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Listeo_Core_Listing class
 */
class Listeo_Core_iCal {

    private static $_instance = null;
    private static $bookings = null;


    /**
     * Allows for accessing single instance of class. Class should only be constructed once per call.
     *
     * @return self Main instance.
     * @since  1.26
     * @static
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;

    }

    public function __construct() {

        Listeo_Core_iCal::$bookings = new Listeo_Core_Bookings_Calendar;

        add_action( 'wp_ajax_add_new_listing_ical', array( $this, 'add_new_listing_ical' ) );
        add_action( 'wp_ajax_add_remove_listing_ical', array( $this, 'add_remove_listing_ical' ) );
        add_action( 'wp_ajax_refresh_listing_import_ical', array( $this, 'refresh_listing_import_ical' ) );

        // set schedules to generate ical files
        if ( ! wp_next_scheduled( 'listeo_update_booking_icals' ) ) {
            wp_schedule_event( time(), '30min', 'listeo_update_booking_icals' );
        }

        add_action( 'listeo_update_booking_icals', array( $this, 'listeo_update_booking_icals' ) );

    }


    function add_new_listing_ical() {

        $listing_id   = $_POST['listing_id'];
        $name         = $_POST['name'];
        $url          = $_POST['url'];
        $force_update = $_POST['force_update'];

        if ( empty( $name ) || empty( $url ) || ! intval( $listing_id ) ) {
            $result['type']         = 'error';
            $result['notification'] = esc_html__( "Please fill the form fields", "listeo_core" );
            wp_send_json( $result );
            die();
        }

        $extension = pathinfo( $url, PATHINFO_EXTENSION );

        $extension = explode( '?', $extension );

        $name = sanitize_title( $name );

        if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {

            $result['type']         = 'error';
            $result['notification'] = esc_html__( "Please provide valid URL", "listeo_core" );
            wp_send_json( $result );

            die();
        }

        if ( ! in_array( $extension[0], array( 'html', 'ical', 'ics', 'ifb', 'icalendar' ) ) ) {
            if ( strpos( $url, 'calendar' ) !== false || strpos( $url, 'accommodation_id' ) !== false ) {
                //let listeo in
            } else {

                $result['type']         = 'error';
                $result['notification'] = esc_html__( "No valid iCal file recognized. Please import 'ical', 'ics', 'ifb' or 'icalendar' file", "listeo_core" );
                wp_send_json( $result );

                die();

            }
        }

        $icals_array = array();
        $temp_array  = array();

        $new_ical = array(
            'name'         => $name,
            'url'          => $url,
            'force_update' => $force_update,
        );

        $temp_array['url']             = esc_url_raw( $url );
        $temp_array['name']            = esc_html( $name );
        $temp_array['force_update']    = esc_html( $force_update );
        $temp_array['bookings_author'] = get_current_user_id();

        $icals_array[] = $temp_array;
        $current_icals = get_post_meta( $listing_id, 'listeo_ical_imports', true );

        if ( is_array( $current_icals ) ) {
            //todo check if the same link was already added
            if ( in_array( $name, array_column( $current_icals, 'name' ) ) ) {
                $result['type']         = 'error';
                $result['notification'] = esc_html__( "It look's like you've already calendar with that name", "listeo_core" );
                wp_send_json( $result );

                die();
            } else if ( in_array( $url, array_column( $current_icals, 'url' ) ) ) {

                $result['type']         = 'error';
                $result['notification'] = esc_html__( "It look's like you've already added that calendar URL", "listeo_core" );
                wp_send_json( $result );

                die();
            } else {
                $current_icals = array_merge( $current_icals, $icals_array );
            }
        } else {
            $current_icals = $icals_array;
        }

        $action = update_post_meta( $listing_id, 'listeo_ical_imports', $current_icals );

        if ( $action ) {
            $output   = $this->get_saved_icals( $listing_id );
            $imported = $this->import_bookings_from_ical( $temp_array, $listing_id );
            /**
             * $imported = [
             *      imported'               => (int)
             *      skipped_already_booked  => (int)
             *      skipped_missing_slot    => (int)
             *      skipped_server_error    => (int)
             *      skipped_past            => (int)
             */

            if ( 0 < $imported['imported'] ) {
                //$imported_info = sprintf( __( "We've successfully imported %s events", 'listeo_core' ), $imported );
                $imported_info = sprintf( _n( "We've successfully imported %s event", "We've successfully imported %s events", $imported['imported'], 'listeo_core' ), $imported['imported'] );
            } else {
                $imported_info = esc_html__( "No events imported", "listeo_core" );
            }

            if ( $output ) {
                $result['type']         = 'success';
                $result['output']       = $output;
                $result['notification'] = $imported_info;
            } else {
                $result['type']         = 'error';
                $result['output']       = $output;
                $result['notification'] = $imported_info;
            }
        } else {
            $result['type']         = 'error';
            $result['notification'] = esc_html__( 'There was problem updating the field.', 'listeo_core' );

        }

        wp_send_json( $result );

        die();
    }

    function add_remove_listing_ical() {

        $listing_id = $_POST['listing_id'];
        $index      = $_POST['index'];

        $current_icals = get_post_meta( $listing_id, 'listeo_ical_imports', true );

        $removed_ical = $current_icals[ $index ];

        unset( $current_icals[ $index ] );

        $action = update_post_meta( $listing_id, 'listeo_ical_imports', $current_icals );

        $output  = $this->get_saved_icals( $listing_id );
        $removed = $this->remove_from_ical( $removed_ical, $listing_id ); // false or int (number of removed)

        if ( $removed ) {

            $removed_info = sprintf( _n( "We've successfully removed this calendar with %s event", "We've successfully removed this calendar with %s events", $removed, 'listeo_core' ), $removed );

        } else {

            $removed_info = esc_html__( "Calendar was removed, no events deleted", "listeo_core" );

        }
        if ( $action ) {

            $result['type']         = 'success';
            $result['output']       = $output;
            $result['notification'] = $removed_info;

        } else {
            $result['type']         = 'error';
            $result['output']       = $output;
            $result['notification'] = $removed_info;
        }

        wp_send_json( $result );

        die();
    }

    function refresh_listing_import_ical() {
        $listing_id = $_POST['listing_id'];
        if ( ! empty( $listing_id ) || intval( $listing_id ) ) {

            $this->import_events( $listing_id );
            $result['type']         = 'success';
            $result['notification'] = esc_html__( 'Events from calendars were imported', 'listeo_core' );

        } else {

            $result['type']         = 'error';
            $result['notification'] = esc_html__( 'There was error with the import, please try again', 'listeo_core' );

        }
        wp_send_json( $result );

        die();
    }

    public static function get_saved_icals( $listing_id ) {

        $icals_list = get_post_meta( $listing_id, 'listeo_ical_imports', true );

        ob_start();

        if ( ! empty( $icals_list ) ) : ?>
            <h4><?php esc_html_e( 'Imported Calendars', 'listeo_core' ); ?></h4>
            <ul>
                <?php
                $i = 0;
                foreach ( $icals_list as $key => $value ) { ?>
                    <li><span><?php echo esc_html( $value['name'] ); ?></span>
                        <small><?php echo url_shorten( $value['url'] ); ?></small>
                        <a href="#" data-listing-id="<?php echo esc_attr( $listing_id ); ?>"
                           data-remove="<?php echo esc_attr( $key ) ?>"
                           class="ical-remove"><?php esc_html_e( 'Remove', 'listeo_core' ); ?></a>
                    </li>
                    <?php $i ++;
                } ?>
            </ul>
            <a href="#" data-listing-id="<?php echo esc_attr( $listing_id ); ?>"
               class="update-all-icals"><?php esc_html_e( 'Import manually all calendars now', 'listeo_core' ); ?><i
                        class="tip"
                        data-tip-content="<?php esc_html_e( 'All calendars are automaticaly refreshed every 30 minutes', 'listeo_core' ); ?>"></i></a>
        <?php
        endif;
        $list = ob_get_contents();
        ob_end_clean();

        return $list;
    }


    public static function get_ical_export_url( $id ) {

        $ical_page = get_option( 'listeo_ical_page' );

        if ( $ical_page ) {

            $url  = get_permalink( $ical_page );
            $slug = get_post_field( 'post_name', $id );
            $hash = bin2hex( $id . '|' . $slug );

            return esc_url_raw( add_query_arg( 'calendar', $hash, $url ) );

        } else {
            return false;
        }

    }


    public static function generate_event( $value ) {

        $details = json_decode( $value['comment'] );
        $comment = '';
        $id      = $value['listing_id'];
        if ( isset( $details->first_name ) || isset( $details->last_name ) ) :
            $comment .= esc_html__( 'Name: ' );
            if ( isset( $details->first_name ) ) {
                $comment .= $details->first_name . ' ';
            }
            if ( isset( $details->last_name ) ) {
                $comment .= $details->last_name . ' ';
            }
            $comment .= ' ';
        endif;
        if ( isset( $details->email ) ) : $comment .= esc_html__( 'Email: ' ) . $details->email . ' '; endif;
        if ( isset( $details->phone ) ) : $comment .= esc_html__( 'Phone: ' ) . $details->phone . ' '; endif;

        $start_date = $value['date_start'];
        $end_date   = $value['date_end'];

        if ( get_option( 'listeo_ical_timezone' ) ) {

            $timestamp = date_i18n( 'Ymd\THis', time(), true );

            if ( $start_date != '' ) {
                $start_date = strtotime( $start_date );
                $start_date = date( "Ymd\THis", $start_date );
            }

            if ( $end_date != '' ) {
                $end_date = strtotime( $end_date );
                $end_date = date( 'Ymd\THis', $end_date );
            } else {
                $end_date = date( "Ymd\THis", $start_date + ( 1 * 60 * 60 ) ); // 1 hour after
            }
        } else {
            //create a UTC equivalent time for all events irrespective of timezone


            $timestamp = date_i18n( 'Ymd\THis\Z', time(), true );

            if ( $start_date != '' ) {
                $start_date = strtotime( $start_date );
                $start_date = date( "Ymd\THis\Z", $start_date );
            }

            if ( $end_date != '' ) {
                $end_date = strtotime( $end_date );
                $end_date = date( 'Ymd\THis\Z', $end_date );
            } else {
                $end_date = date( "Ymd\THis\Z", $start_date + ( 1 * 60 * 60 ) ); // 1 hour after
            }

        }


        $event = "BEGIN:VEVENT
SUMMARY:" . get_the_title( $id ) . "
DESCRIPTION:" . listeo_escape_string( $comment ) . "
DTSTART:" . $start_date . "
DTEND:" . $end_date . "
UID:" . md5( uniqid( mt_rand(), true ) ) . "@" . $_SERVER['HTTP_HOST'] . "
DTSTAMP:" . $timestamp . "
END:VEVENT
";

        // $event = 0;
        return $event;
    }


    public static function get_ical_events( $id ) {

        $ical         = false;
        $listing_type = get_post_meta( $id, '_listing_type', true );
        if ( $listing_type == 'rental' || $listing_type == 'service' ) {

            $eol  = "\r\n";
            $post = get_post( $id );

            $booking = array();

            // get reservations for next 10 years to make unable to set it in datapicker
            if ( $listing_type == 'rental' ) {
                $records = self::$bookings->get_bookings(
                    date( 'Y-m-d H:i:s', strtotime( '-1 year' ) ),
                    date( 'Y-m-d H:i:s', strtotime( '+2 years' ) ),
                    array(
                        'listing_id' => $id,
                        'type'       => 'reservation',
                        'status'     => 'icalimports', //filter out other imports
                    ),
                    $by = 'booking_date', $limit = '', $offset = '', $all = '',
                    $listing_type = 'rental'
                );
            } else {
                $records = self::$bookings->get_bookings(
                    date( 'Y-m-d H:i:s', strtotime( '-1 year' ) ),
                    date( 'Y-m-d H:i:s', strtotime( '+2 years' ) ),
                    array(
                        'listing_id' => $id,
                        'type'       => 'reservation',
                        'status'     => 'icalimports', //filter out other imports
                    ),
                    'booking_date',
                    $limit = '',
                    $offset = ''
                );
            }

            ob_start();
            foreach ( $records as $key => $value ) {
                //var_dump($value['date_start']);
                echo self::generate_event( $value );

            }
            $ical = ob_get_contents();
            ob_end_clean();
        }

        return $ical;
    }


    function listeo_update_booking_icals() {

        $args = array(
            'post_type'      => 'listing',
            'post_status'    => 'publish',
            'posts_per_page' => - 1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => 'listeo_ical_imports',
                    'compare' => 'EXISTS'
                )
            )
        );

        $query = new WP_Query( $args );
        $posts = $query->get_posts();

        foreach ( $posts as $post ) {
            $ical = $this->import_events( $post );

        }
    }

    function import_events( $listing_id ) {

        $icals_list = get_post_meta( $listing_id, 'listeo_ical_imports', true );

        if ( ! empty( $icals_list ) ) :
            foreach ( $icals_list as $key => $value ) {
                self::import_bookings_from_ical( $value, $listing_id );
            }
        endif;
    }

    function remove_from_ical( $arr, $listing_id ) {

        $url  = $arr['url'];
        $name = $arr['name'];
        $id   = $listing_id;

        //instead of just name, safer approach is to use hash, as it has standard length
        $external_status = sprintf( 'external-%s-%d', md5( $name ), $id );

        //comment unique name
        $comment_calendar_name = sprintf( '%s%dicalimport', $name, $id );

        //remove previously added bookings for given external status
        $removed = self::$bookings->delete_bookings( array(
            'listing_id' => $listing_id,
            'type'       => 'reservation',
            'status'     => $this->generate_external_status_name( $listing_id, $name ),
        ) );

        //legacy delete
        $removed += self::$bookings->delete_bookings( array(
            'listing_id' => $listing_id,
            'type'       => 'reservation',
            'comment'    => $comment_calendar_name,
        ) );

        return $removed;
    }


    /**
     * @param $arr
     * @param $listing_id
     *
     * $arr elements:
     * - url (url to iCal file)
     * - name (user-defined name)
     * - force_update (user-defined force_update field)
     * - bookings_author (wp user ID to be associated with import)
     *
     * @return array $response = [
     *      imported                    => (int) number of imported iCal Event imports.
     *      skipped_already_booked      => (int) number of skipped iCal Event imports due to lack of availability in given time slot.
     *      skipped_missing_slot        => (int) number of skipped iCal Event imports due to missing time slot.
     *      skipped_server_error        => (int) number of skipped iCal Event imports due to problem with constructing dates.
     *      skipped_past                => (int) number of skipped iCal Event imports due to import being in the past.
     * ]
     */
    function import_bookings_from_ical( $arr, $listing_id ): array {
        $url        = $arr['url'];
        $local_name = $arr['name'];
        //should update be forced, false string by default for backward compatibility
        $force_update = $arr['force_update'] ?? 'false';
        $force_update = filter_var( $force_update, FILTER_VALIDATE_BOOLEAN );
        //bookings author ID - 0 by default for backward compatibility
        $bookings_author = $arr['bookings_author'] ?? 0;

        $listing_type = get_post_meta( $listing_id, '_listing_type', true );

        try {
            $ical = new Listeo_Core_iCal_Reader(
                [
                    $url,
                ]
            );
        } catch ( Exception $exception ) {
            if ( true === WP_DEBUG ) {
                error_log( $exception->getMessage(), $exception->getCode(), $exception->getFile() );
            }

            $ical = new Listeo_Core_iCal_Reader();
        }

        $import_response = array(
            'imported'               => 0,
            'skipped_already_booked' => 0,
            'skipped_missing_slot'   => 0,
            'skipped_server_error'   => 0,
            'skipped_past'           => 0,
        );

        if ( $ical->has_events() ) {

            // before import clean everything from before for given calendar and listing
            /**
             * FIXME:
             * this should be optimized so same record will not be removed and restored every time.
             * Maybe having script that will run after import has been completed, to remove/cleanup records that are
             * missing from external calendar
             */
            $this->remove_from_ical( $arr, $listing_id );

            foreach ( $ical->events() as $event ) {

                try {
                    $event_dates = $this->parse_event_dates( $event );
                } catch ( Exception $exception ) {
                    if ( true === WP_DEBUG ) {
                        error_log( $exception->getMessage(), $exception->getCode(), $exception->getFile() );
                    }

                    //if any of the dates can't be retrieved skip this insert
                    $import_response['skipped_server_error'] ++;
                    continue;
                }

                if ( $event_dates['current']['local'] > $event_dates['date_end']['local'] ) {
                    //skip events from the past
                    $import_response['skipped_past'] ++;
                    continue;
                }

                switch ( $listing_type ) {
                    case 'service':
                        $booking_insert_response = $this->update_service_booking_for_ical_event( $listing_id, $local_name, $bookings_author, $event, $force_update );
                        break;
                    case 'rental':
                        $booking_insert_response = $this->update_rental_booking_for_ical_event( $listing_id, $local_name, $bookings_author, $event );
                        break;
                    case 'event':
                        $booking_insert_response = $this->update_event_booking_for_ical_event( $listing_id, $local_name, $bookings_author, $event );
                        break;
                    default:
                        $booking_insert_response = array(
                            'imported'               => 0,
                            'skipped_already_booked' => 0,
                            'skipped_missing_slot'   => 0,
                            'booking_ids'            => array(),
                        );
                }

                $import_response['imported']               += $booking_insert_response['imported'];
                $import_response['skipped_already_booked'] += $booking_insert_response['skipped_already_booked'];
                $import_response['skipped_missing_slot']   += $booking_insert_response['skipped_missing_slot'];
            }

        }

        return $import_response;
    }

    /**
     * @param int $listing_id
     * @param string $local_name
     * @param int $bookings_author
     * @param mixed $ical_event
     * @param false $force_update
     *
     * @return array $import_response = [
     *      imported                    => (int) Number of imported Bookings
     *      skipped_already_booked      => (int) Number of skipped imports due to lack of availability for slot
     *      skipped_missing_slot        => (int) Number of skipped imports due to missing slot
     *      booking_ids                 => (int[]) Array of booking IDs added
     * ]
     *
     * @throws Exception
     */
    public function update_service_booking_for_ical_event( int $listing_id, string $local_name, int $bookings_author, $ical_event, $force_update = false ): array {
        $import_response = array(
            'imported'               => 0,
            'skipped_already_booked' => 0,
            'skipped_missing_slot'   => 0,
            'booking_ids'            => array(),
        );

        try {
            $event_date = $this->parse_event_dates( $ical_event );
        } catch ( Exception $exception ) {
            if ( true === WP_DEBUG ) {
                error_log( $exception->getMessage(), $exception->getCode(), $exception->getFile() );
            }

            return $import_response;
        }


        $listing_slots = Listeo_Core_Bookings_Calendar::get_slots_from_meta( $listing_id );
        $day_of_week   = $this->get_day_of_the_week_for_date( $event_date['date_start']['local'] );

        $listing_slots_for_day = array();
        if ( true === isset( $listing_slots[ $day_of_week ] ) ) {
            $listing_slots_for_day = $listing_slots[ $day_of_week ];
        }

        //by default presume that given slot does not exist
        $slot_exists = false;
        //by default presume that given slot is occupied
        $slot_already_booked = true;
        //by default presume that import has failed
        $slot_imported = false;

        array_walk( $listing_slots_for_day,
            function ( &$slot_data, $key ) {
                $slot_details = explode( '|', $slot_data );
                $slot_data    = $slot_details[0];
                $slot_times   = explode( '-', $slot_data );

                /**
                 * timezone is not contained in string, and it would be treated as Zulu/UTC timezone.
                 * For this reason it is needed to say that we are looking for UTC timezone to avoid default
                 * WordPress timezone to make problem when importing.
                 *
                 * This might seems as mistake, but it will return actual time defined in slot.
                 * If default (local wp install) timezone would be used, it would cause offset of X hours depending
                 * on timezone that was selected.
                 *
                 */
                $slot_data = [
                    'time_start'    => wp_date( 'H:i:s', strtotime( trim( $slot_times[0] ) ), new DateTimeZone( 'UTC' ) ),
                    'time_end'      => wp_date( 'H:i:s', strtotime( trim( $slot_times[1] ) ), new DateTimeZone( 'UTC' ) ),
                    'max_occupancy' => intval( $slot_details[1] ),
                ];
            }
        );

        $slot_max_occupancy = 0;

        /**
         * When force update is FALSE match actual slots for insert and verify slot availability
         * It will only import if slot exists with given times and if it is available
         *
         * When force update is TRUE data would always be imported. But it will occupy as many slots as it is needed
         * To make sure that all data are entered, and slots would be displayed as booked.
         */

        foreach ( $listing_slots_for_day as $slot_data ) {
            /**
             * Simple way to match if time portion of datetime equals given time is to use strpos.
             * For ex. $date_start = 2021-02-28 12:00:00 and time_start is 12:00:00
             * expected strpos here is 11. And match will always produce 11 as result of standard sizes
             * of date string.
             *
             */
            if (
                11 === strpos( $event_date['date_start']['local'], $slot_data['time_start'] )
                && 11 === strpos( $event_date['date_end']['local'], $slot_data['time_end'] )
            ) {
                $slot_exists        = true;
                $slot_max_occupancy = $slot_data['max_occupancy'];
            }
        }

        if ( true === $slot_exists ) {
            /**
             * Matching slot found for given times.
             * If update IS FORCED go to insert regardless of is something occupying that slot already.
             * If update IS NOT FORCED check for other bookings on give date and time to determine can slot be occupied.
             */
            if ( true === $force_update ) {
                $booking_id = $this->create_booking_from_event( $listing_id, $local_name, $bookings_author, $ical_event );

                if ( 0 < $booking_id ) {
                    $import_response['imported'] ++;
                    $import_response['booking_ids'][] = $booking_id;
                }
            } else {
                $slot_bookings = Listeo_Core_Bookings_Calendar::get_slots_bookings(
                    $event_date['date_start']['local'],
                    $event_date['date_end']['local'],
                    array(
                        'listing_id' => $listing_id,
                        'type'       => 'reservation'
                    )
                );

                $slot_occupancy = count( $slot_bookings );

                if ( $slot_occupancy < $slot_max_occupancy ) {
                    $slot_already_booked = false;
                } else {
                    $import_response['skipped_already_booked'] ++;
                }
            }
        } else {
            /**
             * Slot does not exist.
             * If update IS FORCED there is series of checks to make sure what to do with this event
             * If update IS NOT FORCED skip and increment skipped_missing_slot value.
             */
            if ( true === $force_update ) {
                /**
                 * There are 2 use-cases when update IS FORCED and no slots match
                 * If there ARE slots for the day, where we need to calculate slot occupancy for given time
                 * If there ARE NO slots for the day, in which case we can simply import, as there is nothing to check
                 */

                if ( 0 < count( $listing_slots_for_day ) ) {
                    /**
                     * Slots defined for this day. But times defined do not match slots.
                     * Goal is to detect slots based on times, so that event will occupy one or more slots
                     * Resulting in occupied slots to be subtracted properly when generating slots dropdown on frontend.
                     */

                    /**
                     * Timezone is not relevant but important to avoid problems in calculation.
                     * Usage of local datetime is confusing but necessary as gmt datetime will have offset
                     */
                    $event_start_time = wp_date( 'His', strtotime( $event_date['date_start']['local'] ), $event_date['zulu_timezone'] );
                    $event_end_time   = wp_date( 'His', strtotime( $event_date['date_end']['local'] ), $event_date['zulu_timezone'] );

                    $event_slots = array_map(
                        function ( $slot_data ) use ( $event_start_time, $event_end_time, $event_date ) {
                            $slot_start_time = wp_date( 'His', strtotime( $slot_data['time_start'] ), $event_date['zulu_timezone'] );
                            $slot_end_time   = wp_date( 'His', strtotime( $slot_data['time_end'] ), $event_date['zulu_timezone'] );

                            /**
                             * Make sure that slot times occipied are correct when event start before first slot start
                             * and when it ends after last slot end time.
                             */
                            if ( $slot_start_time <= $event_end_time && $slot_end_time >= $event_start_time ) {
                                if ( $slot_start_time <= $event_start_time && $slot_end_time >= $event_end_time ) {
                                    /**
                                     * Matches events that are fitting in one slot
                                     * [start]    |--------slot---------|    [end]
                                     * [start]        |----event----|       [end]
                                     */

                                    return $slot_data;
                                } elseif ( $slot_start_time <= $event_start_time && $slot_end_time < $event_end_time ) {
                                    /**
                                     * Matches events that are starting in one and ending in one of the upcoming slots
                                     * [start]    |--------slot 1--------|--------slot 2--------|    [end]
                                     * [start]        |------------event------------|       [end]
                                     */

                                    return $slot_data;
                                } elseif ( $slot_start_time > $event_start_time && $slot_end_time >= $event_end_time ) {
                                    return $slot_data;
                                }
                            }

                            /**
                             * Return null by default; it will get removed by array_filter afterwards
                             */
                            return null;
                        },
                        $listing_slots_for_day
                    );

                    //remove items with empty value
                    $event_slots = array_filter( $event_slots );

                    foreach ( $event_slots as $event_slot ) {

                        $date = wp_date( 'Y-m-d', strtotime( $event_date['date_start']['local'] ), $event_date['zulu_timezone'] );

                        $slot_date_start_local = $date . ' ' . $event_slot['time_start'];
                        $slot_date_end_local   = $date . ' ' . $event_slot['time_end'];

                        $slot_date_start_local_datetime = new DateTime( $slot_date_start_local, $event_date['local_timezone'] );
                        $slot_date_start_local_datetime->setTimezone( $event_date['zulu_timezone'] );
                        $slot_date_start_gmt = $slot_date_start_local_datetime->format( 'Y-m-d H:i:s' );

                        $slot_date_end_local_datetime = new DateTime( $slot_date_end_local, $event_date['local_timezone'] );
                        $slot_date_end_local_datetime->setTimezone( $event_date['zulu_timezone'] );
                        $slot_date_end_gmt = $slot_date_end_local_datetime->format( 'Y-m-d H:i:s' );

                        $custom_date = [
                            'date_start' => [
                                'gmt'   => $slot_date_start_gmt,
                                'local' => $slot_date_start_local,
                            ],
                            'date_end'   => [
                                'gmt'   => $slot_date_end_gmt,
                                'local' => $slot_date_end_local,
                            ],
                        ];

                        $booking_id = $this->create_booking_from_event( $listing_id, $local_name, $bookings_author, $ical_event, $custom_date );

                        if ( 0 < $booking_id ) {
                            $import_response['imported'] ++;
                            $import_response['booking_ids'][] = $booking_id;
                        }
                    }

                    /**
                     * If event is can't be placed in slots might be out of range.
                     * Just move it to calendar when update is forced
                     */
                    if ( true === empty( $event_slots ) ) {
                        $booking_id = $this->create_booking_from_event( $listing_id, $local_name, $bookings_author, $ical_event );

                        if ( 0 < $booking_id ) {
                            $import_response['imported'] ++;
                            $import_response['booking_ids'][] = $booking_id;
                        }
                    }


                } else {
                    /**
                     * No slots for the day - run insert immediately
                     */
                    $booking_id = $this->create_booking_from_event( $listing_id, $local_name, $bookings_author, $ical_event );

                    if ( 0 < $booking_id ) {
                        $import_response['imported'] ++;
                        $import_response['booking_ids'][] = $booking_id;
                    }
                }
            } else {
                $import_response['skipped_missing_slot'] ++;
            }
        }

        if ( false === $slot_already_booked ) {
            $booking_id = $this->create_booking_from_event( $listing_id, $local_name, $bookings_author, $ical_event );

            if ( 0 < $booking_id ) {
                $import_response['imported'] ++;
                $import_response['booking_ids'][] = $booking_id;
            }
        }

        return $import_response;
    }

    /**
     * @param int $listing_id
     * @param string $local_name
     * @param int $bookings_author
     * @param mixed $ical_event
     *
     * @return array $import_response = [
     *      imported                    => (int) Number of imported Bookings
     *      skipped_already_booked      => (int) Number of skipped imports due to lack of availability for slot
     *      skipped_missing_slot        => (int) Number of skipped imports due to missing slot
     *      booking_ids                 => (int[]) Array of booking IDs added
     * ]
     *
     * @throws Exception
     */
    public function update_rental_booking_for_ical_event( int $listing_id, string $local_name, int $bookings_author, $ical_event ): array {
        $import_response = array(
            'imported'               => 0,
            'skipped_already_booked' => 0,
            'skipped_missing_slot'   => 0,
            'booking_ids'            => array(),
        );

        try {
            $event_date = $this->parse_event_dates( $ical_event );
        } catch ( Exception $exception ) {
            if ( true === WP_DEBUG ) {
                error_log( $exception->getMessage(), $exception->getCode(), $exception->getFile() );
            }

            return $import_response;
        }

        if ( true === $ical_event->all_day_event ) {
            /**
             * If it is set as all-day event by standard time portion is not included.
             * This will make problems as DateTime as mutable value will vary based on timezone.
             * Ignore timezone by using event_days from Event object
             *
             */

            $event_days                = $ical_event->event_days;
            $event_date_start_datetime = new DateTimeImmutable( current( $event_days ) );
            $event_date_end_datetime   = new DateTimeImmutable( end( $event_days ) );
        } else {
            /**
             * If multi-day event is set there is different problem.
             * Now we have timezone included which is fine, but end-result should be the same.
             * Booking should take 00:00:00 on first day and 23:59:59 on last day of booking.
             *
             * To achieve this some datetime manipulation is required to avoid timezone confusion again, and date-switching
             * due to timezone discrepancy.
             *
             * Staring with GMT/UTC timezone it is possible to have correct date in local (WordPress) timezone.
             * Stripping the time part of date time string is easy, and effective in this case, as end result is date only.
             * For both, start and end time same logic is applied to convert datetime to proper date in local timezone.
             *
             * Same logic is then applied as for all-day event, where local and gmt time will match.
             */

            $date_start = preg_replace( '/(\d{4}-\d{2}-\d{2})\s([\d{2}:?]+)/', '$1', $event_date['date_start']['local'] );
            $date_end   = preg_replace( '/(\d{4}-\d{2}-\d{2})\s([\d{2}:?]+)/', '$1', $event_date['date_end']['local'] );

            $event_date_start_datetime = new DateTimeImmutable( $date_start );
            $event_date_end_datetime   = new DateTimeImmutable( $date_end );
        }

        $event_date_start = $event_date_start_datetime->format( 'Y-m-d H:i:s' );
        $event_date_end   = $event_date_end_datetime->add( new DateInterval( 'PT23H59M59S' ) )->format( 'Y-m-d H:i:s' );

        /**
         * Force event to use all day (00:00 until 23:59) always
         */
        $custom_date = [
            'date_start' => [
                'gmt'   => $event_date_start,
                'local' => $event_date_start,
            ],
            'date_end'   => [
                'gmt'   => $event_date_end,
                'local' => $event_date_end,
            ],
        ];

        $booking_id = $this->create_booking_from_event( $listing_id, $local_name, $bookings_author, $ical_event, $custom_date );

        if ( 0 < $booking_id ) {
            $import_response['imported'] ++;
            $import_response['booking_ids'][] = $booking_id;
        }

        return $import_response;
    }

    /**
     * @param int $listing_id
     * @param string $local_name
     * @param int $bookings_author
     * @param mixed $ical_event
     *
     * @return array $import_response = [
     *      imported                    => (int) Number of imported Bookings
     *      skipped_already_booked      => (int) Number of skipped imports due to lack of availability for slot
     *      skipped_missing_slot        => (int) Number of skipped imports due to missing slot
     *      booking_ids                 => (int[]) Array of booking IDs added
     * ]
     *
     * @throws Exception
     */
    public function update_event_booking_for_ical_event( int $listing_id, string $local_name, int $bookings_author, $ical_event ): array {
        $import_response = array(
            'imported'               => 0,
            'skipped_already_booked' => 0,
            'skipped_missing_slot'   => 0,
            'booking_ids'            => array(),
        );

        /**
         * TODO: add option to read attendees to mark number of registered people for event
         */

        $booking_id = $this->create_booking_from_event( $listing_id, $local_name, $bookings_author, $ical_event );

        if ( 0 < $booking_id ) {
            $import_response['imported'] ++;
            $import_response['booking_ids'][] = $booking_id;
        }

        return $import_response;
    }

    /**
     * Create booking from ical event.
     * Dates in event can be customized using $customize_date array
     * It has same format as format returned by parse_event_dates
     *
     * @param int $listing_id
     * @param string $local_name
     * @param int $bookings_author
     * @param object|mixed $ical_event
     * @param array $customize_date
     *
     * @return int
     * @throws Exception
     * @see Listeo_Core_iCal::parse_event_dates() for format
     *
     */
    public function create_booking_from_event( int $listing_id, string $local_name, int $bookings_author, $ical_event, $customize_date = [] ): int {
        $event_date = $this->parse_event_dates( $ical_event );

        if ( false === empty( $customize_date ) ) {
            $event_date = array_merge( $event_date, $customize_date );
        }

        $booking_id = self::$bookings->insert_booking(
            array(
                'listing_id'      => $listing_id,
                'type'            => 'reservation',
                'bookings_author' => $bookings_author,
                'owner_id'        => 0,
                'date_start'      => $event_date['date_start']['local'],
                'date_start_gmt'  => $event_date['date_start']['gmt'],
                'date_end'        => $event_date['date_end']['local'],
                'date_end_gmt'    => $event_date['date_end']['gmt'],
                'comment'         => json_encode( $ical_event ),
                'order_id'        => null,
                'status'          => $this->generate_external_status_name( $listing_id, $local_name ),
                'price'           => 0,
            )
        );

        return $booking_id;
    }

    /**
     * Parse dates and timezones from iCal event
     * Calendar timezone is irelevant due to usage of Zulu (\Z) timedate string from iCal event.
     * This way we have standard time that can be converted to any timezone using DateTime class and setTimezone method.
     *
     * @param mixed|object $ical_event
     *
     * @return array $dates = [
     *      'date_start' => [
     *          'gmt'           => (string) Start Datetime in GMT/UTC/Zulu Timezone
     *          'local'         => (string) Start Datetime in Local (WP) Timezone
     *      ],
     *      'date_end'=> [
     *          'gmt'           => (string) End Datetime in GMT/UTC/Zulu Timezone
     *          'local'         => (string) End Datetime in Local (WP) Timezone
     *      ],
     *      'current'=> [
     *          'gmt'           => (string) Current Datetime in GMT/UTC/Zulu Timezone
     *          'local'         => (string) Current Datetime in Local (WP) Timezone
     *      ],
     *      'local_timezone'    => (DateTimeZone) DateTimeZone object of Local (WP) Timezone
     *      'zulu_timezone'     => (DateTimeZone) DateTimeZone object of GMT/UTC/Zulu Timezone
     * ]
     * @throws Exception
     */
    public function parse_event_dates( $ical_event ): array {
        //zulu/gmt/utc time zone - Zulu is still represented in iCal file using Z at the end of the date string
        $zulu_timezone = new DateTimeZone( 'UTC' );
        //local timezone (WP install timezone)
        $local_timezone = new DateTimeZone( wp_timezone_string() );

        //start date in Etc/Zulu (UTC/GMT+0) time zone
        $date_start_gmt = wp_date( 'Y-m-d H:i:s', strtotime( $ical_event->dtstart ), $zulu_timezone );
        //start date in WordPress default time zone (defined in Settings)
        $date_start_datetime = new DateTime( $date_start_gmt, $zulu_timezone );
        $date_start_datetime->setTimezone( $local_timezone );
        $date_start = $date_start_datetime->format( 'Y-m-d H:i:s' );

        //end date in Etc/Zulu (UTC/GMT+0) time zone
        $date_end_gmt = wp_date( 'Y-m-d H:i:s', strtotime( $ical_event->dtend ), $zulu_timezone );
        //end date in WordPress default time zone (defined in Settings)
        $date_end_datetime = new DateTime( $date_end_gmt, $zulu_timezone );
        $date_end_datetime->setTimezone( $local_timezone );
        $date_end = $date_end_datetime->format( 'Y-m-d H:i:s' );

        return array(
            'date_start'     => [
                'gmt'   => $date_start_gmt,
                'local' => $date_start,
            ],
            'date_end'       => [
                'gmt'   => $date_end_gmt,
                'local' => $date_end,
            ],
            'current'        => [
                'gmt'   => wp_date( 'Y-m-d H:i:s', time(), $zulu_timezone ),
                'local' => wp_date( 'Y-m-d H:i:s', time(), $local_timezone ),
            ],
            'local_timezone' => $local_timezone,
            'zulu_timezone'  => $zulu_timezone,
        );
    }

    /**
     * Generates unique booking status string.
     *
     * @param int $listing_id
     * @param string $local_name
     *
     * @return string
     */
    public function generate_external_status_name( int $listing_id, string $local_name ): string {
        //instead of just name, safer approach is to use hash, as it has standard length
        return sprintf( 'external-%s-%d', md5( $local_name ), $listing_id );
    }

    /**
     * Return day of the week for given date
     *
     * @param string $date
     *
     * @return false|int|string
     */
    private function get_day_of_the_week_for_date( string $date ) {
        $day_of_week = date( 'w', strtotime( $date ) );
        if ( 0 === $day_of_week ) {
            $day_of_week = 6;
        } else {
            $day_of_week = $day_of_week - 1;
        }

        return $day_of_week;
    }

    public function has_slot_for_date( $listing_id, $date_start, $date_end ) {
        $has_slot      = false;
        $listing_slots = Listeo_Core_Bookings_Calendar::get_slots_from_meta( $listing_id );

    }

    /**
     * Get available slots for Listing typed Service for given start and end date
     *
     * @param $listing_id
     * @param $date_start
     * @param $date_end
     */
    public function get_available_service_listing_slots( $listing_id, $date_start, $date_end ) {
        $listing_slots = Listeo_Core_Bookings_Calendar::get_slots_from_meta( $listing_id );
        $day_of_week   = date( 'w', strtotime( $date_start ) );
        if ( 0 === $day_of_week ) {
            $day_of_week = 6;
        } else {
            $day_of_week = $day_of_week - 1;
        }

        $listing_slots_for_day = array();
        if ( true === isset( $listing_slots[ $day_of_week ] ) ) {
            $listing_slots_for_day = $listing_slots[ $day_of_week ];
        }

        foreach ( $listing_slots_for_day as $key => $slot ) {
            $slot_details = explode( '|', $slot );
            $free_places  = $slot_details[1];

            $hours_range     = explode( ' - ', $slot_details[0] );
            $slot_hour_start = date( "H:i:s", strtotime( $hours_range[0] ) );
            $slot_hour_end   = date( "H:i:s", strtotime( $hours_range[1] ) );

            $slot_date_start = $date_start . ' ' . $slot_hour_start;
            $slot_date_end   = $date_end . ' ' . $slot_hour_end;

            $result = Listeo_Core_Bookings_Calendar::get_slots_bookings( $date_start, $date_end, array(
                'listing_id' => $listing_id,
                'type'       => 'reservation'
            ) );
        }

        die;
    }
}