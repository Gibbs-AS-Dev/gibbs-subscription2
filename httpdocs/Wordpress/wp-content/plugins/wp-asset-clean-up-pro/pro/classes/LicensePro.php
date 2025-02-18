<?php
namespace WpAssetCleanUpPro;

use WpAssetCleanUp\Main;
use WpAssetCleanUp\Misc;

/**
 * Class License
 * @package WpAssetCleanUpPro
 */
class LicensePro
{
	/**
	 *
	 */
	public function init()
	{
		add_action('admin_init',          array($this, 'activateLicense'));
		add_action('admin_init',          array($this, 'markLicenseAsActive'));
		add_action('admin_init',          array($this, 'deactivateLicense'));
		add_action('wpacu_admin_notices', array($this, 'adminNotices'));

		// In the plugins page, make sure to explain that the license has to be added and activated to qualify for Dashboard updates
		add_action('in_plugin_update_message-'.plugin_basename(WPACU_PLUGIN_FILE), array($this, 'licenseNotActivated'), 10, 2);

		add_action('admin_footer', array($this, 'getLicenseInfoScripts'));
	    add_action('wp_ajax_'.WPACU_PLUGIN_ID.'_get_license_info', array($this, 'ajaxGetLicenseInfo'));
	}

	/**
	 *
	 */
	public function licensePage()
	{
		$license = get_option(WPACU_PLUGIN_ID . '_pro_license_key');
		$status  = get_option(WPACU_PLUGIN_ID . '_pro_license_status');

        $data = array(
            'license'      => $license,
            'status'       => $status,
            'pro_template' => true // it will load from "/pro/templates/"
        );

		Main::instance()->parseTemplate('admin-page-license', $data, true);
	}

	/**
     *
    */
	public function activateLicense()
	{
		// listen for our activate button to be clicked
		if ( ! isset( $_POST['wpacu_license_activate'] ) ) {
			return;
		}

		// run a quick security check
		$nonceValue = Misc::getVar('post',WPACU_PLUGIN_ID . '_pro_nonce');
		if ( ! wp_verify_nonce($nonceValue, WPACU_PLUGIN_ID . '_pro_nonce') ) {
			$message = esc_html__('The security nonce is not valid. Please retry!', 'wp-asset-clean-up');
			$this->activationErrorRedirect($message); // stop here and redirect
		}

		// retrieve the license from the input
        $licenseKeyInputName = WPACU_PLUGIN_ID . '_pro_license_key';
		$licenseKeyValue = (isset($_POST[$licenseKeyInputName]) && trim($_POST[$licenseKeyInputName]) !== '') ? trim(sanitize_text_field($_POST[$licenseKeyInputName])) : '';

		// data to send in our API request
		$apiParams = array(
			'edd_action'      => 'activate_license',
			'activation_type' => 'manual',
			'license'         => $licenseKeyValue,
			'item_id'         => WPACU_PRO_PLUGIN_STORE_ITEM_ID, // The ID of the item in EDD Store
			'url'             => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post(
			WPACU_PRO_PLUGIN_STORE_LICENSE_ACTION_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $apiParams
			)
		);

		// make sure the response came back okay
		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
		    $errorResponseMessage = $response->get_error_message();
			$message = (is_wp_error($response) && ! empty($errorResponseMessage))
                ? $errorResponseMessage
                : __( 'An error occurred, please try again.');
		} else {
			$licenseData = json_decode(wp_remote_retrieve_body($response));

			if (isset($licenseData->error, $licenseData->upgrades_output) && $licenseData->error === 'no_activations_left') {
                set_transient('wpacu_no_activations_left_upgrades_output', $licenseData->upgrades_output, 30);
            }

			if (false === $licenseData->success) {
				switch ($licenseData->error) {
					case 'expired':
					    $dateFormatted = date_i18n( get_option( 'date_format' ), strtotime( $licenseData->expires, current_time( 'timestamp' ) ) );
					    $licenseRenewalUrl = self::generateRenewalLink($licenseKeyValue, $licenseData);

						$message = 'expired';
					    $messageToPrint = sprintf(
							__('The license key you submitted expired on %s. %s %sRenew it now (%s off)%s', 'wp-asset-clean-up'),
							$dateFormatted,
                            '&nbsp;<span style="color: green;" class="dashicons dashicons-update"></span>',
                            '<a target="_blank" style="font-weight: bold; color: green;" href="'.$licenseRenewalUrl.'">',
                            '15%',
                            '</a>'
						);

					    set_transient('wpacu_license_activation_failed_msg', $messageToPrint);

						Misc::addUpdateOption( WPACU_PLUGIN_ID . '_pro_license_key', $licenseKeyValue);
						Misc::addUpdateOption( WPACU_PLUGIN_ID . '_pro_license_status', 'expired');
						break;

					case 'revoked':
						$message = esc_html__('Your license key has been disabled.', 'wp-asset-clean-up');
						break;

					case 'missing':
						$message = esc_html__('The license you submitted is invalid. Please update your license key with the one you received in your purchase email receipt and then activate it.', 'wp-asset-clean-up');
						break;

					case 'invalid':
					case 'site_inactive' :
						$message = esc_html__('Your license is not active for this URL.', 'wp-asset-clean-up');
						break;

					case 'item_name_mismatch':
						$message = sprintf(esc_html__('This appears to be an invalid license key for %s.', 'wp-asset-clean-up'), WPACU_PRO_PLUGIN_STORE_ITEM_NAME);
						break;

					case 'no_activations_left':
						$message = esc_html__('Your license key has reached its activation limit.', 'wp-asset-clean-up') . ' '.
						           esc_html__('You can increase the limit by upgrading the license type.', 'wp-asset-clean-up');
						break;

					default:
						$message = esc_html__('An error occurred, please try again.', 'wp-asset-clean-up');
						break;
				}
			}
		}

		// Check if anything passed on a message constituting a failure
		if ( ! empty($message) ) {
			wp_redirect(add_query_arg(
				array( 'wpacu_pro_activation' => 'false', 'message' => urlencode( $message ) ),
				esc_url( admin_url( 'admin.php?page=' . WPACU_PLUGIN_ID . '_license' ) )
			));
			exit();
		}

		// $licenseData->license will be either "valid" or "invalid"
		Misc::addUpdateOption( WPACU_PLUGIN_ID . '_pro_license_status', $licenseData->license);

		$baseUrl = esc_url(admin_url( 'admin.php?page=' . WPACU_PLUGIN_ID . '_license'));

		if ($licenseData->license === 'valid') {
			Misc::addUpdateOption( WPACU_PLUGIN_ID . '_pro_license_key', $licenseKeyValue);
			Misc::addUpdateOption( WPACU_PLUGIN_ID . '_pro_license_status', $licenseData->license);
		    $redirect = add_query_arg(array('wpacu_pro_activation' => 'true'), $baseUrl);
		    set_transient(WPACU_PLUGIN_ID . '_license_just_activated', true, 30);
		} else {
		    $redirect = $baseUrl;
		}

		wp_redirect($redirect);
		exit();
	}

	/**
	 *
	 */
	public function markLicenseAsActive()
    {
        if (! empty($_REQUEST) && Misc::getVar('request', 'wpacu_mark_license_valid_button') !== '') {
	        // retrieve the license from the input
	        $licenseKeyInputName = WPACU_PLUGIN_ID . '_pro_license_key';
	        $licenseKeyValue = (isset($_POST[$licenseKeyInputName]) && trim($_POST[$licenseKeyInputName]) !== '') ? trim(sanitize_text_field($_POST[$licenseKeyInputName])) : '';

	        Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_key', $licenseKeyValue);
	        Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_status', 'valid');

	        wp_redirect(add_query_arg(
		        array('wpacu_pro_activation' => 'true'),
		        esc_url(admin_url('admin.php?page=' . WPACU_PLUGIN_ID . '_license'))
	        ));
	        exit();
        }
    }

    /*
      * Illustrates how to deactivate a license key.
      * This will decrease the site count
    */
	/**
	 *
	 */
	public function deactivateLicense()
	{
        // listen for our activate button to be clicked
        if ( ! isset( $_POST['wpacu_license_deactivate']) ) {
            return;
        }

		// run a quick security check
        $nonceValue = Misc::getVar('post',WPACU_PLUGIN_ID . '_pro_nonce');

	 	if ( ! wp_verify_nonce($nonceValue, WPACU_PLUGIN_ID . '_pro_nonce') ) {
	 	    $message = __('The security nonce is not valid. Please retry!', 'wp-asset-clean-up');
		    $this->activationErrorRedirect($message); // stop here and redirect
	 	}

		// retrieve the license from the database
		$license = trim(get_option( WPACU_PLUGIN_ID . '_pro_license_key'));

		// data to send in our API request
		$apiParams = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => urlencode(WPACU_PRO_PLUGIN_STORE_ITEM_NAME), // the exact name of the product in EDD
			'item_id'    => WPACU_PRO_PLUGIN_STORE_ITEM_ID,
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post(WPACU_PRO_PLUGIN_STORE_LICENSE_ACTION_URL, array('timeout' => 15, 'sslverify' => false, 'body' => $apiParams));

		// make sure the response came back okay
		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			if (is_wp_error($response)) {
				$message = $response->get_error_message();
			} else {
				$message = __('An error occurred, please try again.', 'wp-asset-clean-up');
			}

			$this->activationErrorRedirect($message); // stop here and redirect
		}

		$bodyResponse = wp_remote_retrieve_body($response);

		// The key is no longer valid (if it ever was)
		if (stripos($bodyResponse, 'invalid license key') !== false) {
			delete_option(WPACU_PLUGIN_ID . '_pro_license_key');
			delete_option(WPACU_PLUGIN_ID . '_pro_license_status');
		}

		// decode the license data
		$licenseData = @json_decode($bodyResponse);

		// $licenseData->license will be either "deactivated" or "failed"
		if (in_array($licenseData->license, array('deactivated', 'failed'))) {
			delete_option(WPACU_PLUGIN_ID . '_pro_license_key');
			delete_option(WPACU_PLUGIN_ID . '_pro_license_status');
		}

		wp_redirect( add_query_arg(array('deactivated' => '1'), esc_url(admin_url( 'admin.php?page=' . WPACU_PLUGIN_ID . '_license' ))) );
		exit();
	}

	/**
	 * @param $message
	 */
	public function activationErrorRedirect($message)
    {
	    wp_redirect( add_query_arg(
		    array('wpacu_pro_activation' => 'false', 'message' => urlencode($message)),
		    esc_url(admin_url( 'admin.php?page=' . WPACU_PLUGIN_ID . '_license' ))
	    ) );
	    exit();
    }

	/**
      * This is a means of catching errors from the activation method above and displaying it to the customer
	 */
	public function adminNotices()
	{
        if ( ! isset($_GET['wpacu_pro_activation']) ) {
            return;
        }

		static $noticeShown = false;

		switch ($_GET['wpacu_pro_activation']) {
            case 'false':
	            if ( ! $noticeShown && isset($_GET['message']) ) {
	                if ($message = get_transient('wpacu_license_activation_failed_msg')) {
	                    delete_transient('wpacu_license_activation_failed_msg');
                    } else {
		                $message = urldecode( $_GET['message'] );
	                }
		            ?>
                    <div class="wpacu-error">
                        <p><?php echo esc_html($message); ?></p>

                        <?php
                        if ($upgradesOutput = get_transient('wpacu_no_activations_left_upgrades_output')) {
                            delete_transient('wpacu_no_activations_left_upgrades_output');
                            echo preg_replace( '@<(script|style|iframe)[^>]*?>.*?</\\1>@si', '', $upgradesOutput );
                         }
                        ?>
                    </div>
		            <?php
	            }
	            $noticeShown = true;
            break;

            case 'true':
            default:
                if (! $noticeShown && get_transient(WPACU_PLUGIN_ID . '_license_just_activated')) {
                    delete_transient(WPACU_PLUGIN_ID . '_license_just_activated');
	                ?>
                    <div class="notice notice-success is-dismissible">
                        <span class="dashicons dashicons-yes"></span> <?php _e('The license has been activated successfully.', 'wp-asset-clean-up'); ?>
                    </div>
	                <?php
                }
	            $noticeShown = true;
            break;
        }
    }

	/**
	 * @param $pluginData
	 * @param $response
	 */
	public function licenseNotActivated($pluginData, $response)
	{
		static $shownLicenseNotActivatedMessage = false;

		$license = get_option( WPACU_PLUGIN_ID . '_pro_license_key');
		$status  = get_option( WPACU_PLUGIN_ID . '_pro_license_status');

		if ((($status !== 'valid') || (! $license)) && (! $shownLicenseNotActivatedMessage)) {
			echo '<strong><a href="'.esc_url(admin_url('admin.php?page=wpassetcleanup_license')).'">'.
			     '&nbsp;&nbsp;<span class="dashicons dashicons-warning"></span>&nbsp;'.'Please make sure you have a valid license activated in "License" plugin\'s page to qualify for plugin updates.'
			     .'</a></strong>';
			$shownLicenseNotActivatedMessage = true;
		}
	}

	/**
	 *
	 */
	public function getLicenseInfoScripts()
	{
		global $current_screen;

		$licenseKeyValue = get_option(WPACU_PLUGIN_ID . '_pro_license_key');

		if ( ! $licenseKeyValue ) {
		    return;
		}

        $currentPageIsPluginLicense = isset($_GET['page']) && $_GET['page'] === WPACU_PLUGIN_ID.'_license';

        // Trigger every {$hoursMax} hours (or whenever they are accessed, it could be after 10 hours since last time)
        // the plugin license check on any plugin page and a few key pages from the Dashboard
        $currentlyOnPagesToTriggerEveryXTime =
	        (isset($_GET['page']) && is_string($_GET['page']) && (strpos($_GET['page'], WPACU_PLUGIN_ID.'_') === 0)) ||
            $current_screen->base === 'dashboard' ||
            $current_screen->base === 'plugins' ||
            $current_screen->base === 'update-core';

		$triggerIf = $currentPageIsPluginLicense || $currentlyOnPagesToTriggerEveryXTime;

		if ( ! $triggerIf ) {
			return; // Nothing to check here
		}

		// Outside plugin's "License" page; Don't check too often
		if ( ! $currentPageIsPluginLicense && $currentlyOnPagesToTriggerEveryXTime ) {
            $lastTimeCheckOutsideLicensePage = get_transient('wpacu_last_time_check_outside_license_page');

            if ($lastTimeCheckOutsideLicensePage) {
	            $hoursMax = 6;

                $diffFromLastCheckUntilNowSeconds = (time() - $lastTimeCheckOutsideLicensePage);

                if (($diffFromLastCheckUntilNowSeconds / 3600) < $hoursMax) {
                    // If less than $hoursMax hours have passed since the last check, don't trigger it
                    return;
                }
		    }

			set_transient('wpacu_last_time_check_outside_license_page', time());
		}
		?>
		<script type="text/javascript">
            if ('undefined' !== typeof window.jQuery) {
                jQuery(document).ready(function ($) {
                    $.ajax('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                        type: 'POST',
                        data: {
                            action: '<?php echo WPACU_PLUGIN_ID . '_get_license_info'; ?>'
                        },
                        success: function (response) { // Callback
                            var wpacuResponseJson = $.parseJSON(response),
                                wpacuMenuLicenseTargetEl = '.wpacu-tab-current .extra-info.license-status';

                            var wpacuOutputTableRows = wpacuResponseJson.output,
                                wpacuRenewalLink = wpacuResponseJson.renewal_link,
                                wpacuLicenseStatus = wpacuResponseJson.license_status,
                                wpacuLicenseStatusUpdated = wpacuResponseJson.new_license_status,
                                wpacuLicenseStatusHtml = wpacuResponseJson.license_status_html;

                            // In case there are glitches and duplicate values are printed
                            $('tr.wpacu-license-extra-info').remove();

                            // Append the extra license info to the table
                            $('#wpacu-license-table-info tbody').prepend(wpacuOutputTableRows);

                            // Set renew license link
                            $('#wpacu-license-renewal-link').show().find('a').attr('href', wpacuRenewalLink);

                            // Update the status from the top menu within "License" tab
                            if ($(wpacuMenuLicenseTargetEl).length > 0) {
                                /*
								 * The license status was updated during the checking (e.g. from expired to valid as the license was renewed)
								 */
                                if (wpacuLicenseStatusUpdated === 'valid') {
                                    $(wpacuMenuLicenseTargetEl).removeClass('inactive').addClass('active').html('active');
                                    $('#wpacu-sidebar-menu-license-status').hide();
                                } else {
                                    /*
									 * No license status was updated during the checking (e.g. from expired to active after a license renewal)
									 */
                                    if (wpacuLicenseStatus === 'expired') {
                                        $(wpacuMenuLicenseTargetEl).removeClass('active').addClass('inactive').html('expired');
                                    } else if (wpacuLicenseStatus === 'site_inactive' || wpacuLicenseStatus === 'invalid') {
                                        // e.g. Moved from one domain to another without reactivating the license
                                        $(wpacuMenuLicenseTargetEl).removeClass('active').addClass('inactive').html('inactive');
                                    } else if (wpacuLicenseStatus === 'disabled') {
                                        // e.g. Moved from one domain to another without reactivating the license
                                        $(wpacuMenuLicenseTargetEl).removeClass('active').addClass('inactive').html('disabled');
                                    } else if (wpacuLicenseStatus === 'active') {
                                        $(wpacuMenuLicenseTargetEl).removeClass('inactive').addClass('active').html('active');
                                        $('#wpacu-sidebar-menu-license-status').hide();
                                    }
                                }
                            }

                            if (wpacuLicenseStatusHtml) {
                                $('#wpacu-license-status-area').html(wpacuLicenseStatusHtml);
                            }

                            // Hide the loading spinner as the license page information has been updated
                            $('#wpacu-license-spinner-for-info').hide();
                        }
                    });
                });
            }
		</script>
		<?php
	}

	/**
	 * Triggered in /wp-admin/admin-ajax.php
	 */
	public function ajaxGetLicenseInfo()
	{
		// Triggered in /wp-admin/admin-ajax.php
		$licenseKeyValue         = trim(get_option(WPACU_PLUGIN_ID . '_pro_license_key'));
		$licenseKeyCurrentStatus = trim(get_option(WPACU_PLUGIN_ID . '_pro_license_status'));
		$isRightAction           = isset($_POST['action']) && ($_POST['action'] === WPACU_PLUGIN_ID.'_get_license_info');

		if ($isRightAction && $licenseKeyValue) {
			// data to send in our API request
			$apiParams = array(
				'edd_action' => 'check_license',
				'license'    => $licenseKeyValue,
				'item_id'    => WPACU_PRO_PLUGIN_STORE_ITEM_ID, // The ID of the item in EDD Store
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post(
				WPACU_PRO_PLUGIN_STORE_LICENSE_ACTION_URL,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $apiParams
				)
			);

			// make sure the response came back okay
			if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
				} else {
				ob_start();

				$licenseData = json_decode(wp_remote_retrieve_body($response));

/*
				 * [START active license on current site for Unlimited license]
				 * If it's already activated, then the action will have no effect
				 */
                // If the user has the Unlimited license, then auto-active the current site as long as the license is already "active"
				// To make his/her life easier as there's no point in showing up errors when for instance the site was moved from staging to live
				if (in_array($licenseData->license, array('active', 'valid'))
				    && ($licenseData->license_limit === 0 || $licenseData->activations_left === 'unlimited')
				) {
					$newLicenseData = $this->autoActivationAttempt($licenseKeyValue, $licenseData);

					if ($newLicenseData !== false) {
						$licenseData = $newLicenseData;
					}
				}
				/*
				 * [END activate license on current site for Unlimited license]
				 * If it's already activated, then the action will have no effect
				 */

				if (isset($licenseData->price_id) && $licenseData->price_id) {
					?>
                    <tr valign="top" class="wpacu-license-extra-info">
                        <th scope="row" valign="top">
							<?php _e('Activation Limit', 'wp-asset-clean-up'); ?>
                        </th>
                        <td style="padding-bottom: 18px;">
	                        <span style="background: rgba(255, 255, 255, 0.5); border: 1px solid #e7e7e7; border-radius: 4px; padding: 5px;">
								<?php
	                            $licenseLimit = ($licenseData->license_limit === 0) ? 'Unlimited' : $licenseData->license_limit;
	                            echo (int)$licenseData->site_count.' / <strong>'.$licenseLimit.'</strong>';
								?>
	                        </span>
	                        <?php
                            echo '&nbsp;&nbsp;';

                            // For any license except the Unlimited one
                            if ($licenseData->license_limit > 0) {
                            	if ($licenseLimit === 1) { // Single
		                            echo ' &nbsp; If you need to use the license on more than one website, <a target="_blank" href="https://www.gabelivan.com/customer-dashboard/license-update/">click here to view the upgrade options.</a>';
	                            } elseif ($licenseLimit > 1) { // Plus
                            		if ($licenseData->site_count === $licenseLimit) {
                            			echo '&nbsp;You have reached the maximum number of activations.';
		                            }
		                            echo '&nbsp;If you need to use the license on more websites, <a target="_blank" href="https://www.gabelivan.com/customer-dashboard/license-update/">click here to view the upgrade options.</a>';
	                            }
                            }
                            ?>
                        </td>
                    </tr>
					<?php
				}

				if (isset($licenseData->expires) && $licenseData->expires) {
				    ?>
                    <tr valign="top" class="wpacu-license-extra-info">
                        <th scope="row" valign="top">
		                    <?php _e('Expiration Date', 'wp-asset-clean-up'); ?>
                        </th>
                        <td style="padding-bottom: 18px;">
                            <?php echo date(get_option('date_format'), strtotime($licenseData->expires)); ?>

                            <?php
                            if (isset($licenseData->license) && $licenseData->license === 'expired') {
                                echo ' * <em>this license has to be renewed in order to be eligible for updates &amp; premium customer support</em>';

                                // Mark it as expired
                                Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_status', 'expired');
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }

				$outputTableRows = ob_get_clean();

				$responseArray = array(
					'output'             => $outputTableRows,
					'license_status'     => $licenseData->license,
					'new_license_status' => '', // if any
					'renewal_link'       => ''  // it will get filled later on
				);

				if (in_array($licenseData->license, array('valid', 'expired', 'site_inactive', 'invalid', 'disabled'))) {
					ob_start();

					if ($licenseData->license === 'valid') {
							// The license expired (as its current status is set) and it was renewed
							// Attempt to re-activate it, otherwise set it to inactive
							if ($licenseKeyCurrentStatus === 'expired') {
								$newLicenseData = $this->autoActivationAttempt($licenseKeyValue);

								if (isset($newLicenseData->license) && $newLicenseData->license === 'valid' && ! (isset($newLicenseData->error) && $newLicenseData->error)) {
									Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_status', 'valid');
									$responseArray['new_license_status'] = 'valid';
								} elseif (isset($licenseData->error) && $licenseData->error === 'no_activations_left') {
									Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_status', 'inactive');
									$responseArray['new_license_status'] = 'inactive';
								} else {
									Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_status', 'inactive');
									$responseArray['new_license_status'] = 'inactive';
								}
							}
					?>
						<span style="color: green; font-weight: bold;"><span class="dashicons dashicons-yes"></span> <?php _e('active', 'wp-asset-clean-up'); ?>&nbsp;&nbsp;</span>
					<?php } ?>

					<?php  if ($licenseData->license === 'expired') { ?>
						<span style="color: #cc0000; font-weight: bold;"><?php _e('expired', 'wp-asset-clean-up'); ?>&nbsp;&nbsp;</span> <span style="vertical-align: top;" id="wpacu-license-renewal-link">&nbsp;<a href="<?php echo esc_url(self::generateRenewalLink($licenseKeyValue, $licenseData)); ?>" class="button button-primary">Renew License for a 15% discount</a></span>
					<?php } ?>

					<?php if (in_array($licenseData->license, array('site_inactive', 'invalid', 'disabled'))) { ?>
						<span style="color: #cc0000; font-weight: bold;"><span class="dashicons dashicons-warning"></span> <?php
							if ($licenseData->license === 'invalid') {
								_e( 'invalid', 'wp-asset-clean-up' );
								?>
								* <small style="font-weight: 300; font-style: italic;">It looks like the license is no longer valid. Please renew it!</small>
								<?php
							} elseif ($licenseData->license === 'disabled') {
								// Mark it as expired
								Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_status', 'disabled');

								_e( 'disabled', 'wp-asset-clean-up' );
								?>
								* <small style="font-weight: 300; font-style: italic;">It looks like the license has been disabled. It usually happens when a refund has been issued. If you believe this is a mistake and the license should be active, <a target="_blank" href="https://www.gabelivan.com/contact/">please write a support ticket</a>.</small>
								<?php
							} else {
								_e( 'inactive', 'wp-asset-clean-up' );
							}
							?>&nbsp;&nbsp;</span>
					<?php } ?>
					<?php
					$responseArray['license_status_html'] = ob_get_clean();
				}

				if ($licenseData->license === 'expired' && $responseArray['new_license_status'] === '') {
					$responseArray['renewal_link'] = self::generateRenewalLink($licenseKeyValue, $licenseData);

                    Misc::addUpdateOption(WPACU_PLUGIN_ID . '_pro_license_renewal_link', $responseArray['renewal_link']);
				}

				if ( $responseArray['new_license_status'] === 'valid' || (isset($licenseData->license) && $licenseData->license === 'valid') ) {
                    delete_option(WPACU_PLUGIN_ID . '_pro_license_renewal_link'); // no need to store the renewal link if the license is active
                }

				echo wp_json_encode($responseArray);
			}
		}

		exit();
	}

	/**
	 * @param $licenseKeyValue
	 * @param $licenseData
	 *
	 * @return string
	 */
	public static function generateRenewalLink($licenseKeyValue, $licenseData)
	{
		$licenseKeyValueHiddenInUrl = strrev(
			substr_replace(
				$licenseKeyValue,
				str_replace('.', '', uniqid('', true)),
				6,
				20
			)
		);

		// Product ID & Payment ID
		$prIdPayId = strrev( WPACU_PRO_PLUGIN_STORE_ITEM_ID.'/' . $licenseData->payment_id );

		return WPACU_PRO_PLUGIN_STORE_URL.'/checkout/?nocache=true' .
		               '&wpacu_str_one=' . $licenseKeyValueHiddenInUrl .
		               '&wpacu_str_two=' . $prIdPayId;
	}

	/**
	 * @param $licenseKeyValue
	 * @param bool $licenseData
	 *
	 * @return array|bool|mixed|object
	 */
	public function autoActivationAttempt($licenseKeyValue, $licenseData = false)
	{
		// data to send in our API request
		$apiParams = array(
			'edd_action'      => 'activate_license',
			'activation_type' => 'automatic',
			'license'         => $licenseKeyValue,
			'item_id'         => WPACU_PRO_PLUGIN_STORE_ITEM_ID, // The ID of the item in EDD Store
			'url'             => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post(
			WPACU_PRO_PLUGIN_STORE_LICENSE_ACTION_URL,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $apiParams
			)
		);

		// make sure the response came back okay
		if ( ! (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) ) {
			// Overwrite the $license_data with a new one, that has the license active
			$licenseData = json_decode( wp_remote_retrieve_body( $response ) );
		}

		return $licenseData;
	}

	}
