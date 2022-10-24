<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once './configs/database.php';
require_once './includes/classes.php';

$api = new MyAPI($main_conn);

$all_products = $api->Read('products', 'all');
$user_name;
$item_rows;

$ip_address = $api->IP_address();
$remember_me = $api->Read('remember_me', 'set', 'ip_address', "$ip_address");
if (!empty($remember_me)) {
   $email = $remember_me[0]->email;
   $get_user = $api->Read('user', 'set', 'email', "$email");
   $_SESSION['users'] = $get_user;
}

if (isset($_SESSION['users'])) {
   $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $cart_items_row = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
   $user_name = $user_info[0]->firstname;

   $item_rows = $cart_items_row;
} else {
   $user_name = '';
   $item_rows = '';
}

$title = 'Home Page | Dairy Raisers';

require_once './includes/header.php';
require_once './includes/navbar.php';
?>

<!-- <script src="https://www.paypal.com/sdk/js?client-id=AQgNY8h_OleHBW0NqUKKc8l2YWqTqqabExw_NZp8s4AHa6QaeFBMiqwMNMZekCmCrMZbdd1XvDXwBm1p"></script> -->

<main class="container-fluid" style="width: 100%; height: 100%;">
   <div class="glide" style="width: 100%; height: 100%;">
      <div class="glide__track" data-glide-el="track" style="width: 100%; height: 100%;">
         <ul class="glide__slides" style="width: 100%; height: 100%;">
            <li class="glide__slide" style="width: 100%; height: 100%;">
               <div class="d-flex justify-content-between align-items-center" style="width: 100%; height: 100%;">
                  <div class="px-5 d-flex flex-column justify-content-center align-items-center gap-2">
                     <div class="mx-0 d-flex justify-content-between align-items-center ">
                        <img src="./img/company-logo.png" style="width: 200px;" alt="">
                        <div class="text-center" style="font-family: Aquino;">
                           <h1 style="font-size: 40px">Welcome To</h1>
                           <h1 style="font-size: 40px">Dairy Raisers!</h1>
                        </div>
                     </div>
                     <div class="mx-0 card py-2 text-center" style="background:inherit;  border: none;">
                        <div style="z-index: 5;"
                           class="card-body text-center d-flex flex-column justify-content-center align-items-center px-3 w-100 h-100">
                           <h2 style="font-family: Public Sans ExBold;font-size: 20px;letter-spacing: 6px;white-space: nowrap;"
                              class="text-uppercase first-letter-head">Always fresh from the utter.</h2>
                           <p style="font-family: Public Sans Light;font-size:14px;margin: 0;text-align: justify;"
                              class="card-text">The General Trias Dairy Raisers Multi-Purpose Cooperative promotes
                              dairying and dairy enterprise that imporves quality life of carabao raisers, from 7-21
                              dairy products and flavor variants.</p>
                           <div class="mt-4">
                              <a href="../../../shop/products.php?page=all" style="font-family: Public Sans ExBold;"
                                 class="btn  btn-primary me-2 px-4"><i class="me-2 bi bi-bag-check-fill"></i> Shop Now
                                 !</a>
                              <a href="#" class="btn  btn-outline-primary"><i class="me-2 bi bi-facebook"></i> Follow Us
                                 On Facebook</a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="d-flex justify-content-center align-items-center w-100 h-100 ">
                     <div class="w-100 h-100 d-flex justify-content-center align-items-center"
                        style="position: relative;">
                        <img src="./img/energizer.svg" class="img-fluid" alt="energizer"
                           style="width: 220px;top: 40%;right: 35%;z-index: 2;position: absolute;">
                        <img src="./img/pouring-milk.png" class="img-fluid" alt="pouring milk" style="width: 100%; ">
                     </div>

                  </div>
               </div>
            </li>
            <li class="glide__slide ">
               <div class="j d-flex flex-column justify-content-center align-items-center">
                  <h1>Milk taste so good</h1>
                  <h5>It does a body good!</h5>
               </div>
            </li>
            <li class="glide__slide">

               <div class="jumbotron d-flex flex-column justify-content-center align-items-center">
                  <h1>Have You Had Your Dairy Today?</h1>
                  <h5>Buffalo Milk is a Nutritional Purchase</h5>
               </div>
            </li>
         </ul>
      </div>

      <div class="btn-group-vertical glide__arrows position-absolute d-flex mx-0 px-0 rounded-start"
         data-glide-el="controls">
         <button class="glide__arrow btn btn-outline-dark glide__arrow--left" data-glide-dir="<"><i
               class="bi bi-chevron-double-left fw-bold fs-4 "></i></button>
         <button class="glide__arrow btn btn-outline-dark glide__arrow--right" data-glide-dir=">"><i
               class="bi bi-chevron-double-right fw-bold fs-4 "></i></button>
      </div>

      <div class="glide__bullets" data-glide-el="controls[nav]">
         <button class="glide__bullet" data-glide-dir="=0"></button>
         <button class="glide__bullet" data-glide-dir="=1"></button>
         <button class="glide__bullet" data-glide-dir="=2"></button>
      </div>
   </div>

</main>

<!-- FOOTER -->
<?php

require_once('./includes/footer.php');

if (isset($_SESSION['index-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['index-message']['title']; ?>",
   "<?= $_SESSION['index-message']['body']; ?>",
   "<?= $_SESSION['index-message']['type']; ?>"
);
</script>
<?php

endif;
unset($_SESSION['index-message']);
?>

<script>
const config = {
   type: "carousel",
   perView: 1
}
new Glide('.glide', config).mount()

// paypal.Buttons({
//     createOrder: (data, actions) => {

//         return actions.order.create({
//             purchase_units: [{
//                 amount: {
//                     value: "100",
//                     current_code : "PHP"
//                 },
//             }]
//         })
//     },
//     onApprove: (data, actions) => {
//         console.log('Data = '+data);
//         console.log('Actions = '+actions);

//         return actions.order.capture().then((details) => {
//             console.log('Details = '+details);
//         })
//     }
// }).render('#paypal-button-container');
</script>