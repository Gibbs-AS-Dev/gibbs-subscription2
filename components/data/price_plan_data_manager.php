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
  // Return an array of price dates for a price plan with a single $price, starting at the given $start_date, with the
  // given description. Since there is only one price, the returned array will have only one entry. The returned array
  // of price dates can be used when creating a price plan.
  //
  // The returned price dates have the following fields:
  //   from_date : string       The starting date of the period, in the format "yyyy-mm-dd".
  //   price : integer          The amount the customer has to pay.
  //   cause : string           The reason the price changed: the price rules and modifiers applied.
  //   description : string     The reason for the given price in this period, as displayed to the customer.
  //
  public static function create_single_price_price_dates($price, $start_date, $cause, $cause_modifier, $description)
  {
    return array(
      self::create_price_date($start_date, $price, self::get_cause_with_modifier($cause, $cause_modifier), $description)
    );
  }

  // *******************************************************************************************************************
  // Return an array of price dates based on the given list of special offer $price_mods, starting at the given
  // $start_date and using the provided base $price. The returned array of price dates can be used when creating a
  // price plan. $offer_description will be used for all price dates based on the special offer. $normal_description
  // will be used when the price returns to the base price, after the end of the price rule's price modifications.
  //
  // Each price mod is expected to have the following fields:
  //   price_mod : integer      The percentage modification to the base price. -10 is a 10% discount.
  //   duration : integer       The number of months that the price mod should last.
  // When the price mods stop, the base price should apply.
  //
  // The returned price dates have the following fields:
  //   from_date : string       The starting date of the period, in the format "yyyy-mm-dd".
  //   price : integer          The amount the customer has to pay.
  //   cause : string           The reason the price changed: the price rules and modifiers applied.
  //   description : string     The reason for the given price in this period, as displayed to the customer.
  public static function create_price_dates_from_price_mods($price, $start_date, $price_mods, $offer_cause,
    $offer_description, $normal_cause, $normal_modifier, $normal_description)
  {
    $price_dates = array();
    $next_date = Utility::string_to_date($start_date);
    foreach ($price_mods as $price_mod)
    {
      // Create a price date to represent this price mod.
      $price_dates[] = self::create_price_date(Utility::date_to_string($next_date),
        Utility::get_modified_price($price, $price_mod['price_mod']),
          self::get_compound_cause($normal_cause, $normal_modifier, $offer_cause, $price_mod['price_mod']),
          $offer_description);
      $next_date = self::get_next_date($next_date, $price_mod['duration']);
    }
    // Add a price date for the base price, after the end of the price rule's price modifications.
    $price_dates[] = self::create_price_date(Utility::date_to_string($next_date), $price,
      self::get_cause_with_modifier($normal_cause, $normal_modifier), $normal_description);
    return $price_dates;
  }

  // *******************************************************************************************************************
  // Create a price plan in the database, including the corresponding price plan lines. A price plan states how the
  // price of something varies over time. It has a start date, and the price that applies from that date. That price
  // will apply until the next entry in the price plan, or indefinitely, if there are no further entries.
  //
  // The new price plan will link to the subscription with the given $subscription_id. $type gives the type of product
  // for which the price plan applies, using the type values from the "additional products" database table. Pass null in
  // order to have the price plan apply to the storage unit rent. $price_dates is an array of price dates, where each
  // price date is an array that holds the following fields:
  //   from_date : string       The starting date of the period, in the format "yyyy-mm-dd".
  //   price : integer          The amount the customer has to pay.
  //   cause : string           The reason the price changed: the price rules and modifiers applied.
  //   description : string     The reason for the given price in this period, as displayed to the customer.
  //
  // The method returns true if everything was created successfully. It is up to the caller to deal with database
  // transactions, and roll back the transaction if the operation was only partially successful. Note that this method
  // does not check whether price plans for the given subscription already existed.
  public static function create_price_plan($subscription_id, $type, $price_dates)
  {
    global $wpdb;

    // Error check.
    if (!is_numeric($subscription_id))
    {
      return false;
    }
    $subscription_id = intval($subscription_id);
    if ($type !== null)
    {
      if (!is_numeric($type))
      {
        return false;
      }
      $type = intval($type);
    }

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
  // Return the product price plan lines - that is, the price plan for the storage unit rent - for the given
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
      $reference_date = Utility::get_today();
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
  // Combine the two given causes into a single string, separating the causes with a comma if they are both present.
  // Either cause may be null. If both are, an empty string will be returned.
    // *** // Add price modifiers for each.
  protected static function get_compound_cause($normal_cause, $normal_modifier, $offer_cause, $offer_modifier)
  {
    if (empty($normal_cause))
    {
      if (empty($offer_cause))
      {
        // Neither is present.
        return '';
      }
      // Only the special offer price rule is present.
      return self::get_cause_with_modifier($offer_cause, $offer_modifier);
    }
    if (empty($offer_cause))
    {
      // Only the capacity price rule is present.
      return self::get_cause_with_modifier($normal_cause, $normal_modifier);
    }
    // Both are present.
    return self::get_cause_with_modifier($normal_cause, $normal_modifier) . ', ' .
      self::get_cause_with_modifier($offer_cause, $offer_modifier);
  }

  // *******************************************************************************************************************
  // Return a string that states the given $cause of a price modification, along with the $modifier that was applied.
  // The $modifier is assumed to be a percentage, with -10 giving a 10% discount. If the cause is not stated, the
  // method will return an empty string.
  protected static function get_cause_with_modifier($cause, $modifier)
  {
    if (empty($cause))
    {
      return '';
    }
    return $cause . ': ' . strval($modifier) . '%';
  }

  // *******************************************************************************************************************
  // Return the next date in a price plan, as a DateTime object. $previous_date, which should also be a DateTime object,
  // is the start of the previous period, which lasts for the number of months given in $duration. $previous_date can be
  // a date in the middle of a month. In that case, a 1 month duration will give you the first of next month, a 2 month
  // duration will return the first of the month after that, and so on. The method always returns a date that is the
  // first of a month.
  protected static function get_next_date($previous_date, $duration)
  {
    // Get the year and month of the previous date.
    $year = intval($previous_date->format('Y'));
    $month = intval($previous_date->format('m'));
    // Add the duration to the month counter. Update the year counter if necessary.
    $month += $duration;
    while ($month > 12)
    {
      $year++;
      $month -= 12;
    }
    // Return a new DateTime object for that date.
    return Utility::string_to_date("{$year}-{$month}-01");
  }

  // *******************************************************************************************************************
  // Return a single price date, containing the given information.
  protected static function create_price_date($start_date, $price, $cause, $description)
  {
    if ($cause === null)
    {
      $cause = '';
    }
    if ($description === null)
    {
      $description = '';
    }
    return array(
      'from_date' => $start_date,
      'price' => $price,
      'cause' => $cause,
      'description' => $description
    );
  }

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

    $sql = $wpdb->prepare("SELECT id FROM subscription_price_plan WHERE subscription_id = %d;", $subscription_id);
    $results = $wpdb->get_results($sql, ARRAY_A);
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
  // return "(id = 1) OR (id = 2) OR (id = 3)". The values must be integers.
  protected static function get_selection_list($field_name, $values)
  {
    $clauses = array();
    foreach ($values as $value)
    {
      if (is_numeric($value))
      {
        $value = intval($value);
        $clauses[] = "({$field_name} = {$value})";
      }
    }
    return implode(' OR ', $clauses);
  }

  // *******************************************************************************************************************
  // Create the price plan lines for the price plan with the given $price_plan_id, using the given $price_dates. Return
  // true if all lines were inserted successfully. $price_dates is an array of price dates, where each price date is an
  // array that holds the following fields:
  //   from_date : string       The starting date of the period, in the format "yyyy-mm-dd".
  //   price : integer          The amount the customer has to pay.
  //   cause : string           The reason the price changed: the price rules and modifiers applied.
  //   description : string     The reason for the given price in this period, as displayed to the customer.
  protected static function create_price_plan_lines($price_plan_id, $price_dates)
  {
    global $wpdb;

    if (!is_numeric($price_plan_id))
    {
      return false;
    }
    $price_plan_id = intval($price_plan_id);

    $values = self::get_price_plan_line_values($price_plan_id, $price_dates);
    $result = $wpdb->query("
      INSERT INTO
        subscription_price_plan_line (price_plan_id, from_date, price, cause, description)
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
  // Turn the given $price_dates array into a string of comma-separated value sets that can be inserted into the
  // database.
  protected static function get_price_plan_line_values($price_plan_id, $price_dates)
  {
    $result = array();
    foreach ($price_dates as $price_date)
    {
      $result[] = "({$price_plan_id}, \"{$price_date['from_date']}\", {$price_date['price']}, \"{$price_date['cause']}\", \"{$price_date['description']}\")";
    }
    return implode(',', $result);
  }

  // *******************************************************************************************************************
}
?>