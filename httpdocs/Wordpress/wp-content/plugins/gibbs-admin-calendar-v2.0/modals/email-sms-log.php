<?php
global $wpdb;
$gibbs_email_log_table_name = $wpdb->prefix . 'gibbs_email_log';
$gibbs_sms_log_table_name = $wpdb->prefix . 'gibbs_sms_log';

// Fetching data from the database
$result_data = $wpdb->get_results("
    SELECT *
    FROM $gibbs_email_log_table_name
    where order_id = $record->order_id
    ORDER BY id DESC
");
$sms_data = $wpdb->get_results("
    SELECT *
    FROM $gibbs_sms_log_table_name
    where order_id = $record->order_id
    ORDER BY id DESC
");
function remove_style_tags($content) {
    return preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content); // Remove <style> tags and their content
}
?>
<style>
    .subject-td.dtr-control {
        display: flex;
        justify-content: left;
        word-spacing: normal;
        word-break: break-all;
        white-space: normal !important;
    }
</style>
<table id="SmsEmailLogTable" class="display responsive nowrap" style="width:100%">
    <thead>
        <tr>
            <th class="subject-th">Emne</th>
            <th>Tlf/Epost</th>
            <th>Epost innhold</th>
            <th>Tid sendt </th>
         <!--    <th>Status</th> -->
            
        </tr>
    </thead>
    <tbody>
        <?php foreach ($result_data as $result_d) { ?>
            <tr>
                <td class="subject-td" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap !important;">
                    <?php 
                    echo $result_d->delivery_status == 1 
                        ? '<i class="fa-solid fa-check" style="color: green; font-size: 18px; margin: 4px 10px 0px -2px;"></i>' 
                        : '<i class="fa-solid fa-times" style="color: red; font-size: 18px; margin: 4px 10px 0px -2px;"></i>'; 
                    ?>
                    <b><?php echo esc_html($result_d->subject); ?></b> 
                </td>


                <td><?php echo esc_html($result_d->sent_to_email); ?></td>
                <td>
                    <button type="button" class="btn btn-primary showDataContent">Åpne</button>
                    <div class="sms_email_content" style="display:none"><?php echo remove_style_tags($result_d->message);?></div>
                </td>
                <td><?php echo esc_html($result_d->sent_date); ?></td>
                <!-- <td><?php echo $result_d->delivery_status == 1 ? 'Yes' : 'No'; ?></td> -->
                
            </tr>
        <?php } ?>
        <?php foreach ($sms_data as $sms_d) { ?>
            <tr>
                <td class="subject-td" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap !important;">
                    <?php 
                    echo $sms_d->delivery_status == 1 
                        ? '<i class="fa-solid fa-check" style="color: green; font-size: 18px; margin: 4px 10px 0px -2px;"></i>' 
                        : '<i class="fa-solid fa-times" style="color: red; font-size: 18px; margin: 4px 10px 0px -2px;"></i>'; 
                    ?>
                    <b><?php echo esc_html("SMS"); ?></b> 
                </td>


                <td><?php echo $sms_d->country_code.esc_html($sms_d->phone); ?></td>
                <td>
                    <button type="button" class="btn btn-primary showDataContent">Åpne</button>
                    <div class="sms_email_content" style="display:none"><?php echo remove_style_tags($sms_d->message);?></div>
                </td>
                <td><?php echo esc_html($sms_d->send_date); ?></td>
                <!-- <td><?php echo $sms_d->delivery_status == 1 ? 'Yes' : 'No'; ?></td> -->
                
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
function initializeSmsEmailTable() {
    jQuery('#SmsEmailLogTable').DataTable({
        responsive: true,
        paging: false,
        searching: false,
        info: true,
        ordering: false,
    });
}

</script>
