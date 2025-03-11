<?php
// *********************************************************************************************************************
// *** Bugs.
// *********************************************************************************************************************
// - Bug: Items were inserted into the database twice. How on earth did that happen? Did the user
//   click twice? Try disabling the submit button by displaying an overlay whenver a submit button
//   is clicked? The button should not be a submit button, and should run a function when clicked.
//   CTRL U (used to display code) will cause the page URL to be reloaded. Is this what happened?
// - Bug: The user was logged out while doing... something with locations and products?
// - Bug: NaN displayed if you don't have an offer in the database. How to reproduce this one?
// - Bug: During booking, if you use an invalid e-mail for logging in, the user interface says "login successful". The
//   server does an HTTP 302 redirect, and the new URL has ?login=invalid_email. The code should catch it, and return
//   a more descriptive error code - and certainly not show "login successful".

// - Bug: I was allowed to delete a product type, even though there were still products using that type. Check database
//   constraints.
// - Bug: Book a particular product from admin_rental_overview. Ensure the location has no insurances. The insurance
//   tab still shows, and while you skip it when selecting the product type, you can return to it from the summary tab.
// - Bug: When displaying order history, if you open an order, the total amount table cell still has an underline.
// - Bug: When editing user notes "He's totally bonkers!" got "Error fetching or updating user notes: SyntaxError:
//   JSON.parse: bad escaped character at line 4 column 20 of the JSON data" when loading notes.
// - Bug: A Gibbs admin was somehow given a user role in a user group. This should not happen. Don't assign roles to
//   Gibbs administrators. They have access everywhere, in any case.
// - Bug: In admin_rental_overview, I was unable to book a unit that had been cancelled. Was it because it needs
//   checking?
// - Bug: In book_subscription, if there is no available insurance, you go directly to the summary tab. But if you click
//   "Tilbake", you'll go to an empty tab. You should go further back.
// - Bug: When settings were saved, a lot of settings just disappeared. Nets keys, URLs and e-mail information. Was
//   everything reset to defaults? If so, why?

// *********************************************************************************************************************
// *** To do before release.
// *********************************************************************************************************************

// Changes for Lagergutta.

// Modifications from test:
// - In admin_book_subscription, display help text to say that negative price modifiers are discounts.
// - In admin_book_subscription, display the payment notification in red if invoice is not permitted for the currently
//   selected customer type. Update the text to say so as well.
// - Bug? As admin, sell a product. No order will be created. Should it?
// - Create subscription now. Cancel immediately. End date will be before start date. Disallow?
// - When creating several storage units, display a sample of what the names will look like. Or a complete list, with
//   the opportunity to modify the unit type?
// - For all items, especially categories and unit types: display a description of what they are, if none have been
//   created. Include sample values.
// - Wherever a table row can be opened or closed, use carets, rather than plus or minus icons.
// - Bug: Go to login page for Minilager from Gibbs booking. You have to log in again. Why?
// - Bug: Prod. Terminating subscription immediately (with the end date yesterday and creation date today) did not cause
//   the ready status to be set to "check". Try again.
// - Bug: As Gibbs admin, changing the language from admin_rental_overview does not always work. The page is reloaded,
//   but there are no items in the table.
//   How to repeat?
// - Bug: Set ready status to yes for a subscription that was just cancelled. Got unknown error message from server.
//   Unable to repeat.

// Rental overview (8696e7pv2):
// - In admin_products, rename the checkbox "Kan bestilles når ledig" to "enabled". Add explanation of what happens when
//   the unit is already rented.
// - In admin_products, enable / disable units with a separate menu item, as on the admin_rental_overview page.
// - Add setting to display or hide the product status images. Store for each user, rather than for each user group.
//   Separate tab in Settings? Separate menu option?

// Other:
// - In SidebarMenu._getLinkItem, do processing in a separate function. Support shift-click as well as ctrl-click.
// - Ensure all links support opening in a separate tab or separate window.
// - Prevent an administrator from creating a subscription. They should create a test user for this instead. What do we
//   do if a user with existing subscriptions is promoted to administrator? Prevent that too?
// - Delete a product. Is the sorting preserved?
// - Update appearance of unit types during booking.
// - Allow the end date of a price rule to be modified after the price rule has expired? Why not? At least copy the
//   price rule, and allow dates to be set starting from today.
// - Option to hide expired price rules.
// - Update the code to reflect the fact that the active flag for a subscription might be changed to 0 later.
// - Flag: has access. Per subscription. Can the customer access his storage unit? Remove automatically if order not
//   paid. Display this in admin_subscriptions. Do not display access codes in user_dashboard?
// - Add function to find duplicate product names in the current view.
// - Wherever user notes are loaded and saved, use the spinner in a uniform manner.
// - Get rid of the document.getElementById calls when using displaySpinnerThenSubmit.
// - admin_edit_user does not have a placeholder text when there are no subscriptions. Should it?
// - Allow admin to resume a cancelled storage unit, provided nobody else has booked it. Add e-mail trigger for this.
// - Allow admin to assign a different storage unit of the same unit type. Select from a list of free units. Add e-mail
//   trigger for this.

// Security:
// - Use $wpdb->prepare for all queries where user-supplied information is used. The sanitisation of input parameters
//   may not be enough to prevent SQL hacks.
//   Further processing:
//     Price_Plan_Data_Manager.create_price_plan_lines
//       From: User_Subscription_Data_Manager.add_rent_and_insurance_price_plans
//     Price_Rule_Data_Manager.create_price_mods
//     Product_Data_Manager.create - validate the "name" property
//     Product_Data_Manager.create_multiple
//     Nets keys in Settings
//     All get_data_item methods.
// - Verify everything read from the client.
//   Verified user pages until register.php
//   Check User::register.

// E-mail and SMS communications: Kamil, Abdi.
// Bank ID identity check: Kamil, Sunil.
// Tripletex integration: Kamil.
// Credit check: Kamil.
// Lock integration: Kamil, Abdi.

// *********************************************************************************************************************
// *** Done.
// *********************************************************************************************************************
// - In admin_subscriptions, add link to admin_edit_user.
// - In admin_locations, add dialogue box with the URL to book_subscription with the location pre-selected.
// - Use the full mode when determining how to display product types.
// - Display unavailable product types as if they were available, and redirect to submit_request.
// - Display info icon and dialogue box with screenshots to see how the full mode works.
// - Add to settings a full mode, and a list of locations.
// - Edit the full mode in admin_settings.
// - Add location_id parameter to select_booking_type. Verify that the submitted ID is valid.
// - Add initial_location_id parameter to book_subscription. Verify that the submitted ID is valid.
// - In admin_book_subscription, verify that submitted location IDs are valid.
// - Add user interface to insert data fields in e-mail and SMS templates.
// - Set default sort order in admin_email_sms_log.
// - In admin_email_sms_log, always display the button to view message contents in a dialogue. Line breaks might cause
//   the message to be different from what is displayed in the  table.
// - In admin_email_sms_log, when displaying message contents, display them in a textarea, like when viewing user notes.
//   This will display line breaks.
// - Bug: In admin_email_sms_log, the page does not finish loading if any of the messages includes line breaks.
// - Add "copy to" field to e-mail templates. Store in and read from database. Add to data table. Display and edit in
//   admin_email_sms_templates. Do not display for SMS templates. Use the full width for the edit box.
// - Check all occurrences of "GROUP BY", to see if they should read "ORDER BY".
// - Bug: The list of orders shows fewer order than there are subscriptions.
// - Remove the ability to edit an e-mail and SMS template trigger. The trigger can only be selected when a template is
//   created.
// - In admin_locations, add link to booking.
// - On the dashboard, add user group ID to the login URL.
// - Update the list of e-mail and SMS triggers.
// - Preserve line breaks when editing e-mail and SMS templates.
// - In book_subscription, display dialogue box with complete price information.
// - In book_subscription, use price information dialogue when clicking the "i" icon on the summary page.
// - In book_subscription, rearrange elements when selecting available product types according to new design.
// - In admin_book_subscription, display dialogue box with complete price information.
// - In admin_book_subscription, use price information dialogue when clicking the "i" icon on the summary page.
// - In admin_book_subscription, rearrange elements when selecting available product types according to new design.
// - Create user groups and grant them a licence. Required for testing.
// - In location_data_manager, allow passing database results to the read method, so that those results can be used for
//   other purposes as well.
// - In book_subscription, when the user selects a location, check the booking type and - if required - the location.
//   Switch to submit_request if required.
// - In book_subscription, when the user selects an alternate location, and the alternate location requires a request,
//   ensure that the selected category is included as a parameter to submit_request.
// - In book_subscription, when there is only one location, check the booking type and send redirect to submit_request
//   if required.
// - Remove hard coded link to open locks.
// - In select_booking_type, when booking type is "select for each location", treat it as if it were "self service".
// - Add "select for each location" option for booking type in settings. Store booking method for each location.
// - Update admin_settings to include new booking type option, and display list of locations.
// - Remove password edit box from admin_book_subscription.
// - Remove password edit box from submit_request.
// - Display access code and access link in admin_rental_overview.
// - Remove password edit box from book_subscription. Users will have to log in using e-mail. Store "test1234" if the
//   purpose is evaluation, and a randomly generated password when the purpose is production. Generate the password on
//   the server, not the client.
// - On log_in_to_dashboard, change headline and add link to new login page.
// - In admin_edit_user, change the Norwegian title to "kundekort".
// - Use the popup menu on the admin_edit_user page.
// - Find a better icon for product notes than fa-pen-to-square. User notes have a custom one.
// - Decode line breaks before displaying product notes in the table in both admin_rental_overview and admin_products.
//   Also before editing. It doesn't work when loading notes as part of the table. It does after editing them.
// - In admin_rental_overview, the menu is too long, and is displayed above the top edge of the window.
// - In admin_products, add dialogue box to edit product notes.
// - In admin_products, add menu item to view / edit product notes.
// - In admin_products, add code to store product notes.
// - Bug: Product notes are not read from the database and downloaded on the admin_rental_overview page.
// - Add file to load and save product notes asynchronously.
// - In admin_rental_overview, add dialogue box to edit product notes.
// - In admin_rental_overview, add menu item to view / edit product notes.
// - In admin_rental_overview, add code to store product notes.
// - Bug: When booking as an administrator, a disabled button still has the icon in white with green background.
// - Bug: When booking as an administrator, low-profile buttons have the same colour as the main buttons.
// - In admin_dashboard, modify the link to the login page to a hard coded one.
// - In admin_book_subscription, display warning that the payment method will always be invoice when booking on behalf
//   of a customer.
// - Display dummy graphics on the admin dashboard.
// - Fix bug reading the access code and access link for a subscription.
// - In user_dashboard, only display lock information if the subscription is ongoing.
// - Display key code and link to open locks in user_dashboard.
// - If there's nothing in the database to open locks, add hardcoded link:
//     https://server2.locky.thundertech.no:443/lockyapi/mobilekey/iplink?tenantId=6619a08347d76a0f37fb8799&security=b2fc6b4e-59fd-4ca7-9cc5-f9a762e751c4
// - On the settings page, describe what the list of terms and conditions URLs contains.
// - Better description for "number of storage units" on the settings page.
// - On the settings page, describe that "production" uses real money; "evaluation" does not.
// - Instead of checking "resultCode >= 0", create utility method isError(resultCode).
// - Close user dropdown when you click elsewhere, just like the table menus.
// - Bug: When display error message after asynchronous requests, the code uses "resultCode" rather than
//   "data.resultCode" in a number of locations. Fix all.
// - Bug: When cancelling a subscription from admin_edit_user and admin_subscriptions, in English, the button says
//   "End subscription", but the button is not wide enough. Change to "Confirm".
// - Bug: When cancelling a subscription from admin_rental_overview, in English, both buttons say "Cancel".
// - Bug: Prod. Setting ready status to check did not cause any change.
// - In admin_dashboard, link to select_booking_type, rather than book_subscription.
// - Bug: When editing a location, the postcode seems to be interpreted as a number. "0665" doesn't work. Modify
//   database.
// - In the menu, use "configuration" rather than "settings" for the folder.
// - When setting terms and conditions URLs, note that the URL must be complete, with http or https.
// - Bug: When selling as an admin, and adjusting the price, the base price is listed as "undefined".
// - On the dashboard, add URLs for the customer links (booking, user dashboard, registration, etc.)
//     https://www.gibbs.no/subscription/html/book_subscription.php?user_group_id=X
// - Hide the "send offer" radio button when booking as admin, since it's not implemented.
// - Nets "offentlig nøkkel" should read "betalingsnøkkel" / "checkout key". Display description of how to get it.
// - Add separator between entries on the settings page.
// - Delete settings.termsUrlCount. Use Object.keys instead.
// - Use ordinary buttons in the terms table in settings. The headline "add/delete" confuses people.
// - Use fa-hammer for disabled products, and a check mark for enabled ones. Use both in menu and table, in both
//   admin_rental_overview and admin_products.
// - Use fa-magnifying-glass for ready state check, and a check mark for ready status yes. Use both in menu and table,
//   in both admin_rental_overview and admin_products.
// - In admin_products, display product notes in separate column. Truncate to 25 characters. Add sorting. Include in
//   freetext search.
// - In admin_rental_overview, display product notes in separate column. Last column before menu button. Only when the
//   product is disabled? Truncate to 25 characters. Add sorting. Include in freetext search.
// - Include product notes in data table when loading products.
// - In Product_Info_Utility, add methods to read, write and update product notes.
// - Add option to display icon in getStatusLabel.
// - Display icon for enabled / disabled statuses.
// - Display icon for ready statuses.
// - Update FilterTabset.setActiveTabFromFilter to handle the case where each preset holds a set of filters.
// - Update the edit filter dialogue to match the tabset configuration options.
// - Add utility method getStatusLabel. Use it wherever appropriate. Remove styling from translations.
// - Bug: Once a Gibbs admin has been given a role in a user group, switching to the Gibbs admin role does nothing.
// - In admin_rental_overview, update the filter tabset contents. Combine product status, enabled flag and ready status
//   in one tabset.
// - Review the icons used on the ready status menu buttons.
// - Move ready status methods from Product_Data_Manager to a Product_Info_Utility. Use that from Subscription_Utility.
// - Use the require_check_after_cancel setting. Whenever a subscription end date is set, set the product status to
//   "check", so it is no longer available for rent.
// - Add a require_check_after_cancel setting to say whether to set the product status to "check" when a subscription is
//   cancelled.
// - In admin_rental_overview, if rented, add menu option and dialogue box to cancel the current subscription.
// - Add test data.
// - In admin_rental_overview, allow booking a cancelled product. Set the initial date to the current subscription's end
//   date.
// - In admin_products, display the product status in separate column. Remove the check marks.
// - In admin_rental_overview, include check-out dates even if the date has passed. That is, display check-out dates for
//   expired subscriptions.
// - In admin_rental_overview, add menu item to set the ready status.
// - Add asynchronous call to set the ready status.
// - In product_data_manager, implement methods to set ready status.
// - In admin_rental_overview, update the text of the set enabled / set disabled menu item.
// - Decide how to combine settings for enabled with the option to enter the results of a check. Result: Do not combine
//   them at all.
// - In admin_rental_overview, figure out which menu options should be present. Vary with status?
// - In admin_rental_overview, figure out which tabs we should have in the filter tabset. What should be the criteria
//   for each tab?
// - Hide the product status images.
// - In admin_rental_overview, add column "Unit status". Use the enabled flag. Place next to last.
// - In admin_rental_overview, add column for "needs check". Use the ready status. 
// - In admin_rental_overview, modify status short names.
// - In admin_rental_overview, modify status descriptions to match the short names.
// - In admin_rental_overview, rename check-in / check-out columns to "Moving in", "Moving out".
// - Remove STATUS_NOT_READY and st.prod.NOT_READY. Use STATUS_NEW and the enabled flag.
// - Remove STATUS_CHECK_NOW and st.prod.CHECK_NOW. Use STATUS_VACATED and the ready status.
// - Check all uses of the is_free function. Does any of them also need to consider the enabled field? Make an
//   is_bookable method.
// - Remove STATUS_CHECK_SOON and st.prod.CHECK_SOON. Use STATUS_CANCELLED and the ready status.
// - Rename the "can book" flag to "enabled". Keep using the post_status field to store the value.
// - Define constants for ready status, both on the client and on the server. Link them in comments. Values: "ready",
//   "check". Instead of "not ready", use the "disabled" state of what is currently the "can book" flag.
// - In the product data manager, add ready status to ptn_postmeta whenever a product is created. Key name:
//   "product_status".
// - In the product data manager, add ready status to ptn_postmeta whenever we create multiple products at once.
// - In the product data manager, update ready status in ptn_postmeta whenever a product is edited. Override the
//   update method.
// - In the product data manager, delete ready status in ptn_postmeta whenever a product is deleted. Override the
//   delete method.
// - On the client, when creating one or more products, pass the ready_status field.
// - On the client, when editing a product, pass the ready_status field.
// - Add ready status to data tables wherever required.
// - When creating several products at once, store an array of the IDs created.

// *********************************************************************************************************************
// *** Backlog.
// *********************************************************************************************************************

// See if done or no longer relevant:
// - Why do we have both Role_Data_Manager.get_user_list and User_Data_Manager.get_users?
// - Test the changes to the user menu. Select a user group.
// - Ensure that verify_is_user is called on all pages that require login. What do we do in
//   set_language.php? Should verify_is_user return true if the user is an admin? Or should we have
//   a user_has_permissions method?
// - Read the initial page for a user from user_metadata.
// - Store the initial page for a user to user_metadata when he switches using the drop-down menu.
// - When creating test subscriptions, ensure that the dates do not overlap existing subscriptions.
// - Settings page that clients can access. Set things like how long in advance you can book, how
//   many characters a password need to have, etc. Also set the CSS file to use (and allow the
//   client to upload and edit it). Choose which file names to use for each file, so that we can
//   customise individual files for each client. (Verify that all the settings are available.)
// - When an administrator creates a new user, don't immediately log in as the new user.
// - When creating a new location, display an error if an existing location has the same name.
// - When creating products, if you have selected the batch options, disable the submit button if
//   you enter a text in the numeric edit boxes, or if the numbers are outside the allowed range.
//   Also disable the submit button if the numeric edit boxes are empty.

// Sorting:
// - Store sort order for each column. Revert to that sort order when a new column is sorted on.

// Monthly payments:
// - Order_Data_Manager.get_order_data should return a result code.
// - Deal with tax.
// - When orders have been created, verify the subscriptions where the payment method is Nets. Report problems to the
//   customer.

// E-mail communications:
// - Add a language code to all templates, so that we can have one for each supported language?
// - Store template ID in e-mail and SMS log. Use it in the message log, to display the trigger that caused the message
//   to be sent.
// - Filter on freetext on key up, not on enter. This will only work if the toolbar is not regenerated every time. Do
//   that when the edit box loses focus.

// Orders page.
// - Add parameter to admin_subscriptions.php to highlight a particular subscription. Scroll to the highlighted
//   subscription.
// - On the admin_orders page, link to the subscription.
// - Link to admin_orders from admin_edit_user and admin_subscriptions.
// - Delete or change status for an order.

// Admin dashboard:
// - Implement the various widgets on the admin dashboard page. Figure out what we want to have there.

// Settings:
// - Add setting for terms and conditions URL. How to deal with different languages?
// - Add colour picker dialogue. Use it when editing colour values.
// - Add an option to settings to say how to round the price when modified due to capacity. In GBP, for instance, we
//   might not want to round to the nearest whole number. This is currently done in
//   Price_Rule_Data_Manager.get_price_based_on_capacity.
// - Add an option to settings to say which currency to be used? Also add a setting to say how to describe it (NOK vs
//   kr)?

// Subscriptions page:
// - Option to deactivate subscriptions. Use the same button as the one to cancel the subscription. Display a dialogue
//   box to ask whether to deactivate immediately, or cancel normally. Note that an inactive subscription cannot
//   (easily) be activated again, as somebody else may have booked the product in the meantime.
// - Filter on start dates in admin_subscriptions. Use a calendar to select a date. Needs to be in the past.
// - Filter on end dates in admin_subscriptions. Use a calendar to select a date. Needs to be in the past.
// - Filter buyer names in admin_subscriptions. Partial match, begins with, etc.
// - Filter product names in admin_subscriptions. Partial match, begins with, etc.

// Send offer requests, instead of going through the booking process:
// - On the admin_requests page, click an edit button next to the status to get a custom dialogue with all the statuses
//   listed.
// - Bug: Line breaks are not preserved when editing the comment field. Use sanitize_textarea_field, rather than
//   sanitize_text_field when reading client input.
// - On the admin_requests page, display the created_at and/or updated_at times somewhere. updated_at in the list,
//   created_at when editing?
// - Link from admin_requests page to booking, in order to create an offer. Automatically fill in the information from
//   the request.
// - "My requests" page on the user side.
// - On the admin_requests page, click the status to move to the next one? Right click to go back? No. I don't think so.

// General:
// - Can we find suitable icons for the rental statuses? Arrow up-right for moving out, arrow down-right for moving in,
//   arrow right for rented? Big round zero for free?
// - In book_subscription, when the user selects an alternate location, and the alternate location requires a request:
//   can we pass the product type to submit_request, so that the administrator will know what the customer actually
//   selected?
// - Not yet: Create setting that says "How long after a storage unit is vacated should it have the 'recently
//   vacated' status?".
// - Not yet: Add status "recently vacated" when loading products. This is status "Ledig (tidligere leieforhold
//   avsluttet)" where the previous rental ended within the time stored in settings.
// - If a product is free and inactive, set status "Needs check".
// - Add a 100 ms or so delay before the spinner displays. If the operation is finished in the meantime,
//   cancel the timer.
// - Use the popup menu component for the user menu in the header.
// - Add e-mail trigger for when the administrator resets your password (if we don't already have it).
// - When going from Gibbs admin dashboard to booking without being logged in, preserve the language selection.
// - Write down why we're not using any frameworks.
// - Do not cancel a subscription if the subscription already has and end date, or if the start date
//   is after today's date.
// - Download payment history as PDF.
// - On the e-mail settings tab, add a field for time zone. Waiting for specification of format.
// - Add e-mail / SMS templates if required: Purring. When a Nets subscription is expired. When a Nets subscription
//   validation fails. When admin sends an offer.
// - Move a subscription to a different user. What to do about payment?
// - Rename "Prishistorikk" to "Prisutvikling"?
// - Update index.jsp to delete the session and move you on to booking, if a group ID is provided. Or do we already
//   log out when using the booking anonymously?
// - Turn the Config class into a non-static class. Read the file in the constructor.
// - In Order_Data_Manager.create_initial_payment, determine if termsUrl should be submitted to Nets. After all, the
//   user has already approved.
// - Make sure no order metadata ever contains ">" and "|". That includes the user's address. State why?
// - In a narrow window, hide the name of the logged-in user.
// - When booking as an admin, display at all times the comment from the request, if the booking is based on a request.
// - In admin_book_subscription, show disabled radio buttons and list boxes as disabled.
// - Implement the Create customer button in gibbs_licencees. Create both the user group and a dummy user, and assign
//   the dummy user the expected role.
// - Update status label appearance. White text on solid background.
// - Update the button colour in the admin interface.
// - Link to register.php from log_in_to_dashboard.php, in case you don't have a user. See
//   Deleted_Code\link_to_registration_from_log_in.txt. We have to know the user group ID on the registration page.
// - Download and integrate external component for country codes in phone number. See link in
//   Gibbs\Issues\2023\2023-11-01_Subscription_MVP\work\2024-08-12_Country_code
// - Implement storing user information on the admin_edit_user page.
// - Implement creating new users on the admin_edit_user page.
// - Use data types in PHP functions and class variable declarations.
// - Display the address in admin_users?
// - Fill in the area automatically, if it is empty.
// - For insurance and price rules, allow the user to select no product types or locations. At the moment, this counts
//   as selecting all of them. However, selecting none is legitimate, as you may not want the thing to appear yet. In
//   order to permit selecting none, we need to store the "for all locations" and "for all product types" flags in the
//   database. At the moment, if no links to product types or locations are found, that is interpreted as "all".
// - In admin_product_types, place categories and product types on separate tabs.
// - Put postcode on the same line as the area.
// - Add separate country code combo for the phone number. Use a flag image.
// - Store a country code ("NO"). Add a field to specify country? We have it when editing locations.
// - Display inactive subscriptions on the admin_edit_user page (but not user_dashboard).
// - Use Utility.getEditBox wherever appropriate.
// - Option to display the password in clear text on all edit boxes where new or existing passwords are entered.
// - Distinguish between companies and private individuals when registering, both on the register page and on the
//   booking page. Display the difference in the user list and the edit user page.
// - Pass user information on to Nets, so the user doesn't have to enter it twice.
// - When booking, if there are no storage units at all in a given category at the selected warehouse, do not display
//   the category. Option to always display all the categories anyway?
// - Option to change language on the registration page.
// - Change the placeholders in getText to start at zero.
// - Always use the term "expired" for finished / completed subscriptions.
// - Add application version constant in PHP. Pass it as a parameter when loading CSS and JS files.
// - What happens if discounts reduce the first payment to 0? Do we still need to do it, to establish the subscription?
// - Should we replace all occurrences of ,- with kr?
// - Option to apply price rules to categories, rather than product types.
// - Add range checking on price mod values, if we don't already have it. -100 to about +100000.
// - Set priority for special offer price rules, so that we know which one to choose?
// - Add user interface to display inactive subscriptions, with option to activate or delete them.
// - Merge User_Subscription_Data_Manager.create and .create_subscription_from_list? The only difference should be how
//   many product IDs are stored in the offer.
// - Store the insurance price on the offer as well? The chance of it changing is much less, but it shouldn't change.
// - When creating subscriptions based on a list of product IDs, store the list of any error messages generated in the
//   Offer?
// - Add a current time variable, when the application role is not production, in order to simulate the passage of time.
// - Ensure the order of product types and locations is the same for insurance and price rules.
// - When editing price mods, do not rewrite the table when edit box values change. Otherwise, focus will be lost. On
//   the other hand, rewrite the table when mods are moved, deleted, added or sorted.
// - When editing price rules, add a filter button and dialogue to filter on status.
// - When booking, the list of available locations needs a scroll bar. It also needs more unit for the names.
// - In Price_Rule_Data_Manager.get_price_based_on_capacity, cache the calculated capacities with the list of product
//   types as the key, so we don't calculate the same capacity several times. It's a heavy calculation.
// - In the Tabset, add a clickMode property to say how clicks should be handled. All; before current step; previous
//   step.
// - In the Tabset, document all the properties.
// - See if payment history code can be extracted into a separate component. Just pass a table of translations, like we
//   do with the calendar.
// - In the calendar, perform error checking on all dates. Add Utility method, if we don't have one.
// - Ensure the payment error handling catches everything. Ensure subscriptions and orders are always deleted. Display
//   suitable error messages.
// - If the user has paid for both the current month and the next, and then tries to cancel, cancel the subscription at
//   the end of next month. Update the user interface prompts to reflect the date. This only applies when the
//   subscription has just been booked.
// - The code to calculate prices should take into account the subscription end date, if present. Both on the server and
//   on the client.
// - In the payment history, write the price for each order line as a button which displays the price plan dialogue.
//   Is there a way to highlight the line which was used? Return to the payment history when closed.
// - In data managers, use $this->id_db_name wherever appropriate. Don't hard code the ID in the SQL.
// - User_Subscription_Data_Manager.create_subscription_from_list will return Result::PRODUCT_ALREADY_BOOKED, even if the
//   products could not be booked due to other errors. Store the other result codes. If they are all the same, return
//   that result code instead.
// - When displaying the "please wait" text for payment history, include the dialogue box header and footer.
// - Update payment history dummy data, to ensure it works with the new format. Do we still need to?
// - Find payment method for Nets payments. Display in payment history for users and admins.
// - Use constants for payment status. When a new value is set, ensure it is valid.
// - How do we mark an order as overdue? Run code once a day?
// - Allow the user to switch to a different insurance type from the dashboard?
// - Allow the administrator to change the insurance from the admin_edit_user page.
// - Insurance products should include an order integer, so an admin can ajust the sorting.
// - Display the tabset on the payment page. At the moment it disappears when you get that far.
// - Add a <form> around all buttons. For instance in book_subscription.php, tabset buttons. Which attributes have to be
//   present? 
// - Use different dialogue boxes for creating and editing products, to have different maximum heights.
// - Move admin_ files to admin directory. Remove prefix.
// - Move Gibbs admin files to gibbs directory. Remove prefix.
// - Purge all role numbers from the code. Use the ROLE_ constants instead. Convert wherever the
//   database is manipulated.
// - Ensure data managers convert tinyints and other odd stuff to numbers before returning them, so
//   users don't have to deal with unexpected strings.
// - When calculating subscription price on the client, get the current date from the server, so we
//   know the calculation is correct?
// - Theoretically support several payment providers.
// - Add database tables for blacklists.
// - Implement admin page for blacklisted users.
// - Display information about credit checks somewhere. Where? Booking summary?
// - Display error message when login fails. Don't redirect to the login page with the error as a
//   parameter. This only happens when the error comes from Wordpress.
// - When creating a test subscription, use the Role_Data_Manager to asynchronously load the list of
//   eligible users. Have the user select from the list, rather than just specify an ID.
// - Always have access control when using a data manager. Even when reading, or calling methods
//   directly. Pass an action to perform_action?
// - Once we have complete access control in data managers: Verify that access control works on
//   index.php. Can we read the list of customers without being logged in?
// - Call can_create in Product_Data_Manager.create_multiple?
// - Allow an admin to search for products that are free in a given time interval, and create an
//   offer for those.
// - The ROLE_ constants do not use the same numbers as in the database. Fix that? Use the constants
//   in Role_Data_Manager and calls to it.
// - Mark "most wanted response" button with colour #008474.
// - When a user logs in, set the language based on the locale setting in his user information.
// - When a user switches languages, write the setting to the locale property.
// - When a new user has been registered, if a group_id were passed, use that to determine the
//   initial page. We can't have a customer registering with company B, an be taken to the home page
//   of company A.
// - Find the proper way to identify a Gibbs admin in user_has_role.
// - Use constants for role numbers.
// - Put an overlay under the drop-down user menu. Close it when the user clicks elsewhere.
// - Allow the user to pass a user group and role when logging in, in case the previous initial page
//   is no longer accessible. That way, the user can be redirected to the appropriate page
//   immediately.
// - When adding or editing locations, fill in the town automatically, if it is empty and the
//   postcode returns a hit. Set "Norge" as default as well.
// - Edit the postcode in a shorter box. Place the town edit box next to it.
// - Read filter parameters properly. The sanitize_ function is probably not enough to stop code
//   injection.
// - When creating test subscriptions, have a checkbox to decide whether the end date field is
//   available.
// - When creating test subscriptions, use a calendar to select start and end dates. Our current component does not
//   support past dates, so fix that first.
// - On pages with several dialogue boxes, ensure IDs of form elements are unique.
// - Add a button to show that the current user menu can be opened and closed.
// - Make the language icons work.
// - Image gallery for locations. Carousel. View images in full size when clicked.
// - Polish the size of all dialogue boxes.
// - When the user clicks a disabled menu item in the main menu, display an alert box to say what he
//   needs to do to get to that page.
// - Fix all // *** // notes.
// - Add more commments wherever required.
// - Display which fields are mandatory.
// - When the user clicks a disabled button, display an alert that says why he could not click?
// - Green check mark next to each box, to say whether it's valid or not. If it isn't, display a
//   text that says why.
// - Validate phone numbers: "+" <digits> <space> <parentheses>
// - When creating products, give an error message after the fact if one or more new products had
//   the same name as an existing product. List the products that were not created.
// - Whenever a request to the server is submitted, display an overlay and a progress bar, to let
//   the user know something is happening.
// - Select countries from a list. Have one list in Norwegian, another with the same countries in
//   English, and store the index in the list, rather than the country itself. Filter function when
//   selecting.
// - When adding an ellipsis to a long string in Utility.curtail, count character entities as a
//   single character. Don't break the string in the middle of a character entity.
// - Add a means of allowing the user to translate services and opening hours into different
//   languages.
// - Add a list of services, and corresponding icons. When specifying services for a location,
//   choose from the list. Allow the user to translate the texts.

// Gibbs administrator interface:
// - Display the "user registered successfully" message in a way that doesn't look like an error.
// - In the Gibbs administrator interface, display list of administrators for a particular user group.
// - In the Gibbs administrator interface, add ability to designate an ordinary user as administrator. Modify the role.
// - In the Gibbs administrator interface, add ability to revert administrators to ordinary users. Modify the role.
// - Display inactive subscriptions. Option to delete them.
// - Add an association between a user and a user group. Assign a role. Create Gibbs admin UI to do
//   so. Do it from the licencees page? Or create a subpage with list of users for a particular
//   user group?
// - Once we have a UI to change roles, disable creating administrators through the registration
//   page.
// - Add a left menu for Gibbs admin.
// - Create test data for the Gibbs admin pages.
// - On the Gibbs admin pages, we need to write a link to the edit_user and set_language pages, but
//   we don't have a group ID. Ensure that this works.
// - In gibbs_licencees.php, create a user group that has the "Minilager" licence. Also create a
//   dummy user to go with it. Can we use the dummy user's meta table to store the settings? Such as
//   application role and whether to use hard coded data?
// - Translate the Gibbs admin interface.

// Client-side general:

// Server-side general:
// - When creating, updating or deleting items, submit an asynchronous request. Update the local
//   data table based on the server response, to avoid having to reload the entire page.
// - When a form is read, and found to contain errors, fill in the relevant data fields with the
//   data that was posted, so the user won't have to fill it all in again. Don't fill in passwords,
//   though.
// - Test the code that rejects a new password if it contains illegal characters.
// - admin_dashboard.js is not currently used. Delete it? We'll need it later.
// - Add the currently logged in user's information to the top right drop-down menu.
// - Implement PHP to provide and receive data everywhere.
// - Test that the server-side validation of e-mail addresses works.

// On the admin_edit_user page:
// - Implement creating users.
// - Implement updating existing users.
// - Decide whether to call it "user name" or "e-mail". Don't use both.
// - When a new password is set, also allow the admin to specify that the customer must change it
//   the next time he logs in.

// On the admin_products page:
// - When you deselect all items from the filter, treat that as if the user wants to delete the
//   filter.
// - Add a scrolling div to the location and product type filter dialogues. Ensure they use the
//   entire box, and add a scroll bar when required.
// - Perform error checking on filter parameters, to ensure that only valid IDs are passed. Don't
//   require the brackets to be passed as part of the parameter.
// - Display counter that says how many products satisfy the current filter criteria, and how many
//   are currently selected.
// - When the batch create function is selected, perform validation before enabling the submit
//   button.
// - Download the entire subscription history.
// - Determine current status and dates on the client based on the subscription history.
// - Option to display the entire subscription history in a dialogue box, with links to customers.
// - Buttons and dialogue boxes to filter on name.
// - Add the display customer button icons to our FontAwesome kit.
// - In the edit filter dialogue, ensure the elements are properly adjusted vertically.

// On the admin_product_types page:
// - Before deleting a product type, ask the server if any products of that type exist. If so,
//   inform the user that the operation cannot be performed.
// - When creating a product type, disable the submit button if a product type with the given name
//   already exists? Can we have duplicates? Verify that the price is a float. Or an integer?

// On the user_dashboard page:
// - Delete user_dashboard_old.
// - In the price plan, ensure that the sort order of events is as expected. Use the payment date for the first payment?
// - Have the order lines fill the entire bottom of the order card. For the sum, have a div with rounded corners.
// - Button and caption for access code.
// - Hide completed subscriptions?
// - Sorting and filtering?
// - Map of location.
// - Location information (opening hours, services, etc).
// - Add requests to another list at the bottom of the page?
// - In the price plan, add entry for when the subscription was cancelled. 

// Booking process as admin:
// - When booking as an admin, implement the option to send offers to a prospective customer. Store the offer in the
//   database.
// - When sending an offer, allow the administrator to write a message to pass to the recipient.
// - When sending an offer, should the offered storage unit be reserved, somehow? If so, for how long? Or should we
//   just sort storage units that are part of an offer last when somebody else books? Let the admin choose?
// - When booking as an admin, the confirm booking button needs a better text. Have different text for sending offers
//   and creating the subscription.
// - When booking as an admin, add the insurance to the stored offer presented to the customer? Or let the customer
//   choose?
// - When booking as an admin, consider whether we need to dump the user to the admin_dashboard. Do not ever go to the
//   user_dashboard.
// - Create a user interface for the user to examine offers, and accept and pay.
// - When booking as an admin, add link to the confirmation page to go to the admin_edit_user page.
// - When booking as an admin, add link to the confirmation page to return to the requests page, if the user came from
//   there.
// - When booking from the requests page, automatically update the request status.

// New booking process:
// - Allow an existing user to post the registration form? Use the existing user ID. How does this work if the user is
//   not logged in to Wordpress?
// - Asynchronous request to see if an entered e-mail is in use. This is a security hole. Triggered on defocus. If it is
//   in use, display an error message straight away. Also fill in the rest of the form? This is another security hole,
//   but apparently useful enough to allow.
// - Add "forgot password" button.
// - Text to say why the confirm booking button is disabled.
// - Scroll to top when switching tabs.
// - Set the URL when switching tabs, so that the browser back button takes you to the previous tab.
// - Add address.
// - Add toggle for company or individual, and extra fields for the company information.
// - Should the active tab be green when it isn't a button?
// - Get new design for the list of offers when selecting product types.
// - Get proper design for the "Only X left" message. Get a nice yellow triangle with exclamation mark icon?
// - Figure out how to display the price rules on the summary page.
// - When the server determines that a storage unit type is available at the selected warehouse from a particular date,
//   add that storage unit type to the offer, so that the user can select it straight away?
// - If the amount to be paid now is 0, add a text to say that the customer must still add his credit card in order to
//   create a subscription and pay next month.
// - In new and old process, have consistent naming of functions that display a tab. Call it displayXTab, not ...Page or
//   ...Box.
// - Get proper design for the message that says the warehouse has no vacancies.
// - Add pictures of product types.
// - Add pictures of locations.
// - Sort locations. Alphabetically?
// - Sort product types according to category and price.
// - Sort insurance according to price.
// - Set fixed position for each location in the admin interface. Don't look up the address here.
// - Search for an address, and sort the locations according to distance.
// - Sort locations according to distance when displaying alternate locations during booking. Display the distance.
// - Display all locations in a single map when booking subscriptions.
// - Display search box where you can enter an address. Sort all locations according to the distance from the stated
//   reference point.

// On the old book_subscription page:
// - In the sales summary, display checkbox for accepting terms and conditions.
// - Search for an address, and sort the locations according to distance.
// - Sort locations according to distance when displaying alternate locations during booking. Display the distance.
// - Display all locations in a single map when booking subscriptions.
// - In the book_subscription tabset, have a separate line to display what has been selected.
// - Restyle the tabset on the summary tab when booking.
// - Configuration option to select only category, or category and product.
// - Distinguish between storage units that are simply busy, and ones that don't exist at all at
//   the selected location.
// - For non-available products, add a button to change the date or location of the search.
// - Click on a finished step to go to that step.
// - Add indication of where disabled products might be found.
// - Mark Norwegian bank holidays in red.
// - Display locations as a list, like the list of products? Have a scrolling box, in case there are
//   too many locations.
// - Make the month headline into a combo box? No need, since there aren't that many months.

// On the admin_locations page:
// - Display and edit extra information about the location: opening hours, services.
// - Select icons for each service (somehow). Display when booking subscriptions. Display on the
//   user dashboard.

// *********************************************************************************************************************
// *** Postponed.
// *********************************************************************************************************************
// - In admin_rental_overview, display READY_STATUS_CHECK in different colour when the customer has not yet moved out?
//   This is only relevant if the ready status is always displayed? If we display it with the status, we don't need it?
// - In admin_rental_overview, add a dialogue box with a comprehensive product status, with bullet points.
// - In admin_rental_overview, for products that have ready status "check", add a menu option to enter the results
//   of the check. Either "ready", or "disabled". (The latter will later have a text area in which to enter notes.)
// - If a product is set as ready before the subscription expires, should there be an option to also terminate
//   the subscription at that point, so that a new customer can move in immediately? For the old customer, the full
//   length of the subscription must still be shown, though.
// - Create big flowchart of the storage unit lifecycle. Display the status that corresponds to each point. Write
//   text to illustrate what happens in the transition. Click on a particular point to see storage units in that state.
//   Show which statuses are picked when looking for a free storage unit. Display different flowchart if the "check"
//   status is not set when a subscription is cancelled.
// - When setting a product to "disabled" as the result of a check, edit the notes at the same time. Do we display
//   previously entered notes?
// - When enabling a product, add option to clear the notes? Edit them?

// *********************************************************************************************************************
// *** Rejected.
// *********************************************************************************************************************
// - In admin_rental_overview, include check-in dates even if the date has passed. That is, display check-in dates for
//   ongoing subscriptions. This potentially leaves us with two dates to be displayed, however. Resolve that.
// - In admin_rental_overview, when displaying check-out dates for expired subscriptions, and check-in dates for ongoing
//   subscriptions, alter the column headline? It's not "flytter ut" - it's already happened.
// - In admin_rental_overview, when displaying check-out dates for expired subscriptions, and check-in dates for ongoing
//   subscriptions, display these dates in grey?
// - Add status "checkout today"? This is status "Blir ledig (nåværende leieforhold oppsagt)" and product
//   disabled, on the last day of the rental. Do we need this? Use it if product is enabled as well? Create image for
//   this one. What if it's today and it's booked? So many variations!
// - Once we have "checkout today", remove "needs check soon" from "check" tab on admin_rental_overview.
// - What happens if a unit is checked and found to be not empty? Separate status for this?
// - Add an "i" icon next to the "Lagerbod" table column header in admin_rental_overview. Explain in detail what active
//   and inactive means. Use on admin_products as well.

// *********************************************************************************************************************

// Load WordPress core.
require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/config.php';

// Class to hold result codes and errors that might result from various operations, such as posting data to the server.
class Result
{
  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // If these are changed, also modify the corresponding constants in common.js.
  public const NO_ACTION_TAKEN = -2;
  public const OK = -1;
  public const MISSING_INPUT_FIELD = 0;
  public const DATABASE_QUERY_FAILED = 1;
  public const NOT_IMPLEMENTED = 2;
  public const PRODUCT_ALREADY_BOOKED = 3;
  public const MISSING_NUMBER_PLACEHOLDER_IN_PRODUCT_NAME = 4;
  public const DATABASE_QUERY_FAILED_PARTIALLY = 5;
  public const WORDPRESS_ERROR = 6;
  public const INVALID_PASSWORD = 7;
  public const PASSWORD_TOO_SHORT = 8;
  public const INVALID_EMAIL = 9;
  public const EMAIL_EXISTS = 10;
  public const USER_NOT_FOUND = 11;
  public const PASSWORD_CHANGED = 12;
  public const INVALID_ACTION_HANDLER = 13;
  public const INCORRECT_APPLICATION_ROLE = 14;
  public const ACCESS_DENIED = 15;
  public const LICENCE_EXPIRED = 16;
  public const PRODUCT_NOT_FOUND = 17;
  public const REQUEST_FAILED = 18;
  public const PAYMENT_FAILED = 19;
  public const UNABLE_TO_CREATE_ORDER = 20;
  public const OFFER_NOT_FOUND = 21;
  public const NO_PRODUCTS_IN_OFFER = 22;
  public const USER_GROUP_NOT_FOUND = 23;
  public const WRONG_PASSWORD = 24;
  public const INVALID_PAYMENT_METHOD = 25;
  public const INVALID_MONTH = 26;
  public const ORDER_ALREADY_EXISTS = 27;
  public const PREVIOUS_ORDER_NOT_FOUND = 28;
  public const INVALID_PAYMENT_INFO = 29;
  public const CONFIG_FILE_ERROR = 30;

  // *******************************************************************************************************************
  // *** Static methods.
  // *******************************************************************************************************************

  public static function is_error($result_code)
  {
    return $result_code > self::OK;
  }

  // *******************************************************************************************************************
}

// Class which holds information about the user's access level: his role, and the user group in which he has that role.
class Access_Token
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The ID of the user group for which data is being manipulated.
  protected $user_group_id = -1;

  // The role of the user using this class. Some data managers may restrict access to certain actions to certain roles.
  protected $role = Utility::ROLE_NONE;

  // A result code that can be used to report any errors encountered when the access token was created. Often, if an
  // error occurs, the server will simply redirect the client to an error page, and this result code will not be used.
  // However, if the client made an asynchronous request, the client must receive a result code, not HTML from an error
  // page. This result code can be used for that purpose.
  protected $result_code = Result::OK;

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_user_group_id, $new_role, $result_code = Result::OK)
  {
    if (is_numeric($new_user_group_id))
    {
      $new_user_group_id = intval($new_user_group_id);
      if ($new_user_group_id >= 0)
      {
        $this->user_group_id = $new_user_group_id;
      }
    }

    if (is_numeric($new_role))
    {
      $new_role = intval($new_role);
      if (Utility::is_valid_role($new_role))
      {
        $this->role = $new_role;
      }
    }

    if (is_numeric($result_code))
    {
      $this->result_code = intval($result_code);
    }
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************

  public function is_error()
  {
    return Result::is_error($this->result_code);
  }

  // *******************************************************************************************************************
  // If the access token has an error, send an appropriate HTTP response to the user.
  public function redirect_on_error()
  {
    if ($this->result_code === Result::ACCESS_DENIED)
    {
      User::send_access_denied();
    }

    if ($this->result_code === Result::LICENCE_EXPIRED)
    {
      if ($this->is_company_admin())
      {
        User::send_licence_expired_for_admins();
      }
      else
      {
        User::send_licence_expired_for_users();
      }
    }

    if ($this->result_code === Result::USER_GROUP_NOT_FOUND)
    {
      Utility::redirect_to('/subscription/index.php');
    }

    if ($this->is_error())
    {
      error_log('Unknown error when redirecting: ' . strval($this->result_code));
    }

    // All good. Continue.
  }

  // *******************************************************************************************************************
  // Return true if the current user has the company administrator role. Note that a Gibbs administrator might have been
  // granted this role as well.
  public function is_company_admin()
  {
    return $this->role === Utility::ROLE_COMPANY_ADMIN;
  }

  // *******************************************************************************************************************
  // *** Property servicing methods.
  // *******************************************************************************************************************
  // Return the ID of the user group for which this access token is valid. The value is always an integer.
  public function get_user_group_id()
  {
    return $this->user_group_id;
  }

  // *******************************************************************************************************************

  public function get_role()
  {
    return $this->role;
  }

  // *******************************************************************************************************************

  public function get_result_code()
  {
    return $this->result_code;
  }

  // *******************************************************************************************************************
}

class Utility
{
  // *******************************************************************************************************************
  // *** Subscription and product status constants.
  // *******************************************************************************************************************
  // Individual subscription status constants.
  //                              Start date                    End date
  // - Inactive                   N/A                           N/A
  // - Finished                   Before today's date           Exists; before today's date
  // - Ongoing                    Before today's date           Does not exist
  // - Cancelled                  Before today's date           Exists; after today's date
  // - Booked                     After today's date            Who cares?
  public const SUB_INACTIVE = 0;
  public const SUB_EXPIRED = 1;
  public const SUB_ONGOING = 2;
  public const SUB_CANCELLED = 3;
  public const SUB_BOOKED = 4;

  // Product status constants. These represent the overall status of the product based on subscriptions. When updating,
  // also update the st.prod constants in common.js. Statuses are generated in Product_Data_Manager.get_product_status.
  public const STATUS_NEW = 0;
  public const STATUS_VACATED = 1;
  public const STATUS_BOOKED = 2;
  public const STATUS_VACATED_BOOKED = 3;
  public const STATUS_RENTED = 4;
  public const STATUS_CANCELLED = 5;
  public const STATUS_CANCELLED_BOOKED = 6;

  // Product readiness status constants. These represent the status of the product, based on the attributes of the
  // product itself. When updating, also update the st.ready constants in common.js.
  // - The product is in use, or ready for use. The normal status.
  public const READY_STATUS_YES = 0;
  // - The product readiness status is unknown, and must be checked in order to determine the status. This status is
  //   used even if the check cannot be performed immediately, for instance when waiting for a customer to vacated the
  //   storage unit.
  public const READY_STATUS_CHECK = 1;
  // - The product has been checked, and determined not to be ready for use for whatever reason. Product notes can be
  //   entered in order to explain the reason, and the measures that must be taken in order to render the product ready
  //   for use.
  //
  //   This status is not in use. If the product is checKed, and deemed to be not ready, it will be disabled instead.
  // public const READY_STATUS_NO = 2;

  // Request status constants.
  //   0    Received          We have received the request, and need to do something about it.
  //   1    Need info         We need more information, and should contact the customer.
  //   2    Pending info      We are waiting for the customer to provide more information.
  //   3    Create offer      We need to create an offer for the customer.
  //   4    Pending approval  We are waiting for the customer to consider our offer.
  //   5    Offer approved    The customer gave us his money. Yay!
  //   6    Customer lost     The customer declined, or vanished.
  //   7    Not qualified     The customer was looking for something else.
  public const REQ_STATUS_RECEIVED = 0;
  public const REQ_STATUS_NEED_INFO = 1;
  public const REQ_STATUS_PENDING_INFO = 2;
  public const REQ_STATUS_CREATE_OFFER = 3;
  public const REQ_STATUS_PENDING_APPROVAL = 4;
  public const REQ_STATUS_OFFER_APPROVED = 5;
  public const REQ_STATUS_CUSTOMER_LOST = 6;
  public const REQ_STATUS_NOT_QUALIFIED = 7;

  // *******************************************************************************************************************
  // *** Nets API URLs.
  // *******************************************************************************************************************
  // The location of the Nets payment API when the application_role is "production". Used to create a payment, but also
  // to retrieve information about an existing one. For the latter, append "/" and the payment ID at the end of the URL,
  // and add any parameters you might want.
  public const NETS_PAYMENT_URL_PROD = 'https://api.dibspayment.eu/v1/payments';

  // The location of the Nets payment API when the application_role is not "production". Used to create a payment, but
  // also to retrieve information about an existing one. For the latter, append "/" and the payment ID at the end of the
  // URL, and add any parameters you might want.
  public const NETS_PAYMENT_URL_TEST = 'https://test.api.dibspayment.eu/v1/payments';

  // The location of the Nets payment API when the application_role is "production". Used to create a bulk charge, but
  // also to retrieve information about an existing one. For the latter, append "/" and the bulk ID at the end of the
  // URL, and add any parameters you might want.
  public const NETS_BULK_CHARGE_URL_PROD = 'https://api.dibspayment.eu/v1/subscriptions/charges';

  // The location of the Nets payment API when the application_role is not "production". Used to create a bulk charge,
  // but also to retrieve information about an existing one. For the latter, append "/" and the bulk ID at the end of
  // the URL, and add any parameters you might want.
  public const NETS_BULK_CHARGE_URL_TEST = 'https://test.api.dibspayment.eu/v1/subscriptions/charges';

  // The location of the Javascript file for payment with Nets, when the application_role is "production".
  public const NETS_JS_URL_PROD = 'https://checkout.dibspayment.eu/v1/checkout.js?v=1';

  // The location of the Javascript file for payment with Nets, when the application_role is not "production".
  public const NETS_JS_URL_TEST = 'https://test.checkout.dibspayment.eu/v1/checkout.js?v=1';

  // *******************************************************************************************************************
  // *** Constants.
  // *******************************************************************************************************************
  // Build number constant. Increment this when publishing to production, to avoid caching issues.
  public const BUILD_NO = 1;

  // User role constants.
  public const ROLE_NONE = -1;
  public const ROLE_USER = 0;
  public const ROLE_COMPANY_ADMIN = 1;
  public const ROLE_GIBBS_ADMIN = 2;

  // The role numbers stored in the database. Not to be confused with roles.
  public const ROLE_NUMBER_USER = 1;
  public const ROLE_NUMBER_LOCAL_ADMIN = 2;
  public const ROLE_NUMBER_COMPANY_ADMIN = 3;
  public const ROLE_NUMBER_GIBBS_ADMINISTRATOR = 6;

  // The role ID used for a Gibbs administrator dummy role. These roles are not in the database, but the Gibbs
  // administrator has access anyway.
  public const ROLE_ID_GIBBS_ADMINISTRATOR = -2;

  // verify_fields result constants.
  public const ALL_EMPTY = 0;
  public const SOME_PRESENT = 1;
  public const ALL_PRESENT = 2;

  // Supported language constants.
  public const NORWEGIAN = 'nb_NO';
  public const ENGLISH = 'en_GB';
  public const DEFAULT_LANGUAGE  = self::NORWEGIAN;
  // "A locale is a combination of language and territory (region or country) information."
  // Examples of locales are: nb_NO, nn_NO, sv_SE, da_DK, en_GB, en_US, de_DE
  // The language codes are found here: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
  // See also: https://stackoverflow.com/questions/3191664/list-of-all-locales-and-their-short-codes

  // The maximum number of digits that can be used when padding a storage unit number. The minimum is 1.
  public const MAX_PADDING_DIGIT_COUNT = 10;

  // The minimum length of a password.
  public const PASSWORD_MIN_LENGTH = 8;

  // Payment method constants. See documentation in order_data_manager.php. When changing, also modify common.js
  // PAYMENT_METHOD_ constants.
  public const PAYMENT_METHOD_UNKNOWN = 0;
  public const PAYMENT_METHOD_NETS = 1;
  public const PAYMENT_METHOD_INVOICE = 2;
  public const PAYMENT_METHOD_NETS_THEN_INVOICE = 3;

  // Payment status constants. See documentation in order_data_manager.php. Note that the FIRST and LAST statuses exist
  // for validation purposes. They are not used. When updating these, also update PAYMENT_STATUS_ constants and
  // PAYMENT_STATUS_COLOURS in common.js.
  public const PAYMENT_STATUS_FIRST = 0;
  public const PAYMENT_STATUS_UNKNOWN = 0;
  public const PAYMENT_STATUS_NOT_PAID = 1;
  public const PAYMENT_STATUS_PAID = 2;
  public const PAYMENT_STATUS_PARTIALLY_PAID = 3;
  public const PAYMENT_STATUS_NOT_PAID_OVERDUE = 4;
  public const PAYMENT_STATUS_NOT_PAID_REMINDER_SENT = 5;
  public const PAYMENT_STATUS_NOT_PAID_WARNING_SENT = 6;
  public const PAYMENT_STATUS_NOT_PAID_SENT_TO_COLLECTION = 7;
  public const PAYMENT_STATUS_PAID_TO_COLLECTION = 8;
  public const PAYMENT_STATUS_LOST = 9;
  public const PAYMENT_STATUS_CREDITED = 10;
  public const PAYMENT_STATUS_FAILED_AT_PROVIDER = 11;
  public const PAYMENT_STATUS_ERROR = 12;
  public const PAYMENT_STATUS_REFUNDED = 13;
  public const PAYMENT_STATUS_DISPUTED = 14;
  public const PAYMENT_STATUS_NOT_PAID_NO_INVOICE_SENT = 15;
  public const PAYMENT_STATUS_NOT_PAID_INVOICE_SENT = 16;
  public const PAYMENT_STATUS_NOT_PAID_CHARGE_REQUESTED = 17;
  public const PAYMENT_STATUS_DELETED = 18;
  public const PAYMENT_STATUS_LAST = 18;

  // Additional product type constants.
  public const ADDITIONAL_PRODUCT_INSURANCE = 1;

  // The name of the user meta field that stores the ID of the role that was last used by the user.
  public const ACTIVE_ROLE_ID = 'active_role_id';

  // Entity type constants.
  public const ENTITY_TYPE_INDIVIDUAL = 0;
  public const ENTITY_TYPE_COMPANY = 1;

  // Message type constants.
  public const MESSAGE_TYPE_SMS = 0;
  public const MESSAGE_TYPE_EMAIL = 1;

  // Trigger type constants.
  //   REGISTERED                 When the user has registered, and received a role in a particular user group (even if
  //                              he didn't buy a subscription).
  //   FORGOT_PASSWORD            When the user asks to change his password ("forgot password").
  //   BOUGHT_SUB                 When a subscription is created (and payment succeeded, if paying through Nets) for an
  //                              existing user.
  //   REGISTERED_AND_BOUGHT_SUB  When a subscription is created (and payment succeeded, if paying through Nets) for a
  //                              new user.
  //   SUB_VALIDATION_FAILURE     When a subscription validation failed (a few weeks before next month's charge), for
  //                              instance because the buyer's credit card has expired.
  //   BEFORE_EXPIRES             Before a Nets subscription expires (also a few weeks before next month's charge).
  //   MONTHLY_PAYMENT_SUCCESS    When a Nets subscription was charged successfully (the storage company may not want to
  //                              bother the customer, but they should be able to).
  //   MONTHLY_PAYMENT_FAILURE    When a Nets subscription was not charged successfully.
  //   INVOICE_FIRST_REMINDER     When an invoice is overdue (reminder).
  //   INVOICE_SECOND_REMINDER    When an invoice is overdue and the reminder didn't work (collection agency referral
  //                              warning).
  //   CANCELLED_SUB              When a user cancels a subscription.
  //   TERMS_CHANGED              When the terms and conditions change (not implemented yet).
  //   PRICE_CHANGED              When the price of a subscription changes (not implemented yet).
  //   NEWSLETTER                 When the storage company wants to send a newsletter or special offer (not implemented
  //                              yet).
  //   MAINTENANCE                When the storage company wants to send a notification about maintenance or closures at
  //                              a particular location (not implemented yet).
  //   ACCESS_CODE_MODIFIED       When an access code is modified (do we need this?).
  //   DELETED_ACCOUNT            When a user deletes his account (is that even possible?).
  public const TRIGGER_TYPE_REGISTERED = 0;
  public const TRIGGER_TYPE_FORGOT_PASSWORD = 1;
  public const TRIGGER_TYPE_BOUGHT_SUB = 2;
  public const TRIGGER_TYPE_REGISTERED_AND_BOUGHT_SUB = 3;
  public const TRIGGER_TYPE_SUB_VALIDATION_FAILURE = 4;
  public const TRIGGER_TYPE_BEFORE_EXPIRES = 5;
  public const TRIGGER_TYPE_MONTHLY_PAYMENT_SUCCESS = 6;
  public const TRIGGER_TYPE_MONTHLY_PAYMENT_FAILURE = 7;
  public const TRIGGER_TYPE_INVOICE_FIRST_REMINDER = 8;
  public const TRIGGER_TYPE_INVOICE_SECOND_REMINDER = 9;
  public const TRIGGER_TYPE_CANCELLED_SUB = 10;
  public const TRIGGER_TYPE_TERMS_CHANGED = 11;
  public const TRIGGER_TYPE_PRICE_CHANGED = 12;
  public const TRIGGER_TYPE_NEWSLETTER = 13;
  public const TRIGGER_TYPE_MAINTENANCE = 14;
  public const TRIGGER_TYPE_ACCESS_CODE_MODIFIED = 15;
  public const TRIGGER_TYPE_DELETED_ACCOUNT = 16;

  // Cancel type constants. Used when cancelling a subscription.
  public const CANCEL_TYPE_STANDARD = 0;
  public const CANCEL_TYPE_IMMEDIATE = 1;
  public const CANCEL_TYPE_CUSTOM = 2;

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return true if the given candidate is an array, and that array has one or more elements in it. Otherwise, return
  // false - for instance, if it is null, false, an object, or an empty array.
  public static function non_empty_array($candidate)
  {
    return is_array($candidate) && (count($candidate) > 0);
  }

  // *******************************************************************************************************************
  // Return true if the given candidate is an array, and that array has precisely one element in it. Otherwise, return
  // false - for instance, if it is null, false, an object, an empty array, or an array with three items.
  public static function array_with_one($candidate)
  {
    return is_array($candidate) && (count($candidate) === 1);
  }

  // *******************************************************************************************************************
  // Read the list of field names from the POST request, and return an array where those field names are the keys, and
  // posted data is the values, as strings.
  public static function read_posted_strings($field_names)
  {
    $result = array();
    foreach ($field_names as $field_name)
    {
      $result[$field_name] = self::read_posted_string($field_name);
    }
    return $result;
  }

  // *******************************************************************************************************************

  public static function read_posted_string($field_name, $default_value = '')
  {
    if (isset($_POST[$field_name]))
    {
      return sanitize_text_field($_POST[$field_name]);
    }
    return $default_value;
  }

  // *******************************************************************************************************************

  public static function read_passed_string($field_name, $default_value = '')
  {
    if (isset($_REQUEST[$field_name]))
    {
      return sanitize_text_field($_REQUEST[$field_name]);
    }
    return $default_value;
  }

  // *******************************************************************************************************************

  public static function read_posted_integer($field_name, $default_value = -1)
  {
    if (isset($_POST[$field_name]))
    {
      return intval(sanitize_text_field($_POST[$field_name]));
    }
    return $default_value;
  }

  // *******************************************************************************************************************

  public static function read_passed_integer($field_name, $default_value = -1)
  {
    if (isset($_REQUEST[$field_name]))
    {
      return intval(sanitize_text_field($_REQUEST[$field_name]));
    }
    return $default_value;
  }

  // *******************************************************************************************************************
  // Return the number of days in the month represented by the given date.
  public static function get_days_in_month($date)
  {
    return cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));
  }

  // *******************************************************************************************************************
  // Return a string, in the format "yyyy-mm-dd", that represents the last day of the month given in $month, which
  // should be a string in the format "yyyy-mm". However, the method will return proper values even if the $month string
  // does not have leading zeroes.
  public static function get_last_date($month)
  {
    $last_day = date('t', strtotime($month . '-01'));
    // Parse the date string to ensure there are no illegal values, then return a correctly formatted date, with leading
    // zeroes if required.
    return date('Y-m-d', strtotime($month . '-' . strval($last_day)));
  }

  // *******************************************************************************************************************
  // Return a string, with the format "yyyy-mm", that represents the month before the given $month, which should also be
  // a string with the format "yyyy-mm". Return null if the $month was not valid. $month is optional. If not present,
  // the current month is used.
  public static function get_previous_month($month = null)
  {
    try
    {
      if (empty($month))
      {
        $date = new DateTime();
      }
      else
      {
        $date = DateTime::createFromFormat('Y-m', $month);
      }
      $date->modify('-1 month');
      return $date->format('Y-m');
    }
    catch (Exception $e)
    {
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return a string, with the format "yyyy-mm", that represents the current month.
  public static function get_this_month()
  {
    return date('Y-m');
  }

  // *******************************************************************************************************************
  // Return a string, with the format "yyyy-mm", that represents the month after the given $month, which should also be
  // a string with the format "yyyy-mm". Return null if the $month was not valid. $month is optional. If not present,
  // the current month is used.
  public static function get_next_month($month = null)
  {
    try
    {
      if (empty($month))
      {
        $date = new DateTime();
      }
      else
      {
        $date = DateTime::createFromFormat('Y-m', $month);
      }
      $date->modify('+1 month');
      return $date->format('Y-m');
    }
    catch (Exception $e)
    {
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return true if the given $month string holds a valid month in the format "yyyy-mm".
  public static function is_valid_month($month)
  {
    // If the string matches the "yyyy-mm" pattern, create a datetime object and make sure the value is valid.
    if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month))
    {
      try
      {
        $date = new DateTime($month . '-01');
        return $date->format('Y-m') === $month;
      }
      catch (Exception $e)
      {
      }
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the given date string holds a valid date in the format "yyyy-mm-dd".
  public static function is_valid_date($date_string)
  {
    $date_time = DateTime::createFromFormat('Y-m-d', $date_string);
    return ($date_time && ($date_time->format('Y-m-d') === $date_string));
  }

  // *******************************************************************************************************************
  // Return a string, with the format "yyyy-mm-dd", that represents the day before the given $day, which should also be
  // a string with the format "yyyy-mm-dd". Return null if the $day was not valid. $day is optional. If not present,
  // the current day is used.
  public static function get_previous_day($day = null)
  {
    try
    {
      if (empty($day))
      {
        $date = new DateTime();
      }
      else
      {
        $date = DateTime::createFromFormat('Y-m-d', $day);
      }
      $date->modify('-1 day');
      return $date->format('Y-m-d');
    }
    catch (Exception $e)
    {
    }
    return null;
  }

  // *******************************************************************************************************************
  // Return a string, with the format "yyyy-mm-dd", that represents the current date.
  public static function get_today()
  {
    return date('Y-m-d');
  }

  // *******************************************************************************************************************
  // Convert the given DateTime object to a string. The returned string will have the format "yyyy-mm-dd". If the given
  // date is not a valid DateTime object, return the given default value.
  public static function date_to_string($date, $default_value = '')
  {
    if (($date !== null) && ($date instanceof DateTime))
    {
      return $date->format('Y-m-d');
    }
    return $default_value;
  }

  // *******************************************************************************************************************
  // Convert the given $time_string to a DateTime object. The string is expected to have the format "yyyy-mm-dd". Return
  // null if the $time_string was invalid.
  public static function string_to_date($time_string)
  {
    $result = DateTime::createFromFormat('Y-m-d', $time_string);
    if ($result === false)
    {
      return null;
    }
    $result->setTime(0, 0, 0);
    return $result;
  }

  // *******************************************************************************************************************

  public static function read_posted_date($field_name)
  {
    if (isset($_POST[$field_name]))
    {
      return self::string_to_date(sanitize_text_field($_POST[$field_name]));
    }
    return null;
  }

  // *******************************************************************************************************************

  public static function read_passed_date($field_name)
  {
    if (isset($_REQUEST[$field_name]))
    {
      return self::string_to_date(sanitize_text_field($_REQUEST[$field_name]));
    }
    return null;
  }

  // *******************************************************************************************************************

  public static function read_posted_boolean($field_name)
  {
    if (isset($_POST[$field_name]))
    {
      $field_value = sanitize_text_field($_POST[$field_name]);
      if (($field_value === '1') || ($field_value === 'true') || ($field_value === 'on'))
      {
        return true;
      }
      if (($field_value === '0') || ($field_value === 'false'))
      {
        return false;
      }
    }
    return null;
  }

  // *******************************************************************************************************************

  public static function read_passed_boolean($field_name)
  {
    if (isset($_REQUEST[$field_name]))
    {
      $field_value = sanitize_text_field($_REQUEST[$field_name]);
      if (($field_value === '1') || ($field_value === 'true') || ($field_value === 'on'))
      {
        return true;
      }
      if (($field_value === '0') || ($field_value === 'false'))
      {
        return false;
      }
    }
    return null;
  }

  // *******************************************************************************************************************
  // Check the given array of strings. Return one of the values from the constants section above, to say whether the
  // fields were all empty, whether some but not all were present, or whether all were present.
  public static function verify_fields($fields)
  {
    $allPresent = true;
    $allEmpty = true;

    foreach ($fields as $field)
    {
      if (is_string($field) && !empty($field))
      {
        // At least one non-empty string found.
        $allEmpty = false;
      }
      else
      {
        // At least one empty or non-string item found.
        $allPresent = false;
      }
    }

    if ($allPresent)
    {
      return self::ALL_PRESENT;
    }
    if ($allEmpty)
    {
      return self::ALL_EMPTY;
    }
    return self::SOME_PRESENT;
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were posted to the current page.
  public static function strings_passed($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!isset($_REQUEST[$field_name]))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were posted to the current page.
  public static function strings_posted($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!isset($_POST[$field_name]))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************

  public static function string_passed($field_name)
  {
    return isset($_REQUEST[$field_name]);
  }

  // *******************************************************************************************************************

  public static function string_posted($field_name)
  {
    return isset($_POST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were passed to the current page as integers. That is, they were passed as
  // strings that can be successfully converted to an integer.
  public static function integers_passed($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!self::integer_passed($field_name))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return true if all the given fields were posted to the current page as integers. That is, they were posted as
  // strings that can be successfully converted to an integer.
  public static function integers_posted($field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!self::integer_posted($field_name))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // Return true if the given field was passed as part of the request to the current page - regardless of whether that
  // request was a GET, POST, or something else - and is a string that can be successfully converted to a number.
  public static function integer_passed($field_name)
  {
    return isset($_REQUEST[$field_name]) && is_numeric($_REQUEST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if the given field was posted to the current page, and is a string that can be successfully converted
  // to a number.
  public static function integer_posted($field_name)
  {
    return isset($_POST[$field_name]) && is_numeric($_POST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if the field with the given name was passed to the current page, and is a string with a valid date in
  // the yyyy-mm-dd format.
  public static function date_passed($field_name)
  {
    if (isset($_REQUEST[$field_name]))
    {
      return self::is_valid_date(sanitize_text_field($_REQUEST[$field_name]));
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the field with the given name was posted to the current page, and is a string with a valid date in
  // the yyyy-mm-dd format.
  public static function date_posted($field_name)
  {
    if (isset($_POST[$field_name]))
    {
      return self::is_valid_date(sanitize_text_field($_POST[$field_name]));
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the field with the given name was passed to the current page, and is a string with a valid month in
  // the yyyy-mm format.
  public static function month_passed($field_name)
  {
    if (isset($_REQUEST[$field_name]))
    {
      return self::is_valid_month(sanitize_text_field($_REQUEST[$field_name]));
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the field with the given name was posted to the current page, and is a string with a valid month in
  // the yyyy-mm format.
  public static function month_posted($field_name)
  {
    if (isset($_POST[$field_name]))
    {
      return self::is_valid_month(sanitize_text_field($_POST[$field_name]));
    }
    return false;
  }

  // *******************************************************************************************************************
  // Return true if the given field was passed as part of the request to the current page - regardless of whether that
  // request was a GET, POST, or something else - and is a string with a valid CSS colour.
  public static function colour_passed($field_name)
  {
    return isset($_REQUEST[$field_name]) && self::is_valid_colour($_REQUEST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if the given field was posted to the current page, and is a string with a valid CSS colour.
  public static function colour_posted($field_name)
  {
    return isset($_POST[$field_name]) && self::is_valid_colour($_POST[$field_name]);
  }

  // *******************************************************************************************************************
  // Return true if the fields with the given field names appear in the given array, and are non-empty strings.
  public static function non_empty_strings($array, $field_names)
  {
    foreach ($field_names as $field_name)
    {
      if (!is_string($array[$field_name]) || empty($array[$field_name]))
      {
        return false;
      }
    }
    return true;
  }

  // *******************************************************************************************************************
  // If the given error message is a non-empty string, enclose it in a div tag with the class "error-message".
  public static function enclose_in_error_div($error_message)
  {
    if (is_string($error_message) && !empty($error_message))
    {
      return '<div class="error-message">' . $error_message . '</div>';
    }
    return '';
  }

  // *******************************************************************************************************************

  public static function is_valid_email($email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
  }

  // *******************************************************************************************************************
  // Return true if the given colour is a string that contains a valid CSS colour value.
  public static function is_valid_colour($colour)
  {
    // The first test checks for hex colour code (e.g., #FFF, #FFFFFF). The second test checks for RGB or RGBA
    // (e.g., rgb(255,255,255), rgba(255,255,255,1)). The third test checks for HSL or HSLA (e.g., hsl(360,100%,50%),
    // hsla(360,100%,50%,1)).
    return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $colour) ||
      preg_match('/^rgb(a?)\((\s*\d+\s*,\s*){2}\d+\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/', $colour) ||
      preg_match('/^hsl(a?)\(\s*\d+\s*,\s*\d+%\s*,\s*\d+%\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/', $colour);
  }

  // *******************************************************************************************************************
  // Return true if the given $value holds a valid payment method.
  public static function is_valid_payment_method($value)
  {
    if (!is_numeric($value))
    {
      return false;
    }
    $value = intval($value);
    return ($value === self::PAYMENT_METHOD_NETS) || ($value === self::PAYMENT_METHOD_INVOICE) ||
      ($value === self::PAYMENT_METHOD_NETS_THEN_INVOICE);
  }

  // *******************************************************************************************************************
  // Return true if the given $value holds a valid payment status.
  public static function is_valid_payment_status($value)
  {
    if (!is_numeric($value))
    {
      return false;
    }
    $value = intval($value);
    return ($value >= self::PAYMENT_STATUS_FIRST) && ($value <= self::PAYMENT_STATUS_LAST);
  }

  // *******************************************************************************************************************
  // Return true if the given $value holds a valid product readiness status.
  public static function is_valid_ready_status($value)
  {
    if (!is_numeric($value))
    {
      return false;
    }
    $value = intval($value);
    return ($value === self::READY_STATUS_YES) || ($value === self::READY_STATUS_CHECK);
  }

  // *******************************************************************************************************************

  public static function is_valid_role($role)
  {
    return ($role === self::ROLE_NONE) || ($role === self::ROLE_USER) || ($role === self::ROLE_COMPANY_ADMIN) ||
      ($role === self::ROLE_GIBBS_ADMIN);
  }

  // *******************************************************************************************************************
  // Return the role that corresponds to the given $role_number. The $role_number is what's stored in the database. The
  // role is what the application code uses. The mapping is:
  //
  //   Role number                                Role
  //
  //   ROLE_NUMBER_USER (1)                       ROLE_USER (0)
  //   ROLE_NUMBER_LOCAL_ADMIN (2)                ROLE_COMPANY_ADMIN (1)
  //   ROLE_NUMBER_COMPANY_ADMIN (3)              ROLE_COMPANY_ADMIN (1)
  //   ROLE_NUMBER_GIBBS_ADMINISTRATOR (6)        ROLE_GIBBS_ADMIN (2)
  //   Any other value                            ROLE_NONE (-1)
  public static function role_number_to_role($role_number)
  {
    // Error check.
    if (!is_numeric($role_number))
    {
      return self::ROLE_NONE;
    }
    $role_number = intval($role_number);

    // Find the role.
    if ($role_number === self::ROLE_NUMBER_USER)
    {
      return self::ROLE_USER;
    }
    if (($role_number === self::ROLE_NUMBER_LOCAL_ADMIN) || ($role_number === self::ROLE_NUMBER_COMPANY_ADMIN))
    {
      return self::ROLE_COMPANY_ADMIN;
    }
    if ($role_number === self::ROLE_NUMBER_GIBBS_ADMINISTRATOR)
    {
      return self::ROLE_GIBBS_ADMIN;
    }
    return self::ROLE_NONE;
  }

  // *******************************************************************************************************************
  // Return the page title. This varies with the current language.
  public static function get_page_title()
  {
    $language = self::get_current_language();
    if ($language === self::NORWEGIAN)
    {
      return 'Gibbs minilager';
    }
    return 'Gibbs self storage';
  }

  // *******************************************************************************************************************
  // Return true if the given $language is valid and supported by the application.
  public static function is_valid_language($language)
  {
    return isset($language) && is_string($language) &&
      (($language === self::NORWEGIAN) || ($language === self::ENGLISH));
  }

  // *******************************************************************************************************************
  // Read the current language from the session. If not set, use the default language and update the session.
  public static function get_current_language()
  {
    // Return the language stored on the session, if any.
    if (isset($_SESSION['language']))
    {
      $language = $_SESSION['language'];
    }
    else
    {
      $language = null;
    }
    if (self::is_valid_language($language))
    {
      return $language;
    }

    // No language was set. Set the default language.
    self::set_current_language(self::DEFAULT_LANGUAGE);
    return self::DEFAULT_LANGUAGE;
  }

  // *******************************************************************************************************************
  // Store the given language on the session.
  public static function set_current_language($language)
  {
    if (self::is_valid_language($language))
    {
      $_SESSION['language'] = $language;
    }
  }

  // *******************************************************************************************************************
  // Return the terms-and-conditions URL for the given language. $language is optional. If not present, the currently
  // selected language is used. Return an empty string if the URL was not found.
  public static function get_gibbs_terms_url($language = null)
  {
    if (!isset($language))
    {
      $language = self::get_current_language();
    }
    $config = Config::read_config_file();
    $url = Config::get_gibbs_terms_url($language, $config);
    if (empty($url))
    {
      return '';
    }
    return self::get_domain() . $url;
  }

  // *******************************************************************************************************************
  // Return the complete URL of the file that handles Nets events.
  public static function get_nets_webhook_url()
  {
    return 'https://' . $_SERVER['HTTP_HOST'] . '/subscription/webhooks/handle_nets_event.php';
  }

  // *******************************************************************************************************************
  // Return the complete URL of the login page, with the colour profile of the current user group.
  public static function get_login_url()
  {
      // *** // Hopefully temporary:
      return self::get_domain() . '/logg-inn-unbranded-minilager/?group_id=' . User::get_user_group_id();

    // return self::get_domain() . '/subscription/html/log_in_to_dashboard.php?user_group_id=' . User::get_user_group_id();
  }

  // *******************************************************************************************************************
  // Return the complete URL of the book subscription page, with the colour profile of the current user group.
  public static function get_booking_url()
  {
    return self::get_domain() . '/subscription/html/select_booking_type.php?user_group_id=' . User::get_user_group_id();
  }

  // *******************************************************************************************************************
  // Return true if the given integer is an even number.
  public static function is_even($number)
  {
    return $number % 2 == 0;
  }

  // *******************************************************************************************************************
  // Ensure the given value is within the range of min to max, inclusive. Return an integer in the legal range.
  public static function clamp_number($value, $min, $max)
  {
    return max($min, min($max, $value));
  }

  // *******************************************************************************************************************
  // Convert the given integer to a string, and pad it with leading zeroes to the given minimum length. Return the
  // string.
  public static function pad_number($number, $length)
  {
    // Calculate the number of zeroes to add, and add the padding.
    $result = strval($number);
    $paddingCount = max(0, $length - strlen($result));
    return str_repeat('0', $paddingCount) . $result;
  }

  // *******************************************************************************************************************
  // Return the price when the given monthly base price is modified with the given modifier. The modifier is a
  // percentage, with a negative number signifying a discount.
    // *** // Add setting to specify how to round the number, if at all. In GBP, for instance, rounding to the nearest integer may not be appropriate.
  public static function get_modified_price($base_price, $modifier)
  {
    return round($base_price * (1.0 + (0.01 * $modifier)));
  }

  // *******************************************************************************************************************
  // In the given $input string, encode all types of line breaks using the ¤ character.
  public static function encode_line_breaks($input)
  {
    return preg_replace('/\r\n?|\n/', '¤', $input);
  }

  // *******************************************************************************************************************
  // Remove all types of line breaks from the given $input string.
  public static function remove_line_breaks($input)
  {
    return preg_replace('/\r\n?|\n/', '', $input);
  }

  // *******************************************************************************************************************
  // Redirect to the given URL with HTTP status code 302, and stop executing the current script.
  public static function redirect_to($url)
  {
    header('HTTP/1.1 302 Found');
    header('Location: ' . $url);
    exit;
  }

  // *******************************************************************************************************************
  // Return the current HTTP protocol and domain, without the trailing backslash. For instance: "https://www.gibbs.no".
  public static function get_domain()
  {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    {
      return 'https://' . $_SERVER['HTTP_HOST'];
    }
    return 'http://' . $_SERVER['HTTP_HOST'];
  }

  // *******************************************************************************************************************
  // Return a string of comma separated value triplets. Each triplet is enclosed in parentheses. The first item is the
  // $id. The second item is the key from the given $values, while the third is the corresponding value.
  public static function get_key_value_data_string($id, $values)
  {
    $result = array();
    foreach ($values as $key => $value)
    {
      $result[] = "({$id}, \"{$key}\", \"{$value}\")";
    }
    return implode(',', $result);
  }

  // *******************************************************************************************************************
  // Return a string of comma separated value pairs. Each pair is enclosed in parentheses. The first item in the pair is
  // the $fixed_value. This is assumed to be an integer. The second item is drawn from the given table of
  // $variable_values. Each of these will be converted to an integer.
  public static function get_value_data_string($fixed_value, $variable_values)
  {
    $result = array();
    foreach ($variable_values as $item)
    {
      if (is_numeric($item))
      {
        $item = intval($item);
        $result[] = "({$fixed_value}, {$item})";
      }
    }
    return implode(',', $result);
  }

  // *******************************************************************************************************************
  // Return the given array of numbers as a string containing a Javascript declaration. The array may be null, in which
  // case the method will return "null".
  public static function get_js_array_of_values($values)
  {
    if (isset($values))
    {
      // Remove the array keys to leave just an array of numbers, then convert to a comma separated string.
      return '[' . implode(',', array_values($values)) . ']';
    }
    return 'null';
  }
  
  // *******************************************************************************************************************
  // Verify a set of filter values that are passed to the server. The filter values are assumed to be passed in a
  // parameter with the given $filter_name. The parameter should hold a string with comma separated integers. If the
  // filter parameter was not passed, or contained no valid values, return the given $default_value. $default_value is
  // optional. If not provided, return the string 'null'. If the parameter contained valid values, return a string with
  // a Javascript array declaration that holds those values. This method does not verify the integers in the filter.
  public static function verify_filter($filter_name, $default_value = 'null')
  {
    // If the filter parameter was not passed, return the default value.
    if (!self::string_passed($filter_name))
    {
      return $default_value;
    }

    // Read the filter parameter. If it contained "null", return null to denote an empty filter.
    $source = self::read_passed_string($filter_name);
    if ($source === 'null')
    {
      return 'null';
    }

    // The source was a list of values. Verify each value.
    $source = explode(',', $source);
    $result = array();
    foreach ($source as $id)
    {
      if (is_numeric($id))
      {
        $result[] = intval($id);
      }
    }

    // If there were no valid IDs, return the default value.
    if (count($result) <= 0)
    {
      return $default_value;
    }

    // Return the list of valid IDs as a Javascript table declaration.
    return '[' . implode(', ', $result) . ']';
  }

  // *******************************************************************************************************************
  // Read the sorting order from parameters passed to the page, and return a string that declares Javascript variables
  // to specify the initial sorting order.
  public static function write_initial_sorting($default_ui_column = -1, $default_direction = -1,
    $ui_column_name = 'sort_on_ui_column', $direction_name = 'sort_direction',
    $ui_column_var = 'initialUiColumn', $direction_var = 'initialDirection')
  {
    // Read parameters.
    $ui_column = self::read_passed_integer($ui_column_name, $default_ui_column);
    $direction = self::read_passed_integer($direction_name, $default_direction);

    // Generate variable declarations. Note that the line breaks have to be declared with double quotes. PHP is fun!
    return "// Initial sorting.\nvar " . $ui_column_var . " = " . $ui_column . ";\nvar " . $direction_var . " = " .
      $direction . ";\n";
  }

  // *******************************************************************************************************************
  // Verify a set of month filter values that are passed to the server. The filter values are assumed to be passed in a
  // parameter with the given $filter_name. The parameter should hold a string with comma separated months, where each
  // month is in the format "yyyy-mm". If the filter parameter was not passed, or contained no valid values, return the
  // string 'null'. If the parameter contained valid values, return a string with a Javascript array declaration that
  // holds those values. This method verifies that each value is a valid (existing) month, but does not verify that the
  // months in the filter are valid for the data they are filtering (for instance, there is no point in including
  // "2024-05" in the filter, if there is no data for that month anyway).
  public static function verify_month_filter($filter_name)
  {
    // If the filter parameter was not passed, return nothing.
    if (!self::string_passed($filter_name))
    {
      return 'null';
    }

    // Read the filter parameter, and verify each value.
    $source = explode(',', self::read_passed_string($filter_name));
    $result = array();
    foreach ($source as $month)
    {
      if (self::is_valid_month($month))
      {
        $result[] = $month;
      }
    }

    // If there were no valid months, return nothing.
    if (count($result) <= 0)
    {
      return 'null';
    }
    // Return the list of valid months as a Javascript table declaration (or actually JSON, but that works).
    return json_encode($result);
  }

  // *******************************************************************************************************************
  // In the given source string, convert special characters to HTML entities.
  public static function add_html_entities($source)
  {
    return htmlspecialchars($source, ENT_QUOTES | ENT_HTML401, 'UTF-8');
  }

  // *******************************************************************************************************************
  // In the given source string, convert HTML entities to special characters.
  public static function remove_html_entities($source)
  {
    return htmlspecialchars_decode($source, ENT_QUOTES | ENT_HTML401);
  }

  // *******************************************************************************************************************
  // Remove the final character from the given $source string, and return the new string. The string is presumed to be
  // non-empty, and the final character is presumed to be a comma. However, these presumptions are not verified, due to
  // performance considerations.
  public static function remove_final_comma($source)
  {
    return substr($source, 0, -1);
  }

  // *******************************************************************************************************************
  // Return a string of the given $length that contains a random combination of lowercase letters and numbers.
  public static function get_random_string($length = 8)
  {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $characters_length = strlen($characters);
    $result = '';
    
    for ($i = 0; $i < $length; $i++)
    {
      $result .= $characters[rand(0, $characters_length - 1)];
    }
    return $result;
  }

  // *******************************************************************************************************************
  // Return a string that states the current date and time, including milliseconds.
  public static function get_timestamp()
  {
    // Get the current date and time with milliseconds.
    $currentDateTime = microtime(true);

    // Extract the time with milliseconds.
    $milliseconds = sprintf('%06d', ($currentDateTime - floor($currentDateTime)) * 1000000);
    $time = date('H:i:s', $currentDateTime) . '.' . substr($milliseconds, 0, 3);

    // Return the date, in "yyyy-mm-dd" format, and time.
    return date('Y-m-d', $currentDateTime) . ' ' . $time;
  }

  // *******************************************************************************************************************
  // Return HTML code to add a spinner to the page. If $visible is true, the spinner will be visible and spinning. If
  // so, make sure to add code to hide it once the page has loaded.
  public static function get_spinner($visible = true)
  {
    return
      '<div id="spinner" class="spinner"' . ($visible ? '>' : ' style="display: none;">') .
<<<EOD
      <div class="spinner-box">
        <div class="spinner-circle" role="status" aria-label="Please wait!">
          &nbsp;
        </div>
      </div>
    </div>
    <script type="text/javascript">

var spinner = document.getElementById('spinner');

    </script>
EOD;
  }

  // *******************************************************************************************************************
}
?>