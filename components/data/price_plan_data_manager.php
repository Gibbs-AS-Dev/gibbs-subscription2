<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Price_Plan_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct()
  {
    // Doing nothing in particular.
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return an array of price dates for a price plan with a single price, starting at the given date, with the given 
  // description. Since there is only one price, the returned array will have only one entry. The returned array of
  // price dates can be used when creating a price plan.
  public static function create_single_price_price_dates($price, $start_date, $description)
  {
    return array(
      array(
        'from_date' => $start_date,
        'price' => $price,
        'description' => $description
      )
    );
  }

  // *******************************************************************************************************************
  // Create a price plan in the database, including the corresponding price plan lines. A price plan states how the
  // price of something varies over time. It has a start date, and the price that applies from that date. That price
  // will apply until the next entry in the price plan, or indefinitely, if there are no further entries.
  //
  // The new price plan will link to the subscription with the given $subscription_id. $type gives the type of product
  // for which the price plan applies, using the type values from the additional products table. Pass null in order to
  // have the price plan apply to the storage room rent. $price_dates is an array of price dates, where each pride date
  // is an array that holds fields for "from_date" - a date string in the format "yyyy-mm-dd" - and "price" - an
  // integer.
  //
  // The method returns true if everything was created successfully. It is up to the caller to deal with database
  // transactions, and roll back the transaction if the operation was only partially successful. Note that this method
  // does not check whether price plans for the given subscription already existed.
  public static function create_price_plan($subscription_id, $type, $price_dates)
  {
    global $wpdb;

    // Create the price plan.
    $data_item = array(
      'subscription_id' => $subscription_id,
      'type' => $type
    );
    $result = $wpdb->insert('subscription_price_plan', $data_item);
    if ($result === false)
    {
      error_log("Error while creating price plan for subscription {$subscription_id}: {$wpdb->last_error}.");
      return false;
    }
    if ($result !== 1)
    {
      error_log("Database query inserted the wrong number of rows while creating price plan for subscription {$subscription_id}. Expected: 1. Actual: {$result}.");
      return false;
    }

    // Create the price plan lines.
    return self::create_price_plan_lines($wpdb->insert_id, $price_dates);
  }

  // *******************************************************************************************************************
  // Delete all price plans linked to the subscription with the given $subscription_id, as well as all the lines that
  // belong to those price plans. Return true if the operation succeeded.
  public static function delete_price_plans_for($subscription_id)
  {
    global $wpdb;

    // Find the IDs of all price plans that are connected to the subscription with the given ID.
    $price_plan_ids = self::get_price_plan_ids_for_subscription($subscription_id);

    if (!empty($price_plan_ids))
    {
      // Delete all price plan lines for the price plans identified.
      $selection_list = self::get_selection_list('price_plan_id', $price_plan_ids);
      $result = $wpdb->query("DELETE FROM subscription_price_plan_line WHERE {$selection_list};");
      if ($result === false)
      {
        error_log("Error while deleting price plan lines for subscription {$subscription_id}: {$wpdb->last_error}. Selection list: {$selection_list}");
        return false;
      }

      // Delete all the price plans.
      $selection_list = self::get_selection_list('id', $price_plan_ids);
      $result = $wpdb->query("DELETE FROM subscription_price_plan WHERE {$selection_list};");
      if ($result === false)
      {
        error_log("Error while deleting price plans for subscription {$subscription_id}: {$wpdb->last_error}. Selection list: {$selection_list}");
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return the product price plan lines - that is, the price plan for the storage room rent - for the given
  // $subscription, or null if they could not be found.
  public static function get_product_price_plan($subscription)
  {
    return self::get_price_plan_for_type($subscription, -1);
  }

  // *******************************************************************************************************************
  // Return the insurance price plan lines for the given $subscription, or null if they could not be found.
  public static function get_insurance_price_plan($subscription)
  {
    return self::get_price_plan_for_type($subscription, Utility::ADDITIONAL_PRODUCT_INSURANCE);
  }

  // *******************************************************************************************************************
  // Return the price for the given $price_plan_lines at the moment given in $reference_date, which should be a string
  // in the format "yyyy-mm-dd". $reference_date is optional. If not present, the current date is used. Return the price
  // as an integer, or -1 if the price plan has not come into effect yet - that is, the $reference_date is before the
  // first item in the price plan.
  public static function get_price_from_price_plan($price_plan_lines, $reference_date = null)
  {
    // If the price plan lines are not provided, return -1.
    if (!isset($price_plan_lines))
    {
      return -1;
    }
    // If the date is not provided, use today's date.
    if (!isset($reference_date))
    {
      $reference_date = date('Y-m-d');
    }
    // Price plans are sorted by date. Examine each line in the price plan, and find the last line which applies to the
    // reference date.
    $price = -1;
    foreach ($price_plan_lines as $line)
    {
      // The dates are stored as strings in ISO format, and can be compared alphabetically.
      if ($reference_date < $line['start_date'])
      {
        // The reference date is before this line in the price plan comes into effect. Return the last price we had.
        return $price;
      }
      // The reference date is equal to or after this line in the price plan, so this price applies. Store the price.
      // It might be superseded by later lines, but in that case the price will be updated in the next iterations.
      $price = $line['price'];
    }
    // There were no more lines in the price plan, so the last price we found applies indefinitely. Return that.
    return $price;
  }
  
  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return the price plan lines of the price plan of the given $plan_type for the given $subscription, or null if they
  // could not be found.
  protected static function get_price_plan_for_type($subscription, $plan_type)
  {
    foreach ($subscription['price_plans'] as $price_plan)
    {
      if ($price_plan['type'] === $plan_type)
      {
        return $price_plan['lines'];
      }
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return an array of IDs of price plans that are connected to the subscription with the given $subscription_id. Each
  // ID is an integer. If the subscription was not found, or anything else went wrong, return an empty array.
  protected static function get_price_plan_ids_for_subscription($subscription_id)
  {
    global $wpdb;

    $results = $wpdb->get_results("SELECT id FROM subscription_price_plan WHERE subscription_id = {$subscription_id};",
      ARRAY_A);
    $ids = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $price_plan)
      {
        $ids[] = intval($price_plan['id']);
      }
    }
    return $ids;
  }

  // *******************************************************************************************************************
  // Return an SQL string that checks for all values in the given $values array. The name of the field that holds the
  // values is given in $field_name. For instance, if $field_name is "id" and $values is [1, 2, 3], the method will
  // return "(id = 1) OR (id = 2) OR (id = 3)".
  protected static function get_selection_list($field_name, $values)
  {
    $clauses = array();
    foreach ($values as $value)
    {
      $clauses[] = "({$field_name} = {$value})";
    }
    return implode(' OR ', $clauses);
  }

  // *******************************************************************************************************************
  // Create the price plan lines for the price plan with the given $price_plan_id, using the given $price_dates. Return
  // true if all lines were inserted successfully. The $price_dates array must be as described in the create_price_plan
  // method.
  protected static function create_price_plan_lines($price_plan_id, $price_dates)
  {
    global $wpdb;

    $values = self::get_price_plan_line_values($price_plan_id, $price_dates);
    $result = $wpdb->query("
      INSERT INTO
        subscription_price_plan_line (price_plan_id, from_date, price)
      VALUES
        {$values};
    ");
    if ($result === false)
    {
      error_log("Error while inserting lines for price plan {$price_plan_id}: {$wpdb->last_error}. Tried to insert lines: {$values}");
      return false;
    }
    if ($result !== count($price_dates))
    {
      error_log("Failed to insert the correct number of lines for price plan {$price_plan_id}. Expected: {count($price_dates)}. Actual: {$result}. Tried to insert lines: {$values}");
      return false;
    }
    return true;
  }

  // *******************************************************************************************************************
  // Turn the given $price_dates array into a string of comma-separated value triplets that can be inserted into the
  // database.
  protected static function get_price_plan_line_values($price_plan_id, $price_dates)
  {
    $result = array();
    foreach ($price_dates as $price_date)
    {
      $result[] = "({$price_plan_id}, \"{$price_date['from_date']}\", {$price_date['price']})";
    }
    return implode(',', $result);
  }

  // *******************************************************************************************************************
}
?>