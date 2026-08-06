@extends('layouts.store')
@section('title', 'Checkout')
@section('content')
<section class="section checkout">
    <form method="post" action="{{ route('checkout.store') }}" class="panel stack">
        @csrf
        <h1>Delivery details</h1>
        <div class="fields">
            <label>Full name<input name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" required></label>
            <label>Phone<input name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" required></label>
            <label>Email<input type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}"></label>
            <label>Delivery zone
                <select name="delivery_zone_id" id="deliveryZone" required>
                    @foreach($zones as $zone)<option value="{{ $zone->id }}" data-fee="{{ $zone->fee }}" @selected($selectedZone?->id === $zone->id)>{{ $zone->name }} — PKR {{ number_format($zone->fee) }}</option>@endforeach
                </select>
            </label>
            <label class="wide">Address<input name="line1" value="{{ old('line1') }}" required></label>
            <label class="wide">Apartment / landmark<input name="line2" value="{{ old('line2') }}"></label>
            <label>Preferred delivery date<input type="date" name="delivery_date" value="{{ old('delivery_date') }}" min="{{ today()->format('Y-m-d') }}"></label>
            <label class="wide">Order note<textarea name="customer_note">{{ old('customer_note') }}</textarea></label>
        </div>
        <h3>Payment</h3>
        <label class="choice"><input type="radio" checked> Cash on delivery</label>
        <button class="button">Place order · <span id="placeOrderTotal">PKR {{ number_format($total) }}</span></button>
    </form>
    <aside class="panel checkout-summary">
        <h2>Order summary</h2>
        @foreach($items as $item)<p class="summary-line"><span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span><b>PKR {{ number_format($item['line_total']) }}</b></p>@endforeach
        <hr>
        <p class="summary-line"><span>Subtotal</span><b>PKR {{ number_format($subtotal) }}</b></p>
        <p class="summary-line"><span>Delivery</span><b id="deliveryFee">PKR {{ number_format($delivery_fee) }}</b></p>
        <p class="summary-line checkout-summary__total"><span>Total</span><b id="checkoutTotal">PKR {{ number_format($total) }}</b></p>
        <small>The server verifies current prices, inventory, and delivery fee again before creating your order.</small>
    </aside>
</section>
<script>
const subtotal = {{ $subtotal }};
const formatPkr = (value) => `PKR ${new Intl.NumberFormat('en-PK').format(value)}`;
const zone = document.getElementById('deliveryZone');
const updateTotals = () => {
    const fee = Number(zone.options[zone.selectedIndex]?.dataset.fee || 0);
    document.getElementById('deliveryFee').textContent = formatPkr(fee);
    document.getElementById('checkoutTotal').textContent = formatPkr(subtotal + fee);
    document.getElementById('placeOrderTotal').textContent = formatPkr(subtotal + fee);
};
zone?.addEventListener('change', updateTotals);
updateTotals();
</script>
@endsection
