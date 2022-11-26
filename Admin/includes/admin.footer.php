<footer class="mt-5">
   <div class="container">
      <div class="d-flex justify-content-between align-items-stretch border-top mt-3 p-3">
         <div class="col text-center d-flex justify-content-center align-items-center">
            <p class="text-muted " style="font-size: 15px;">© 2022 Dairy Raises Multipurpose Cooperative, Inc</p>
         </div>
         <div class="col d-flex justify-content-center align-items-center">
            <img src="../../img/company-logo.png" class="img-fluid" style="width: 50px;" alt="company logo">
         </div>
         <div class="col d-flex flex-row justify-content-center align-items-center">
            <a href="https://www.facebook.com/gentridairy"
               class="text-center text-muted text-decoration-none text-black">
               <p><i class="bi bi-facebook me-2" style="font-size: 15px;"></i>visit us on facebook</p>
            </a>
         </div>
      </div>
   </div>
</footer>

<footer style="visibility: hidden; display: none;" id="footer" class="mt-5">
   <div class="container mt-5">
      <div class="d-flex justify-content-between align-items-stretch border-top mt-3 p-3">
         <div class="col text-center d-flex justify-content-center align-items-center">
            <img src="../../img/company-logo.png" class="img-fluid logo" alt="company logo">
            <p class="text-muted ">© 2022 Dairy Raises Multipurpose Cooperative, Inc</p>
         </div>
      </div>
   </div>
</footer>

</div>
</div>
</main>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<!-- Sweet Alert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<!-- tables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.4.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>

<!-- chart js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
   integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
<!-- sidebar -->
<script src="../../Admin/scripts/admin.sidebar.js"></script>

</body>

</html>

<?php if (isset($_SESSION['admins'])) : ?>

<!-- session timeout -->
<script src="../../../../scripts/session_timeout.js"></script>

<?php endif; ?>

<script type="text/javascript">
var loader = document.querySelector('.preLoader');

window.addEventListener("load", () => {
   loader.classList.toggle('done');
})
</script>