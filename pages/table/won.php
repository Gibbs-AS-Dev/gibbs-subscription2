<?php
  global $wpdb;
  $table_subscription_manual_requests = "subscription_manual_requests";
  $requests = $wpdb->get_results("select * from $table_subscription_manual_requests where status='approved'  order by id DESC");
?> 
<div class="vs-hidden main-table-bk">
  <div class="form-group has-search">
    <span class="fa fa-search form-control-feedback"></span>
    <input type="text" class="form-control" placeholder="Search" id="searchWon">
  </div>
  <table id="won_bk" class="display nowrap" style="width:100%">
      <thead>
          <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Created</th>
              <th>Size</th>
              <th class="comment">Comment</th>
              <th>Status</th>
              <th>Action</th>
          </tr>
      </thead>
      <tbody>
          <?php foreach ($requests as  $request) { ?>
            <tr>
                <td><?php echo $request->name;?></td>
                <td><?php echo $request->email;?></td>
                <td><?php echo $request->phone;?></td>
                <td><?php echo date("Y-m-d H:i:s",strtotime($request->created_at));?></td>
                <td><?php echo $request->size;?></td>
                <td class="comment"><?php echo $request->comment;?></td>
                <td><span class="status-btn <?php echo $request->status;?>"><?php echo $request->status;?></span></td>
                <td>
                  <form method="post" action="">
                    <input type="hidden" name="request_id" value="<?php echo $request->id;?>">
                    <input type="hidden" name="active" value="won">
                    <select class="select-items status_change" name="status">
                       <option value="" style="display:none">...</option>
                       <option value="waiting">Waiting</option>
                       <option value="approved">Approved</option>
                       <option value="cancelled">Cancelled</option>
                    </select>
                  </form>
                </td>
            </tr>
          <?php } ?>
      </tbody>
  </table>
</div>
