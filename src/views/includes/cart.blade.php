<!-- Product Cart -->
<div class="menu-right-area">
    <a href="{{ url('process/confirmar-compra') }}" class="mini-cart-icon">
      <i class="icon_cart_alt"></i>
      <span>{{ count($cart_items) }}</span>
    </a>
    <div class="top-cart-content">
      @if(count($cart_items)>0)
        @foreach($cart_items as $item)
          <!-- Cart Single -->
          <div class="media header-middle-checkout">
            <div class="media-left check-img">
              <a href="#">
                <img alt="" src="{{ Asset::get_image_path('product-image', 'cart', $item->product->image) }}">
              </a>
            </div>
            <div class="media-body checkout-content">
              <h4 class="media-heading">
                <a href="{{ url('product/'.$item->product->slug) }}">{{ $item->product->name }}</a>
                <a href="{{ url('process/delete-cart-item/'.$item->id) }}">
                  <span title="Quitar" class="btn-remove checkout-remove">
                    <i class="fa fa-trash"></i>
                  </span>
                </a>
              </h4>
              <p>{{ $item->quantity }} x Bs. {{ $item->total_price }}</p>
            </div>
          </div>
          <!-- Cart Single -->
        @endforeach
        <div class="actions">
          <a href="{{ url('process/confirmar-compra') }}">
            <button type="button" title="Checkout-botton" class="Checkout-botton">
              <span>Comprar</span>
            </button>
          </a>
        </div>
      @else
        <br><p>Su carro de compras está vacío.</p>
      @endif
    </div>
</div>
<!-- Product Cart -->