@extends('layouts.store')
@inject('customizer', 'App\Services\PageCustomizerService')
@section('title', $page->meta_title ?: $page->title)
@php($activePage = $page->slug)
@section('meta')
@if($page->meta_keywords)<meta name="keywords" content="{{ $page->meta_keywords }}">@endif
<link rel="stylesheet" href="{{ asset('css/page-customizer-store.css') }}?v={{ filemtime(public_path('css/page-customizer-store.css')) }}">
<script src="{{ asset('customizer/site.js') }}?v={{ filemtime(public_path('customizer/site.js')) }}" defer></script>
@endsection
@if($page->schema)
@push('structured-data')
<script type="application/ld+json">{!! json_encode($page->schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endpush
@endif
@section('content')
<div class="page-customizer-content">
    {!! isset($customizerTemplate) ? $customizer->renderTemplate($customizerTemplate) : $customizer->render($page->customizer_template) !!}
</div>
@endsection
