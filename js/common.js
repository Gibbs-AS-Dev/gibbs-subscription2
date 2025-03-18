// *************************************************************************************************
// *** Gibbs © 2023-2024
// *************************************************************************************************

// *************************************************************************************************
// *** Result code constants.
// *************************************************************************************************
// Result codes that are processed differently from the standard handling. This is just a subset of
// the result codes that are used on the server.
var result =
  {
    OK: -1,
    PRODUCT_ALREADY_BOOKED: 3,
    MISSING_NUMBER_PLACEHOLDER_IN_PRODUCT_NAME: 4,
    PASSWORD_CHANGED: 12,
    REQUEST_FAILED: 18,
    PAYMENT_FAILED: 19,
    INVALID_PAYMENT_METHOD: 25
  };

// *************************************************************************************************
// *** Column constants.
// *************************************************************************************************
// Object for column constants. This is used to avoid polluting the namespace, and prevent duplicate
// column names with different numerical values.
var c = {};

// Menu column constants:
//   url : string               The URL of the link destination. You can also use the special
//                              strings "[separator]" and "[drawer]". The remaining elements depend
//                              on the URL field:
//
// [separator]
//   (no more elements)
//
// [drawer]
//   open : boolean             Flag that says whether this drawer is open. Only present for
//                              drawers.
//   text : string              The correctly translated text of the menu item.
//   subitems : array           List of subitems, each of which uses the same column constants.
//
// [all others]
//   icon : string              For instance "fa-close". Not present for separators.
//   text : string              The correctly translated text of the menu item.
//   enabled : boolean          Flag that says whether the item can be clicked. Present but
//                              presently not relevant for drawers.
//   selected : boolean         Flag that says whether the item is currently selected. Present but
//                              presently not relevant for drawers.
c.mnu =
  {
    URL: 0,

    OPEN: 1,
    TEXT: 2,
    SUBITEMS: 3,

    ICON: 1,
    // TEXT: 2,
    ENABLED: 3,
    SELECTED: 4
  };

// locations table column indexes. NAME, STREET, POSTCODE, TOWN, COUNTRY, OPENING_HOURS and
// SERVICES are all strings.
c.loc =
  {
    ID: 0,
    NAME: 1,
    STREET: 2,
    POSTCODE: 3,
    TOWN: 4,
    COUNTRY: 5,
    OPENING_HOURS: 6,
    SERVICES: 7
  };

// products table column indexes. SUBSCRIPTIONS is a table with its own column indexes; see the
// c.prs constants below. For STATUS, use the st.prod constants. The END_DATE is only present if the
// status is CANCELLED or CANCELLED_BOOKED; otherwise it will be an empty string. The RESERVED_DATE
// is only present if the status is BOOKED, VACATED_BOOKED or CANCELLED_BOOKED; otherwise it will be
// an empty string. ENABLED is a boolean. If false, the product cannot be booked. If it is
// currently booked, that is all right, but the restriction applies as soon as it is free.
// READY_STATUS says whether the product itself is ready to be rented, or must be checked to
// determine the status. Use the st.ready constants.
c.prd =
  {
    ID: 0,
    NAME: 1,
    LOCATION_ID: 2,
    PRODUCT_TYPE_ID: 3,
    SUBSCRIPTIONS: 4,
    STATUS: 5,
    END_DATE: 6,
    RESERVED_DATE: 7,
    MODIFIED_DATE: 8,
    ENABLED: 9,
    ACCESS_CODE: 10,
    ACCESS_LINK: 11,
    READY_STATUS: 12,
    NOTES: 13,
  };

// subscriptions table column indexes for the subscriptions found in the products table. Note that
// these are different from the subscriptions tables used elsewhere. For c.prs.STATUS, use the
// st.sub constants.
c.prs =
  {
    ID: 0,
    USER_ID: 1,
    STATUS: 2,
    START_DATE: 3,
    END_DATE: 4
  };

// subscriptions table column indexes for a single user. The PAYMENT_HISTORY may be loaded
// asyhcnronously when required. When it has not yet been loaded, the value will be null. Pages
// using this table format may not need to load products, as the names will be included in the
// table. They do need to load the product types and locations, however. Note that the subscriptions
// included as part of the products table use different column indexes, as do the list of
// subscriptions for all users.
c.sub =
  {
    ID: 0, // The subscription ID.
    PRODUCT_NAME: 1, // The name of the product.
    LOCATION_ID: 2,
    PRODUCT_TYPE_ID: 3,
    STATUS: 4, // The subscription status. Use the st.sub constants.
    START_DATE: 5,
    END_DATE: 6,
    INSURANCE_NAME: 7,
    PRICE_PLANS: 8,
    PAYMENT_HISTORY: 9,
    ACCESS_CODE: 10,
    ACCESS_LINK: 11,
    // Price plan column indexes:
    PLAN_TYPE: 0, // The type of additional product for which the price plan applies, or -1 if the
                  // price plan applies to the rent.
    PLAN_LINES: 1,
    // Price plan line column indexes:
    LINE_START_DATE: 0,
    LINE_PRICE: 1,
    LINE_CAUSE: 2,
    LINE_DESCRIPTION: 3
  };

// All subscriptions table column indexes. The PAYMENT_HISTORY may be loaded asyhcnronously when
// required. When it has not yet been loaded, the value will be null. Pages using this table format
// may not need to load products, as the names will be included in the table. They do need to load
// the product types and locations, however.  They also need to load the list of users separately.
// Note that the subscriptions included as part of the products table use different column indexes,
// as do the subscriptions for a single user.
c.sua =
  {
    ID: 0, // The subscription ID.
    PRODUCT_NAME: 1, // The name of the product.
    LOCATION_ID: 2,
    PRODUCT_TYPE_ID: 3,
    STATUS: 4, // The subscription status. Use the st.sub constants.
    START_DATE: 5,
    END_DATE: 6,
    INSURANCE_NAME: 7,
    PRICE_PLANS: 8,
    PAYMENT_HISTORY: 9,
    BUYER_ID: 10,
    // Price plan column indexes:
    PLAN_TYPE: 0, // The type of additional product for which the price plan applies, or -1 if the
                  // price plan applies to the rent.
    PLAN_LINES: 1,
    // Price plan line column indexes:
    LINE_START_DATE: 0,
    LINE_PRICE: 1,
    LINE_CAUSE: 2,
    LINE_DESCRIPTION: 3
  };

// Payment history column indexes. PAYMENT_METHOD is an integer. Use the PAYMENT_METHOD_ constants.
c.pay =
  {
    ID: 0,
    NAME: 1,
    ORDER_DATE: 2,
    PAY_BY_DATE: 3,
    PAYMENT_METHOD: 4,
    PAYMENT_STATUS: 5,
    PAYMENT_INFO: 6,
    ORDER_LINES: 7,
    OPEN: 8,
    // ORDER_LINES column indexes:
    LINE_ID: 0,
    LINE_TEXT: 1,
    LINE_AMOUNT: 2
  };

// productTypes table column indexes.
c.typ =
  {
    ID: 0,
    NAME: 1,
    PRICE: 2,
    CATEGORY_ID: 3
  };

// categories table column indexes.
c.cat =
  {
    ID: 0,
    NAME: 1
  };

// users table column indexes.
c.usr =
  {
    ID: 0,
    NAME: 1,
    EMAIL: 2,
    PHONE: 3,
    HAS_ACTIVE_SUBSCRIPTION: 4,
    ENTITY_TYPE: 5,
  };

// Available product types table column indexes.
// - ID is the product type ID.
// - NAME is the product type name.
// - PRICE is the capacity price - that is, the base price modified due to the location's capacity.
//   This is the regular monthly price, barring any special offer discounts.
// - PRICE_MODS is an array of special offer price mods, or null if no special offer is provided.
// - IS_AVAILABLE is a boolean value that says whether that particular product type is available at
//   the selected location, from the selected date.
// - If the product type is not available, ALTERNATIVE_LOCATION_IDS is an array of IDs of the
//   other locations that have available products of this type.
// - If the product type is not available, FIRST_AVAILABLE_DATE is the date, as a string in the
//   format "yyyy-mm-dd", on which the product type becomes available for rent.
// - AVAILABLE_PRODUCT_COUNT is the number of products in this category that are free and can be
//   booked. If the product type is unavailable, the number will be 0.
// - If the caller requested information about a single product, the PRODUCT_NAME column will hold
//   the name of the requested product. Otherwise it will be an empty string.
// - If the caller is an administrator, the BASE_PRICE column will hold the price of the storage
//   room type. This is the basis on which the capacity price is calculated. If the user is not an
//   administrator, the column will not be present at all.
//
// Each entry in the PRICE_MODS table has the following fields:
//   PRICE_MOD      The percentage change in prices. -10 is a 10% discount.
//   DURATION       The number of months that this change will last, or -1 if the change applies
//                  indefinitely.
c.apt =
  {
    ID: 0,
    NAME: 1,
    PRICE: 2,
    PRICE_MODS: 3,
    CATEGORY_ID: 4,
    IS_AVAILABLE: 5,
    ALTERNATIVE_LOCATION_IDS: 6,
    FIRST_AVAILABLE_DATE: 7,
    AVAILABLE_PRODUCT_COUNT: 8,
    PRODUCT_NAME: 9,
    BASE_PRICE: 10,

    PRICE_MOD: 0,
    DURATION: 1
  };

// licencees table column indexes.
c.lic =
  {
    ID: 0,                  // The ID of the item that connects the user group and the licence.
    NAME: 1,                // The name of the user group.
    USER_GROUP_ID: 2,
    USER_GROUP_USER_ID: 3,  // The ID of the user group's dummy user.
    IS_ACTIVE: 4,
    LICENCE_ID: 5           // The ID of the "Minilager" licence entry.
  };

// userGroups table column indexes.
c.ugr =
  {
    ID: 0,          // The ID of the user group.
    NAME: 1         // The name of the user group.
  };

// insurances table column indexes.
//   FOR_PRODUCT_TYPES            Array of IDs from the productTypes table, or null - which means
//                                "all of them".
//   FOR_LOCATIONS                Array of IDs from the locations table, or null - which means "all
//                                of them".
c.ins =
  {
    ID: 0,
    NAME: 1,
    DESCRIPTION: 2,
    PRICE: 3,
    FOR_PRODUCT_TYPES: 4,
    FOR_LOCATIONS: 5
    // MUTUALLY_EXCLUSIVE_WITH: 6
  };

// capacityPriceRules and specialOfferPriceRules table column indexes.
//   STATUS                       Whether the price rule has not started applying yet, is in effect,
//                                or has stopped applying. Use the st.pru constants.
//   PRICE_MODS                   Array of modifications to prices. Each entry is an array with the
//                                items described below. The items differ or capacity and special
//                                offer price rules.
//   FOR_LOCATIONS                Array of location IDs for which this offer should apply, or null
//                                if it applies everywhere. An empty array will apply nowhere.
//   FOR_PRODUCT_TYPES            Array of product type IDs for which this offer should apply, or
//                                null if it applies to all of them. An empty array applies to none.
//   OPEN                         Always false.
//
// For capacity price rules, the PRICE_MODS table has the following columns:
//   PRICE_MOD      The percentage change in prices. -10 is a 10% discount.
//   MIN_CAPACITY   The minimum capacity which will give this price modification. If capacity is
//                  equal to or greater than this number, the modification will apply.
//   MAX_CAPACITY   The maximum capacity which will give this price modification. If capacity is
//                  less than this number, the modification will apply. We don't need to reach
//                  exactly 100%, because then the warehouse is full anyway, and nothing can be
//                  rented.
//
// For special offer price rules, the PRICE_MODS table has the following columns:
//   PRICE_MOD      The percentage change in prices. -10 is a 10% discount.
//   DURATION       The number of months that this change will last, or -1 if the change applies
//                  indefinitely.
c.pru =
  {
    ID: 0,
    NAME: 1,
    STATUS: 2,
    START_DATE: 3,
    END_DATE: 4,
    PRICE_MODS: 5,
    FOR_PRODUCT_TYPES: 6,
    FOR_LOCATIONS: 7,
    OPEN: 8,
    // PRICE_MODS column indexes:
    PRICE_MOD: 0,
    MIN_CAPACITY: 1,
    MAX_CAPACITY: 2,
    DURATION: 1
  };

// capacities table column indexes.
//   location_id : integer        The ID of this location.
//   total_count : integer        The total number of storage units at this location.
//   ongoing_count : integer      The number of storage units at this location that have an
//                                ongoing subscription.
//   cancelled_count : integer    The number of storage units at this location that have a
//                                cancelled subscription, and no booked subscription.
//   booked_count : integer       The number of storage units at this location that have a booked 
//                                subscription. Storage units that have a finished or cancelled 
//                                subscription, or no previous subscriptions at all, can have a
//                                booked subscription. Storage units with ongoing subscriptions
//                                cannot.
//   occupied_count : integer     The number of storage units at this location that are currently 
//                                occupied. Storage units are occupied if they have an ongoing,
//                                cancelled or booked subscription.
//   free_count : integer         The number of storage units at this location that can be booked. 
//                                Storage units are free if they have a finished subscription, or no
//                                previous subscriptions at all.
c.cap =
  {
    ID: 0,
    NAME: 1,
    TOTAL_COUNT: 2,
    ONGOING_COUNT: 3,
    CANCELLED_COUNT: 4,
    BOOKED_COUNT: 5,
    OCCUPIED_COUNT: 6,
    FREE_COUNT: 7,
    USED_CAPACITY: 8
  };

// requests table column indexes. For status, use the st.req constants.
c.req = 
  {
    ID: 0,
    USER_ID: 1,
    LOCATION_ID: 2,
    CATEGORY_ID: 3,
    START_DATE: 4,
    COMMENT: 5,
    STATUS: 6
  };

// requestUsers table column indexes. Also used for the list of subscriptions. Note that the company
// ID number is only present if the entity type indicates that it is a company.
c.rqu =
  {
    ID: 0,
    NAME: 1,
    EMAIL: 2,
    PHONE: 3,
    ADDRESS: 4,
    POSTCODE: 5,
    AREA: 6,
    ENTITY_TYPE: 7,
    COMPANY_ID_NUMBER: 8
  };

// orders table column indexes.
//   id : integer                 The order ID.
//   user_id : integer            The ID of the buyer who pays for the subscription.
//   subscription_id : integer    The ID of the subscription (in our database, not the Nets
//                                subscription).
//   location_id : integer        The ID of the location in which the product is located.
//   product_name : string        The name of the product for which the order is paying.
//   amount : integer             The total amount of the order (can also be read from data).
//   created : string             The date and time at which the order was created.
//   data : array                 An array of order metadata.
//
// The data array has the following fields:
//   key : string                 The name of the field.
//   value : string               The value of that field. You may have to convert the type.
c.ord =
  {
    ID: 0,
    USER_ID: 1,
    SUBSCRIPTION_ID: 2,
    LOCATION_ID: 3,
    PRODUCT_NAME: 4,
    AMOUNT: 5,
    CREATED: 6,
    DATA: 7,
    // DATA column indexes:
    KEY: 0,
    VALUE: 1
  };

// templates table column indexes.
//   id : integer                 The template ID.
//   name : string                The name of the template.
//   copy_to : string             Comma separated list of e-mail addresses to which a copy should be
//                                sent. Not used for SMS.
//   header : string              The subject field of an e-mail. Not used for SMS.
//   content : string             The content of an e-mail or SMS.
//   delay : integer              The delay, in minutes, between the trigger triggering, and the
//                                message being sent.
//   active : boolean             Toggle that says whether messages will currently be generated
//                                based on this template.
//   message_type : integer       The type of message that will be generated (e-mail or SMS). Use
//                                the MESSAGE_TYPE_ constants.
//   trigger_type : integer       The trigger that will cause a message to be generated based on
//                                this template. Use the TRIGGER_TYPE_ constants.
c.tpl =
  {
    ID: 0,
    NAME: 1,
    COPY_TO: 2,
    HEADER: 3,
    CONTENT: 4,
    DELAY: 5,
    ACTIVE: 6,
    MESSAGE_TYPE: 7,
    TRIGGER_TYPE: 8
  };

// Message log table column constants.
//   id : integer                 The ID in the database of this message. Note that e-mail and SMS
//                                are kept in different tables, so you can have two entries with
//                                the same ID, provided they have different message types.
//   message_type : integer       The type of message that was sent (e-mail or SMS). Use the
//                                MESSAGE_TYPE_ constants.
//   user_id : integer            The ID of the user who received the message.
//   subscription_id : integer    The ID of the subscription (in our database, not the Nets
//                                subscription) to which the message was related, if relevant. Some
//                                messages are not related to a subscription, in which case this
//                                value will be -1.
//   product_name : string        The name of the product to which the user is subscribed, if any.
//                                If not, the result will be an empty string.
//   recipient : string           For an SMS, this contains the recipient's phone number, including
//                                the country code. For an e-mail, this contains the recipient's
//                                e-mail address.
//   header : string              For an e-mail message, this is a string that holds the subject
//                                line of the message that was sent. For an SMS message, this field
//                                will be an empty string.
//   content : string             The message that was actually sent to the recipient, including any
//                                information that was inserted into the template when the message
//                                was composed.
//   time_sent : string           The moment the message was sent. // *** // Specify format.
//   delivered : boolean          True if the message was delivered successfully.
//   error_message : string       If delivered was false, a string that says why the delivery
//                                failed. If the delivery succeeded, this field will be an empty
//                                string.
c.log =
  {
    ID : 0,
    MESSAGE_TYPE: 1,
    USER_ID: 2,
    SUBSCRIPTION_ID: 3,
    PRODUCT_NAME: 4,
    RECIPIENT: 5,
    HEADER: 6,
    CONTENT: 7,
    TIME_SENT: 8,
    DELIVERED: 9,
    ERROR_MESSAGE: 10
  };

// *************************************************************************************************
// *** Subscription and product status onstants.
// *************************************************************************************************
// Object for status constants. This is used to avoid polluting the namespace, and prevent duplicate
// constants with different numerical values. It should be called "status", but that is already
// defined by... something, and doesn't work.
var st = {};

// Individual subscription status constants.
//                              Start date                    End date
// - Finished                   Before today's date           Exists; before today's date
// - Ongoing                    Before today's date           Does not exist
// - Cancelled                  Before today's date           Exists; after today's date
// - Booked                     After today's date            Who cares?
st.sub =
  {
    INACTIVE: 0,
    EXPIRED: 1,
    ONGOING: 2,
    CANCELLED: 3,
    BOOKED: 4
  };

st.sub.COLOURS =
  [
    'red',
    'red',
    'green',
    'blue',
    'green'
  ];

// Product status constants. When updating, also update the Utility::STATUS_ constants in
// utility.php, the st.prod.TEXTS and st.prod.TEXTS_BRIEF declarations in admin_rental_overview.php,
// the st.prod.COLOURS table below and the /resources/status_X.png images.
st.prod =
  {
    NEW: 0,
    VACATED: 1,
    BOOKED: 2,
    VACATED_BOOKED: 3,
    RENTED: 4,
    CANCELLED: 5,
    CANCELLED_BOOKED: 6
  };

st.prod.COLOURS =
  [
    'red',
    'red',
    'green',
    'green',
    'green',
    'blue',
    'green'
  ];

// Product readiness constants. When updating, also update the READY_STATUS_ constants in
// utility.php.
st.ready =
  {
    YES: 0,
    CHECK: 1
  };

st.ready.COLOURS =
  [
    'green',
    'red'
  ];
st.ready.ICONS =
  [
    'fa-check',
    'fa-magnifying-glass'
  ];

// Enabled / disabled constants.
st.enabled = {};
st.enabled.COLOURS =
  [
    'red',
    'green'
  ];
st.enabled.ICONS =
  [
    'fa-hammer',
    'fa-check'
  ];

// Price rule status constants.
st.pru =
  {
    EXPIRED: 0,
    ONGOING: 1,
    NOT_STARTED: 2
  };

st.pru.COLOURS =
  [
    'red',
    'green',
    'blue'
  ];

// Request status constants.
//   0    Received          We have received the request, and need to do something about it.
//   1    Need info         We need more information, and should contact the customer.
//   2    Pending info      We are waiting for the customer to provide more information.
//   3    Create offer      We need to create an offer for the customer.
//   4    Pending approval  We are waiting for the customer to consider our offer.
//   5    Offer approved    The customer gave us his money. Yay!
//   6    Customer lost     The customer declined, or vanished.
//   7    Not qualified     The customer was looking for something else.
st.req =
  {
    RECEIVED: 0,
    NEED_INFO: 1,
    PENDING_INFO: 2,
    CREATE_OFFER: 3,
    PENDING_APPROVAL: 4,
    OFFER_APPROVED: 5,
    CUSTOMER_LOST: 6,
    NOT_QUALIFIED: 7
  };

st.req.COLOURS =
  [
    'red',
    'red',
    'blue',
    'red',
    'blue',
    'green',
    'green',
    'green'
  ];

// *************************************************************************************************
// *** Constants.
// *************************************************************************************************
// The minimum number of characters in a password.
var PASSWORD_MIN_LENGTH = 8;

// The colours to use for various payment statuses.
var PAYMENT_STATUS_COLOURS =
  [
    'blue',
    'red', 
    'green', 
    'blue',
    'red', 
    'red', 
    'red', 
    'red', 
    'green', 
    'red', 
    'green', 
    'red', 
    'red', 
    'green', 
    'red',
    'red',
    'blue',
    'blue',
    'red'
  ];

// Additional product type constants.
var ADDITIONAL_PRODUCT_INSURANCE = 1;

// Application role constants.
var APP_ROLE_PRODUCTION = 'production';
var APP_ROLE_EVALUATION = 'evaluation';
var APP_ROLE_TEST = 'test';

// Booking type constants. When changing, also modify Settings::BOOKING_TYPE_ constants.
var BOOKING_TYPE_SELF_SERVICE = 0;
var BOOKING_TYPE_REQUEST = 1;
var BOOKING_TYPE_BOTH = 2;
var BOOKING_TYPE_REQUEST_AT_SOME_LOCATIONS = 3;

// Full mode constants. When changing, also modify Settings::FULL_MODE constants.
var FULL_MODE_ALTERNATIVES = 0;
var FULL_MODE_REQUEST = 1;
var FULL_MODE_REQUEST_AT_SOME_LOCATIONS = 2;

// Payment status constants. When updating these, also update PAYMENT_STATUS_ constants in
// utility.php, and PAYMENT_STATUS_COLOURS in this file.
var PAYMENT_STATUS_UNKNOWN = 0;
var PAYMENT_STATUS_FIRST = 0;
var PAYMENT_STATUS_UNKNOWN = 0;
var PAYMENT_STATUS_NOT_PAID = 1;
var PAYMENT_STATUS_PAID = 2;
var PAYMENT_STATUS_PARTIALLY_PAID = 3;
var PAYMENT_STATUS_NOT_PAID_OVERDUE = 4;
var PAYMENT_STATUS_NOT_PAID_REMINDER_SENT = 5;
var PAYMENT_STATUS_NOT_PAID_WARNING_SENT = 6;
var PAYMENT_STATUS_NOT_PAID_SENT_TO_COLLECTION = 7;
var PAYMENT_STATUS_PAID_TO_COLLECTION = 8;
var PAYMENT_STATUS_LOST = 9;
var PAYMENT_STATUS_CREDITED = 10;
var PAYMENT_STATUS_FAILED_AT_PROVIDER = 11;
var PAYMENT_STATUS_ERROR = 12;
var PAYMENT_STATUS_REFUNDED = 13;
var PAYMENT_STATUS_DISPUTED = 14;
var PAYMENT_STATUS_NOT_PAID_NO_INVOICE_SENT = 15;
var PAYMENT_STATUS_NOT_PAID_INVOICE_SENT = 16;
var PAYMENT_STATUS_NOT_PAID_CHARGE_REQUESTED = 17;
var PAYMENT_STATUS_DELETED = 18;
var PAYMENT_STATUS_LAST = 18;

// Payment method constants. When changing, also modify Utility::PAYMENT_METHOD_ constants.
var PAYMENT_METHOD_UNKNOWN = 0;
var PAYMENT_METHOD_NETS = 1;
var PAYMENT_METHOD_INVOICE = 2;
var PAYMENT_METHOD_NETS_THEN_INVOICE = 3;

// Entity type constants.
var ENTITY_TYPE_INDIVIDUAL = 0;
var ENTITY_TYPE_COMPANY = 1;

// Message type constants.
var MESSAGE_TYPE_SMS = 0;
var MESSAGE_TYPE_EMAIL = 1;

// Trigger type constants.
//   REGISTERED                 When the user has registered, and received a role in a particular user group (even if he didn't buy a subscription).
//   FORGOT_PASSWORD            When the user asks to change his password ("forgot password").
//   BOUGHT_SUB                 When a subscription is created (and payment succeeded, if paying through Nets) for an existing user.
//   REGISTERED_AND_BOUGHT_SUB  When a subscription is created (and payment succeeded, if paying through Nets) for a new user.
//   SUB_VALIDATION_FAILURE     When a subscription validation failed (a few weeks before next month's charge), for instance because the buyer's credit card has expired.
//   BEFORE_EXPIRES             Before a Nets subscription expires (also a few weeks before next month's charge).
//   MONTHLY_PAYMENT_SUCCESS    When a Nets subscription was charged successfully (the storage company may not want to bother the customer, but they should be able to).
//   MONTHLY_PAYMENT_FAILURE    When a Nets subscription was not charged successfully.
//   INVOICE_FIRST_REMINDER     When an invoice is overdue (reminder).
//   INVOICE_SECOND_REMINDER    When an invoice is overdue and the reminder didn't work (collection agency referral warning).
//   CANCELLED_SUB              When a user cancels a subscription.
//   TERMS_CHANGED              When the terms and conditions change (not implemented yet).
//   PRICE_CHANGED              When the price of a subscription changes (not implemented yet).
//   NEWSLETTER                 When the storage company wants to send a newsletter or special offer (not implemented yet).
//   MAINTENANCE                When the storage company wants to send a notification about maintenance or closures at a particular location (not implemented yet).
//   ACCESS_CODE_MODIFIED       When an access code is modified (do we need this?).
//   DELETED_ACCOUNT            When a user deletes his account (is that even possible?).
/*
var TRIGGER_TYPE_REGISTERED = 0;
var TRIGGER_TYPE_FORGOT_PASSWORD = 1;
var TRIGGER_TYPE_BOUGHT_SUB = 2;
var TRIGGER_TYPE_REGISTERED_AND_BOUGHT_SUB = 3;
var TRIGGER_TYPE_SUB_VALIDATION_FAILURE = 4;
var TRIGGER_TYPE_BEFORE_EXPIRES = 5;
var TRIGGER_TYPE_MONTHLY_PAYMENT_SUCCESS = 6;
var TRIGGER_TYPE_MONTHLY_PAYMENT_FAILURE = 7;
var TRIGGER_TYPE_INVOICE_FIRST_REMINDER = 8;
var TRIGGER_TYPE_INVOICE_SECOND_REMINDER = 9;
var TRIGGER_TYPE_CANCELLED_SUB = 10;
var TRIGGER_TYPE_TERMS_CHANGED = 11;
var TRIGGER_TYPE_PRICE_CHANGED = 12;
var TRIGGER_TYPE_NEWSLETTER = 13;
var TRIGGER_TYPE_MAINTENANCE = 14;
var TRIGGER_TYPE_ACCESS_CODE_MODIFIED = 15;
var TRIGGER_TYPE_DELETED_ACCOUNT = 16;
*/
// *************************************************************************************************
// *** Fields.
// *************************************************************************************************
// Component instances are stored in a registry, accessible through a global variable. This is so
// that event handlers can access their parent component. Use Utility.registerInstance to add a
// component instance to the registry and receive the index that can be used to access it. Use
// Utility.getInstance to retrieve it.
var _instanceRegistry = [];

// *************************************************************************************************
// *** Sorting functions.
// *************************************************************************************************
// Sorting function to sort integers in ascending order. Elements are assumed to be integers, and
// are not converted if they are not.
function asIntAscending(a, b)
{
  return a - b;
}

// *************************************************************************************************
// *** Functions.
// *************************************************************************************************
// Return a text from the texts array that should be provided by the server. If the text was not
// found, return the defaultValue instead. Any occurrences of $1, $2, ... $n in the string will be
// replaced with any additional data provided to this function. data is an array where the first
// entry corresponds to $1, the second to $2, and so on.
function getText(index, defaultValue, data)
{
  var text;

  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, texts))
    text = texts[index];
  else
    text = defaultValue;

  if (data)
    return Utility.expandText(text, data);
  return text;
}

// *************************************************************************************************
// Close the current user menu if the menu is open and the user clicked anywhere outside of the
// menu or the menu button.
function clickOutsideCurrentUserMenu(event)
{
  var userMenu, userMenuButton;
  
  userMenu = document.getElementById('currentUserMenu');
  userMenuButton = document.getElementById('currentUserMenuButton');
  // If the user clicked the menu itself, don't close the menu. If the user clicked the menu button,
  // the menu will be closed anyway, so don't do anything.
  if (userMenu && userMenuButton && Utility.displayed(userMenu) &&
    !userMenu.contains(event.target) && !userMenuButton.contains(event.target))
    toggleCurrentUserMenu();
}

// *************************************************************************************************
// Open or close the user menu in the top-right corner of the header.
function toggleCurrentUserMenu()
{
  var userMenu;
  
  userMenu = document.getElementById('currentUserMenu');
  if (userMenu)
  {
    if (userMenu.style.height === '280px')
    {
      userMenu.style.height = '0';
      setTimeout(
        function ()
        {
          Utility.hide(userMenu);
        },
        320);
    }
    else
    {
      userMenu.style.height = '0';
      Utility.display(userMenu);
      setTimeout(
        function ()
        {
          userMenu.style.height = '280px';
        },
        10)
    }
  }
}

// *************************************************************************************************

function submitLanguageSelection()
{
  var form;

  form = document.getElementById('selectLanguageForm');
  if (form)
    Utility.displaySpinnerThenSubmit(form);
}

// *************************************************************************************************
// Switch to the user group with the given ID. If id is invalid, nothing will happen.
function setUserGroup(id)
{
  id = parseInt(id, 10);
  id = Utility.getValidInteger(id, -1);
  if (id !== -1)
    Utility.displaySpinnerThenGoTo('/subscription/html/set_user_group.php?role_id=' + String(id));
}

// *************************************************************************************************
// Update the colour in the colour preview square for the given colour editBox. editBox may be a
// string with an ID, or the actual HTML element. The preview box is assumed to have an ID of
// id + '_preview'. If the colour value in the edit box is not valid, the preview will be black.
function updateColourPreview(editBox)
{
  var preview, colour;

  editBox = Utility.getElement(editBox);
  if (editBox)
  {
    preview = document.getElementById(editBox.id + '_preview');
    if (preview)
    {
      colour = editBox.value;
      if (Utility.isValidColour(colour))
        preview.style.backgroundColor = colour;
      else
        preview.style.backgroundColor = '#000';
    }
  }
}

// *************************************************************************************************
// *** class SidebarMenu
// *************************************************************************************************
// The SidebarMenu class displays a vertical menu.
class SidebarMenu
{

// *************************************************************************************************
// *** Constructors.
// *************************************************************************************************
// Create a new SidebarMenu. The menu will be displayed in the given target element, which can be a
// string or a pointer to an HTML element. menu is an array of menu items, using the c.mnu
// constants. The iems will be stored, and can be manipulated. The menu will be displayed as soon as
// the page has finished loading.
constructor(target, menu)
{
  // Properties.
  this._menu = menu;
  this._target = target;

  // Register the object in the instance registry. This is required for event handlers to be able to
  // talk to their parent object.
  this._registryIndex = Utility.registerInstance(this);

  // Add an event handler that will display the menu when the page has finished loading.
  window.addEventListener('load', this.display.bind(this));
}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************
// Display the menu in the target element.
display()
{
  this.target.innerHTML = this._getMenuItems(this._menu, 0);
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the target HTML element.
get target()
{
  this._target = Utility.getElement(this._target);
  return this._target;
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************
// Return HTML code for all the given menu items. level is an integer that says which level these
// menu items are on, starting with 0 for top-level items.
_getMenuItems(items, level)
{
  var o, p, i;

  o = new Array(items.length);
  p = 0;

  for (i = 0; i < items.length; i++)
    o[p++] = this._getMenuItem(items[i], level, i);
  return o.join('');
}

// *************************************************************************************************
// Return HTML code for the given menu item. level is an integer that says which level these menu
// items are on, starting with 0 for top-level items. index is the index of this item in the list of
// items at the given level.
_getMenuItem(item, level, index)
{
  // Write a separator.
  if (item[c.mnu.URL] === '[separator]')
    return '<div class="sidebar-separator">&nbsp;</div>';

  // Write a drawer that can be opened or closed, including subitems.
  if (item[c.mnu.URL] === '[drawer]')
    return this._getDrawer(item, level, index);

  // Write a selected menu item.
  if (item[c.mnu.SELECTED])
    return this._getNonLinkItem(item, level, true);

  // Write an enabled menu item.
  if (item[c.mnu.ENABLED])
    return this._getLinkItem(item, level);

  // Write a disabled menu item.
  return this._getNonLinkItem(item, level, false);
}

// *************************************************************************************************
// Return HTML code for a menu item that opens or closes a list of subitems. level is an integer
// that says which level these menu items are on, starting with 0 for top-level items. index is the
// index of this item in the list of items at the given level.
//
// Note that, at the moment, you can only have drawers on the top level.
_getDrawer(item, level, index)
{
  var o, p;

  if (level !== 0)
    return '';
  o = new Array(12);
  p = 0;

  // Write drawer.
  o[p++] = '<a href="javascript:void(0);" onclick="Utility.getInstance(';
  o[p++] = String(this._registryIndex);
  o[p++] = ')._toggleDrawer(';
  o[p++] = String(index);
  o[p++] = ');" class="sidebar-item sidebar-item-enabled"><i class="fa-solid ';
  o[p++] = (item[c.mnu.OPEN] ? 'fa-chevron-down' : 'fa-chevron-right');
  o[p++] = ' sidebar-icon-';
  o[p++] = String(level);
  o[p++] = '"></i> ';
  o[p++] = item[c.mnu.TEXT];
  o[p++] = '</a><br />';

  // Write subitems, if present.
  if (item[c.mnu.OPEN])
    o[p++] = this._getMenuItems(item[c.mnu.SUBITEMS], level + 1);

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for a menu item that is not a link to anything - either because it is selected,
// or because it is disabled. Use the isSelected flag to indicate which it is. item is the menu item
// data table. level should be an integer that states the level of the menu item, starting at 0 for
// a top-level item.
_getNonLinkItem(item, level, isSelected)
{
  var o, p;

  o = new Array(9);
  p = 0;

  o[p++] = '<span class="sidebar-item sidebar-item-';
  o[p++] = (isSelected ? 'selected' : 'disabled');
  o[p++] = '"><i class="fa-solid ';
  o[p++] = item[c.mnu.ICON];
  o[p++] = ' sidebar-icon-';
  o[p++] = String(level);
  o[p++] = '"></i> ';
  o[p++] = item[c.mnu.TEXT];
  o[p++] = '</span><br />';
  return o.join('');
}

// *************************************************************************************************
// Return HTML code for a menu item that is a hyperlink. item is the menu item data table. level
// should be an integer that states the level of the menu item, starting at 0 for a top-level item.
_getLinkItem(item, level)
{
  var o, p;

  o = new Array(11);
  p = 0;

  o[p++] = '<a href="';
  o[p++] = item[c.mnu.URL];
  o[p++] = '" onclick="if (!event.ctrlKey && !event.metaKey) { event.preventDefault(); Utility.displaySpinnerThenGoTo(\'';
  o[p++] = item[c.mnu.URL];
  o[p++] = '\'); }" class="sidebar-item sidebar-item-enabled"><i class="fa-solid ';
  o[p++] = item[c.mnu.ICON];
  o[p++] = ' sidebar-icon-';
  o[p++] = String(level);
  o[p++] = '"></i> ';
  o[p++] = item[c.mnu.TEXT];
  o[p++] = '</a><br />';
  return o.join('');
}

// *************************************************************************************************
// Open or close the top-level drawer with the given index.
_toggleDrawer(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, this._menu))
  {
    this._menu[index][c.mnu.OPEN] = !this._menu[index][c.mnu.OPEN];
    this.display();
  }
}

// *************************************************************************************************

}

// *************************************************************************************************
// *** class Utility
// *************************************************************************************************

class Utility
{

// *************************************************************************************************
// *** Static methods.
// *************************************************************************************************
// If the given element is a string, return the HTML element with that ID. If element already
// contains an HTML element, return that element.
static getElement(element)
{
  if (typeof element === 'string')
    return document.getElementById(element);
  return element;
}

// *************************************************************************************************
// Render the given HTML element visible. You can pass the ID of the element, or the element itself.
static display(element)
{
  element = Utility.getElement(element);
  if (element)
    element.style.display = '';
}

// *************************************************************************************************
// Render the given HTML element invisible. You can pass the ID of the element, or the element
// itself.
static hide(element)
{
  element = Utility.getElement(element);
  if (element)
    element.style.display = 'none';
}

// *************************************************************************************************
// Toggle the visibility of the given HTML element.
static toggle(element)
{
  element = Utility.getElement(element);
  if (element)
  {
    if (Utility.displayed(element))
      element.style.display = 'none';
    else
      element.style.display = '';
  }
}

// *************************************************************************************************
// Set the visibility of the given HTML element. You can pass the ID of the element, or the element
// itself.
static setDisplayState(element, displayed)
{
  element = Utility.getElement(element);
  if (element)
  {
    if (!!displayed)
      element.style.display = '';
    else
      element.style.display = 'none';
  }
}

// *************************************************************************************************
// Return true if the given element is currently displayed. If not, or if the element was not found,
// return false.
static displayed(element)
{
  element = Utility.getElement(element);
  if (element)
    return element.style.display !== 'none';
  return false;
}

// *************************************************************************************************
// Locate the HTML elements given in the ids table, and store them in global variables with the same
// names. ids should be an array of strings. If it contains ['itemA', 'itemB'], the method will look
// for HTML elements with IDs "itemA" and "itemB", and store them in global variables "itemA" and
// "itemB".
static readPointers(ids)
{
  var i;

  if (Array.isArray(ids))
  {
    for (i = 0; i < ids.length; i++)
      window[ids[i]] = document.getElementById(ids[i]);
  }
}

// *************************************************************************************************
// Store the given component instance in the registry, and return the index, which can be used to
// access it later, using the getInstance method. Return -1 if the component instance could not be
// registered.
static registerInstance(instance)
{
  var index;

  if (instance)
  {
    index = _instanceRegistry.length;
    _instanceRegistry[index] = instance;
    return index;
  }
  return -1;
}

// *************************************************************************************************
// Return the component instance with the given index in the component instance registry, or null if
// it could not be found.
static getInstance(index)
{
  index = parseInt(index, 10);
  if (Utility.isValidIndex(index, _instanceRegistry))
    return _instanceRegistry[index];
  return null;
}

// *************************************************************************************************
// Extract JSON data from the given HTTP response object. Return a promise to provide the JSON once
// it has been parsed. You can either do "await" to pause until the JSON data is available, or add a
// callback using the "then" method.
static extractJson(response)
{
  if (!response.ok)
    throw new Error('Error parsing HTTP response: ' + response.statusText + '; status: ' +
      String(response.status));
  return response.json();
}

// *************************************************************************************************
// Return true if the given resultCode represents an error. If the value is not a number, the method
// will return true.
static isError(resultCode)
{
  resultCode = parseInt(resultCode, 10);
  return !isFinite(resultCode) || (resultCode >= 0);
}

// *************************************************************************************************

static expandText(text, data)
{
  var regExp, getReplacementValue;

  // Use a regular expression to match placeholders like $1, $2, etc.
  regExp = /\$(\d+)/g;
  // Use a function that parses the matched "$n" strings to determine the index of the text to
  // insert.
  getReplacementValue = function (match, captureGroup)
    {
      var index;

      index = parseInt(captureGroup, 10);
      if (isFinite(index))
      {
        index--;
        if ((index >= 0) && (index < data.length))
          return data[index];
      }
      return match;
    };

  // Replace each placeholder with the corresponding data value.
  return String(text).replace(regExp, getReplacementValue);
}

// *************************************************************************************************
// Replace line breaks in the given data string with a special character.
//
static encodeLineBreaks(data)
{
  // The regular expression /\n/g ensures that all line breaks in the string are replaced globally
  // (g flag).
  return data.replace(/\n/g, '¤');
}

// *************************************************************************************************
// Replace a special character in the given data string with line breaks.
static decodeLineBreaks(data)
{
  // The regular expression /\<br\s*\/?>/gi matches <br>, <br/>, and other variations with spaces
  // (\s* allows optional spaces), using the case-insensitive (i) and global (g) flags to ensure all
  // instances are replaced.
  // return data.replace(/<br\s*\/?>/gi, '\n');

  return data.replace(/¤/g, '\n');
}

// *************************************************************************************************

static isValidEMail(eMail)
{
  var emailRegexp;

  emailRegexp = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegexp.test(eMail);
}

// *************************************************************************************************
// Return today's date as an ISO date string, in the format "yyyy-mm-dd".
static getCurrentIsoDate()
{
  return Utility.getIsoDate(new Date());
}

// *************************************************************************************************
// Return today's month as an ISO-style month string, in the format "yyyy-mm".
static getCurrentIsoMonth()
{
  return Utility.getIsoMonth(new Date());
}

// *************************************************************************************************
// Return a date as an ISO date string, in the format "yyyy-mm-dd". You can either pass a single
// Javascript Date object, or integers for year, month and day. In the latter case, the month should
// be in the Javascript format, which is zero based.
static getIsoDate(year, month, day)
{
  var date;

  // Convert from a Javascript Date to year, month and day figures, if required.
  if (year && (typeof month === 'undefined') && (typeof day === 'undefined'))
  {
    date = year;
    year = date.getFullYear();
    month = date.getMonth();
    day = date.getDate();
  }

  // Format the date.
  return Utility.pad(year, 4) + '-' + Utility.pad(month + 1, 2) + '-' + Utility.pad(day, 2);
}

// *************************************************************************************************
// Return a month as an ISO-style month string, in the format "yyyy-mm". You can either pass a
// single Javascript Date object, or integers for year and month. In the latter case, the month
// should be in the Javascript format, which is zero based.
static getIsoMonth(year, month)
{
  var date;

  // Convert from a Javascript Date to year and month figures, if required.
  if (year && (typeof month === 'undefined'))
  {
    date = year;
    year = date.getFullYear();
    month = date.getMonth();
  }

  // Format the date.
  return Utility.pad(year, 4) + '-' + Utility.pad(month + 1, 2);
}

// *************************************************************************************************
// Given a date, in the format "yyyy-mm-dd", return the previous day in the same format.
static getDayBefore(date)
{
  date = new Date(date);
  date.setDate(date.getDate() - 1);
  return Utility.getIsoDate(date);
}

// *************************************************************************************************
// Given a date, in the format "yyyy-mm-dd", return the next day in the same format.
static getDayAfter(date)
{
  date = new Date(date);
  date.setDate(date.getDate() + 1);
  return Utility.getIsoDate(date);
}

// *************************************************************************************************
// Return a date, in the format "yyyy-mm-dd", that represents the last day of the given year and
// month. You can either pass a single Javascript Date object, or integers for year and month. In
// the latter case, the month should be in the Javascript format, which is zero based.
static getLastDay(year, month)
{
  var date, nextMonth;

  // Convert from a Javascript Date to year, month and day figures, if required.
  if (year && (typeof month === 'undefined'))
  {
    date = year;
    year = date.getFullYear();
    month = date.getMonth();
  }

  nextMonth = new Date(year, month + 1, 1);
  return Utility.getIsoDate(new Date(nextMonth - 1));
}

// *************************************************************************************************
// Return the number of whole days between the two given dates a and b. If a is "2024-10-01" and b
// is "2024-10-05", the result will be 4. a and b can be dates in "yyyy-mm-dd" format, or Date
// objects.
static getDaysBetween(a, b)
{
  var delta;

  // Ensure the dates are Date objects.
  if (typeof a === 'string')
    a = new Date(a);
  if (typeof b === 'string')
    b = new Date(b);
  
  // Calculate the time difference in milliseconds, and return the result as a number of days.
  delta = Math.abs(b - a);
  return Math.ceil(delta / 86400000); // 1000 * 60 * 60 * 24
}
// *************************************************************************************************
// Return the price when the given monthly base price is modified with the given modifier. The
// modifier is a percentage, with a negative number signifying a discount.
static getModifiedPrice(basePrice, modifier)
{
  return Math.round(basePrice * (1.0 + (0.01 * modifier)));
}

// *************************************************************************************************
// Pad the given number with leading zeroes, to the given length. Return the padded number as a
// string.
static pad(number, length)
{
  number = String(number);
  while (number.length < length)
    number = '0' + number;
  return number;
}

// *************************************************************************************************
// Limit the given text to the given maximum length. If the text is longer, the final character will
// be replaced by an ellipsis character (...).
static curtail(text, length)
{
  text = String(text);
  if (text.length > length)
    return text.substring(0, length - 1) + '&hellip;';
  return text;
}

// *************************************************************************************************
// If the given source string is, or can be converted to, a non-empty string, return that string. If
// not, return the default value.
static getValidString(source, defaultValue)
{
  if ((typeof source === 'undefined') || (source === null))
    return defaultValue;
  source = String(source);
  if (source === '')
    return defaultValue;
  return source;
}

// *************************************************************************************************

static getValidInteger(source, defaultValue)
{
  source = parseInt(source, 10);
  if (isFinite(source))
    return source;
  return defaultValue;
}

// *************************************************************************************************

static getPositiveInteger(source, defaultValue)
{
  source = parseInt(source, 10);
  if (isFinite(source) && (source >= 0))
    return source;
  return defaultValue;
}

// *************************************************************************************************

static isValidIndex(index, array)
{
  index = parseInt(index, 10);
  return isFinite(index) && (index >= 0) && Array.isArray(array) && (index < array.length);
}

// *************************************************************************************************
// Return true if the given isoDate string holds a string in the "yyyy-mm-dd" format.
static isValidDate(isoDate)
{
  // Create a date based on the given string, and convert it to ISO format. If it matches the
  // original, the original had the correct format.
  return Utility.getIsoDate(new Date(isoDate)) === isoDate;
}

// *************************************************************************************************
// Return true if the given colour is a string that contains a valid CSS colour value.
static isValidColour(colour)
{
  // The first test checks for hex colour code (e.g., #FFF, #FFFFFF). The second test checks for RGB
  // or RGBA (e.g., rgb(255,255,255), rgba(255,255,255,1)). The third test checks for HSL or HSLA
  // (e.g., hsl(360,100%,50%), hsla(360,100%,50%,1)).
  return (/^#(?:[0-9a-fA-F]{3}){1,2}$/.test(colour)) ||
    (/^rgb(a?)\((\s*\d+\s*,\s*){2}\d+\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/.test(colour)) ||
    (/^hsl(a?)\(\s*\d+\s*,\s*\d+%\s*,\s*\d+%\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/.test(colour));
}

// *************************************************************************************************
// Return true if the given value is a valid payment method.
static isValidPaymentMethod(value)
{
  value = parseInt(value, 10);
  return (value === PAYMENT_METHOD_NETS) || (value === PAYMENT_METHOD_INVOICE) ||
    (value === PAYMENT_METHOD_NETS_THEN_INVOICE);
}

// *************************************************************************************************
// Return true if the given value is a valid product readiness status.
static isValidReadyStatus(value)
{
  value = parseInt(value, 10);
  return (value === st.ready.YES) || (value === st.ready.CHECK);
}

// *************************************************************************************************

static valueInArray(value, array)
{
  var i;

  if (Array.isArray(array))
  {
    for (i = 0; i < array.length; i++)
    {
      if (array[i] === value)
        return true;
    }
  }
  return false;
}

// *************************************************************************************************
// Make the array entries at the two given indexes switch positions.
static switchArrayEntries(array, index1, index2)
{
  var entry;

  index1 = parseInt(index1, 10);
  index2 = parseInt(index2, 10);
  if (Utility.isValidIndex(index1, array) && Utility.isValidIndex(index2, array))
  {
    entry = array[index1];
    array[index1] = array[index2];
    array[index2] = entry;
  }
}

// *************************************************************************************************

static getAddress(location)
{
  var o, p;

  o = new Array(5);
  p = 0;

  o[p++] = location[c.loc.STREET];
  o[p++] = ', ';
  o[p++] = location[c.loc.POSTCODE];
  o[p++] = ' ';
  o[p++] = location[c.loc.TOWN];
  o[p++] = ', ';
  o[p++] = location[c.loc.COUNTRY];
  return o.join('');
}

// *************************************************************************************************
// Return the index in the locations table of the location with the given ID, or -1 if it was not
// found. A table named "locations" is assumed to exist.
static getLocationIndex(id)
{
  var i;

  for (i = 0; i < locations.length; i++)
    if (locations[i][c.loc.ID] === id)
      return i;
  return -1;
}

// *************************************************************************************************
// Return the name of the location with the given ID, or the given default value if no such location
// was found. defaultValue is optional. If not provided, the function returns a non-breaking space.
// A table named "locations" is assumed to exist.
static getLocationName(id, defaultValue)
{
  var i;
  
  for (i = 0; i < locations.length; i++)
    if (locations[i][c.loc.ID] === id)
      return locations[i][c.loc.NAME];
  if (typeof defaultValue === 'undefined')
    return '&nbsp;';
  return defaultValue;
}

// *************************************************************************************************
// Return the index in the category table of the category with the given ID, or -1 if it was not
// found. A table named "categories" is assumed to exist.
static getCategoryIndex(id)
{
  var i;

  for (i = 0; i < categories.length; i++)
    if (categories[i][c.cat.ID] === id)
      return i;
  return -1;
}

// *************************************************************************************************
// Return the name of the category with the given ID, or the given defaultValue if no such
// category was found. defaultValue is optional. If not provided, the function returns a
// non-breaking space. A table named "categories" is assumed to exist.
static getCategoryName(id, defaultValue)
{
  var i;

  for (i = 0; i < categories.length; i++)
    if (categories[i][c.cat.ID] === id)
      return categories[i][c.cat.NAME];
  if (typeof defaultValue === 'undefined')
    return '&nbsp;';
  return defaultValue;
}

// *************************************************************************************************
// Return the name of the product type with the given ID, or the given defaultValue if no such
// product type was found. defaultValue is optional. If not provided, the function returns a
// non-breaking space. A table named "productTypes" is assumed to exist.
static getProductTypeName(id, defaultValue)
{
  var i;
  
  for (i = 0; i < productTypes.length; i++)
    if (productTypes[i][c.typ.ID] === id)
      return productTypes[i][c.typ.NAME];
  if (typeof defaultValue === 'undefined')
    return '&nbsp;';
  return defaultValue;
}

// *************************************************************************************************
// Return the index in the locations table of the location with the given ID, or -1 if it was not
// found. A table named "locations" is assumed to exist.
static getLocationIndex(id)
{
  var i;

  for (i = 0; i < locations.length; i++)
    if (locations[i][c.loc.ID] === id)
      return i;
  return -1;
}

// *************************************************************************************************
// Return the index in the subscriptions table of the subscription with the given ID, or -1 if it
// was not found. A table named "subscriptions" is assumed to exist.
static getSubscriptionIndex(id)
{
  var i;

  for (i = 0; i < subscriptions.length; i++)
    if (subscriptions[i][c.sub.ID] === id)
      return i;
  return -1;
}

// *************************************************************************************************
// Return the index in the products table of the product with the given ID, or -1 if it was not
// found. A table named "products" is assumed to exist.
static getProductIndex(id)
{
  var i;

  for (i = 0; i < products.length; i++)
    if (products[i][c.prd.ID] === id)
      return i;
  return -1;
}

// *************************************************************************************************
// Return true if the user, if he cancels a subscription right now, will continue the subscription
// until the end of this month only. If the method returns false, he will have to continue the
// subscription until the end of next month.
//
// A subscription can be cancelled at the end of this month if the current date is the 15th of the
// month, or earlier. The method depends on the customer's time being set correctly, so it may be
// wrong. However, the server has the final word on when the subscription will end.
static canCancelThisMonth()
{
  var currentDate;

  currentDate = new Date().getDate();
  return currentDate <= 15;
}

// *************************************************************************************************
// A set of radio buttons has different IDs for each button. However, they all share the same name.
// Locate all radio buttons with the given name, and return the value of the selected one. If they
// were not found, or if none was selected, return the defaultValue.
static getRadioButtonValue(name, defaultValue)
{
  var button, buttons;

  buttons = document.getElementsByName(name);
  for (button of buttons)
  {
    if (button.checked)
      return button.value;
  }
  return defaultValue;
}

// *************************************************************************************************
// Return HTML code for an edit box with a preceding label, enclosed in a form-element div.
//
// id is the id of the edit box, through which the value can be retrieved. It is mandatory, as it is
// used to link the label to the edit box.
//
// name is the name of the edit box, which will be passed to the server if the form is submitted. It
// is optional; you may pass null, in which case the name will not be included.
//
// label is the text displayed in front of the edit box. It is mandatory, but you may pass '&nbsp;'
// to display an empty string.
//
// value is the contents of the edit box. Pass null to not set a value, which will result in an
// empty box.
//
// handler is a string with the code that will be executed when the user presses a key while the
// edit box has focus, or when the edit box contents change. Pass null to set
// "enableSubmitButton();".
//
// labelClass is the class applied to the label. Pass null to set "standard-label".
// 
// editClass is the class applied to the label. Pass null to set "long-text".
// 
// isMandatory is a boolean flag that says whether the edit box must be filled in. If true, it will
// display a visible mark next to the label. It is optional, and may be omitted. The default value
// is true.
//
// isPassword is a boolean flag that says whether the edit box is used to input a password,
// in which case the characters will be masked. It is optional, and may be omitted. The  default
// value is false.
static getEditBox(id, name, label, value, handler, labelClass, editClass, isMandatory, isPassword)
{
  var o, p;

  if (!handler)
    handler = 'enableSubmitButton();';
  if (!labelClass)
    labelClass = 'standard-label';
  if (!editClass)
    editClass = 'long-text';
  if (isMandatory !== false)
    isMandatory = true;
  o = new Array(25);
  p = 0;

  o[p++] = '<div class="form-element"><label for="';
  o[p++] = String(id);
  o[p++] = '" class="';
  o[p++] = String(labelClass);
  o[p++] = '">';
  o[p++] = String(label);
  if (isMandatory)
    o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><input type="';
  if (isPassword)
    o[p++] = 'password';
  else
    o[p++] = 'text';
  o[p++] = '" id="';
  o[p++] = String(id);
  o[p++] = '" ';
  if (name)
  {
    o[p++] = 'name="';
    o[p++] = String(name);
    o[p++] = '" ';
  }
  if (value)
  {
    o[p++] = 'value="';
    o[p++] = String(value);
    o[p++] = '" ';
  }
  o[p++] = 'class="';
  o[p++] = String(editClass);
  o[p++] = '" onkeyup="';
  o[p++] = String(handler);
  o[p++] = '" onchange="';
  o[p++] = String(handler);
  o[p++] = '" /></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for a numeric edit box with a preceding label, enclosed in a form-element div.
//
// id is the id of the edit box, through which the value can be retrieved. It is mandatory, as it is
// used to link the label to the edit box.
//
// name is the name of the edit box, which will be passed to the server if the form is submitted. It
// is optional; you may pass null, in which case the name will not be included.
//
// label is the text displayed in front of the edit box. It is mandatory, but you may pass '&nbsp;'
// to display an empty string.
//
// value is the contents of the edit box. Pass null to not set a value, which will result in an
// empty box.
//
// min is the minimum value the number is allowed to have. Pass null in order to not set a minimum
// value.
//
// max is the maximum value the number is allowed to have. Pass null in order to not set a maximum
// value.
//
// handler is a string with the code that will be executed when the user presses a key while the
// edit box has focus, or when the edit box contents change. Pass null to set
// "enableSubmitButton();".
//
// labelClass is the class applied to the label. Pass null to set "standard-label".
// 
// editClass is the class applied to the label. Pass null to set "numeric".
// 
// isMandatory is a boolean flag that says whether the edit box must be filled in. If true, it will
// display a visible mark next to the label. It is optional, and may be omitted. The default value
// is true.
static getNumericEditBox(id, name, label, value, min, max, handler, labelClass, editClass,
  isMandatory)
{
  var o, p;

  if (!handler)
    handler = 'enableSubmitButton();';
  if (!labelClass)
    labelClass = 'standard-label';
  if (!editClass)
    editClass = 'numeric';
  if (isMandatory !== false)
    isMandatory = true;
  o = new Array(29);
  p = 0;

  o[p++] = '<div class="form-element"><label for="';
  o[p++] = String(id);
  o[p++] = '" class="';
  o[p++] = String(labelClass);
  o[p++] = '">';
  o[p++] = String(label);
  if (isMandatory)
    o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><input type="number" id="';
  o[p++] = String(id);
  o[p++] = '" ';
  if (name)
  {
    o[p++] = 'name="';
    o[p++] = String(name);
    o[p++] = '" ';
  }
  if (value)
  {
    o[p++] = 'value="';
    o[p++] = String(value);
    o[p++] = '" ';
  }
  if (min)
  {
    o[p++] = 'min="';
    o[p++] = String(min);
    o[p++] = '" ';
  }
  if (max)
  {
    o[p++] = 'max="';
    o[p++] = String(max);
    o[p++] = '" ';
  }
  o[p++] = 'class="';
  o[p++] = String(editClass);
  o[p++] = '" onkeyup="';
  o[p++] = String(handler);
  o[p++] = '" onchange="';
  o[p++] = String(handler);
  o[p++] = '" /></div>';

  return o.join('');
}

// *************************************************************************************************
// Return HTML code for an edit box with a preceding label, enclosed in a form-element div. The edit
// box is designed to let the user edit colours, and displays the currently selected colour in a box
// to the right of the edit box.
//
// id is the id of the edit box, through which the value can be retrieved. It is mandatory, as it is
// used to link the label to the edit box.
//
// name is the name of the edit box, which will be passed to the server if the form is submitted. It
// is optional; you may pass null, in which case the name will not be included.
//
// label is the text displayed in front of the edit box. It is mandatory, but you may pass '&nbsp;'
// to display an empty string.
//
// value is the contents of the edit box. Pass null to not set a value, which will result in an
// empty box.
//
// handler is a string with the code that will be executed when the user presses a key while the
// edit box has focus, or when the edit box contents change.
//
// labelClass is the class applied to the label. Pass null to set "extra-wide-label".
// 
// editClass is the class applied to the label. Pass null to set "colour-edit".
// 
// isMandatory is a boolean flag that says whether the edit box must be filled in. If true, it will
// display a visible mark next to the label. It is optional, and may be omitted. The default value
// is false.
static getColourEditBox(id, name, label, value, handler, labelClass, editClass, isMandatory)
{
  var o, p;

  if (!labelClass)
    labelClass = 'extra-wide-label';
  if (!editClass)
    editClass = 'colour-edit';
  o = new Array(31);
  p = 0;

  o[p++] = '<div class="form-element"><label for="';
  o[p++] = String(id);
  o[p++] = '" class="';
  o[p++] = String(labelClass);
  o[p++] = '">';
  o[p++] = String(label);
  if (isMandatory)
    o[p++] = Utility.getMandatoryMark();
  o[p++] = '</label><input type="text" id="';
  o[p++] = String(id);
  o[p++] = '" ';
  if (name)
  {
    o[p++] = 'name="';
    o[p++] = String(name);
    o[p++] = '" ';
  }
  if (value)
  {
    o[p++] = 'value="';
    o[p++] = String(value);
    o[p++] = '" ';
  }
  o[p++] = 'class="';
  o[p++] = String(editClass);
  o[p++] = '" onkeyup="updateColourPreview(\'';
  o[p++] = String(id);
  o[p++] = '\'); ';
  o[p++] = String(handler);
  o[p++] = '" onchange="updateColourPreview(\'';
  o[p++] = String(id);
  o[p++] = '\'); ';
  o[p++] = String(handler);
  o[p++] = '" /><div id="';
  o[p++] = String(id);
  o[p++] = '_preview" class="colour-preview" style="background-color: ';
  if (value)
    o[p++] = String(value);
  else
    o[p++] = '#000';
  o[p++] = '">&nbsp;</div></div>';

  return o.join('');
}

// *************************************************************************************************
// Return a hidden input element with the given name and value.
static getHidden(name, value)
{
  var o, p;

  o = new Array(5);
  p = 0;

  o[p++] = '<input type="hidden" name="';
  o[p++] = String(name);
  o[p++] = '" value="';
  o[p++] = String(value);
  o[p++] = '" />';

  return o.join('');
}

// *************************************************************************************************

static getMandatoryMark()
{
  return ' <span class="mandatory">*</span>';
}

// *************************************************************************************************
// Return an icon that represents the given message type. Use the MESSAGE_TYPE_ constants.
static getMessageTypeIcon(messageType)
{
  if (messageType === MESSAGE_TYPE_SMS)
    return '<i class="fa-solid fa-message-sms icon-blue"></i>';
  if (messageType === MESSAGE_TYPE_EMAIL)
    return '<i class="fa-solid fa-envelope icon-purple"></i>';
  return '<i class="fa-solid fa-message-question"></i>';
}

// *************************************************************************************************
// Return HTML code for a status label with text from the given text array, and colour from the
// given colour array. index is the index of the text and colour in the arrays.
//
// Alternately, if the index is not provided, or is null, text and colour are assumed to be strings,
// and will be used as is.
//
// If an icon is passed, for instance "fa-xmark", an icon will be displayed in front of the status
// label. icon can be either a string, or an array of strings indexed by index. icon is optional. If
// the string is empty, no icon will be displayed.
static getStatusLabel(text, colour, index, icon)
{
  var o, p, hasIcon;

  index = parseInt(index, 10);
  hasIcon = !!icon;
  if ((typeof index === 'undefined') || (index === null))
  {
    // There is no index. Use the text and colour as provided.
    text = String(text);
    colour = String(colour);
    if (hasIcon)
      icon = String(icon);
  }
  else
  {
    // There is an index. If it is valid, use it to find the text and colour.
    if (!Utility.isValidIndex(index, text) || !Utility.isValidIndex(index, colour))
      return '';
    text = String(text[index]);
    colour = String(colour[index]);
    if (hasIcon)
      icon = String(icon[index])
  }
  hasIcon = hasIcon && (icon !== '');

  // Generate the status label.
  o = new Array(8);
  p = 0;

  o[p++] = '<span class="status-label status-';
  o[p++] = colour;
  o[p++] = '">';
  if (hasIcon)
  {
    o[p++] = '<i class="fa-solid ';
    o[p++] = icon;
    o[p++] = '"></i> ';
  }
  o[p++] = text;
  o[p++] = '</span>';

  return o.join('');
}

// *************************************************************************************************

static getTimestamp()
{
  var now, o, p;

  now = new Date();
  o = new Array(13);
  p = 0;

  // Get the date in "yyyy-mm-dd" format.
  o[p++] = now.getFullYear();
  o[p++] = '-';
  o[p++] = Utility.pad(now.getMonth() + 1, 2);
  o[p++] = '-';
  o[p++] = Utility.pad(now.getDate(), 2);
  o[p++] = ' ';
  // Get the time, including milliseconds.
  o[p++] = Utility.pad(now.getHours(), 2);
  o[p++] = ':';
  o[p++] = Utility.pad(now.getMinutes(), 2);
  o[p++] = ':';
  o[p++] = Utility.pad(now.getSeconds(), 2);
  o[p++] = '.';
  o[p++] = Utility.pad(now.getMilliseconds(), 3);
  return o.join('');
}

// *************************************************************************************************
// Display the spinner, to indicate that the application is working. Then call the given handler.
// This method assumes that a variable called "spinner" exists. If the handler does not exist, the
// method will do nothing.
static displaySpinnerThen(handler)
{
  if (handler)
  {
    Utility.display(window['spinner']);
    setTimeout(handler, 10);
  }
}

// *************************************************************************************************
// Display the spinner, to indicate that the application is working. Then go to the given URL.
// This method assumes that a variable called "spinner" exists. If url is not a valid string, the
// method will do nothing.
static displaySpinnerThenGoTo(url)
{
  if ((typeof url === 'string') && (url !== ''))
  {
    Utility.display(window['spinner']);
    setTimeout(
      function ()
      {
        window.location.href = url;
      },
      10);
  }
}

// *************************************************************************************************
// Display the spinner, to indicate that the application is working. Then submit the given form.
// This method assumes that a variable called "spinner" exists. If the form does not exist, the
// method will do nothing.
static displaySpinnerThenSubmit(form)
{
  if (form)
  {
    Utility.display(window['spinner']);
    setTimeout(
      function ()
      {
        form.submit();
      },
      10);
  }
}

// *************************************************************************************************
// Hide the spinner, to indicate that the application has completed its work. This method assumes
// that a variable called "spinner" exists.
static hideSpinner()
{
  Utility.hide(spinner);
}

// *************************************************************************************************
// Return true if the two given arrays a and b contain the same items. Both arrays are expected to
// be sorted in the same order. No type conversion is performed.
static arraysEqual(a, b)
{
  var i;

  // Ensure a and b are both arrays.
  if (!Array.isArray(a) || !Array.isArray(b))
    return false;
  // Ensure they have the same length.
  if (a.length !== b.length)
    return false;
  // Ensure they contain the same elements.
  for (i = 0; i < a.length; i++)
  {
    if (a[i] !== b[i])
      return false;
  }
  return true;
}

/*
  If the arrays are known to contain numbers, and we desire to sort them so the order of items does
  not matter:

    a1 = [...a].sort((a, b) => a - b);
    b1 = [...b].sort((a, b) => a - b);
*/
// *************************************************************************************************
// Copy the contents of the given element to the clipboard. element is expected to be a text input
// element (or possibly a textarea), and can be either the element itself, or a string with the ID
// of the element. Return true if the operation succeeded.
static copyToClipboard(element)
{
  element = Utility.getElement(element);

  // Select the text in the input field. setSelectionRange is used for mobile compatibility.
  element.select();
  element.setSelectionRange(0, 99999);

  // Try to copy the text.
  try
  {
    return document.execCommand('copy');
  }
  catch (e)
  {
    return false;
  }
}

// *************************************************************************************************

}
