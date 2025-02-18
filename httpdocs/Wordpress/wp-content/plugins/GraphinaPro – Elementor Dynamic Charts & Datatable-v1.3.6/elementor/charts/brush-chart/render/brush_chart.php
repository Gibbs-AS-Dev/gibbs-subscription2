<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) exit;

$settings = $this->get_settings_for_display();
$title = (string)graphina_get_dynamic_tag_data($settings,'iq_brush_chart_heading');
$description = (string)graphina_get_dynamic_tag_data($settings,'iq_brush_chart_content');

if(isRestrictedAccess('brush',$this->get_id(),$settings,true)) {
    if($settings['iq_brush_restriction_content_type'] ==='password'){
        return true;
    }
    echo html_entity_decode($settings['iq_brush_restriction_content_template']);
    return true;
}
?>

<div class="<?php echo $settings['iq_brush_chart_card_show'] === 'yes' ? 'chart-card' : ''; ?>">
    <div class="">
        <?php if ($settings['iq_brush_is_card_heading_show'] && $settings['iq_brush_chart_card_show']) { ?>
            <h4 class="heading graphina-chart-heading" style="text-align: <?php echo $settings['iq_brush_card_title_align'];?>; color: <?php echo strval($settings['iq_brush_card_title_font_color']);?>;"><?php echo html_entity_decode($title); ?></h4>
        <?php }
        if ($settings['iq_brush_is_card_desc_show'] && $settings['iq_brush_chart_card_show']) { ?>
            <p class="sub-heading graphina-chart-sub-heading" style="text-align: <?php echo $settings['iq_brush_card_subtitle_align'];?>; color: <?php echo strval($settings['iq_brush_card_subtitle_font_color']);?>;"><?php echo html_entity_decode($description); ?></p>
        <?php } ?>
    </div>
    <div class="<?php echo $settings['iq_brush_chart_border_show'] === 'yes' ? 'chart-box' : ''; ?>">
        <div class="brush-chart-<?php esc_attr_e($this->get_id()); ?>-1">

        </div>
        <div class="brush-chart-<?php esc_attr_e($this->get_id()); ?>-2">
        </div>
    </div>
</div>