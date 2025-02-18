<?php
$user_id = $this->get_super_admin();

$stripe_customer_id = get_user_meta($user_id, 'stripe_customer_id', true);

// Get user's subscriptions
if ($stripe_customer_id) {
    // Fetch subscriptions from Stripe
    try {
        $subscriptions = $this->stripe->subscriptions->all(['customer' => $stripe_customer_id]);
        // Render subscription details and management options
        if (count($subscriptions->data) > 0) {
            echo '<div class="subscription-management">';
            foreach ($subscriptions->data as $subscription) {
                // Retrieve customer details if needed
                $customer = $this->stripe->customers->retrieve($stripe_customer_id);
                $customer_name = $customer->name ?? 'N/A'; // Default if not set

                // Fetch the price details
                $price_id = $subscription->items->data[0]->price->id; // Assuming single price
                $price = $this->stripe->prices->retrieve($price_id);
                
                // Subscription details
                echo '<div class="subscription-card">';
                echo '<h3>Customer: ' . esc_html($customer_name) . '</h3>';
                echo '<p>Subscription ID: ' . esc_html($subscription->id) . '</p>';
                echo '<p>Status: <span class="status ' . esc_attr($subscription->status) . '">' . esc_html(ucfirst($subscription->status)) . '</span></p>';
                echo '<p>Valid Until: ' . esc_html(date('Y-m-d H:i:s', $subscription->current_period_end)) . '</p>';
                echo '<p>Next Payment: ' . esc_html($price->currency) . ' ' . esc_html(number_format($price->unit_amount / 100, 2)) . '</p>';
                
                // Convert the timestamp to a readable date format for next payment
                $next_payment_time = date('Y-m-d H:i:s', $subscription->current_period_end);
                echo '<p>Next Payment Date: ' . esc_html($next_payment_time) . '</p>';
                
                echo '<p>Paid: ' . esc_html($subscription->status === 'active' ? 'Yes' : 'No') . '</p>';
                echo '<button class="cancel-subscription" data-subscription-id="' . esc_attr($subscription->id) . '">Cancel Subscription</button>';
                echo '</div>'; // End subscription-card
            }
            echo '</div>'; // End subscription-management
        } else {
            echo '<p>No active subscriptions found.</p>';
        }
    } catch (Exception $e) {
        echo '<p>Error fetching subscriptions: ' . esc_html($e->getMessage()) . '</p>';
    }
} else {
    echo '<p>No Stripe customer found.</p>';
}