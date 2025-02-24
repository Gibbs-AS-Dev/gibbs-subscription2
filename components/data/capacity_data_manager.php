<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/live_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/user/user.php';

class Capacity_Data_Manager extends Live_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read the capacity figures for all locations accessible to the current user from the database. Return them as a
  // string containing a Javascript array declaration with the following fields:
  //   id : integer                 The ID of this location.
  //   name : string                The name of the location.
  //   total_count : integer        The total number of storage units at this location.
  //   ongoing_count : integer      The number of storage units at this location that have an ongoing subscription.
  //   cancelled_count : integer    The number of storage units at this location that have a cancelled subscription, and
  //                                no booked subscription.
  //   booked_count : integer       The number of storage units at this location that have a booked subscription.
  //                                Storage units that have a finished or cancelled subscription, or no previous
  //                                subscriptions at all, can have a booked subscription. Storage units with ongoing
  //                                subscriptions cannot.
  //   occupied_count : integer     The number of storage units at this location that are currently occupied. Storage
  //                                units are occupied if they have an ongoing, cancelled or booked subscription.
  //   free_count : integer         The number of storage units at this location that can be booked. Storage units are
  //                                free if they have a finished subscription, or no previous subscriptions at all.
  //   used_capacity : float        the currently used capacity at this location, as a floating point number between 0
  //                                and 1. The number is the occupied_count divided by the total_count. If the
  //                                total_count is 0, the number will be -1.
  public function read()
  {
    global $wpdb;

    $query = "
      SELECT
        l.id AS id,
        l.name AS name,
        COUNT(p.ID) AS total_count,
        COUNT(CASE WHEN s_ongoing.product_id IS NOT NULL THEN p.ID END) AS ongoing_count,
        COUNT(CASE WHEN (s_cancelled.product_id IS NOT NULL) AND (s_booked.product_id IS NULL) THEN p.ID END) AS cancelled_count,
        COUNT(CASE WHEN s_booked.product_id IS NOT NULL THEN p.ID END) AS booked_count
      FROM
        subscription_product_location l
      LEFT JOIN
        {$wpdb->prefix}posts p ON l.id = p.location_id
      LEFT JOIN
        subscriptions s_ongoing ON (p.ID = s_ongoing.product_id) AND (s_ongoing.start_date <= CURRENT_DATE) AND (s_ongoing.end_date IS NULL)
      LEFT JOIN
        subscriptions s_cancelled ON (p.ID = s_cancelled.product_id) AND (s_cancelled.start_date <= CURRENT_DATE) AND (s_cancelled.end_date >= CURRENT_DATE)
      LEFT JOIN
        subscriptions s_booked ON (p.ID = s_booked.product_id) AND (s_booked.start_date > CURRENT_DATE)
      WHERE 
        p.post_author = {$this->get_user_group_user_id()} AND
        p.post_type = 'subscription' AND
        p.subscription IS NOT NULL
      GROUP BY
        l.id;
    ";
    $results = $wpdb->get_results($query, ARRAY_A);
    $table = "[";
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $location)
      {
        $table .= "[";
        $table .= strval($location['id']);
        $table .= ", '";
        $table .= strval($location['name']);
        $table .= "', ";
        $total_count = intval($location['total_count']);
        $table .= strval($total_count);
        $table .= ", ";
        $table .= strval($location['ongoing_count']);
        $table .= ", ";
        $table .= strval($location['cancelled_count']);
        $table .= ", ";
        $table .= strval($location['booked_count']);
        $table .= ", ";
        $occupied_count = intval($location['ongoing_count']) + intval($location['cancelled_count']) +
          intval($location['booked_count']);
        $table .= strval($occupied_count);
        $table .= ", ";
        $table .= strval($total_count - $occupied_count);
        $table .= ", ";
        if ($total_count <= 0)
        {
          $table .= "-1";
        }
        else
        {
          $table .= strval($occupied_count / $total_count);
        }
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Return the currently used capacity, as a floating point number between 0 and 1, at the location with the given ID,
  // or -1 if it could not be found. The used capacity is the number of occupied storage units, divided by the total
  // number of storage units. If $product_types is present, this method limits the calculation to storage units of the
  // type given in $product_types, both for the occupied storage units and the total number of them, as if the location
  // only had units of those types. $product_types should be an array of product type IDs.
  //
  // A product is occupied if either of the following is true:
  //   - It has an ongoing subscription (the start date is before or equal to today's date, and the end date is null).
  //   - It has a cancelled subscription (the start date is before or equal to today's date, and the end date is equal
  //     to or after today's date).
  //   - It has a booked subscription (the start date is after today's date).
  public function read_used_capacity($location_id, $product_types = null)
  {
    global $wpdb;

    // Convert the array of product type IDs to a comma separated string.
    if (isset($product_types) && is_array($product_types))
    {
      $product_types = implode(', ', array_map('intval', $product_types));
    }
    else
    {
      $product_types = null;
    }

    // Create the SQL statement, including a filter for product types, if appropriate. The filter values can be
    // inserted directly into the string, as they have been sanitised here.
    $query = "
      SELECT
        l.id AS id,
        l.name AS name,
        COUNT(p.ID) AS total_count,
        COUNT(CASE WHEN s_ongoing.product_id IS NOT NULL THEN p.ID END) AS ongoing_count,
        COUNT(CASE WHEN (s_cancelled.product_id IS NOT NULL) AND (s_booked.product_id IS NULL) THEN p.ID END) AS cancelled_count,
        COUNT(CASE WHEN s_booked.product_id IS NOT NULL THEN p.ID END) AS booked_count
      FROM
        subscription_product_location l
      LEFT JOIN
        {$wpdb->prefix}posts p ON l.id = p.location_id
      LEFT JOIN
        subscriptions s_ongoing ON (p.ID = s_ongoing.product_id) AND (s_ongoing.start_date <= CURRENT_DATE) AND (s_ongoing.end_date IS NULL)
      LEFT JOIN
        subscriptions s_cancelled ON (p.ID = s_cancelled.product_id) AND (s_cancelled.start_date <= CURRENT_DATE) AND (s_cancelled.end_date >= CURRENT_DATE)
      LEFT JOIN
        subscriptions s_booked ON (p.ID = s_booked.product_id) AND (s_booked.start_date > CURRENT_DATE)
      WHERE 
        l.id = %d AND
    " .
    (isset($product_types) ? "p.product_type_id IN ({$product_types}) AND" : '') .
    "
        p.post_author = {$this->get_user_group_user_id()} AND
        p.post_type = 'subscription' AND
        p.subscription IS NOT NULL;
    ";
    $sql = $wpdb->prepare($query, $location_id);
    $results = $wpdb->get_results($sql, ARRAY_A);
    if (!Utility::array_with_one($results) || !is_array($results[0]))
    {
      error_log("Failed to get used capacity at location {$location_id}. Result: " . print_r($results, true));
      return -1;
    }
    $location = $results[0];
    $total_count = intval($location['total_count']);
    if (intval($total_count) <= 0)
    {
      return -1;
    }
    $occupied_count = intval($location['ongoing_count']) + intval($location['cancelled_count']) +
      intval($location['booked_count']);
    return $occupied_count / $total_count;
  }

  // *******************************************************************************************************************
}
?>