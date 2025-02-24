// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Initialise the page by caching pointers and embedding the payment iFrame.
function initialise()
{
  var parameters, paymentId, paymentOptions, payment, paymentBox;

  if (settings.useTestData)
  {
    // We are using dummy data, and the payment provider was not contacted. Display fake payment
    // image in the payment box.
    paymentBox = document.getElementById('paymentBox');
    paymentBox.innerHTML = '<a href="/subscription/html/paid.php"><img src="/subscription/resources/payment.png" /></a>';
  }
  else
  {
    // We are using real data, although the payment may still be a test payment. Display the payment
    // provider's content in the payment box.
    parameters = new URLSearchParams(window.location.search);
    paymentId = parameters.get('paymentId');
    if (paymentId)
    {
      paymentOptions =
        {
          checkoutKey: settings.netsCheckoutKey,
          paymentId: paymentId,
          containerId: 'paymentBox'
        };
      payment = new Dibs.Checkout(paymentOptions);
      payment.on('payment-completed', paymentComplete);
    }
    else
    {
      console.error('Payment ID not found in request to pay.php.');
        // *** // Should we display this, or does the customer already know?
      alert(getText(0,
        'Det oppstod en feil ved betaling. Vennligst prøv igjen, eller kontakt kundeservice.'));
      window.location.href = '/subscription/html/user_dashboard.php';
    }
  }
}

// *************************************************************************************************

function paymentComplete(response)
{
  // We'd like to do error handling somewhere, but the response here contains only the paymentId.
  window.location.href = '/subscription/html/paid.php';
}

// *************************************************************************************************
