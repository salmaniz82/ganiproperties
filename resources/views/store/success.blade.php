@extends('layouts.store') @section('title','Order Confirmed') @section('content')
<section class="section narrow"><div class="panel centered"><small>ORDER CONFIRMED</small><h1>Thank you, {{ $order->customer_name }}!</h1><p>Your order number is <b>{{ $order->number }}</b>. Keep it handy for tracking.</p><h2>PKR {{ number_format($order->total) }}</h2><a class="button" href="{{ route('track',['number'=>$order->number]) }}">Track order</a></div></section>
@endsection
