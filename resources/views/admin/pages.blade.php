@extends('layouts.admin')
@section('title', 'Pages')
@section('heading', 'Pages')
@section('content')
<div class="card">
    <div class="card-head"><div><h2>Pages</h2><p>Manage footer and policy pages.</p></div><a class="button" href="{{ route('admin.pages.create') }}">Create page</a></div>
    <div class="table-wrap"><table>
        <tr><th>Page</th><th>Slug</th><th>Template</th><th>Position</th><th>Status</th><th></th></tr>
        @foreach($pages as $page)
        <tr>
            <td><b>{{ $page->title }}</b><small>{{ $page->meta_title }}</small></td>
            <td><a href="{{ route('pages.show', $page->slug) }}" target="_blank">/{{ $page->slug }}</a></td>
            <td>{{ $page->customizer_template ? 'Customizer' : 'Default' }}</td>
            <td>{{ $page->position }}</td>
            <td><span class="badge {{ $page->is_active ? 'green' : 'gray' }}">{{ $page->is_active ? 'Published' : 'Draft' }}</span></td>
            <td class="row-actions"><a class="preview-action" href="{{ route('pages.show', $page->slug) }}" target="_blank" title="Preview page" aria-label="Preview {{ $page->title }}">👁</a><a href="{{ route('admin.pages.edit', $page) }}">Edit</a>@if($page->customizer_template)<a href="{{ route('admin.pages.customizer', $page) }}">Page customizer</a>@endif</td>
        </tr>
        @endforeach
    </table></div>
</div>
@endsection
