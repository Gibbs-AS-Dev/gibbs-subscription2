<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/single_table_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/offer/offer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/product_type_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/location_data_manager.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/capacity_data_manager.php';

class Price_Rule_Data_Manager extends Single_Table_Data_Manager
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // Price rule type constants. Price rules have different types:
  //   Capacity         Changes to the base monthly price due to the location being very full or very empty.
  //   Special offer    Time limited offers that modify the price for a number of months, then revert to the base price.
  public const RULE_TYPE_CAPACITY = 0;
  public const RULE_TYPE_SPECIAL_OFFER = 1;

  // Price rule status constants.
  public const STATUS_EXPIRED = 0;
  public const STATUS_ONGOING = 1;
  public const STATUS_NOT_STARTED = 2;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    parent::__construct($new_access_token);
    $this->add_action('create_price_rule', Utility::ROLE_COMPANY_ADMIN, 'create');
    $this->add_action('update_price_rule', Utility::ROLE_COMPANY_ADMIN, 'update');
    $this->add_action('delete_price_rule', Utility::ROLE_COMPANY_ADMIN, 'delete');
    $this->database_table = 'subscription_price_rules';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Read all price rules owned by the current user from the database. Return them as an array containing two strings,
  // each of which is a Javascript array declaration. The one stored under the key "capacity" holds capacity price
  // rules. The one stored under the key "special_offer" holds special offer price rules.
  public function read()
  {
    // Read all price rules from the database.
    $price_rules = $this->get_price_rules();

    // Create Javascript tables.
    return array(
      'capacity' => self::get_price_rule_js_table($price_rules, self::RULE_TYPE_CAPACITY),
      'special_offer' => self::get_price_rule_js_table($price_rules, self::RULE_TYPE_SPECIAL_OFFER)
    );
  }

  // *******************************************************************************************************************
  // Create an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_create method.
  //
  // The item to be created can be passed as a parameter. If not, it will be read from the request.
  //
  // Override to create price mods, and set up connections to product types and locations. These are all in separate
  // tables.
  public function create($data_item = null)
  {
    global $wpdb;

    // Read input data.
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
    // Keep the tables for afterwards. Remove them from the price rule that goes to the database. They have to be passed
    // as part of the data item: we need them, but cannot change the signature of the method we are overriding.
    $price_mods = $data_item['price_mods'];
    unset($data_item['price_mods']);
    $for_product_types = $data_item['for_product_types'];
    unset($data_item['for_product_types']);
    $for_locations = $data_item['for_locations'];
    unset($data_item['for_locations']);

    // Create the new price rule in the database.
    $wpdb->query('START TRANSACTION');
    $result = parent::create($data_item);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Create price mods, and links to product types and locations.
    if (!self::create_price_mods($this->created_item_id, $price_mods) ||
      !self::link_price_rule_to_product_types($this->created_item_id, $for_product_types) ||
      !self::link_price_rule_to_locations($this->created_item_id, $for_locations))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // All operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while creating price rule: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Update an item in the database. Return an integer result code that can be used to inform the user of the result of
  // these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_update method.
  //
  // The item to be updated, and its ID, can be passed as parameters. If not, they will be read from the request.
  //
  // Override to delete and recreate price mods, and reestablish connections to product types and locations. These are
  // all in separate tables.
  public function update($id = null, $data_item = null)
  {
    global $wpdb;

    // Sanitise input data, and ensure the ID is available as well.
    if (!isset($data_item))
    {
      $data_item = $this->get_data_item();
      if (!isset($data_item))
      {
        return Result::MISSING_INPUT_FIELD;
      }
    }
    if (!isset($id))
    {
      if (!Utility::integer_posted($this->id_posted_name))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      $id = Utility::read_posted_integer($this->id_posted_name);
    }

    // Keep the tables for afterwards. Remove them from the price rule that goes to the database. They have to be passed
    // as part of the data item: we need them, but cannot change the signature of the method we are overriding.
    $price_mods = $data_item['price_mods'];
    unset($data_item['price_mods']);
    $for_product_types = $data_item['for_product_types'];
    unset($data_item['for_product_types']);
    $for_locations = $data_item['for_locations'];
    unset($data_item['for_locations']);

    // Update the price rule in the database.
    $wpdb->query('START TRANSACTION');
    $result = parent::update($id, $data_item);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // Delete existing price mods, and existing links to product types and locations. Create new ones depending on the
    // data passed from the client.
    if (!self::clear_price_rule_product_type_links($id) ||
      !self::link_price_rule_to_product_types($id, $for_product_types) ||
      !self::clear_price_rule_location_links($id) ||
      !self::link_price_rule_to_locations($id, $for_locations) ||
      !self::delete_price_mods($id) ||
      !self::create_price_mods($id, $price_mods))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // Somehow, all operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while updating price rule: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Delete an item from the database. Return an integer result code that can be used to inform the user of the result
  // of these operations:
  //   OK                             The operation was successful.
  //   MISSING_INPUT_FIELD            The user did not pass all the required fields.
  //   DATABASE_QUERY_FAILED          The call to update the Wordpress database failed, for reasons unknown.
  // The method may return other results as well, depending on the result of the can_delete method.
  //
  // The ID of the item to be deleted can be passed as a parameter. If not, it will be read from the request.
  //
  // Override to first delete the price mods and the connections to product types and locations. These are in separate
  // tables, and the entries must be removed before the price rule itself can be deleted.
  public function delete($id = null)
  {
    global $wpdb;

    // Ensure the ID is available.
    if (!isset($id))
    {
      if (!Utility::integer_posted($this->id_posted_name))
      {
        return Result::MISSING_INPUT_FIELD;
      }
      $id = Utility::read_posted_integer($this->id_posted_name);
    }

    // Ensure the data item with that ID can be deleted.
    $result = $this->can_delete($id);
    if ($result !== Result::OK)
    {
      return $result;
    }

    // Delete product type links, location links and price mods.
    $wpdb->query('START TRANSACTION');
    if (!self::clear_price_rule_product_type_links($id) || !self::clear_price_rule_location_links($id) ||
      !self::delete_price_mods($id))
    {
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }

    // Delete the data item itself.
    $result = parent::delete($id);
    if ($result !== Result::OK)
    {
      $wpdb->query('ROLLBACK');
      return $result;
    }

    // All operations succeeded. Commit the changes.
    if ($wpdb->query('COMMIT') === false)
    {
      error_log('Commit failed while deleting price rule: ' . $wpdb->last_error);
      $wpdb->query('ROLLBACK');
      return Result::DATABASE_QUERY_FAILED;
    }
    return Result::OK;
  }

  // *******************************************************************************************************************
  // Update the given $offer with a capacity price rule and price modifier based on the capacity of the location given
  // in the offer, and the storage unit type selected.
  //
  // Also update the given $offer with a special offer price rule and list of price modifiers, based on the location and
  // product type selected in the offer.
  //
  // If, in either case, no applicable price rule could be found, the offer will be updated to reflect that fact.
  public function apply_price_rules_to(&$offer)
  {
    // Read all price rules from the database. We'll need them all, and the list can be filtered later.
    $all_price_rules = $this->get_price_rules();

    // Update the offer for capacity and special offers.
    $this->update_offer_based_on_capacity($offer, $all_price_rules);
    $this->update_offer_based_on_special_offers($offer, $all_price_rules);
  }

  // *******************************************************************************************************************
  // Read price mods posted to the server. Return false if insufficient information was posted, which includes if no
  // price mods were posted. There must be at least one price mod. Otherwise, return an array of price mods. Each price
  // mod is an array which holds all the fields required for the price mod to be added to the database. Some of the
  // fields in the returned price mods will be null, depending on the $type of price rule being read. Use the RULE_TYPE_
  // constants. The method expects the following fields to be posted:
  //
  //   price_mod_count : integer                  The number of price mods that were actually posted to the server.
  //   price_mod_$1 : integer                     $1 represents a number from 0 to (price_mod_count - 1). Each item
  //                                              holds a price modifier figure.
  // For capacity price mods only:
  //   min_capacity_$1 : integer                  $1 represents a number from 0 to (price_mod_count - 1). Each item
  //                                              holds the minimum capacity for which this price mod applies.
  //   max_capacity_$1 : integer                  $1 represents a number from 0 to (price_mod_count - 1). Each item
  //                                              holds the maximum capacity for which this price mod applies.
  // For special offer price mods only:
  //   duration_$1 : integer                      $1 represents a number from 0 to (price_mod_count - 1). Each item
  //                                              holds the number of months which this price mod will last.
  public static function read_price_mods($type)
  {
    // Read and verify the number of price mods provided.
    if (!Utility::integer_posted('price_mod_count'))
    {
      return false;
    }
    $price_mod_count = Utility::read_posted_integer('price_mod_count');
    if ($price_mod_count <= 0)
    {
      return false;
    }

    // Read price mods.
    $price_mods = array();
    if ($type === self::RULE_TYPE_CAPACITY)
    {
      // Read capacity price mods.
      for ($i = 0; $i < $price_mod_count; $i++)
      {
        if (!Utility::integers_posted(array("price_mod_{$i}", "min_capacity_{$i}", "max_capacity_{$i}")))
        {
          return false;
        }
        $price_mod = array(
          'type' => self::RULE_TYPE_CAPACITY,
          'price_mod' => Utility::read_posted_integer("price_mod_{$i}"),
          'min_capacity' => Utility::read_posted_integer("min_capacity_{$i}"),
          'max_capacity' => Utility::read_posted_integer("max_capacity_{$i}"),
          'duration' => null
        );

        // Validate values.
        if (($price_mod['price_mod'] < -1000) || ($price_mod['price_mod'] > 1000) ||
          ($price_mod['min_capacity'] < 0) || ($price_mod['min_capacity'] > 99) ||
          ($price_mod['max_capacity'] < 1) || ($price_mod['max_capacity'] > 100) ||
          ($price_mod['max_capacity'] <= $price_mod['min_capacity']))
        {
          return false;
        }
        $price_mods[] = $price_mod;
      }

      // Ensure that none of the capacity ranges overlap. For each price modifier, examine the ranges of
      // all subsequent price modifiers, to ensure any overlap is detected.
      $stopAt = count($price_mods) - 2;
      for ($i = 0; $i <= $stopAt; $i++)
      {
        for ($j = $i + 1; $j < count($price_mods); $j++)
        {
          if (self::capacity_ranges_overlap($price_mods[$i], $price_mods[$j]))
          {
            return false;
          }
        }
      }

      // Sort the price mod entries on min capacity. It might make it easier when reading them from the database.
      self::sort_capacity_price_mods($price_mods);
    }
    else
    {
      // Read special offer price mods.
      for ($i = 0; $i < $price_mod_count; $i++)
      {
        if (!Utility::integers_posted(array("price_mod_{$i}", "duration_{$i}")))
        {
          return false;
        }
        $price_mod = array(
          'type' => self::RULE_TYPE_SPECIAL_OFFER,
          'price_mod' => Utility::read_posted_integer("price_mod_{$i}"),
          'min_capacity' => null,
          'max_capacity' => null,
          'duration' => Utility::read_posted_integer("duration_{$i}")
        );

        // Validate values. A duration of 0 is indefinite.
        if (($price_mod['price_mod'] < -1000) || ($price_mod['price_mod'] > 1000) ||
          ($price_mod['duration'] < 0) || ($price_mod['duration'] > 24))
        {
          return false;
        }
        $price_mods[] = $price_mod;
      }

      // Ensure that any price mod with indefinite duration is the last one in the list. It makes no sense to have
      // another price mod after that.
      $stopAt = count($price_mods) - 2;
      for ($i = 0; $i <= $stopAt; $i++)
      {
        if ($price_mods[$i]['duration'] === 0)
        {
          return false;
        }
      }
    }
    return $price_mods;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Update the given offer with a capacity price rule, if one applies. See apply_price_rules_to. The given $offer may
  // be modified, wheras the list of $all_price_rules will not.
  protected function update_offer_based_on_capacity(&$offer, &$all_price_rules)
  {
    // Initialise the capacity rule name to null, to indicate we haven't found anything yet.
    $offer->set_capacity_rule_name(null);

    // Get the relevant price rules. That is, price rules of the correct type that are currently ongoing and apply to
    // the selected location and product type.
    $price_rules = self::get_price_rules_for($offer->get_product_type_id(), $offer->get_location_id(), $all_price_rules,
      self::RULE_TYPE_CAPACITY);
    if (!empty($price_rules))
    {
      // There are price rules that depend on the location's capacity. The capacity is calculated only for the specific
      // product types for which the price rules apply, so we cannot get the capacity once, and that's it. We have to
      // calculate the capacity in each case.
        // *** // Cache the results with the list of product types as the key, so we don't calculate the same capacity several times.
      $capacity_data = new Capacity_Data_Manager($this->access_token);
      foreach ($price_rules as $price_rule)
      {
        // Find the used capacity. If the for_product_types table is null, the method will handle that gracefully.
        $used_capacity = $capacity_data->read_used_capacity($offer->get_location_id(),
          $price_rule['for_product_types']);
        // See if this price rule gives us a better modifier than any we have found so far.
        self::update_best_price_mod($price_rule, $used_capacity, $offer);
      }
    }
  }

  // *******************************************************************************************************************
  // Update the given offer with a special offer price rule, if one applies. See apply_price_rules_to. The given $offer
  // may be modified, wheras the list of $all_price_rules will not.
  protected function update_offer_based_on_special_offers(&$offer, &$all_price_rules)
  {
    // Get the relevant price rules. That is, price rules of the correct type that are currently ongoing and apply to
    // the selected location and product type.
    $price_rules = self::get_price_rules_for($offer->get_product_type_id(), $offer->get_location_id(), $all_price_rules,
      self::RULE_TYPE_SPECIAL_OFFER);

    // We cannot know which offer is best for the customer, so if there is more than one price rule, just use the first
    // one.
    if (empty($price_rules))
    {
      $offer->set_special_offer_rule_name(null);
    }
    else
    {
      $offer->set_special_offer_rule_name($price_rules[0]['name']);
      $offer->set_special_offer_price_mods($price_rules[0]['price_mods']);
    }
  }

  // *******************************************************************************************************************
  // See whether the given capacity $price_rule has a price modifier applicable to the given offer. If it does, and the
  // modifier gives a better price for the customer than any that has been found previously - or if no price mod has
  // been found yet - update the offer. The location to which the rule is being applied is assumed to have the given
  // $used_capacity. $used_capacity should be a number betweeen 0 and 1. If the given $price_rule did not have a price
  // mod for the given $used_capacity, the offer will not be updated.
  protected static function update_best_price_mod($price_rule, $used_capacity, &$offer)
  {
    // The capacity price modifiers will be in the form of an integer percentage that states the price change, for
    // instance -10 for a 10% reduction in price. We want to use the smallest number, as that gives the best price for
    // the customer. However, if the best number is a price increase (due to the location being full), we have to use
    // that. 
    //
    // The $used_capacity is a number between 0 and 1. However, the min and max capacity is in percent. Convert the used
    // capacity to a percentage.
    $used_capacity *= 100;

    // The price rule is assumed to not have overlapping price mod ranges. Loop through the price mods, to see if one
    // applies to the given used capacity. A capacity price rule may not have price modifications for all capacities.
    foreach ($price_rule['price_mods'] as $price_mod)
    {
      // See if the used capacity falls in this price mod's capacity range. Note the use of >= for the minimum capacity,
      // and < for the maximum capacity. This is on purpose. It means that no price mods will ever apply if the used
      // capacity is at 100%. However, this does not matter, since the location is full. Who cares what the price is
      // anyway?
      if (($used_capacity >= $price_mod['min_capacity']) && ($used_capacity < $price_mod['max_capacity']))
      {
        // This price mod applies. If we haven't found any price mods before, or if we have already found a price mod,
        // but this one is better, this is the one to use.
        if (!$offer->has_capacity_price_mod() || ($price_mod['price_mod'] < $offer->get_capacity_price_mod()))
        {
          $offer->set_capacity_rule_name($price_rule['name']);
          $offer->set_capacity_price_mod($price_mod['price_mod']);
        }
        // Since a price rule cannot have overlapping ranges, and we found a range that applies, there is no reason to
        // check the rest.
        break;
      }
    }
  }

  // *******************************************************************************************************************
  // Return a list of all price rules of the given $rule_type (use the RULE_TYPE_ constants) that apply to the product
  // with the given $product_type_id, and the location with the given $location_id.
  protected static function get_price_rules_for($product_type_id, $location_id, &$all_price_rules, $rule_type)
  {
    $result = array();
    // Note that the & in the reference to the price rule is for performance reason. There's no need to copy it, when
    // we're just moving it to a different table.
    foreach ($all_price_rules as &$price_rule)
    {
      if (($price_rule['type'] === $rule_type) && ($price_rule['status'] === self::STATUS_ONGOING) &&
        self::price_rule_applies_to_product_type($price_rule, $product_type_id) &&
        self::price_rule_applies_to_location($price_rule, $location_id))
      {
        $result[] = $price_rule;
      }
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Return true if the given $price_rule applies to the product type with the given $product_type_id.
  protected static function price_rule_applies_to_product_type($price_rule, $product_type_id)
  {
    // If the table is null, the price rule applies to everything.
    if ($price_rule['for_product_types'] === null)
    {
      return true;
    }

    // The price rule applies if the product type ID is found in the table. Note that each product type ID is stored
    // with itself as the key, so we don't have to loop through the table to find it. If the key does not exist, the
    // value will be null - no exception will be thrown.
    return $price_rule['for_product_types'][$product_type_id] === $product_type_id;
  }

  // *******************************************************************************************************************
  // Return true if the given $price_rule applies to the location with the given $location_id.
  protected static function price_rule_applies_to_location($price_rule, $location_id)
  {
    // If the table is null, the price rule applies to everything.
    if ($price_rule['for_locations'] === null)
    {
      return true;
    }

    // The price rule applies if the location ID is found in the table. Note that each location ID is stored with itself
    // as the key, so we don't have to loop through the table to find it.
    return isset($price_rule['for_locations'][$location_id]) &&
      ($price_rule['for_locations'][$location_id] === $location_id);
  }

  // *******************************************************************************************************************
  // Return an array of price rules accessible to the current user. Pass a $rule_type, using the RULE_TYPE_
  // constants, to only return price rules of that type, or pass -1 to return all price rules.
  protected function get_price_rules($rule_type = -1)
  {
    global $wpdb;

    $results = $wpdb->get_results("
      SELECT
        pr.id AS id,
        pr.type AS type,
        pr.name AS name,
        pr.date_start AS start_date,
        pr.date_end AS end_date,
        pm.id AS price_mod_id,
        pm.price_mod AS price_mod,
        pm.min_capacity AS min_capacity,
        pm.max_capacity AS max_capacity,
        pm.duration AS duration,
        prpt.product_type_id AS product_type_id,
        prl.location_id AS location_id
      FROM
        {$this->database_table} pr
      LEFT JOIN
        subscription_price_mods pm ON pr.id = pm.price_rule_id
      LEFT JOIN 
        subscription_price_rule_for_product_types prpt ON pr.id = prpt.price_rule_id
      LEFT JOIN 
        subscription_price_rule_for_locations prl ON pr.id = prl.price_rule_id
      WHERE
        {$this->get_rule_type_filter($rule_type)}
        owner_id = {$this->get_user_group_user_id()}
      ORDER BY
        start_date, name;
    ", ARRAY_A);

    // Organise the results into a PHP array. The SQL query returns one row for each product type associated with the
    // price rule. Each row includes the price rule information, which means that a price rule is duplicated if it is
    // linked to several product types. On the other hand, if the price rule applies to all product types, the price
    // rule information will appear once, with a NULL in the product_type_id field.
    //
    // Similarly, and in addition, the SQL query returns one row for each location linked to the price rule.
    //
    // Finally, the query returns one row for each price mod linked to the price rule.
    //
    // Create a PHP array with one row for each price rule. Each price rule holds all of its linked price mods, product
    // types and locations in three arrays. If there are no price mods (which there should always be), the price mods
    // array will be empty. The other arrays will be null if the price rule has no linked items.
    $price_rules = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $result_row)
      {
        $price_rule_id = $result_row['id'];

        // If the price rule has not already been found, create it.
        if (!isset($price_rules[$price_rule_id]))
        {
          $price_rules[$price_rule_id] = self::get_price_rule($result_row);
        }

        // Add price mod, if present.
        if (isset($result_row['price_mod_id']))
        {
          $price_mod_id = intval($result_row['price_mod_id']);
          if ($price_rules[$price_rule_id]['type'] === self::RULE_TYPE_CAPACITY)
          {
            $price_rules[$price_rule_id]['price_mods'][$price_mod_id] = self::get_capacity_price_mod($result_row);
          }
          else
          {
            $price_rules[$price_rule_id]['price_mods'][$price_mod_id] = self::get_special_offer_price_mod($result_row);
          }
        }

        // Add product type and location, if present.
        Product_Type_Data_Manager::add_product_type_to_item($price_rules[$price_rule_id], $result_row);
        Location_Data_Manager::add_location_to_item($price_rules[$price_rule_id], $result_row);
      }
    }

    // If capacity price mods are included, sort them on min capacity.
    if ($rule_type !== self::RULE_TYPE_SPECIAL_OFFER)
    {
      foreach ($price_rules as &$price_rule)
      {
        if ($price_rule['type'] === self::RULE_TYPE_CAPACITY)
        {
          $price_rule['price_mods'] = array_values($price_rule['price_mods']);
          self::sort_capacity_price_mods($price_rule['price_mods']);
        }
      }
    }

    return $price_rules;
  }

  // *******************************************************************************************************************
  // Return an SQL clause to select only rule types of the given $rule_type. If $rule_type is -1, all types will be
  // included.
  protected static function get_rule_type_filter($rule_type)
  {
    if ($rule_type === self::RULE_TYPE_CAPACITY)
    {
      return 'type = ' . strval(self::RULE_TYPE_CAPACITY) . ' AND';
    }
    if ($rule_type === self::RULE_TYPE_SPECIAL_OFFER)
    {
      return 'type = ' . strval(self::RULE_TYPE_SPECIAL_OFFER) . ' AND';
    }
    return '';
  }

  // *******************************************************************************************************************
  // Delete from the database all price mods for the price rule with the given $id.
  protected static function delete_price_mods($id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("DELETE FROM subscription_price_mods WHERE price_rule_id = %d;", $id);
    $result = $wpdb->query($sql);
    if ($result === false)
    {
      error_log("Error while deleting price mods for price rule {$id}: {$wpdb->last_error}.");
      return false;
    }
    return true;
  }

  // *******************************************************************************************************************
  // Remove all links in the database between the price rule with the given $id and any product types.
  protected static function clear_price_rule_product_type_links($id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("DELETE FROM subscription_price_rule_for_product_types WHERE price_rule_id = %d;", $id);
    $result = $wpdb->query($sql);
    if ($result === false)
    {
      error_log("Error while deleting product type links for price rule {$id}: {$wpdb->last_error}.");
      return false;
    }
    return true;
  }

  // *******************************************************************************************************************
  // Remove all links in the database between the price rule with the given $id and any locations.
  protected static function clear_price_rule_location_links($id)
  {
    global $wpdb;

    $sql = $wpdb->prepare("DELETE FROM subscription_price_rule_for_locations WHERE price_rule_id = %d;", $id);
    $result = $wpdb->query($sql);
    if ($result === false)
    {
      error_log("Error while deleting location links for price_rule {$id}: {$wpdb->last_error}.");
      return false;
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return an array that describes a price rule, using the information posted to the server. If any of the fields was
  // not passed from the client, the method will return null. Note that the price_mods, for_product_types and
  // for_locations tables must be removed from the array before it is used to update the database. These tables must be
  // inserted or updated separately in the database.
  protected function get_data_item()
  {
    // Ensure everything was posted.
    if (!Utility::string_posted('name') || !Utility::integer_posted('type') || !Utility::date_posted('start_date') ||
      !Utility::date_posted('end_date'))
    {
      return null;
    }

    // Read and verify the type of price rule.
    $type = Utility::read_posted_integer('type');
    if (($type !== self::RULE_TYPE_CAPACITY) && ($type !== self::RULE_TYPE_SPECIAL_OFFER))
    {
      return null;
    }

    // Read price mods.
    $price_mods = self::read_price_mods($type);
    if ($price_mods === false)
    {
      return null;
    }

    // Read product types.
    $for_product_types = Product_Type_Data_Manager::read_posted_product_types();
    if ($for_product_types === false)
    {
      return null;
    }

    // Read locations.
    $for_locations = Location_Data_Manager::read_posted_locations();
    if ($for_locations === false)
    {
      return null;
    }

    // Create data item.
    return array(
      // id will be set automatically.
      'owner_id' => $this->get_user_group_user_id(),
      'type' => $type,
      'name' => Utility::read_posted_string('name'),
      'date_start' => Utility::read_posted_string('start_date'),
      'date_end' => Utility::read_posted_string('end_date'),
      'price_mods' => $price_mods,
      'for_product_types' => $for_product_types,
      'for_locations' => $for_locations
    );

    // *** // Validate values. Empty name, negative date range, etc.
  }

  // *******************************************************************************************************************
  // Return true if the two given capacity price mods have overlapping capacity ranges. If one range starts where the
  // other ends, that is not considered an overlap.
  protected static function capacity_ranges_overlap($mod_i, $mod_j)
  {
    return ($mod_i['max_capacity'] > $mod_j['min_capacity']) && ($mod_j['max_capacity'] > $mod_i['min_capacity']);
  }

  // *******************************************************************************************************************
  // Sort the given array of capacity price mods in ascending order, on the minimum capacity.
  protected static function sort_capacity_price_mods(&$price_mods)
  {
    usort($price_mods, array('Price_Rule_Data_Manager', 'compare_capacity_price_mods'));
  }

  // *******************************************************************************************************************

  public static function compare_capacity_price_mods($a, $b)
  {
    return $a['min_capacity'] - $b['min_capacity'];
  }

  // *******************************************************************************************************************
  // Return an array of price rules that only contains price rules of the given $type.
  protected static function filter_price_rules(&$price_rules, $type)
  {
    $table = array();
    foreach ($price_rules as &$price_rule)
    {
      if ($price_rule['type'] === $type)
      {
        $table[] = $price_rule;
      }
    }
    return $table;
  }

  // *******************************************************************************************************************
  // Return a string that contains a Javascript array declaration that holds information about the price rules from the
  // given $price_rules table. The Javascript array will only contain price rules of the given $type. Use the RULE_TYPE_
  // constants. The Javascript array will have the following fields:
  //   id : integer
  //   name : string
  //   status : integer
  //   start_date : string
  //   end_date : string
  //   price_mods : array
  //   for_locations : array or null
  //   for_product_types : array or null
  //   open : boolean
  protected static function get_price_rule_js_table($price_rules, $type)
  {
    $eligible_rules = self::filter_price_rules($price_rules, $type);

    $table = "[";
    if (!empty($eligible_rules))
    {
      foreach ($eligible_rules as $price_rule)
      {
        $table .= "[";
        $table .= strval($price_rule['id']);
        $table .= ", '";
        $table .= $price_rule['name'];
        $table .= "', ";
        $table .= $price_rule['status'];
        $table .= ", '";
        $table .= $price_rule['start_date'];
        $table .= "', '";
        $table .= $price_rule['end_date'];
        $table .= "', ";
        if ($type === self::RULE_TYPE_CAPACITY)
        {
          $table .= self::get_capacity_price_mod_js_table($price_rule['price_mods']);
        }
        else
        {
          $table .= self::get_special_offer_price_mod_js_table($price_rule['price_mods']);
        }
        $table .= ", ";
        $table .= Utility::get_js_array_of_values($price_rule['for_product_types']);
        $table .= ", ";
        $table .= Utility::get_js_array_of_values($price_rule['for_locations']);
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Return a string that contains a Javascript array declaration that holds information about the price mods from the
  // given $price_mods table. They are assumed to be capacity price mods. The Javascript array will have the following
  // fields:
  //   price_mod : integer
  //   min_capacity : integer
  //   max_capacity : integer
  protected static function get_capacity_price_mod_js_table($price_mods)
  {
    $table = "[";
    if (!empty($price_mods))
    {
      foreach ($price_mods as $price_mod)
      {
        $table .= "[";
        $table .= strval($price_mod['price_mod']);
        $table .= ", ";
        $table .= strval($price_mod['min_capacity']);
        $table .= ", ";
        $table .= strval($price_mod['max_capacity']);
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // Return a string that contains a Javascript array declaration that holds information about the price mods from the
  // given $price_mods table. They are assumed to be special offer price mods. The Javascript array will have the
  // following fields:
  //   price_mod : integer
  //   duration : integer
  protected static function get_special_offer_price_mod_js_table($price_mods)
  {
    $table = "[";
    if (!empty($price_mods))
    {
      foreach ($price_mods as $price_mod)
      {
        $table .= "[";
        $table .= strval($price_mod['price_mod']);
        $table .= ", ";
        $table .= strval($price_mod['duration']);
        $table .= "],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }
  
  // *******************************************************************************************************************
  // Return an array that holds information about a price rule, based on the given $result_row.
  protected static function get_price_rule($result_row)
  {
    $today = Utility::get_today();
    if ($today > $result_row['end_date'])
    {
      $status = self::STATUS_EXPIRED;
    }
    elseif ($today < $result_row['start_date'])
    {
      $status = self::STATUS_NOT_STARTED;
    }
    else
    {
      $status = self::STATUS_ONGOING;
    }

    return array(
      'id' => intval($result_row['id']),
      'type' => intval($result_row['type']),
      'name' => $result_row['name'],
      'start_date' => $result_row['start_date'],
      'end_date' => $result_row['end_date'],
      'status' => $status,
      'price_mods' => array(),
      'for_product_types' => null,
      'for_locations' => null
    );
  }

  // *******************************************************************************************************************
  // Return an array that holds information about a capacity price modifier, based on the given $result_row.
  protected static function get_capacity_price_mod($result_row)
  {
    return array(
      'price_mod' => intval($result_row['price_mod']),
      'min_capacity' => intval($result_row['min_capacity']),
      'max_capacity' => intval($result_row['max_capacity'])
    );
  }

  // *******************************************************************************************************************
  // Return an array that holds information about a special offer price modifier, based on the given $result_row.
  protected static function get_special_offer_price_mod($result_row)
  {
    return array(
      'price_mod' => intval($result_row['price_mod']),
      'duration' => intval($result_row['duration'])
    );
  }

  // *******************************************************************************************************************
  // Create price mods for the price rule with the given $id. For each price mod in the $price_mods array, insert a row
  // in the "price_mods" table. The given $price_mods should contain all the fields in the database table; therefore,
  // this method does not need to distinguish between capacity and special offer price mods.
  protected static function create_price_mods($id, $price_mods)
  {
    global $wpdb;

    if (!is_numeric($id))
    {
      return false;
    }
    $id = intval($id);

    if (Utility::non_empty_array($price_mods))
    {
      $values = self::get_price_mod_values($id, $price_mods);
      $result = $wpdb->query("
        INSERT INTO
          subscription_price_mods (price_rule_id, type, price_mod, min_capacity, max_capacity, duration)
        VALUES
          {$values};
      ");
      if ($result === false)
      {
        error_log("Error while creating price mods for price rule {$id}: {$wpdb->last_error}. Tried to insert price mod values: {$values}.");
        return false;
      }
      if ($result !== count($price_mods))
      {
        error_log("Failed to insert the correct number of price mods for price rule {$id}. Expected: {count($price_mods)}. Actual: {$result}. Tried to insert price mod values: {$values}.");
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return a comma separated string with price mod values that can be inserted into the database. Each price mod is
  // enclosed in parentheses, and contains values for price rule ID, type, price modifier, minimum capacity, maximum
  // capacity and duration. Not all values are used for all types of price mods. Unused values will be rendered as
  // "null".
  protected static function get_price_mod_values($id, $price_mods)
  {
    $result = array();
    foreach ($price_mods as $price_mod)
    {
      // NULL values are rendered as empty strings. We want the result to say "null".
      $string = '(';
      $string .= self::get_value_or_null($id);
      $string .= ', ';
      $string .= self::get_value_or_null($price_mod['type']);
      $string .= ', ';
      $string .= self::get_value_or_null($price_mod['price_mod']);
      $string .= ', ';
      $string .= self::get_value_or_null($price_mod['min_capacity']);
      $string .= ', ';
      $string .= self::get_value_or_null($price_mod['max_capacity']);
      $string .= ', ';
      $string .= self::get_value_or_null($price_mod['duration']);
      $string .= ')';
      $result[] = $string;
    }
    return implode(',', $result);
  }

  // *******************************************************************************************************************
  // Return the given $value as a string, or a string "null" if the given $value is null.
  protected static function get_value_or_null($value)
  {
    if (!isset($value))
    {
      return 'null';
    }
    return strval($value);
  }

  // *******************************************************************************************************************
  // Create a price rule's links to product types, if required. If the given $for_product_types table is null, nothing
  // needs to be done. If not, insert links between the price rule with the ID given in $id and the product types to
  // which it will apply. For each product type ID in $for_product_types, insert a row in the
  // "price_rule_for_product_types" table with that product type ID, and the ID of the price rule. Return true if
  // everything that needed to be done was done successfully. Return false if something went wrong.
  protected static function link_price_rule_to_product_types($id, $for_product_types)
  {
    global $wpdb;

    if (!is_numeric($id))
    {
      return false;
    }
    $id = intval($id);

    if (Utility::non_empty_array($for_product_types))
    {
      $values = Utility::get_value_data_string($id, $for_product_types);
      $result = $wpdb->query("
        INSERT INTO
          subscription_price_rule_for_product_types (price_rule_id, product_type_id)
        VALUES
          {$values};
      ");
      if ($result === false)
      {
        error_log("Error while creating product type links for price rule {$id}: {$wpdb->last_error}. Tried to insert product type IDs: {$values}.");
        return false;
      }
      if ($result !== count($for_product_types))
      {
        error_log("Failed to insert the correct number of product type links for price rule {$id}. Expected: {count($for_product_types)}. Actual: {$result}. Tried to insert product type IDs: {$values}.");
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Create a price rule's links to locations, if required. If the given $for_locations table is null, nothing needs to
  // be done. If not, insert links between the price rule with the ID given in $id and the locations at which it will
  // apply. For each location ID in $for_locations, insert a row in the "price_rule_for_locations" table with that
  // location ID, and the ID of the price rule. Return true if everything that needed to be done was done successfully.
  // Return false if something went wrong.
  protected static function link_price_rule_to_locations($id, $for_locations)
  {
    global $wpdb;

    if (!is_numeric($id))
    {
      return false;
    }
    $id = intval($id);

    if (Utility::non_empty_array($for_locations))
    {
      $values = Utility::get_value_data_string($id, $for_locations);
      $result = $wpdb->query("
        INSERT INTO
          subscription_price_rule_for_locations (price_rule_id, location_id)
        VALUES
          {$values};
      ");
      if ($result === false)
      {
        error_log("Error while creating location links for price rule {$id}: {$wpdb->last_error}. Tried to insert location IDs: {$values}");
        return false;
      }
      if ($result !== count($for_locations))
      {
        error_log("Failed to insert the correct number of location links for price rule {$id}. Expected: {count($for_locations)}. Actual: {$result}. Tried to insert location IDs: {$values}");
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
}
?>