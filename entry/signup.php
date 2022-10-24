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
<main style="width: 700px;margin-top: 30px;margin-bottom: 0;" class='h-100 container-fluid'>
   <!-- main row -->
   <div class="row d-flex justify-content-between align-items-center">
      <!-- column 1 -->
      <div class="col border m-5 px-1 py-3 bg-light">
         <form action="../validation/signup-process.php" method="POST" class="needs-validation d-flex flex-column px-3"
            novalidate>
            <div class="d-flex flex-column justify-content-center align-items-center">
               <img src="../img/company-logo.png" class="img-fluid logo" alt="company logo">
               <p class="fs-5 text-center mx-auto mb-4 lead">Create Your Dairy Raisers Account</p>
            </div>
            <?php
                if (isset($_SESSION['google_signup'])) {
                ?>
            <div class="row">
               <div class="col">
                  <div class="d-flex justify-content-strat align-items-center mb-3">
                     <img src="<?= $_SESSION['picture']; ?>" class="rounded-circle" style="width: 80px;"
                        alt="user picture">
                     <div class="ms-3 text-center">
                        <h5><?= $_SESSION['givenname'] . ' ' . $_SESSION['familyname']; ?></h5>
                        <p><?= $_SESSION['email'] ?></p>
                     </div>
                  </div>
                  <!-- PHONE NUMBER -->
                  <div class="input-group mb-3">
                     <span class="input-group-text" style="z-index: 50;" id="phone" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="FORMAT: 9XX XXX XXXX">+63</span>
                     <div class="form-floating form-floating-group flex-grow-1">
                        <input placeholder="9XX XXX XXXX" aria-describedby="phone" oninput="maxlength(this)"
                           type="number" name="phoneNumber" id="phoneNumber" class="form-control form-control-sm">
                        <div class="invalid-feedback">
                           Invalid phone number.
                        </div>
                        <label for="phoneNumber">Phone Number:</label>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col">
                        <div class="form-group input-group mb-3">
                           <div class="form-floating form-floating-group flex-grow-1">
                              <input placeholder="type password" type="password" name="password" id="password"
                                 class="form-control form-control-sm">
                              <label for="password">Password:</label>
                           </div>
                           <button type="button" id="icon-click" class="btn btn-lg btn-outline-secondary">
                              <i id="eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                        </div>
                     </div>
                     <!-- retype -->
                     <div class="col">
                        <div class="form-group input-group mb-3">
                           <div class="form-floating form-floating-group flex-grow-1">
                              <input placeholder="retype password" type="password" name="rpassword" id="rpassword"
                                 class="form-control form-control-sm">
                              <label for="rpassword">Retype Password:</label>
                           </div>
                           <button type="button" id="re-icon-click" class="btn btn-lg btn-outline-secondary">
                              <i id="re-eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                        </div>
                     </div>
                  </div>
                  <script>
                  document.querySelector('#phoneNumber').addEventListener('click', validatePhoneNumber);
                  console.log(document.querySelector('#phoneNumber'));

                  function validatePhoneNumber(e) {
                     console.log('tue');
                     var phone_number = document.querySelector('#phoneNumber');
                     if (phone_number.value != NULL) {
                        phone_number.classList.add('is-invalid')
                        console.log('true')
                     } else {
                        phone_number.classList.remove('.is-invalid')
                        console.log('flase')

                     }
                     //      if (value ? phone_number.classList.add(
                     //            'is-invalid') : phone_number.classList.remove('.is-invalid'));
                     //   }
                     //   // Example starter JavaScript for disabling form submissions if there are invalid fields
                     //   function checkValidty() {
                     //      var phone_number = document.querySelector('#phoneNumber')
                     //      var err = 0;

                     //      if (phone_number ? err++ : NULL);

                     //      return (err ? true : false);
                     //   }
                     //   (function() {
                     //      'use strict'

                     //      // Fetch all the forms we want to apply custom Bootstrap validation styles to
                     //      var forms = document.querySelectorAll('.needs-validation')

                     //      // Loop over them and prevent submission
                     //      Array.prototype.slice.call(forms)
                     //         .forEach(function(form) {
                     //            form.addEventListener('submit', function(event) {
                     //               event.preventDefault()
                     //               checkValidty();

                     //               //   if (!form.checkValidity()) {
                     //               //      event.preventDefault()
                     //               //      event.stopPropagation()
                     //               //   }

                     //               form.classList.add('was-validated')
                     //            }, false)
                     //         })
                     //   })()
                  </script>
                  <?php
                    } else {
                        ?>
                  <!-- row 1 FIRST AND LAST NAME -->
                  <div class="row">
                     <div class="col">
                        <div class="form-floating mb-3">
                           <input placeholder="enter first Name" autocomplete="off" type="text" name="fname" id="fname"
                              class="form-control form-control-sm
                            <?php if (isset($_POST['submit'])) {
                                if (!empty($reg_Error['fname_err'])) {
                                    echo $reg_Error['fname_err'];
                                }
                            } ?>" value="<?php if (isset($_POST['submit'])) {
                                                echo $fname;
                                            } ?>">
                           <!-- validation -->
                           <div class="invalid-feedback">
                              <?php if (isset($_POST['submit'])) {
                                                echo $reg_ErrorMsg['fname_errMsg'];
                                            } ?>
                           </div>
                           <label for="fname">First Name:</label>
                        </div>
                     </div>
                     <div class="col">
                        <div class="form-floating mb-3">
                           <input placeholder="enter last Name" autocomplete="off" type="text" name="lname" id="lname"
                              class="form-control form-control-sm
                            <?php if (isset($_POST['submit'])) {
                                if (!empty($reg_Error['lname_err'])) {
                                    echo $reg_Error['lname_err'];
                                }
                            } ?>" value="<?php if (isset($_POST['submit'])) {
                                                echo $lname;
                                            } ?>">
                           <!-- validation -->
                           <div class="invalid-feedback">
                              <?php if (isset($_POST['submit'])) {
                                                echo $reg_ErrorMsg['lname_errMsg'];
                                            } ?>
                           </div>
                           <label for="lname">Last Name:</label>
                        </div>
                     </div>
                  </div>
                  <!-- row 2 EMAIL AND PHONE NUMBER -->
                  <div class="row">
                     <div class="col">
                        <div class="form-floating mb-3">
                           <input placeholder="enter email address" maxlength="50" type="text" name="email" id="email"
                              class="form-control form-control-sm
                            <?php if (isset($_POST['submit'])) {
                                if (!empty($reg_Error['email_err'])) {
                                    echo $reg_Error['email_err'];
                                }
                            } ?>" value="<?php if (isset($_POST['submit'])) {
                                                echo $email;
                                            } ?>">
                           <!-- validation -->
                           <div class="invalid-feedback">
                              <?php if (isset($_POST['submit'])) {
                                                echo $reg_ErrorMsg['email_errMsg'];
                                            } ?>
                           </div>
                           <label for="email">Email Address:</label>
                        </div>
                     </div>
                     <div class="col">
                        <div class="input-group mb-3">
                           <span class="input-group-text" style="z-index: 50;" id="phone" data-bs-toggle="tooltip"
                              data-bs-placement="top" title="FORMAT: 9XX XXX XXXX">+63</span>
                           <div class="form-floating form-floating-group flex-grow-1">
                              <input placeholder="9XX XXX XXXX" aria-describedby="phone" oninput="maxlength(this)"
                                 type="number" name="phoneNumber" id="phoneNumber" class=" form-control form-control-sm 
                                <?php if (isset($_POST['submit'])) {
                                    if (!empty($reg_Error['phoneNumber_err'])) {
                                        echo $reg_Error['phoneNumber_err'];
                                    }
                                } ?>" value="<?php if (isset($_POST['submit'])) {
                                                    echo $phoneNumber;
                                                } ?>">
                              <label for="phoneNumber">Phone Number:</label>
                           </div>
                           <!-- validation -->
                           <div class="invalid-feedback">
                              <?php if (isset($_POST['submit'])) {
                                                echo $reg_ErrorMsg['phoneNumber_errMsg'];
                                            } ?>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- row 3 PASSWORD AND RETYPED-->
                  <div class="row">
                     <div class="col">
                        <div class="form-group input-group mb-3">
                           <div class="form-floating form-floating-group flex-grow-1">
                              <input placeholder="type password" type="password" name="password" id="password" class="form-control form-control-sm
                                <?php if (isset($_POST['submit'])) {
                                    if (!empty($reg_Error['pass_err'])) {
                                        echo $reg_Error['pass_err'];
                                    }
                                } ?>" value="<?php if (isset($_POST['submit'])) {
                                                    echo $password;
                                                } ?>">
                              <!-- validation -->
                              <div class="invalid-feedback">
                                 <?php if (isset($_POST['submit'])) {
                                                    echo $reg_ErrorMsg['pass_errMsg'];
                                                } ?>
                              </div>
                              <label for="password">Password:</label>
                           </div>
                           <button type="button" id="icon-click" class="btn btn-lg btn-outline-secondary">
                              <i id="eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                        </div>
                     </div>
                     <!-- retype -->
                     <div class="col">
                        <div class="form-group input-group mb-3">
                           <div class="form-floating form-floating-group flex-grow-1">
                              <input placeholder="retype password" type="password" name="rpassword" id="rpassword"
                                 class="form-control form-control-sm
                            <?php if (isset($_POST['submit'])) {
                                if (!empty($reg_Error['rpass_err'])) {
                                    echo $reg_Error['rpass_err'];
                                }
                            } ?>" value="<?php if (isset($_POST['submit'])) {
                                                if ($rpassword == $password) {
                                                    echo $rpassword;
                                                }
                                            } ?>">
                              <!-- validation -->
                              <div class="invalid-feedback">
                                 <?php if (isset($_POST['submit'])) {
                                                    echo $reg_ErrorMsg['rpass_errMsg'];
                                                } ?>
                              </div>
                              <label for="rpassword">Retype Password:</label>
                           </div>
                           <button type="button" id="re-icon-click" class="btn btn-lg btn-outline-secondary">
                              <i id="re-eye-icon" class="bi bi-eye-fill"></i>
                           </button>
                        </div>
                     </div>
                  </div>
                  <div class="row ">
                     <!-- FACEBOOK AND GOOGLE SIGN IN  -->
                     <div class="user-select-none mb-2">
                        <div class="w-100 px-3 d-flex text-center justify-content-center align-items-center">
                           <div class="border-top w-100" style="height: 2px;"></div>
                           <p class=" lead text-center mx-4"
                              style="font-size: 14px; white-space: nowrap; text-overflow: ellipsis;">SIGN UP AS:</p>
                           <div class="border-top w-100" style="height: 2px;"></div>
                        </div>
                        <div
                           class="d-flex flex-column justify-content-center align-items-center w-100 mb-3 border-bottom py-1">
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
                  <?php
                    }
                        ?>
               </div>
               <div class="mt-3 d-flex justify-content-center align-items-center gap-2">
                  <button type="button" class="btn btn-dark p-0" data-bs-toggle="tooltip" data-bs-placement="right"
                     title=" Password must have atleast: (6) Length, (1) Letter, (1) Number, and (1) Capital letter">
                     <i class="bi bi-info-circle-fill p-2" style="font-size: 20px;"></i>
                  </button>
                  <!-- submit -->
                  <button class="btn btn-primary btn-block" type="submit" name="submit" id="submit"><i
                        class="bi bi-plus-circle-dotted"></i> SIGN UP</button>
               </div>
            </div>
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
      `../validation/signup-process.php?google_signup=true&givenname=${responsePayLoad.given_name}&familyname=${responsePayLoad.family_name}&email=${responsePayLoad.email}&image=${responsePayLoad.picture}`;
   //    window.location.href =
   //       `../validation/login-process.php?google_signin=true&givenname=${responsePayLoad.given_name}&familyname=${responsePayLoad.family_name}&email=${responsePayLoad.email}`;

}

function decodeJwtResponse(data) {
   var tokens = data.split(".");
   return JSON.parse(atob(tokens[1]));
}
</script>
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
$(document).ready(() => {

   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
   var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
   })

   var phoneNumber = document.querySelector('#phoneNumber');

   var invalidChars = [
      "-",
      "+",
      "e",
   ];

   phoneNumber.addEventListener("keydown", function(e) {
      if (invalidChars.includes(e.key)) {
         e.preventDefault();
      }
   });

   function maxlength(phoneNumber) {
      if (phoneNumber.value.length > 10) {
         phoneNumber.value = phoneNumber.value.slice(0, 10);
      };
   };

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

});
</script>

<?php

if (isset($_GET['google_not_bind']) && isset($_GET['givenname']) && isset($_GET['familyname']) && isset($_GET['email'])) {
    if ($_GET['google_not_bind'] == true) {
?>
<script>
$('#fname').val('<?= $_GET['givenname']; ?>');
$('#lname').val('<?= $_GET['familyname']; ?>');
$('#email').val('<?= $_GET['email']; ?>');
</script>
<?php
    }
}

?>