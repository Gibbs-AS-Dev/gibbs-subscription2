<?php
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/offer/offer.php';

// Class which deals with offers. Does not need to be instantiated.
class Offer_Data_Manager
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
  // Store the given array of Offer objects on the session.
  public static function store_offers_on_session($offers)
  {
    $_SESSION['offers'] = serialize($offers);
  }

  // *******************************************************************************************************************
  // Read the list of offers from the session, and delete the session variable. If the list was not found on the
  // session, return null.
  public static function read_offers_from_session()
  {
    if (!isset($_SESSION['offers']))
    {
      return null;
    }

    $offers = unserialize($_SESSION['offers']);
    unset($_SESSION['offers']);
    return $offers;
  }

  // *******************************************************************************************************************
}
?>