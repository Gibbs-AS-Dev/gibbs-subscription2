<?php
namespace Elementor;
if (!defined('ABSPATH')) exit;

$settings = $this->get_settings_for_display();
$title = (string)graphina_get_dynamic_tag_data($settings,'iq_mixed_chart_heading');
$description = (string)graphina_get_dynamic_tag_data($settings,'iq_mixed_chart_content');

if(isRestrictedAccess('mixed',$this->get_id(),$settings,true)) {
    if($settings['iq_mixed_restriction_content_type'] ==='password'){
        return true;
    }
    echo html_entity_decode($settings['iq_mixed_restriction_content_template']);
    return true;
}
?>

<div class="<?php echo $settings['iq_mixed_chart_card_show'] === 'yes' ? 'chart-card' : ''; ?>">
    <div class="">
        <?php if ($settings['iq_mixed_is_card_heading_show'] && $settings['iq_mixed_chart_card_show']) { ?>
            <h4 class="heading graphina-chart-heading" style="text-align: <?php echo $settings['iq_mixed_card_title_align'];?>;  color: <?php echo strval($settings['iq_mixed_card_title_font_color']);?>;"><?php echo html_entity_decode($title); ?></h4>
        <?php }
        if ($settings['iq_mixed_is_card_desc_show'] && $settings['iq_mixed_chart_card_show']) { ?>
            <p class="sub-heading graphina-chart-sub-heading" style="text-align: <?php echo $settings['iq_mixed_card_subtitle_align'];?>;  color: <?php echo strval($settings['iq_mixed_card_subtitle_font_color']);?>;"><?php echo html_entity_decode($description); ?></p>
        <?php } ?>
    </div>
    <div class="<?php echo $settings['iq_mixed_chart_border_show'] === 'yes' ? 'chart-box' : ''; ?>" style="min-height: <?php echo $settings['iq_mixed_chart_height'].'px' ?>">
        <div class="chart-texture mixed-chart-<?php esc_attr_e($this->get_id()); ?>"></div>
    </div>
</div>