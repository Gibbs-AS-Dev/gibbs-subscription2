<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

// Class which holds an offer to a customer for a product of a particular product type, at a particular location. The
// offer includes a price, and records the price rules that went into its making. The offer might be for a particular
// product, or a list of products that can be rented at the specified prices. The class can be used to iterate over the
// product IDs.
//
// Note that the offer does not currently hold information about the selected insurance, although it can hold a custom
// price for whatever insurance is selected.
class Offer
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The ID of the location at which the offered storage units reside.
  protected $location_id = -1;

  // The ID of the storage unit type for which the offer applies.
  protected $product_type_id = -1;

  // The list of storage units included in the offer. Either of these can be rented at the conditions given in thie
  // offer, provided they are still available when the subscription is created. This is not guaranteed.
  protected $product_ids = array();

  // The index in the $product_ids table of the next product ID to be considered when iterating. If this value is
  // greater than or equal to the number of elements in the $product_ids array, the iteration is over.
  protected $next_product_index = 0;

  // The base price of the unit type for which the offer applies.
  protected $base_price;

  // The name of the capacity price rule that was applied, or null if no capacity price rule applies.
  protected $capacity_rule_name = null;

  // The capacity price modifier that was applied. If the $capacity_rule_name is null, this value should not be used.
  protected $capacity_price_mod = 0;

  // The name of the special offer price rule that was applied, or null if no special offer applies.
  protected $special_offer_rule_name = null;

  // The array of price modifiers for the special offer price rule. If the $special_offer_rule_name is null, this value
  // should not be used. Each entry is an array with the following fields:
  //   price_mod : integer
  //   month_count : integer
  protected $special_offer_price_mods = null;

  // The offer does not include an insurance selection. However, in some cases the insurance price may be modified by an
  // administrator. The custom price may be stored here, for later use. If there is no custom price, the value will be
  // -1.
  protected $custom_insurance_price = -1;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($location_id, $product_type)
  {
    $this->location_id = $location_id;
    $this->product_type_id = $product_type->id;
    $this->base_price = $product_type->price;
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return true if this offer has a reference to a capacity price rule that affects the price.
  public function has_capacity_price_mod()
  {
    return $this->capacity_rule_name !== null;
  }

  // *******************************************************************************************************************
  // Return true if this offer has a reference to a special offer price rule that affects the price.
  public function has_special_offer_price_mod()
  {
    return $this->special_offer_rule_name !== null;
  }

  // *******************************************************************************************************************
  // Return the capacity price per month - that is, the base price modified by the best capacity price rule that we have
  // found.
  public function get_capacity_price()
  {
    // If we didn't find any relevant modifiers (which is quite possible, as the price rule often modifies the price
    // only if the used capacity is very high or very low), return the base price.
    if (!$this->has_capacity_price_mod())
    {
      return $this->base_price;
    }

    // Return the base price, modified according to the best price modifier we were able to locate. If the price mod is
    // -10%, we will multiply the base price with 0.9. If it is +20%, we will multiply the base price by 1.2. Finally,
    // we will round to the nearest whole number.
    return Utility::get_modified_price($this->base_price, $this->capacity_price_mod);
  }

  // *******************************************************************************************************************
  // Override the base price in this offer with the given $custom_price. This will remove any capacity price modifier
  // that was applied. This action is not reversible.
  public function override_base_price($custom_price)
  {
    if (is_numeric($custom_price))
    {
      $this->base_price = intval($custom_price);
      $this->capacity_rule_name = null;
      $this->capacity_price_mod = 0;
    }
  }

  // *******************************************************************************************************************
  // Override the price mods in this offer with the given $custom_price_mods array. The array may be empty, but may not
  // be null. Also, a string with a rule name must be supplied. This action will override any existing price mods, and
  // is not reversible.
  public function override_price_mods($custom_price_mods, $custom_rule_name)
  {
    if (is_array($custom_price_mods) && ($custom_rule_name !== null))
    {
      $this->special_offer_rule_name = $custom_rule_name;
      $this->special_offer_price_mods = $custom_price_mods;
    }
  }

  // *******************************************************************************************************************
  // Start iterating over product IDs from the list, by resetting the pointer to the first element.
  public function reset_product_counter()
  {
    $this->next_product_index = 0;
  }

  // *******************************************************************************************************************
  // Return the next product ID from the list. This will advance the pointer to the next element. Note that this method
  // returns the actual ID, not the index in the list. Calling this function when has_more_products() would return false
  // will yield an undefined result (so don't).
  public function get_next_product_id()
  {
    $id = $this->product_ids[$this->next_product_index];
    $this->next_product_index++;
    return $id;
  }

  // *******************************************************************************************************************
  // Return true if, during an iteration, there are more product IDs that remain as part of the iteration. That is, if
  // there are product IDs that have not yet been considered.
  public function has_more_products()
  {
    return $this->next_product_index < count($this->product_ids);
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************

  public function get_location_id()
  {
    return $this->location_id;
  }

  // *******************************************************************************************************************

  public function get_product_type_id()
  {
    return $this->product_type_id;
  }

  // *******************************************************************************************************************

  public function get_product_ids()
  {
    return $this->product_ids;
  }

  // *******************************************************************************************************************

  public function set_product_ids($new_value)
  {
    if (is_array($new_value))
    {
      $this->product_ids = $new_value;
    }
  }

  // *******************************************************************************************************************

  public function get_base_price()
  {
    return $this->base_price;
  }

  // *******************************************************************************************************************

  public function get_capacity_rule_name()
  {
    return $this->capacity_rule_name;
  }

  // *******************************************************************************************************************

  public function set_capacity_rule_name($new_value)
  {
    if ($new_value === null)
    {
      $this->capacity_rule_name = null;
      $this->capacity_price_mod = 0;
    }
    else
    {
      $this->capacity_rule_name = strval($new_value);
    }
  }

  // *******************************************************************************************************************

  public function get_capacity_price_mod()
  {
    return $this->capacity_price_mod;
  }

  // *******************************************************************************************************************

  public function set_capacity_price_mod($new_value)
  {
    if (is_numeric($new_value))
    {
      $this->capacity_price_mod = intval($new_value);
    }
  }
    
  // *******************************************************************************************************************

  public function get_special_offer_rule_name()
  {
    return $this->special_offer_rule_name;
  }

  // *******************************************************************************************************************

  public function set_special_offer_rule_name($new_value)
  {
    if ($new_value === null)
    {
      $this->special_offer_rule_name = null;
      $this->special_offer_price_mods = null;
    }
    else
    {
      $this->special_offer_rule_name = strval($new_value);
    }
  }

  // *******************************************************************************************************************

  public function get_special_offer_price_mods()
  {
    return $this->special_offer_price_mods;
  }

  // *******************************************************************************************************************

  public function set_special_offer_price_mods($new_value)
  {
    if (is_array($new_value))
    {
      $this->special_offer_price_mods = $new_value;
    }
  }
    
  // *******************************************************************************************************************

  public function get_custom_insurance_price()
  {
    return $this->custom_insurance_price;
  }

  // *******************************************************************************************************************

  public function set_custom_insurance_price($new_value)
  {
    if (is_numeric($new_value))
    {
      $this->custom_insurance_price = intval($new_value);
    }
  }

  // *******************************************************************************************************************
}
?>