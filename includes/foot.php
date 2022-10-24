
<!-- JQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
<!-- Sweet Alert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<!-- Font Awesome -->
<script src="https://kit.fontawesome.com/95c5b29ec4.js" crossorigin="anonymous"></script>
<!-- glider js -->
<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>
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