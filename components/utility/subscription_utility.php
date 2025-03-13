<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/product_info_utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/settings/settings.php';

class Subscription_Utility
{
  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return the final day of a subscription, as a string in "yyyy-mm-dd" format, provided it is cancelled right now.
  // Current rules are that, if the subscription is cancelled on the 15th of the month, or earlier, the subscription
  // will end on the final day of the current month. If it is cancelled on the 16th, or later, it will end on the final
  // day of the next month.
  //
  // PHP date function documentation: https://www.w3schools.com/php/func_date_date.asp
  public static function get_end_date_if_cancelled()
  {
    $currentYear = intval(date('Y'));
    $currentMonth = intval(date('m'));
    if (intval(date('j')) <= 15)
    {
      // Today is the 15th or earlier. Return the last day of the current month.
      return Utility::get_last_date("{$currentYear}-{$currentMonth}");
    }
    // Today is the 16th or later. Calculate the next month and year.
    $nextMonth = ($currentMonth + 1) % 12;
    $nextYear = $currentYear + (($currentMonth + 1) > 12 ? 1 : 0);
    // Return the last day of the next month.
    return Utility::get_last_date("{$nextYear}-{$nextMonth}");
  }

  // *******************************************************************************************************************
  // Store a new $end_date for the subscription with the given $subscription_id. $end_date should be a string in the
  // "yyyy-mm-dd" format. Return a result code that says what happened. Possible results are:
  //   OK                             The operation was successful.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  //   MISSING_INPUT_FIELD            This should never happen.
  //
  // This method may also set the product's ready status, if it is recorded in settings that the product needs to be
  // checked before it is booked by someone else. An $access_token is required in order to read settings.
  public static function set_subscription_end_date($subscription_id, $end_date, $access_token)
  {
    global $wpdb;

    // Add the subscription end date to a table of values to be updated. Also add the timestamp that says when it was
    // done.
    $data_item = array(
      'end_date' => $end_date,
      'cancelled_at' => current_time('mysql')
    );
      // *** // Do not set an end date if the subscription already has one, or if the start date is after today's date / the end date.
    $result = $wpdb->update('subscriptions', $data_item, array('id' => $subscription_id));
    if ($result === false)
    {
      error_log("Database query failed while cancelling subscription {$subscription_id}: {$wpdb->last_error}");
      return Result::DATABASE_QUERY_FAILED;
    }
    if ($result !== 1)
    {
      error_log("Database query failed to update the expected number of rows while cancelling subscription {$subscription_id}. Expected: 1. Actual: {$result}.");
      return Result::DATABASE_QUERY_FAILED;
    }

    // See if the product needs to be checked when this subscription ends.
    return self::require_check_when_subscription_ended($subscription_id, $access_token);
  }

  // *******************************************************************************************************************
  // Return the product ID to which the subscription with the given $subscription_id is subscribed, or -1 if it could
  // not be found.
  public static function get_product_for_subscription($subscription_id)
  {
    global $wpdb;

    $subscription_id = intval($subscription_id);
    $query = "
      SELECT
        product_id
      FROM
        subscriptions
      WHERE
        id = {$subscription_id};
    ";
    $result = $wpdb->get_row($query, ARRAY_A);
    if (!$result || !isset($result['product_id']) || !is_numeric($result['product_id']))
    {
      return -1;
    }
    return intval($result['product_id']);
  }

  // *******************************************************************************************************************
  // Return an array of subscriptions, with all price plans gathered neatly in a table. Each price plan has its own
  // table of lines. If $results is null, or anything other than an array, return an empty array.
  //
  // Each subscription has the following fields:
  //   subscription_id : integer
  //   buyer_id : integer
  //   owner_id : integer
  //   active : boolean               Flag that says whether the subscription was created successfully. It does not say
  //                                  whether the subscription is currently running (the end_date is used for that).
  //   start_date : string            Date in the format "yyyy-mm-dd".
  //   end_date : string              Date in the format "yyyy-mm-dd", or null if no end date has been set.
  //   product_id : integer
  //   product_name : string
  //   location_id : integer
  //   product_type_id : integer
  //   insurance_id : integer
  //   insurance_name : string
  //   price_plans : array            Array of price plans, each of which is an array with the following fields:
  //     type : integer               The type of additional product for which the price plan applies, or -1 if the
  //                                  price plan applies to the rent.
  //     lines : array                Array of price plan lines, each of which is an array with the following fields:
  //       start_date : string        String that holds the starting date for this price plan line, in the format
  //                                  'yyyy-mm-dd'.
  //       price : integer            The price that applies, starting on the given date.
  //       cause : string             The reason the price changed.
  //       description : string       String that describes this price change to the customer.
  //   access_code : string           String which holds the access code to use for the storage unit or location lock.
  //   access_link : string           String which holds a URL that can be used to unlock the storage unit or location.
  public static function parse_subscriptions($results)
  {
    $subscriptions = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $subscription_row)
      {
        $subscription_id = intval($subscription_row['subscription_id']);
        // Add the subscription if it does not already exist.
        if (!isset($subscriptions[$subscription_id]))
        {
          $subscriptions[$subscription_id] = array(
            'subscription_id' => $subscription_id,
            'buyer_id' => intval($subscription_row['buyer_id']),
            'owner_id' => intval($subscription_row['owner_id']),
            'active' => isset($subscription_row['active']) && is_numeric($subscription_row['active']) ?
              intval($subscription_row['active']) : 0,
            'start_date' => $subscription_row['start_date'],
            'end_date' => $subscription_row['end_date'],
            'product_id' => intval($subscription_row['product_id']),
            'product_name' => $subscription_row['product_name'],
            'location_id' => intval($subscription_row['location_id']),
            'product_type_id' => intval($subscription_row['product_type_id']),
            'insurance_id' => self::read_insurance_id($subscription_row['insurance_id']),
            'insurance_name' => self::read_insurance_name($subscription_row['insurance_name']),
            'price_plans' => array()
          );
          if (isset($subscription_row['access_code']))
          {
            $subscriptions[$subscription_id]['access_code'] = strval($subscription_row['access_code']);
          }
          else
          {
            $subscriptions[$subscription_id]['access_code'] = '';
          }
          if (isset($subscription_row['access_link']))
          {
            $subscriptions[$subscription_id]['access_link'] = strval($subscription_row['access_link']);
          }
          else
          {
            $subscriptions[$subscription_id]['access_link'] = '';
          }
        }
        // If the source row has price plan information, add it to the appropriate price plan.
        if (isset($subscription_row['price_plan_id']))
        {
          // Add the price plan if it does not already exist.
          $price_plan_id = $subscription_row['price_plan_id'];
          if (!isset($subscriptions[$subscription_id]['price_plans'][$price_plan_id]))
          {
            $type = $subscription_row['price_plan_type'];
            if (!isset($type) || ($type === 'null') || !is_numeric($type))
            {
              $type = -1;
            }
            else
            {
              $type = intval($type);
            }
            $subscriptions[$subscription_id]['price_plans'][$price_plan_id] = array(
              'type' => $type,
              'lines' => array()
            );
          }

          // Add the line to the price plan.
          if (isset($subscription_row['price_plan_line_cause']))
          {
            $cause = $subscription_row['price_plan_line_cause'];
          }
          else
          {
            $cause = '';
          }
          if (isset($subscription_row['price_plan_line_description']))
          {
            $description = $subscription_row['price_plan_line_description'];
          }
          else
          {
            $description = '';
          }
          $subscriptions[$subscription_id]['price_plans'][$price_plan_id]['lines'][] = array(
            'start_date' => $subscription_row['price_plan_line_start_date'],
            'price' => intval($subscription_row['price_plan_line_price']),
            'cause' => $cause,
            'description' => $description
          );
        }
      }
    }

    // Remove array keys for price plans. Sort price plan lines by date.
    foreach ($subscriptions as &$subscription)
    {
      $subscription['price_plans'] = array_values($subscription['price_plans']);
      foreach ($subscription['price_plans'] as &$price_plan)
      {
        // Sort price plan lines in ascending order, based on the date. The date is a string with the format
        // "yyyy-mm-dd", and can be sorted alphabetically using the strcmp function.
        usort($price_plan['lines'],
          function($line0, $line1)
          {
            return strcmp($line0['start_date'], $line1['start_date']);
          });
      }
    }
    return array_values($subscriptions);
  }

  // *******************************************************************************************************************
  // Return a table that holds information about a subscription, based on reading information with the following field
  // names from the given source:
  //   subscription_id
  //   buyer_id
  //   start_date
  //   end_date
  //   active (optional)
  // source is expected to be an array. The resulting table will have the following fields:
  //   id
  //   buyer_id
  //   start_date
  //   end_date
  //   status
  // status uses the SUB_ constants defined in utility.php. $reference_date is the date on which the status is
  // determined. $reference_date is optional. If not passed, today's date will be used.
  public static function get_subscription($source, $reference_date = null)
  {
    $start_date = Utility::string_to_date($source['start_date']);
    $start_date->setTime(0, 0, 0);
    if (empty($source['end_date']))
    {
      $end_date = null;
    }
    else
    {
      $end_date = Utility::string_to_date($source['end_date']);
      $end_date->setTime(0, 0, 0);
    }
    $subscription = array(
      'id' => $source['subscription_id'],
      'buyer_id' => $source['buyer_id'],
      'start_date' => $start_date,
      'end_date' => $end_date,
    );
    if (isset($source['active']))
    {
      $is_active = intval($source['active']);
    }
    else
    {
      $is_active = null;
    }
    $subscription['status'] = self::get_subscription_status($subscription, $reference_date, $is_active);
    return $subscription;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Return the given subscription's status on the given reference date, using the SUB_ constants defined in
  // utility.php. The status is determined using the subscription's start and end dates, and the given $is_active flag.
  //
  // Each subscription can be:    Start date                          End date
  // - Finished                   Before or at the reference date     Exists; before the reference date
  // - Ongoing                    Before or at the reference date     Does not exist
  // - Cancelled                  Before or at the reference date     Exists; after or at the reference date
  // - Booked                     After the reference date            Who cares?
  protected static function get_subscription_status($subscription, $reference_date, $is_active)
  {
    // If the subscription is not active, that's the subscription's status, regardless of any other fields.
    if (isset($is_active) && ($is_active === false || $is_active === 0 || $is_active === 2))
    {
      return Utility::SUB_INACTIVE;
    }

    if (empty($reference_date))
    {
      // No reference date was passed. Create a date object representing today's date, but without any time. A
      // subscription's start and end dates are dates only.
      $reference_date = new DateTime();
      $reference_date->setTime(0, 0, 0);
    }

    // If the start date is after today, it's booked (regardless of whether an end date is set).
    if ($subscription['start_date'] > $reference_date)
    {
      return Utility::SUB_BOOKED;
    }
    // The start date is today, or earlier. If there's no end date, it's ongoing.
    if (!isset($subscription['end_date']))
    {
      return Utility::SUB_ONGOING;
    }
    // There is an end date. If it's before today, the subscription is finished.
    if ($subscription['end_date'] < $reference_date)
    {
      return Utility::SUB_EXPIRED;
    }
    // The end date is today or later. It's cancelled.
    return Utility::SUB_CANCELLED;
  }

  // *******************************************************************************************************************
  // Return the given insurance ID as an integer, or -1 if it was not set.
  protected static function read_insurance_id($insurance_id)
  {
    if (is_numeric($insurance_id))
    {
      return intval($insurance_id);
    }
    return -1;
  }

  // *******************************************************************************************************************
  // Return the given insurance ID as a string, which will be empty if it was not set.
  protected static function read_insurance_name($insurance_name)
  {
    if (isset($insurance_name))
    {
      return strval($insurance_name);
    }
    return '';
  }

  // *******************************************************************************************************************
  // When a subscription ends, a product (storage unit) may need to be checked before another subscription can be
  // booked. Consult settings to see if this is the case, and - if so - set the product's readiness status to say that
  // it needs to be checked.
  protected static function require_check_when_subscription_ended($subscription_id, $access_token)
  {
    // Check settings to determine whether a product needs to be checked after a subscription ends.
    $settings = Settings_Manager::read_settings($access_token);
    if ($settings->get_require_check_after_cancel())
    {
      // It does. Read the product ID from the subscription.
      $product_id = self::get_product_for_subscription($subscription_id);
      if ($product_id < 0)
      {
        return Result::DATABASE_QUERY_FAILED;
      }

      // Update the product's ready status.
      return Product_Info_Utility::set_or_update_ready_status($product_id, Utility::READY_STATUS_CHECK);
    }

    return Result::OK;
  }

  // *******************************************************************************************************************
}
?>