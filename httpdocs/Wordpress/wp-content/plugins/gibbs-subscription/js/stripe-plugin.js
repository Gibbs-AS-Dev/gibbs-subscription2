document.addEventListener('DOMContentLoaded', function () {
    const stripe = Stripe(stripePlugin.publishableKey);

    document.querySelectorAll('.checkout-button').forEach(button => {
        button.addEventListener('click', function () {
            const priceId = this.dataset.priceId;
            const packageId = this.dataset.packageId;
            jQuery(this).parent().parent().find(".load-div").find(".loading").show();


            fetch(stripePlugin.ajaxUrl + '?action=create_checkout_session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ price_id: priceId, package_id: packageId }),
            })
            .then(response => response.json())
            .then(data => {
                jQuery(this).parent().parent().find(".load-div").find(".loading").hide();
                if (data.error) {
                    alert(data.error);
                } else {
                    if(data.subscription_id){
                       window.location.reload();
                    }else{
                        return stripe.redirectToCheckout({ sessionId: data.id });
                    }
                    
                }
            }).catch(error => {
                console.error('Error:', error);
                jQuery(this).parent().parent().find(".load-div").find(".loading").hide(); // Use .loading class selector
            });
        });
    });
    // Update Subscription
    document.addEventListener('click', function (event) {

        if(jQuery(event.target).closest(".gibbs_modal")){
            jQuery(".gibbs_modal").find("iframe").each(function(){
                let src = this.src;

                // Add parameters if they are not already present
                const params = new URLSearchParams(src.split('?')[1]);
                params.set('autoplay', '0');
                
                // Update the iframe src
                this.src = `${src.split('?')[0]}?${params.toString()}`;
            })
        }

        
        // Cancel Subscription
        if (event.target.classList.contains('cancel-subscription')) {
            if(confirm("Are you sure!")){
                const subscriptionId = event.target.dataset.subscriptionId;
                const packageId = event.target.dataset.packageId;


                jQuery(event.target).parent().parent().find(".load-div").find(".loading").show();

                fetch(stripePlugin.ajaxUrl + '?action=cancel_subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ subscription_id: subscriptionId, package_id: packageId }),
                })
                .then(response => response.json())
                .then(data => {
                    jQuery(event.target).parent().parent().find(".load-div").find(".loading").show();
                    if (data.error) {
                        alert(data.error);
                    } else {
                       // alert('Subscription canceled successfully!');
                        location.reload(); // Optionally reload to reflect changes
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    jQuery(event.target).parent().parent().find(".load-div").find(".loading").hide(); // Use .loading class selector
                });
            }
        }
    });
});
