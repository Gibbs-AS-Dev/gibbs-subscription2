<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/data/live_data_manager.php';
// Load components.
require_once $_SERVER['DOCUMENT_ROOT'] . '/subscription/components/utility/utility.php';

class Email_Sms_Log_Data_Manager extends Live_Data_Manager
{
  // *******************************************************************************************************************
  // *** Fields.
  // *******************************************************************************************************************
  // The name of the database table in which data items for the SMS log are stored.
  protected $database_table_for_sms = '';

  // The name of the database table in which data items for the e-mail log are stored.
  protected $database_table_for_email = '';

  // *******************************************************************************************************************
  // *** Constructors.
  // *******************************************************************************************************************

  public function __construct($new_access_token)
  {
    global $wpdb;

    parent::__construct($new_access_token);
    $this->database_table_for_sms = $wpdb->prefix . 'gibbs_sms_log';
    $this->database_table_for_email = $wpdb->prefix . 'gibbs_email_log';
  }

  // *******************************************************************************************************************
  // *** Public methods.
  // *******************************************************************************************************************
  // Return a combined list of e-mail and SMS messages that have been sent to customers, as a string containing a
  // Javascript array declaration. Use the c.log constants.
  public function read()
  {
    // Read SMS and e-mail messages.
    $message_log = array_merge($this->read_sms_log(), $this->read_email_log());

    // Write the Javascript table.
    $table = "[";
    if (Utility::non_empty_array($message_log))
    {
      foreach ($message_log as $message)
      {
        $table .= "[";
        $table .= $message['id'];
        $table .= ", ";
        $table .= $message['message_type'];
        $table .= ", ";
        $table .= $message['user_id'];
        $table .= ", ";
        $table .= $message['subscription_id'];
        $table .= ", '";
        $table .= $message['product_name'];
        $table .= "', '";
        $table .= $message['recipient'];
        $table .= "', '";
        $table .= $message['header'];
        $table .= "', '";
        $table .= $message['content'];
        $table .= "', '";
        $table .= $message['time_sent'];
        $table .= "', ";
        $table .= var_export($message['delivered'], true);
        $table .= ", '";
        $table .= $message['error_message'];
        $table .= "'],";
      }
      $table = Utility::remove_final_comma($table);
    }
    $table .= "]";
    return $table;
  }

  // *******************************************************************************************************************
  // *** Protected methods.
  // *******************************************************************************************************************
  // Read from the database a list of text messages that were sent to customers. Return them in the format specified by
  // the parse_sms_log_results method.
  protected function read_sms_log()
  {
    global $wpdb;

    // Compose and submit request to get the SMS log.
    $sql = "
      SELECT
        sms.id AS id,
        sms.user_id AS user_id,
        sms.subscription_id AS subscription_id,
        sms.message AS content,
        sms.country_code AS country_code,
        sms.phone AS phone,
        sms.send_date AS time_sent,
        sms.delivery_status AS delivery_status,
        sms.error_message AS error_message,
        COALESCE(p.post_title, '') AS product_name
      FROM
        {$this->database_table_for_sms} sms
      LEFT JOIN
        subscriptions s ON s.id = sms.subscription_id
      LEFT JOIN
        {$wpdb->prefix}posts p ON p.ID = s.product_id
      WHERE
        sms.owner_id = {$this->get_user_group_user_id()};
    ";
    $results = $wpdb->get_results($sql, ARRAY_A);

    // Parse the results, and return a table of message objects that are the same for both e-mail and SMS messages.
    return $this->parse_sms_log_results($results);
  }

  // *******************************************************************************************************************
  // Parse the results of a database query that returns SMS messages sent. Return an array of messages, each of which
  // will be an array with the following fields:
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
  //
  // These are the same fields as returned by the parse_email_log_results method.
  protected function parse_sms_log_results($results)
  {
    $messages = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $sms)
      {
        if (is_numeric($sms['subscription_id']))
        {
          $subscription_id = intval($sms['subscription_id']);
        }
        else
        {
          $subscription_id = -1;
        }
        if (empty($sms['error_message']))
        {
          $error_message = '';
        }
        else
        {
          $error_message = $sms['error_message'];
        }
        $messages[] = array(
          'id' => intval($sms['id']),
          'message_type' => Utility::MESSAGE_TYPE_SMS,
          'user_id' => intval($sms['user_id']),
          'subscription_id' => $subscription_id,
          'product_name' => $sms['product_name'],
          'recipient' => '+' . $sms['country_code'] . ' ' . $sms['phone'],
          'header' => '',
          'content' => $sms['content'],
          'time_sent' => $sms['time_sent'],
          'delivered' => $sms['delivery_status'] === '1',
          'error_message' => $error_message
        );
      }
    }
    return $messages;
  }

  // *******************************************************************************************************************
  // Read from the database a list of e-mails that were sent to customers. Return them in the format specified by the
  // parse_email_log_results method.
  protected function read_email_log()
  {
    global $wpdb;

    // Compose and submit request to get the e-mail log.
    $sql = "
      SELECT
        m.id AS id,
        m.user_id AS user_id,
        m.subscription_id AS subscription_id,
        m.header AS header,
        m.message AS content,
        m.sent_to_email AS email,
        m.sent_date AS time_sent,
        m.delivery_status AS delivery_status,
        m.error_message AS error_message,
        COALESCE(p.post_title, '') AS product_name
      FROM
        {$this->database_table_for_email} m
      LEFT JOIN
        subscriptions s ON s.id = m.subscription_id
      LEFT JOIN
        {$wpdb->prefix}posts p ON p.ID = s.product_id
      WHERE
        m.owner_id = {$this->get_user_group_user_id()};
    ";
    $results = $wpdb->get_results($sql, ARRAY_A);

    // Parse the results, and return a table of message objects that are the same for both e-mail and SMS messages.
    return $this->parse_email_log_results($results);
  }

  // *******************************************************************************************************************
  // Parse the results of a database query that returns SMS messages sent. Return an array of messages, each of which
  // will be an array with the following fields:
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
  //
  // These are the same fields as returned by the parse_sms_log_results method.
  protected function parse_email_log_results($results)
  {
    $messages = array();
    if (Utility::non_empty_array($results))
    {
      foreach ($results as $email)
      {
        if (is_numeric($email['subscription_id']))
        {
          $subscription_id = intval($email['subscription_id']);
        }
        else
        {
          $subscription_id = -1;
        }
        if (empty($email['error_message']))
        {
          $error_message = '';
        }
        else
        {
          $error_message = $email['error_message'];
        }
        $messages[] = array(
          'id' => intval($email['id']),
          'message_type' => Utility::MESSAGE_TYPE_EMAIL,
          'user_id' => intval($email['user_id']),
          'subscription_id' => $subscription_id,
          'product_name' => $email['product_name'],
          'recipient' => $email['email'],
          'header' => $email['header'],
          'content' => $email['content'],
          'time_sent' => $email['time_sent'],
          'delivered' => is_numeric($email['delivery_status']) && (intval($email['delivery_status']) === 1),
          'error_message' => $error_message
        );
      }
    }
    return $messages;
  }

  // *******************************************************************************************************************
}
?>