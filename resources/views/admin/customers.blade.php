@extends('layouts.admin')
@section('title', 'Customers')
@section('heading', 'Customers')
@section('content')
<div class="card">
    <div class="card-head"><div><h2>Customers</h2><p>Registered customer accounts and order activity</p></div><button class="button secondary">Export</button></div>
    <div class="table-toolbar"><div class="tabs"><button class="active">All customers</button></div><div class="toolbar-actions"><button>Filter</button><button>Search</button></div></div>
    <div class="table-wrap"><table><tr><th>Customer</th><th>Phone</th><th>Orders</th><th>Joined</th></tr>
    @foreach($customers as $customer)<tr><td><b>{{ $customer->name }}</b><small>{{ $customer->email }}</small></td><td>{{ $customer->phone ?: '-' }}</td><td>{{ $customer->orders_count }}</td><td>{{ $customer->created_at->format('d M Y') }}</td></tr>@endforeach
    </table></div>{{ $customers->links() }}
</div>
@endsection
