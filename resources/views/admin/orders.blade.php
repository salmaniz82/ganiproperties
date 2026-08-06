@extends('layouts.admin')
@section('title', 'Orders')
@section('heading', 'Orders')
@section('content')
<div class="card">
    <div class="card-head"><div><h2>Orders</h2><p>Process payments and fulfillment</p></div><button class="button secondary">Export</button></div>
    <div class="table-toolbar"><div class="tabs"><button class="active">All orders</button><button>Unfulfilled</button><button>Unpaid</button></div><div class="toolbar-actions"><button>Filter</button><button>Search</button></div></div>
    @foreach($orders as $order)
    <details class="order">
        <summary><span><b>{{ $order->number }}</b><small>{{ $order->customer_name }} · {{ $order->customer_phone }}</small></span><span class="badge">{{ $order->status }}</span><span>PKR {{ number_format($order->total) }}</span><span>{{ $order->created_at->format('d M Y') }}</span></summary>
        <div class="order-body">
            <div><h3>Items</h3>@foreach($order->items as $item)<p>{{ $item->name }} × {{ $item->quantity }} <b>PKR {{ number_format($item->total) }}</b></p>@endforeach<h3>Delivery</h3><p>{{ $order->shipping_address['line1'] }}, {{ $order->shipping_address['city'] }}</p></div>
            <form class="form" method="post" action="{{ route('admin.orders.update', $order) }}">@csrf @method('put')
                <div class="two"><label>Order status<select name="status">@foreach(['pending','confirmed','processing','completed','cancelled'] as $value)<option @selected($order->status === $value)>{{ $value }}</option>@endforeach</select></label><label>Payment status<select name="payment_status">@foreach(['pending','paid','failed','refunded'] as $value)<option @selected($order->payment_status === $value)>{{ $value }}</option>@endforeach</select></label></div>
                <label>Fulfillment<select name="fulfillment_status">@foreach(['unfulfilled','preparing','shipped','delivered','cancelled'] as $value)<option @selected($order->fulfillment_status === $value)>{{ $value }}</option>@endforeach</select></label>
                <label>Internal note<textarea name="internal_note">{{ $order->internal_note }}</textarea></label><button class="button">Save changes</button>
            </form>
        </div>
    </details>
    @endforeach
    {{ $orders->links() }}
</div>
@endsection
