<?php
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
   $url = "https://";
else
   $url = "http://";
// Append the host(domain name, ip) to the URL.   
$url .= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL   
$url .= $_SERVER['REQUEST_URI'];

if ($url != 'http://localhost:3000/home.php') {
?>
   <footer class="w-100 mt-4 px-3" style="font-family: Public Sans Light;">
      <div class="container-fluid w-100">
         <div class=" d-flex justify-content-between align-items-center border-top py-3">
            <div class="col text-center d-flex justify-content-center align-items-center">
               <p class="" style="font-size: 16px;">© 2022 Dairy Raises Multi-Purpose Cooperative</p>
            </div>
            <div class="col d-flex justify-content-center align-items-center">
               <img src="../../../img/company-logo.png" class="img-fluid border border-2 border-primary rounded-circle" style="width: 50px;" alt="company logo">
            </div>
            <div class="col d-flex flex-row justify-content-center align-items-center">
               <a href="https://www.facebook.com/gentridairy" target=”_blank” class="text-center text-primary text-decoration-none text-black">
                  <p class="d-flex align-items-center"><i class='bx bxl-facebook-circle bx-tada-hover bx-sm me-2'></i> Visit Us On Facebook</p>
               </a>
            </div>
         </div>
      </div>
   </footer>
<?php
}
?>

<!-- JQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
<!-- Sweet Alert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<!-- glider js -->
<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>
<!-- tables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.4.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script>
   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
   var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
   })
</script>
</body>

</html>

<?php if (isset($_SESSION['users'])) : ?>

   <script src="../../../scripts/session_timeout.js"></script>

<?php endif; ?>
<script type="text/javascript">
   var loader = document.querySelector('.preLoader');

   window.addEventListener("load", () => {
      loader.classList.toggle('done');
   })
</script>