<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';

$user_name;

$item_rows;

if (isset($_SESSION['users'])) {

   header('Location: ../home.php');
} else {

   $user_name = '';

   $item_rows = '';
}

$title = 'Login | Dairy Raisers';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
function statusChangeCallback(response) { // Called with the results from FB.getLoginStatus().
   console.log('statusChangeCallback');
   console.log(response); // The current login status of the person.
   if (response.status === 'connected') { // Logged into your webpage and Facebook.
      testAPI();
   } else { // Not logged into your webpage or we are unable to tell.
      document.getElementById('status').innerHTML = 'Please log ' +
         'into this webpage.';
   }
}


function checkLoginState() { // Called when a person is finished with the Login Button.
   FB.getLoginStatus(function(response) { // See the onlogin handler
      statusChangeCallback(response);
   });
}


window.fbAsyncInit = function() {
   FB.init({
      appId: '793492625208638',
      cookie: true, // Enable cookies to allow the server to access the session.
      xfbml: true, // Parse social plugins on this webpage.
      version: 'v15.0' // Use this Graph API version for this call.
   });


   FB.getLoginStatus(function(response) { // Called after the JS SDK has been initialized.
      statusChangeCallback(response); // Returns the login status.
   });
};

function testAPI() { // Testing Graph API after login.  See statusChangeCallback() for when this call is made.
   console.log('Welcome!  Fetching your information.... ');
   FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
         'Thanks for logging in, ' + response.name + '!';
   });
}
</script>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
   src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v15.0&appId=793492625208638&autoLogAppEvents=1"
   nonce="ZBGCll43"></script>

<!-- login content -->
<main style="width: 500px;margin-top: 80px;margin-bottom: 20px;" class="container-fluid">
   <!-- main row -->
   <div class="row d-flex justify-content-center align-items-center">
      <!-- column 2 -->
      <div class="border px-3 py-4 bg-light">
         <form action="../validation/login-process.php" method="POST" class="d-flex flex-column px-2">
            <div class="d-flex flex-column justify-content-center align-items-center">
               <img src="../img/company-logo.png" class="img-fluid logo" alt="company logo">
               <p class="fs-5 text-center mx-auto mb-4 lead">Login to your Dairy Raisers Account</p>
            </div>
            <!-- ALERT MESSAGE -->
            <?php if (isset($_POST['submit'])) : ?>
            <div class="alert alert-danger"><?= $reg_ErrorMsg['invalid-credentials']; ?> <i
                  class="bi bi-exclamation-circle"></i></div>
            <?php endif; ?>
            <div class="form-floating mb-3">
               <input type="text" name="email" id="email" class="form-control form-control-sm"
                  placeholder="enter email address" required>
               <label for="email">Email Address</label>
            </div>
            <div class="form-floating mb-3">
               <input type="password" name="password" id="password" class="form-control form-control-sm"
                  placeholder="enter password" required>
               <label for="password">Enter Password</label>
            </div>
            <div class="mb-4 d-flex justify-content-between align-items-center user-select-none">
               <a href="./forgot_password.php" class="lead btn btn-sm btn-outline-primary">Forgot Password ?</a>
               <div class="form-check text-primary">
                  <input class="form-check-input" type="checkbox" id="remember_me">
                  <label class="form-check-label" for="remember_me">
                     Remember Me
                  </label>
               </div>
            </div>
            <div class="mb-3 d-flex justify-content-center align-items-center">
               <p>Not yet register? <a href="../entry/signup.php">Sign up Here</a></p>
            </div>
            <!-- submit -->
            <button class="btn btn-primary mb-3" type="submit" name="login"><i class="bi bi-door-open-fill"></i>
               Login</button>
         </form>
      </div>
   </div>
</main>

<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>

<script>
function handleCredentialResponse(response) {
   const responsePayLoad = decodeJwtResponse(response.credential);

   console.log(responsePayLoad);

   console.log('ID: ' + responsePayLoad.sub); // Do not send to your backend! Use an ID token instead.
   console.log('Full Name: ' + responsePayLoad.name);
   console.log('Given Name: ' + responsePayLoad.given_name);
   console.log('Family Name: ' + responsePayLoad.family_name);
   console.log('Image URL: ' + responsePayLoad.picture);
   console.log('Email: ' + responsePayLoad.email);

   var responseArray = [];
   responseArray['email'] = responsePayLoad.email;
   responseArray['firstname'] = responsePayLoad.given_name;
   responseArray['lastname'] = responsePayLoad.family_name;


   window.location.href =
      `../validation/login-process.php?google_signin=true&givenname=${responsePayLoad.given_name}&familyname=${responsePayLoad.family_name}&email=${responsePayLoad.email}`;

}

function decodeJwtResponse(data) {
   var tokens = data.split(".");
   return JSON.parse(atob(tokens[1]));
}
</script>
<!-- FOOTER -->

<?php

require_once('../includes/footer.php');

if (isset($_SESSION['login-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['login-message']['title']; ?>",
   "<?= $_SESSION['login-message']['body']; ?>",
   "<?= $_SESSION['login-message']['type']; ?>"
);
</script>
<?php endif;
unset($_SESSION['login-message']);

?>

<?php
if (isset($_SESSION['google_not_bind'])) {

?>
<script>
swal({
   title: "Your Google does not contain Dairy Raisers Account",
   text: "We detected that <?= $_SESSION['google_not_bind']; ?> is not yet binded into Dairy Raisers Account. Would you like to sign up this Google?",
   icon: "warning",
   closeOnClickOutside: false,
   buttons: ["No", "Sign Up"],
}).then((response) => {
   if (response) {
      console.log('success');
      window.location.href =
         `./signup.php?google_not_bind=true&givenname=<?= $_SESSION['google_given_name']; ?>&familyname=<?= $_SESSION['google_family_name']; ?>&email=<?= $_SESSION['google_not_bind']; ?>`;
   } else {
      console.log('failed');
   }
   <?php
         unset($_SESSION['google_not_bind']);
         unset($_SESSION['google_given_name']);
         unset($_SESSION['google_family_name']);
         ?>
})
</script>

<?php
}
?>