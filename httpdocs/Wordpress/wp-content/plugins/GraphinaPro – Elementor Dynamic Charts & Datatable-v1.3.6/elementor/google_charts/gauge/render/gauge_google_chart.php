<?php
namespace Elementor;
if (!defined('ABSPATH')) exit;

$settings = $this->get_settings_for_display();
$title = (string)graphina_get_dynamic_tag_data($settings,'iq_gauge_google_chart_heading');
$description = (string)graphina_get_dynamic_tag_data($settings,'iq_gauge_google_chart_content');
if(isRestrictedAccess('gauge_google',$this->get_id(),$settings, true)) {
    if($settings['iq_gauge_google_restriction_content_type'] ==='password'){
        return true;
    }
    echo html_entity_decode($settings['iq_gauge_google_restriction_content_template']);
    return true;
}
?>

<div class="<?php echo $settings['iq_gauge_google_chart_card_show'] === 'yes' ? 'chart-card' : ''; ?>">
<div class="">
    <?php if ($settings['iq_gauge_google_is_card_heading_show'] && $settings['iq_gauge_google_chart_card_show']) { ?>
        <h4 class="heading graphina-chart-heading" style="text-align: <?php echo $settings['iq_gauge_google_card_title_align'];?>; color: <?php echo strval($settings['iq_gauge_google_card_title_font_color']);?>;"><?php echo html_entity_decode($title); ?></h4>
    <?php }
    if ($settings['iq_gauge_google_is_card_desc_show'] && $settings['iq_gauge_google_chart_card_show']) { ?>
        <p class="sub-heading graphina-chart-sub-heading" style="text-align: <?php echo $settings['iq_gauge_google_card_subtitle_align'];?>; color: <?php echo strval($settings['iq_gauge_google_card_subtitle_font_color']);?>;"><?php echo html_entity_decode($description); ?></p>
    <?php } ?>
</div>

<?php 
  graphina_filter_common($this,$settings,$this->get_chart_type());
?>
<div class="<?php echo $settings['iq_gauge_google_chart_border_show'] === 'yes' ? 'chart-box' : ''; ?>"  style="<?php echo in_array($settings['iq_gauge_google_chart_data_series_count'],[1,'1']) ? 'display: flex; justify-content: center; align-items: center' : ''?>">
    <div class="chart-texture" id='gauge_google_chart<?php esc_attr_e($this->get_id()); ?>' style="">
    </div>
</div>

</div>