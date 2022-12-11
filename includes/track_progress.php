<?php
function order_validate($key, $target)
{
   $result = '';
   if ($key >= $target) {
      $result = 'btn-danger';
   } else {
      $result = 'btn-secondary';
   }

   return $result;
}

function order_progress($status)
{
   $status_arr = array('order placement', 'preparing', 'on the way', 'delivered');

   $key = array_search($status, $status_arr);

   $order_placed = order_validate($key, 0);
   $preparing = order_validate($key, 1);
   $otg = order_validate($key, 2);
   $delivered = order_validate($key, 3);

?>
   <div class="d-flex flex-column justify-content-center align-items-center gap-0">
      <p style="font-size: 11px;" class="btn btn-sm <?= $order_placed; ?>">Order Placed</p>
      <p class="fa-solid fa-angles-down text-danger"></p>
      <p style="font-size: 11px;" class="btn btn-sm <?= $preparing; ?>">Preparing</p>
      <p class="fa-solid fa-angles-down text-danger"></p>
      <p style="font-size: 11px;" class="btn btn-sm <?= $otg; ?>">On The Way</p>
      <p class="fa-solid fa-angles-down text-danger"></p>
      <p style="font-size: 11px;" class="btn btn-sm <?= $delivered; ?>">Delivered</p>
   </div>
<?php
}
?>

<div class="table-responsive">
   <table class="table table-bordered table-hover table-striped" id="trackorderTable" style="width: 100%; font-size: 13px;">
      <thead>
         <tr>
            <th>Order ID</th>
            <th>Order Status</th>
            <th>Items</th>
            <th>Total</th>
            <th>Date</th>
         </tr>
      </thead>
      <tbody>
         <?php
         foreach ($user_order_track as $order_track) :
            $status_arr = array('order placement', 'preparing', 'on the way', 'delivered');
            echo $k = array_search($order_track->order_status, $status_arr);
         ?>
            <tr class="text-center align-middle">
               <td class="text-center fw-bolder"><?= $order_track->order_details_id; ?></td>
               <td class="text-center">
                  <div class="d-flex flex-column justify-content-center align-items-center gap-0">
                     <p style="font-size: 11px;" class="btn btn-sm <?= order_validate($k, 0); ?>">Order Placed</p>
                     <p class="fa-solid fa-angles-down text-danger"></p>
                     <p style="font-size: 11px;" class="btn btn-sm <?= order_validate($k, 1); ?>">Preparing</p>
                     <p class="fa-solid fa-angles-down text-danger"></p>
                     <p style="font-size: 11px;" class="btn btn-sm <?= order_validate($k, 2); ?>">On The Way</p>
                     <p class="fa-solid fa-angles-down text-danger"></p>
                     <p style="font-size: 11px;" class="btn btn-sm <?= order_validate($k, 3); ?>">Delivered</p>
                  </div>
                  <!-- <?= $order_track->order_status; ?> -->
               </td>
               <td>
                  <!-- <table class="d-flex flex-column justify-content-center align-items-center gap-2"> -->
                  <?php
                  foreach ($user_order_item as $item) :
                     if ($item->order_details_id == $order_track->order_details_id) :
                        $product = $api->Read('products', 'set', 'product_id', $item->product_id);
                  ?>
                        <table class="table w-100">
                           <tbody>
                              <tr>
                                 <td class="d-flex justify-content-center align-items-center gap-3">
                                    <div class="d-flex flex-column justify-content-center align-items-center gap-1">
                                       <img class="img-fluid logo" src="<?= $product[0]->img_url; ?>" alt="<?= $product[0]->productname; ?>">
                                       <p><?= $product[0]->productname; ?></p>
                                    </div>
                                    <p class="text-center fw-bold">x <?= $item->quantity; ?></p>
                                    <p>=</p>
                                    <p class="text-center fw-bolder">₱<?= $item->total; ?>.00</p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                        <!-- <div class="w-50 px-3 d-flex justify-content-between align-items-center gap-4">
                        <div class="d-flex flex-column justify-content-center align-items-center gap-1">
                           <img class="img-fluid logo" src="<?= $product[0]->img_url; ?>"
                              alt="<?= $product[0]->productname; ?>">
                           <p><?= $product[0]->productname; ?></p>
                        </div>
                        <p class="text-center">x <?= $item->quantity; ?></p>
                     </div> -->
                  <?php
                     endif;
                  endforeach;
                  ?>
                  <!-- </table> -->
               </td>
               <td class="fw-bolder">₱<?= $order_track->total; ?>.00</td>
               <td><?= $order_track->date; ?></td>
            </tr>
         <?php
         endforeach;
         ?>
      </tbody>
   </table>
</div>