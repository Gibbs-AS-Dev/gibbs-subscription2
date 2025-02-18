<table class="table user-table table-hover align-items-center booking_datatable display nowrap" style="width:100%">
    <thead>
        <tr>

            <?php foreach ($active_columns as $key => $active_column) { ?>
            	<th class="border-bottom">
                	  <?php echo  $columns[$active_column];?>
                      <?php if($active_column == "sms_time")	{ ?>
                          <i class="fas fa-info-circle" title="Sms timer"></i>
                      <?php } ?>

                </th>
            <?php } ?>

            
        </tr>
    </thead>
    <tbody>
    	<?php 
    	   foreach ($booking_data as $data) {

            require(__DIR__."/data_tr.php");
    	?>
	            
        <?php } ?>
       
        
    </tbody>
 
</table>
<?php  if(isset($page) && isset($totalPages)){ ?>
<div class="row footer_booking">
    <div class="col-md-3">
        <p><!-- Viser <?php echo $page;?>  til <?php echo $totalPages;?> av  --><?php echo $total;?> treff</p>
    </div>
    <div class="col-md-9">

         <div class="pagination-booking">
              <?php
                    if($booking_type == "rec"){
                        $filter = 'filter_function';
                    }else{
                        $filter = 'manage_filter';
                    }
                       // variables for pagination links
                        $page_next  = $page < $totalPages ? $page + 1 : '';
                        $page_prev  = $page > 1 ? $page-1 : '';

                        // page links
                        $N = min($totalPages, 9);
                        $pages_links = array();

                        $tmp = $N;
                        if ($tmp < $page || $page > $N) {
                            $tmp = 2;
                        }
                        for ($i = 1; $i <= $tmp; $i++) {
                            $pages_links[$i] = $i;
                        }

                        if ($page > $N && $page <= ($totalPages - $N + 2)) {
                            for ($i = $page - 3; $i <= $page + 3; $i++) {
                                if ($i > 0 && $i < $totalPages) {
                                    $pages_links[$i] = $i;
                                }
                            }
                        }

                        $tmp = $totalPages - $N + 1;
                        if ($tmp > $page - 2) {
                            $tmp = $totalPages - 1;
                        }
                        for ($i = $tmp; $i <= $totalPages; $i++) {
                            if ($i > 0) {
                                $pages_links[$i] = $i;
                            }
                        }
                        ?>
                        <?php 
                        if($page_prev != "" && $page_prev > 0){ 
                            

                            ?>

                           <a href="javascript:void(0)" onClick="<?php echo $filter;?>('','','<?php echo $page_prev; ?>')">Forrige</a>
                       <?php } ?>
                        <?php $prev = 0; ?>
                           <?php foreach ($pages_links as $p) { ?>
                            <?php if (($p - $prev) > 1) { ?>
                                <a href="javascript:void(0)">...</a>
                            <?php } ?>
                            <?php $prev = $p; ?>

                            <?php
                            $style_active = '';
                            if ($p == $page) {
                                $style_active = 'active"';
                            }
                            ?>
                            <a class="<?php echo $style_active;?>" href="javascript:void(0)" onClick="<?php echo $filter;?>('','','<?php echo $p; ?>')"><?php echo $p; ?></a>
                        <?php } ?>
                        <?php 
                          if($page_next != ""){ ?>
                                <a href="javascript:void(0)" onClick="<?php echo $filter;?>('','','<?php echo $page_next; ?>')">Neste</a>
                        <?php } ?>        

        </div>
                
    </div>
</div>

<?php } ?>