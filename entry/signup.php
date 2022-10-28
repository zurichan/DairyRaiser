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

$title = 'Signup | Dairy Raisers';
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
<!-- signup content -->
<main style="width: 700px;margin-top: 70px;margin-bottom: 0;" class=' container-fluid'>
   <!-- main row -->
   <div class="row border px-1 py-3 d-flex justify-content-between align-items-center">
      <!-- column 1 -->
      <div class="col bg-light">
         <form action="../validation/signup-process.php" method="POST" class="needs-validation d-flex flex-column px-3"
            novalidate>
            <div class="d-flex flex-column justify-content-center align-items-center">
               <img src="../img/company-logo.png" class="img-fluid logo" alt="company logo">
               <p class="fs-5 text-center mx-auto mb-4 lead">Create Your Dairy Raisers Account</p>
            </div>
            <?php
            if (isset($_SESSION['google_signup'])) {
            ?>
            <div class="row mb-3">
               <div class="col">
                  <div class="d-flex justify-content-between align-items-center mb-3 border rounded px-3 py-2">
                     <div class="d-flex justify-content-start align-items-center">
                        <img src="<?= $_SESSION['picture']; ?>" class="rounded-circle" style="width: 80px;"
                           alt="user picture">
                        <div class="ms-3 text-left">
                           <h5><?= $_SESSION['givenname'] . ' ' . $_SESSION['familyname']; ?></h5>
                           <p><?= $_SESSION['email'] ?></p>
                        </div>
                     </div>
                     <div class="">
                        <p><i class="fa-brands fa-google-plus"></i> sign up</p>
                        <a href="../validation/signup-process.php?google_signup_cancel=true"
                           class="btn btn-primary">Cancel</a>
                     </div>
                  </div>
                  <!-- PHONE NUMBER -->
                  <div class="col mb-3">
                     <label for="phoneNumber" class="form-label">Phone Number:</label>
                     <div class="input-group has-validation">
                        <span class="input-group-text">+63</span>
                        <input placeholder="9XX XXX XXXX" aria-describedby="phone" type="number" name="phoneNumber"
                           id="phoneNumber" class=" form-control">
                        <div class="ms-3 invalid-feedback"></div>
                     </div>
                  </div>
                  <!-- PASSWORD AND RETYPE PASSWORD -->
                  <div class="row mb-4">
                     <div class="col mt-2">
                        <label for="password" class="form-label">Password:</label>
                        <div class="input-group has-validation">
                           <button type="button" id="icon-click" class="btn btn-outline-secondary">
                              <i id="eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                           <input placeholder="Type Your Password" type="password" name="password" id="password"
                              class="form-control">
                           <div class="ms-3 invalid-feedback"></div>
                        </div>
                     </div>
                     <!-- RETYPE -->
                     <div class="col mt-2">
                        <label for="password" class="form-label">Retype Password:</label>
                        <div class="input-group has-validation">
                           <button type="button" id="re-icon-click" class="btn btn btn-outline-secondary">
                              <i id="re-eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                           <input placeholder="Re-Enter Your Password" type="password" name="rpassword" id="rpassword"
                              class="form-control">
                           <div class="ms-3 invalid-feedback"></div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col">
                        <div class=" d-flex flex-column justify-content-center align-items-center gap-3 mt-4">
                           <!-- SIGN UP BUTTON -->
                           <button class="btn btn-primary btn-block" type="submit" name="signup-submit" id="submit"><i
                                 class="fa-solid fa-user-plus me-2"></i> Sign Up</button>
                           <p>Already have an account? <a href="../entry/login.php">login here.</a></p>
                        </div>
                     </div>
                  </div>
                  <?php
               } else {
                  ?>
                  <!-- row 1 FIRST AND LAST NAME -->
                  <div class="row mb-3">
                     <div class="col">
                        <label for="fname" class="form-label">First Name:</label>
                        <div class="input-group has-validation">
                           <input placeholder="Enter First Name" autocomplete="off" type="text" name="fname" id="fname"
                              class="form-control">
                           <div class=" ms-3 invalid-feedback"></div>
                        </div>
                     </div>
                     <div class="col">
                        <label for="lname" class="form-label">Last Name:</label>
                        <div class="input-group has-validation">
                           <input placeholder="Enter Last Name" autocomplete="off" type="text" name="lname" id="lname"
                              class="form-control">
                           <div class="ms-3 invalid-feedback">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3">
                     <div class="col">
                        <label for="email" class="form-label">Email Address:</label>
                        <div class="input-group has-validation">
                           <input placeholder="Enter Email Address" maxlength="50" type="text" name="email" id="email"
                              id="fname" class="form-control">
                           <div class=" ms-3 invalid-feedback"></div>
                        </div>
                     </div>
                     <div class="col">
                        <label for="phoneNumber" class="form-label">Phone Number:</label>
                        <div class="input-group has-validation">
                           <span class="input-group-text">+63</span>
                           <input placeholder="9XX XXX XXXX" aria-describedby="phone" type="number" name="phoneNumber"
                              id="phoneNumber" class=" form-control">
                           <div class="ms-3 invalid-feedback"></div>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3">
                     <div class="col">
                        <label for="password" class="form-label">Password:</label>
                        <div class="input-group has-validation">
                           <button type="button" id="icon-click" class="btn btn-outline-secondary">
                              <i id="eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                           <input placeholder="Type Your Password" type="password" name="password" id="password"
                              class="form-control">
                           <div class="ms-3 invalid-feedback"></div>
                        </div>
                     </div>
                     <!-- RETYPE -->
                     <div class="col">
                        <label for="rpassword" class="form-label">Retype Password:</label>
                        <div class="input-group has-validation">
                           <button type="button" id="re-icon-click" class="btn btn btn-outline-secondary">
                              <i id="re-eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                           <input placeholder="Re-Enter Your Password" type="password" name="rpassword" id="rpassword"
                              class="form-control">
                           <div class="ms-3 invalid-feedback"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row mt-3">
                  <div class="col">
                     <!-- FACEBOOK AND GOOGLE SIGN IN  -->
                     <div class="user-select-none mb-2">
                        <div class="w-100 px-3 d-flex text-center justify-content-center align-items-center">
                           <div class="border-top w-100" style="height: 2px;"></div>
                           <p class=" lead text-center mx-4"
                              style="font-size: 14px; white-space: nowrap; text-overflow: ellipsis;">SIGN UP AS:</p>
                           <div class="border-top w-100" style="height: 2px;"></div>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center w-100 mb-3 py-1">
                           <!-- FACEBOOK SIGN IN -->
                           <div class="fb-login-button" data-width="" data-size="medium"
                              data-button-type="continue_with" data-layout="default" data-auto-logout-link="false"
                              data-use-continue-as="false"></div>
                           <!-- GOOGLE SIGN IN  -->
                           <div class=" my-3">
                              <div id="g_id_onload"
                                 data-client_id="368500009640-jfm2bavhodidfrk6p9g26cq4kk6qk8lq.apps.googleusercontent.com"
                                 data-callback="handleCredentialResponse" data-context="signin" data_ux-mode="popup">
                              </div>
                              <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="inline"
                                 data-text="signin_with" data-size="medium" data-auto_prompt="false"
                                 data-logo_alignment="left">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col">
                     <div class=" d-flex flex-column justify-content-center align-items-center gap-3 mt-4">
                        <!-- SIGN UP BUTTON -->
                        <button class="btn btn-primary btn-block" type="submit" name="signup-submit" id="submit"><i
                              class="fa-solid fa-user-plus me-2"></i> Sign Up</button>
                        <p>Already have an account? <a href="../entry/login.php">login here.</a></p>
                     </div>
                  </div>
                  <?php
               }
                  ?>
               </div>
            </div>
         </form>
      </div>
   </div>
</main>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
<?php
// FOOTER
require_once('../includes/footer.php');

if (isset($_SESSION['signup-message'])) {
?>

<script>
swal(
   "<?= $_SESSION['signup-message']['title']; ?>",
   "<?= $_SESSION['signup-message']['body']; ?>",
   "<?= $_SESSION['signup-message']['type']; ?>"
);
</script>

<?php
}
unset($_SESSION['signup-message']);
?>

<script>
/** GOOGLE API */
function handleCredentialResponse(response) {

   const responsePayLoad = decodeJwtResponse(response.credential);

   console.log('ID: ' + responsePayLoad.sub); // Do not send to your backend! Use an ID token instead.
   console.log('Full Name: ' + responsePayLoad.name);
   console.log('Given Name: ' + responsePayLoad.given_name);
   console.log('Family Name: ' + responsePayLoad.family_name);
   console.log('Image URL: ' + responsePayLoad.picture);
   console.log('Email: ' + responsePayLoad.email);

   window.location.href =
      `../validation/signup-process.php?google_signup=true&givenname=${responsePayLoad.given_name}&familyname=${responsePayLoad.family_name}&email=${responsePayLoad.email}&image=${responsePayLoad.picture}`;
}

function decodeJwtResponse(data) {
   var tokens = data.split(".");
   return JSON.parse(atob(tokens[1]));
}
/** /END GOOGLE API */
$(document).ready(() => {
   var givenname = document.querySelector('#fname');
   var familyname = document.querySelector('#lname');
   var email = document.querySelector('#email');
   var phone_number = document.querySelector('#phoneNumber');
   var password = document.querySelector('#password');
   var rpassword = document.querySelector('#rpassword');

   var invalidChars = [
      "-",
      "+",
      "e",
   ];

   phone_number.addEventListener("keydown", function(e) {
      if (invalidChars.includes(e.key)) {
         e.preventDefault();
         console.log('keyup')
      }
      if (phone_number.value.length >= 10) {
         phone_number.value = phone_number.value.slice(0, 10);
      };
   });

   phone_number.addEventListener("input", function(e) {
      this.value = this.value.replace(/[e\+\-]/gi, "");
   });

   $('#icon-click').click((e) => {
      e.preventDefault();
      $('#eye-icon').toggleClass('bi-eye-slash-fill');

      if ($('#password').attr('type') === 'password') {
         $('#password').attr('type', 'text');
      } else {
         $('#password').attr('type', 'password');
      }
   });

   $('#re-icon-click').click((e) => {
      e.preventDefault();
      $('#re-eye-icon').toggleClass('bi-eye-slash-fill');

      if ($('#rpassword').attr('type') === 'password') {
         $('#rpassword').attr('type', 'text');
      } else {
         $('#rpassword').attr('type', 'password');
      }
   });

   if (givenname && familyname && email) {
      givenname.addEventListener('blur', validateGivenName);
      familyname.addEventListener('blur', validateFamilyName);
      email.addEventListener('blur', validateEmail);
   }
   phone_number.addEventListener('blur', validatePhoneNumber);
   password.addEventListener('blur', validatePassword);
   rpassword.addEventListener('blur', validateRetypePassword);

   function validateGivenName() {
      $.ajax({
         type: 'POST',
         url: '../validation/signup-process.php',
         data: {
            signup: true,
            name: givenname.value
         },
         success: ((response) => {
            const result = JSON.parse(response);
            var invalid_feedback = givenname.nextElementSibling;
            if (result.error == 0) {
               givenname.classList.remove('is-invalid');
               givenname.classList.add('is-valid');
            } else {
               givenname.classList.remove('is-valid');
               givenname.classList.add('is-invalid');
               invalid_feedback.innerText = result.message;
            }
         })
      });
   };

   function validateFamilyName() {
      $.ajax({
         type: 'POST',
         url: '../validation/signup-process.php',
         data: {
            signup: true,
            name: familyname.value
         },
         success: ((response) => {
            const result = JSON.parse(response);
            var invalid_feedback = familyname.nextElementSibling;
            if (result.error == 0) {
               familyname.classList.remove('is-invalid');
               familyname.classList.add('is-valid');
            } else {
               familyname.classList.remove('is-valid');
               familyname.classList.add('is-invalid');
               invalid_feedback.innerText = result.message;
            }
         })
      });
   };

   function validateEmail() {
      $.ajax({
         type: 'POST',
         url: '../validation/signup-process.php',
         data: {
            signup: true,
            email: email.value
         },
         success: ((response) => {
            const result = JSON.parse(response);
            var invalid_feedback = email.nextElementSibling;
            if (result.error == 0) {
               email.classList.remove('is-invalid');
               email.classList.add('is-valid');
            } else {
               email.classList.remove('is-valid');
               email.classList.add('is-invalid');
               invalid_feedback.innerText = result.message;
            }
         })
      });
   };

   function validatePhoneNumber() {
      $.ajax({
         type: 'POST',
         url: '../validation/signup-process.php',
         data: {
            signup: true,
            phone_number: phone_number.value
         },
         success: ((response) => {
            const result = JSON.parse(response);
            var invalid_feedback = phone_number.nextElementSibling;
            if (result.error == 0) {
               phone_number.classList.remove('is-invalid');
               phone_number.classList.add('is-valid');
            } else {
               phone_number.classList.remove('is-valid');
               phone_number.classList.add('is-invalid');
               invalid_feedback.innerText = result.message;
            }
         })
      });
   };

   function validatePassword() {
      $.ajax({
         type: 'POST',
         url: '../validation/signup-process.php',
         data: {
            signup: true,
            password: password.value
         },
         success: ((response) => {
            const result = JSON.parse(response);
            var invalid_feedback = password.nextElementSibling;
            if (result.error == 0) {
               password.classList.remove('is-invalid');
               password.classList.add('is-valid');
            } else {
               password.classList.remove('is-valid');
               password.classList.add('is-invalid');
               invalid_feedback.innerText = result.message;
            }
            if (rpassword.value) {
               validateRetypePassword();
            }
         })
      });
   };

   function validateRetypePassword() {
      $.ajax({
         type: 'POST',
         url: '../validation/signup-process.php',
         data: {
            signup: true,
            password: password.value,
            rpassword: rpassword.value
         },
         success: ((response) => {
            const result = JSON.parse(response);
            var invalid_feedback = rpassword.nextElementSibling;
            if (result.error == 0) {
               rpassword.classList.remove('is-invalid');
               rpassword.classList.add('is-valid');
            } else {
               rpassword.classList.remove('is-valid');
               rpassword.classList.add('is-invalid');
               invalid_feedback.innerText = result.message;
            }
         })
      });
   };

   (function() {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
         .forEach(function(form) {
            form.addEventListener('submit', function(event) {

               if (givenname && familyname && email) {
                  validateGivenName();
                  validateFamilyName();
                  validateEmail();
               }
               validatePhoneNumber();
               validatePassword();
               validateRetypePassword();

               var err = 0;

               if (phone_number.value) {
                  var str = phone_number.value.toString();
                  var len = str.length;
                  (len != 10 || len < 0 || phone_number.value == 0 || str[0] != '9') ? err++ : null;
               } else {
                  err++;
               }

               if (password.value) {
                  var str = password.value.toString();
                  var len = str.length;
                  if (len < 5) {
                     err++;
                  } else {
                     (!password.value.match(/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/)) ? err++ : null;
                  }
               } else {
                  err++;
               }

               if (rpassword.value) {
                  (rpassword.value != password.value) ? err++ : null;
               } else {
                  err++;
               }

               (err == 0) ? form.classList.add('.was-validated'): event.preventDefault();
            }, false)
         })
   })()
})
</script>