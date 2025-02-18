<?php
namespace Elementor;
if (!defined('ABSPATH')) exit;
$settings = $this->get_settings_for_display();
$type = $this->get_chart_type();
$title = (string)graphina_get_dynamic_tag_data($settings, 'iq_org_google_chart_heading');
$description = (string)graphina_get_dynamic_tag_data($settings, 'iq_org_google_chart_content');
if (isRestrictedAccess('org_google', $this->get_id(), $settings, true)) {
    if ($settings['iq_org_google_restriction_content_type'] === 'password') {
        return true;
    }
    echo html_entity_decode($settings['iq_org_google_restriction_content_template']);
    return true;
}
?>


<style type='text/css'>
    #org_google_chart<?php esc_attr_e($this->get_id()); ?> .myNodeClass {
        text-align: <?php echo $settings['iq_' . $type . '_google_chart_node_text_align']; ?>;
    }

    #org_google_chart<?php esc_attr_e($this->get_id()); ?> .google-visualization-orgchart-connrow-small,
    #org_google_chart<?php esc_attr_e($this->get_id()); ?> .google-visualization-orgchart-connrow-medium,
    #org_google_chart<?php esc_attr_e($this->get_id()); ?> .google-visualization-orgchart-connrow-large {
        height: <?php echo intval($settings['iq_' . $type . '_google_chart_node_conn_height']).'px';?>;
    }

    #org_google_chart<?php esc_attr_e($this->get_id()); ?> .google-visualization-orgchart-linebottom {
        border-bottom-width: <?php echo intval($settings['iq_' . $type . '_google_chart_node_conn_width']).'px';?>;
        border-bottom-style: <?php echo$settings['iq_' . $type . '_google_chart_node_conn_style']?>;
        border-bottom-color: <?php echo$settings['iq_' . $type . '_google_chart_node_conn_color']?>;
    }

    #org_google_chart<?php esc_attr_e($this->get_id()); ?> .google-visualization-orgchart-lineright {
        border-right-width: <?php echo intval($settings['iq_' . $type . '_google_chart_node_conn_width']).'px';?>;
        border-right-style: <?php echo $settings['iq_' . $type . '_google_chart_node_conn_style']?>;
        border-right-color: <?php echo $settings['iq_' . $type . '_google_chart_node_conn_color']?>;
    }

    #org_google_chart<?php esc_attr_e($this->get_id()); ?> .google-visualization-orgchart-lineleft {
        border-left-width: <?php echo intval($settings['iq_' . $type . '_google_chart_node_conn_width']).'px';?>;
        border-left-style: <?php echo $settings['iq_' . $type . '_google_chart_node_conn_style']?>;
        border-left-color: <?php echo $settings['iq_' . $type . '_google_chart_node_conn_color']?>;
    }

    /* #org_google_chart
    <?php esc_attr_e($this->get_id()); ?>
     .google-visualization-orgchart-noderow-medium .myNodeClass{
                 background:purple;
            }*/


</style>


<div class="<?php echo $settings['iq_org_google_chart_card_show'] === 'yes' ? 'chart-card' : ''; ?>">
    <div class="">
        <?php if ($settings['iq_org_google_is_card_heading_show'] && $settings['iq_org_google_chart_card_show']) { ?>
            <h4 class="heading graphina-chart-heading"
                style="text-align: <?php echo $settings['iq_org_google_card_title_align']; ?>; color: <?php echo strval($settings['iq_org_google_card_title_font_color']); ?>"><?php echo html_entity_decode($title); ?></h4>
        <?php }
        if ($settings['iq_org_google_is_card_desc_show'] && $settings['iq_org_google_chart_card_show']) { ?>
            <p class="sub-heading graphina-chart-sub-heading"
               style="text-align: <?php echo $settings['iq_org_google_card_subtitle_align']; ?>; color: <?php echo strval($settings['iq_org_google_card_subtitle_font_color']); ?>;"><?php echo html_entity_decode($description); ?></p>
        <?php } ?>
    </div>
    <?php
    graphina_filter_common($this, $settings, $this->get_chart_type());
    ?>
    <div class="<?php echo $settings['iq_org_google_chart_border_show'] === 'yes' ? 'chart-box' : ''; ?>">
        <!-- <?php print_r("hi"); ?> -->
        <div class="" id='org_google_chart<?php esc_attr_e($this->get_id()); ?>'>
        </div>
    </div>
</div>





