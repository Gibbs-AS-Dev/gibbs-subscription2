// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** class PricePlan
// *************************************************************************************************

class PricePlan
{

// *************************************************************************************************
// *** Static methods.
// *************************************************************************************************
// Return the price for the price plan with the given pricePlanIndex, for the subscription with the
// given index, at the moment given in referenceDate. referenceDate should be a string in the format
// "yyyy-mm-dd". referenceDate is optional. If not present, the current date is used. Return the
// price as an integer, or -1 if the price plan has not come into effect yet - that is, the
// referenceDate is before the first item in the price plan.
static getPriceFromPricePlan(subscriptions, index, pricePlanIndex, referenceDate)
{
  var i, price, planLines;

  // If the price plan lines could not be found, return -1.
  planLines = PricePlan.getPricePlanLines(subscriptions, index, pricePlanIndex);
  if (planLines === null)
    return -1;

  // If the date is not provided, use today's date.
  if (!referenceDate)
    referenceDate = Utility.getCurrentIsoDate();
  // Price plans are sorted by date. Examine each line in the price plan, and find the last line
  // which applies to the reference date.
  price = -1;
  for (i = 0; i < planLines.length; i++)
  {
    // The dates are stored as strings in ISO format, and can be compared alphabetically.
    if (referenceDate < planLines[i][c.sub.LINE_START_DATE])
    {
      // The reference date is before this line in the price plan comes into effect. Return the last
      // price we had.
      return price;
    }
    // The reference date is equal to or after this line in the price plan, so this price applies.
    // Store the price. It might be superseded by later lines, but in that case the price will be
    // updated in the next iterations.
    price = planLines[i][c.sub.LINE_PRICE];
  }
  // There were no more lines in the price plan, so the last price we found applies indefinitely.
  // Return that.
  return price;
}

// *************************************************************************************************
// Return the array of price plan lines for the price plan with the given pricePlanIndex, for the
// subscription with the given index - or null if the lines were not found.
static getPricePlanLines(subscriptions, index, pricePlanIndex)
{
  index = parseInt(index, 10);
  pricePlanIndex = parseInt(pricePlanIndex, 10);
  if (!Utility.isValidIndex(index, subscriptions) ||
    !Utility.isValidIndex(pricePlanIndex, subscriptions[index][c.sub.PRICE_PLANS]))
    return null;

  return subscriptions[index][c.sub.PRICE_PLANS][pricePlanIndex][c.sub.PLAN_LINES];
}

// *************************************************************************************************
// Return the index of the product price plan - that is, the price plan for renting the storage
// room - for the subscription with the given index, or -1 if it could not be found.
static getProductPricePlan(subscriptions, index)
{
  return PricePlan._getPricePlanForType(subscriptions, index, -1);
}

// *************************************************************************************************
// Return the index of the insurance price plan for the subscription with the given index, or -1 if
// it could not be found.
static getInsurancePricePlan(subscriptions, index)
{
  return PricePlan._getPricePlanForType(subscriptions, index, ADDITIONAL_PRODUCT_INSURANCE);
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Return the index of the price plan with the given planType, for the subscription with the given
// index - or -1 if it could not be found.
static _getPricePlanForType(subscriptions, index, planType)
{
  var i;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, subscriptions))
  {
    for (i = 0; i < subscriptions[index][c.sub.PRICE_PLANS].length; i++)
    {
      if (subscriptions[index][c.sub.PRICE_PLANS][i][c.sub.PLAN_TYPE] === planType)
        return i;
    }
  }
  return -1;
}

// *************************************************************************************************

}
