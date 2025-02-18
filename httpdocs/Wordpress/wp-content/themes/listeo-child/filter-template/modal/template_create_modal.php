<div id="templateCreateModal" class="modal template_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close close_user">&times;</span>
      <h2><?php  echo __("Opprett ny visning","Gibbs");?></h2>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger alert_error_message" role="alert" style="display: none"></div>
      <div class="alert alert-success alert_success_message" role="alert" style="display: none"></div>
      <form method="post" class="template_form" action="javascript:void(0)">
          <input type="hidden" name="action" value="save_template">
          <input type="hidden" name="template_type" value="<?php echo $filter_template_type;?>">
          <?php
          global $wp;
          $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
          ?>
          <input type="hidden" name="current_page" value="<?php echo $current_url;?>">
          <div class="row">
            
            <div class="form-group col-sm-12">
              <label><?php echo __("Visnings navn","gibbs");?></label>
              <input class="form-control" name="template_name" type="text" placeholder="<?php echo __("Skriv inn visnings navn","gibbs");?>" required>
            </div>
            <div class="form-group col-sm-12 template_submit_flex">
              <div></div>
              <div class="right-btn">
                <button class="form-control1 close_user close_template_btn" type="button" ><?php  echo __("Cancel","Gibbs");?></button>
                <input class="form-control submit_btn" type="submit" value="<?php echo __("Save","Gibbs");?>">
              </div>
            </div>
          </div>
      </form>
    </div>
  </div>

</div>

