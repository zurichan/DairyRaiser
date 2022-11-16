$(document).ready(() => {
  // CART
  var productname = Array.from(
    document.querySelectorAll(".cart_item .productname")
  );
  var quantity = Array.from(document.querySelectorAll(".quantity"));
  var sum = Array.from(document.querySelectorAll(".sumProducts"));
  var remove_btn = Array.from(document.querySelectorAll(".remove-cartItem"));
  var total_order_price = document.querySelector("#total-order-price");

  var increment_btn = document.querySelectorAll(".increment-btn");
  var decrement_btn = document.querySelectorAll(".decrement-btn");

  function EvalQuantity(productname, qty, sumsInner) {
    $.ajax({
      type: "POST",
      url: "../validation/cart-process.php",
      dataType: "json",
      encode: true,
      data: {
        product_name: productname,
        quantities: qty,
      },
      success: (response) => {
        console.log(response.subtotal);
        total_order_price.innerText = response.ordertotal;
        sumsInner.innerText = response.subtotal;
      },
    });
  }

  increment_btn.forEach((btn, i) => {
    $(btn).click((e) => {
      var qty = e.target.parentElement.children[1];
      var inputVal = qty.value;
      var newVal = parseInt(inputVal) + 1;
      qty.value = newVal;
      console.log(inputVal + " " + qty.value);
      if (quantity[i].value == 0) {
        e.preventDefault();
      } else {
        EvalQuantity(productname[i].innerHTML, qty.value, sum[i]);
      }
    });
  });

  decrement_btn.forEach((btn, i) => {
    $(btn).click((e) => {
      var qty = e.target.parentElement.children[1];
      var inputVal = qty.value;
      var newVal = parseInt(inputVal) - 1;
      qty.value = newVal;
      if (quantity[i].value <= 0) {
        quantity[i].value = 1;
        e.preventDefault();
      } else {
        EvalQuantity(productname[i].innerHTML, qty.value, sum[i]);
      }
    });
  });

  var invalidChars = ["-", "+", "e"];
  function myMethod(current) {
    if (current > previousState)
      // Do Something

      previousState = current;
  }
  quantity.forEach((item, i) => {
    $(item).keydown((e) => {
      e.preventDefault();
      //   if (invalidChars.includes(e.key)) {
      //     e.preventDefault();
      //     console.log(e.key);
      //   }
    });
    $(item).change((e) => {
      if (quantity[i].value <= 0) {
        e.preventDefault();
      } else {
        EvalQuantity(productname[i].innerHTML, item.value, sum[i]);
      }
    });
  });

  //REMOVE CART ITEM
  remove_btn.forEach((item, i) => {
    $(item).click(() => {
      swal({
        title: "Removing Item: " + productname[i].innerHTML,
        text: "Once deleted, you will not be able to recover.",
        icon: "warning",
        closeOnClickOutside: false,
        buttons: true,
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          $.ajax({
            type: "POST",
            url: "../validation/cart-process.php",
            data: {
              remove_item: productname[i].innerHTML,
            },
          });
          swal("Poof! Your Item has been deleted!", {
            icon: "success",
            closeOnClickOutside: false,
          });
          $(".swal-button--confirm").click(() => {
            document.location.reload();
          });
        } else {
          swal("Your Item is safe!");
        }
      });
    });
  });
});
