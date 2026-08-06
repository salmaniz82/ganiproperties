@extends('layouts.store')
@section('title', 'Your Cart')
@section('content')
<section class="section cart-page">
    <div class="page-head">
        <div><small>SHOPPING BAG</small><h1>Your Cart</h1><p>{{ $item_count }} item(s) across {{ $line_count }} product line(s)</p></div>
        @if($items)<form method="post" action="{{ route('cart.clear') }}">@csrf @method('delete')<button class="link">Clear cart</button></form>@endif
    </div>

    @if($items)
    <div class="cart-layout">
        <div class="cart-lines">
            @foreach($items as $item)
            <article class="cart-row">
                <a href="{{ route('products.show', $item['product']) }}"><img src="{{ asset($item['product']->thumbnail_image) }}" alt="{{ $item['product']->name }}"></a>
                <div class="cart-row__details">
                    <a href="{{ route('products.show', $item['product']) }}"><h3>{{ $item['product']->name }}</h3></a>
                    <small>{{ $item['product']->sku }} · PKR {{ number_format($item['unit_price']) }} each</small>
                    @if($item['customizations'])
                        <div class="cart-customizations">
                            @foreach($item['customizations'] as $label => $value)
                                @unless(str_contains((string) $value, 'customizations/'))<span>{{ ucfirst($label) }}: {{ $value }}</span>@endunless
                            @endforeach
                        </div>
                    @endif
                    <div class="cart-row__actions">
                        <form method="post" action="{{ route('cart.update', $item['key']) }}" class="quantity-form">
                            @csrf @method('put')
                            <button type="button" data-quantity-change="-1" aria-label="Decrease quantity">−</button>
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="0" max="{{ max(1, $item['product']->available_stock) }}" aria-label="Quantity">
                            <button type="button" data-quantity-change="1" aria-label="Increase quantity">+</button>
                            <button class="link update-cart" type="submit">Update</button>
                        </form>
                        <form method="post" action="{{ route('cart.remove', $item['key']) }}">@csrf @method('delete')<button class="link">Remove</button></form>
                    </div>
                </div>
                <strong>PKR {{ number_format($item['line_total']) }}</strong>
            </article>
            @endforeach
        </div>
        <aside class="panel cart-summary">
            <h2>Order summary</h2>
            <p class="summary-line"><span>Items ({{ $item_count }})</span><b>PKR {{ number_format($subtotal) }}</b></p>
            <p class="summary-line"><span>Delivery</span><span>Calculated at checkout</span></p>
            <p class="summary-line cart-summary__total"><span>Subtotal</span><b>PKR {{ number_format($subtotal) }}</b></p>
            <a class="button" href="{{ route('checkout') }}">Proceed to checkout</a>
            <a class="continue-shopping" href="{{ route('shop') }}">Continue shopping</a>
        </aside>
    </div>
    @else
    <div class="panel centered empty-cart"><h2>Your cart is empty</h2><p>Add something special for your next celebration.</p><a class="button" href="{{ route('shop') }}">Start shopping</a></div>
    @endif
</section>
<script>
document.querySelectorAll('[data-quantity-change]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('input[name="quantity"]');
        const next = Number(input.value) + Number(button.dataset.quantityChange);
        input.value = Math.max(Number(input.min), Math.min(Number(input.max), next));
    });
});
</script>
@endsection
