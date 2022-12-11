<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../configs/database.php';
require_once '../includes/classes.php';

$api = new MyAPI($main_conn);

if (isset($_POST['order_call'])) {
   $result = array();

   $track_qry = "SELECT * FROM `order_track` ORDER BY `date` DESC";
   $details_qry = "SELECT * FROM `order_details` ORDER BY `date` DESC";
   $items_qry = "SELECT * FROM `order_items` ORDER BY `date` DESC";

   $track_stmt = $main_conn->prepare($track_qry);
   $detail_stmt = $main_conn->prepare($details_qry);
   $item_stmt = $main_conn->prepare($items_qry);

   $track_stmt->execute();
   $detail_stmt->execute();
   $item_stmt->execute();


   $order_track = $track_stmt->fetchAll();
   $order_detail = $detail_stmt->fetchAll();
   foreach ($order_track as $order) :
      $order_item = $api->Read('order_items', 'set', 'order_details_id', $order->order_details_id);
      $status_arr = array('order placement', 'preparing', 'on the way', 'delivered');
      $k = array_search($order->order_status, $status_arr);
      $date = date("l , m/d/Y - h:i:sa", strtotime($order->date));
      $index = 1;
      $new_order = '';
      $text_order = '';
      switch ($order->order_status) {
         case 'order placement':
            $new_order = 'bg-danger text-light';
            $text_order = 'text-danger';
            break;
         case 'preparing':
            $new_order = 'bg-warning';
            $text_order = 'text-warning';
            break;
         case 'on the way':
            $new_order = 'bg-primary text-light';
            $text_order = 'text-primary';
            break;
         case 'delivered':
            $new_order = 'bg-success';
            $text_order = 'text-success';
            break;
      }
?>
<div class="card pb-2">
   <div class="card-body p-0 d-flex flex-column justify-content-between align-items-center">
      <table class="border table table-sm table-borderless table-light" style="font-size: 12.5px;">
         <p class="card-title mt-1 text-center w-100 <?= $new_order; ?>">Order ID: <?= $order->order_details_id; ?></p>
         <thead class="w-100" style="font-size: 12.5px;">
            <p class="fw-bold" style="font-size: 12.5px;">Placed at <?= $date; ?></p>
            <tr class="border">
            </tr>
            <p style="font-size: 12.5px;">Delivery from <?= $order->user_address; ?></p>
            <p style="font-size: 12.5px;">Order Details:</p>
            <tr class="border">
            </tr>
            <tr>
               <th>Item</th>
               <th>Qty</th>
               <th>Price</th>
            </tr>
         </thead>
         <tr class="border">
         </tr>
         <tbody>
            <?php
                  foreach ($order_item as $item) :
                     if ($item->order_details_id == $order->order_details_id) :
                        $product = $api->Read('products', 'set', 'product_id', $item->product_id);
                  ?>
            <tr>
               <td><?= $product[0]->productname; ?></td>
               <td><?= $item->quantity; ?></td>
               <td>₱<?= $item->total; ?>.00</td>
            </tr>
            <?php
                     endif;
                  endforeach;
                  ?>
         </tbody>
         <tr class="border">
         </tr>
         <tfoot>
            <tr>
               <td colspan="2">Sub Total</td>
               <td>₱<?= $order->total; ?>.00</td>
            </tr>
            <tr>
               <td colspan="2">Delivery Fee</td>
               <td>₱50.00</td>
            </tr>
            <tr class="fw-bolder fs-5">
               <td colspan="2">Total</td>
               <td>₱<?= $order->total + 50; ?>.00</td>
            </tr>
         </tfoot>
      </table>
      <div class="d-flex justify-content-between align-items-center w-100">
         <p style="font-size: 12.5px;">status: <span id="order_status<?= $index; ?>"
               class="<?= $text_order; ?>"><?= $order->order_status; ?></span></p>
         <a href="../../Admin/process/process-order-status.php?process-order-status-id=<?= $order->order_details_id; ?>"
            class="btn btn-sm btn-success btn-process" type="button" data-target="<?= $order->order_details_id; ?>"><i
               class="fa-solid fa-forward"></i></a>
      </div>
   </div>
</div>
<?php
      $index++;
   endforeach;
}
?>