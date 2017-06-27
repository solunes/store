<script type="text/javascript"> 
  $(document).ready(function() {
    $(document).on('change', 'select#currency_id', function() { 
    	console.log('Recalculando');
      calculateTotal();
    });
    $('input#parcial_cost').on("change paste keyup", function() {
      	calculateTotal();
    });
    $('input#shipping_cost').on("change paste keyup", function() {
        calculateTotal();
    });
    $('input#quantity').on("change paste keyup", function() {
      	calculateTotal();
    });
  });
  function calculateTotal () {
    console.log('Recalculando');
    var parcial_cost = $('input#parcial_cost').val();
    var shipping_cost = $('input#shipping_cost').val();
    var quantity = $('input#quantity').val();
    var shipping_cost_unit = parseFloat(shipping_cost) / parseFloat(quantity);
    var cost = parseFloat(parcial_cost) + parseFloat(shipping_cost_unit);
    $('input#cost').val(cost);
    var quantity = $('input#quantity').val();
    var final = parseFloat(cost) * parseFloat(quantity);
    $('input#subtotal').val(Math.round(final));
  };

</script>