<?php
namespace Elementor;
if (!defined('ABSPATH')) exit;

$settings = $this->get_settings_for_display();
$type = 'advance-datatable';

if(isRestrictedAccess('advance-datatable',$this->get_id(),$settings, true)) {
    if($settings['iq_advance-datatable_restriction_content_type'] ==='password'){
        return true;
    }
    echo html_entity_decode($settings['iq_advance-datatable_restriction_content_template']);
    return true;
}
?>
<div class="graphina-element <?php echo $settings['iq_advance-datatable_element_card_show'] === 'yes' ? 'element-card' : ''; ?>">
    <?php if ($settings['iq_' . $type . '_can_show_filter'] === 'yes') { ?>
        <input type="text" class="table-filter" id="table-filter-<?php esc_attr_e($this->get_id()); ?>"
               placeholder="<?php esc_attr_e('Search ...') ?>">
    <?php } ?>
    <div class="graphina-table graphina-table-<?php esc_attr_e($this->get_id()); ?> <?php echo $settings['iq_' . $type . '_table_responsive']==='yes' ? 'table-responsive' : '';?> ">
        <table class="datatable-<?php esc_attr_e($this->get_id()); ?>"></table>
    </div>
</div>
