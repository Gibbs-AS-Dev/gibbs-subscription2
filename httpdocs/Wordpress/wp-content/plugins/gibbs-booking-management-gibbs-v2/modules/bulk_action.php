<div class="search-box-inner bulk_action">
	<span class="user_icon"><i class="fa fa-list" aria-hidden="true"></i></span> 
	<div class="dropdown">
	  <button class="dropbtn"><span class="filter_text">Bulk Actions</span> <span class="count_filter"></span></button>
	  <div id="myDropdown" class="dropdown-content">
	  	<div class="outer-actions1">
	  		<?php if($active == "waiting"){ ?>
		    	<p style="color: #008474">Accept <i class="fa fa-check" ></i></p>
		    <?php } ?>
		    <?php if($active == "waiting" || $active == "approved" || $active == "invoice"){ ?>
	    	   <p style="color: red">Decline <i class="fa fa-remove" ></i></p>
	        <?php }else{ ?>
			        <div class="col-xs-12 col-md-12 other_booking">
		                  <i class="fa fa-exclamation-circle"></i><span>Please open the different tab to see available bulk actions.</span>
		            </div>
		        <style type="text/css">
		        	.bulk_action .dropdown-content {

		        		min-width: 571px; 

		        	}
		        	.bulk_action .outer-actions1 {

		        		padding: 0 10px;

		        	}
		        	
		        </style>
	        <?php } ?>	
	    </div>
	  </div>
	</div>
</div>