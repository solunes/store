<script type="text/javascript"> 
  $(document).ready(function() {
    $("input#currency_product_cost").on("change paste keyup", function() {
      calculateProductCurrencyCost();
    });
    $("input#currency_transport_cost").on("change paste keyup", function() {
      calculateTransportCurrencyCost();
    });
    $("input#exchange").on("change paste keyup", function() {
      calculateProductCurrencyCost();
      calculateTransportCurrencyCost();
    });
    $("input#product_cost").on("change paste keyup", function() {
      calculateCost();
    });
    $("input#transport_cost").on("change paste keyup", function() {
      calculateCost();
    });
    $("input#cost").on("change paste keyup", function() {
      calculateOfferPrice();
      calculateNoInvoicePrice();
    });
    $("input#price").on("change paste keyup", function() {
      calculateNoInvoicePrice();
    });
    /*$("input#quantity").on("change paste keyup", function() {
      calculateTotalCost();
    });*/
  });
  function calculateProductCurrencyCost () {
    console.log('Recalculando');
    var cost = $('input#currency_product_cost').val();
    var exchange = $('input#exchange').val();
    //var quantity = $('input#quantity').val();
    var cost = parseFloat(cost) * parseFloat(exchange);
    $('input#product_cost').val(Math.round(cost));
    calculateCost();
  };
  function calculateTransportCurrencyCost () {
    console.log('Recalculando');
    var cost = $('input#currency_transport_cost').val();
    var exchange = $('input#exchange').val();
    //var quantity = $('input#quantity').val();
    var cost = parseFloat(cost) * parseFloat(exchange);
    $('input#transport_cost').val(Math.round(cost));
    calculateCost();
  };
  function calculateCost () {
    console.log('Recalculando');
    var product_cost = $('input#product_cost').val();
    var transport_cost = $('input#transport_cost').val();
    //var quantity = $('input#quantity').val();
    var cost = parseFloat(product_cost) + parseFloat(transport_cost);
    $('input#cost').val(Math.round(cost));
    calculateOfferPrice();
    //calculateTotalCost();
  };
  function calculateTotalCost () {
    console.log('Recalculando');
    var cost = $('input#cost').val();
    var quantity = $('input#quantity').val();
    //var quantity = $('input#quantity').val();
    var total_cost = parseFloat(cost) + parseFloat(quantity);
    $('input#total_cost').val(Math.round(total_cost));
  };
  function calculateNoInvoicePrice () {
    console.log('Recalculando');
    cost = $('input#cost').val();
    price = $('input#price').val();
    value = {{ $no_invoice_reduction }};
    final = parseFloat(price) - (parseFloat(cost)*parseFloat(value/100));
    if(final>0){
      $('input#no_invoice_price').val(Math.round(final));
    }
  };
  function calculateOfferPrice () {
    console.log('Recalculando');
    cost = $('input#cost').val();
    $('input#offer_price').val(Math.round(parseFloat(cost)));
  };
</script>