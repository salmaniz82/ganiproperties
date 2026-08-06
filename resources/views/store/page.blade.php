@extends('layouts.store')
@section('title', $page->meta_title ?: $page->title)
@section('meta')
@if($page->meta_keywords)<meta name="keywords" content="{{ $page->meta_keywords }}">@endif
@endsection
@if($page->schema)
@push('structured-data')
<script type="application/ld+json">{!! json_encode($page->schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endpush
@endif
@section('content')
<section class="section narrow policy-page">
    <div class="page-head"><div><small>PARTY POPPERS</small><h1>{{ $page->title }}</h1></div></div>
    <div class="panel stack">
        @foreach(preg_split("/\r\n|\n|\r/", $page->content ?: '') as $paragraph)
            @if(trim($paragraph) !== '')<p>{{ $paragraph }}</p>@endif
        @endforeach
    </div>
</section>
@endsection
