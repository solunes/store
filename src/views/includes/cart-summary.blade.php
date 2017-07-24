<table class="table table-bordered-top table-responsive table-store">
	<thead>
		<tr>
			<th class="product-name">Producto</th>
			<th class="product-total">Total</th>
		</tr>             
	</thead>
	<tbody>
	  @if(count($items)>0)
		@foreach($items as $item)
		  <tr class="cart_item">
			<td class="product-name">
				{{ $item->product->name }} <strong class="product-quantity"> X {{ $item->quantity }}</strong>
			</td>
			<td class="product-total">
				<span class="amount">Bs. {{ $item->total_price }}</span>
			</td>
		  </tr>
		@endforeach
	  @endif
	</tbody>
	<tfoot>
		<tr class="cart-subtotal">
			<th>Subtotal</th>
			<td><span class="amount">Bs. {{ $order_amount }}</span></td>
		</tr>
		@foreach($deliveries as $delivery)
		<tr class="cart-subtotal">
			<th>Costo de Envío ({{ $delivery->total_weight }} kg.)</th>
			<td><span class="amount">Bs. {{ $delivery->shipping_cost }}</span></td>
		</tr>
		@endforeach
		<tr class="order-total">
			<th>Precio Total</th>
			<td><strong><span class="amount">Bs. {{ $total_amount }}</span></strong>
			</td>
		</tr>               
	</tfoot>
</table>