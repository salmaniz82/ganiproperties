<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Page customizer | {{ $page->title }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('customizer/customizer.css') }}?v={{ filemtime(public_path('customizer/customizer.css')) }}">
</head>
<body>
  <div class="customizer-shell" data-schema-url="{{ route('admin.pages.customizer.schema', $page) }}" data-template-url="{{ route('admin.pages.customizer.template', $page) }}" data-save-url="{{ route('admin.pages.customizer.save', $page) }}" data-upload-url="{{ route('admin.pages.customizer.upload', $page) }}" data-preview-url="{{ route('admin.pages.customizer.preview', $page) }}">
    <aside class="sidebar">
      <header>
        <div>
          <strong>{{ $page->title }}</strong>
          <span>{{ $page->customizer_template }}</span>
        </div>
        <button id="saveButton" type="button">Save</button>
      </header>
      <nav id="sectionList" class="section-list" aria-label="Sections"></nav>
      <div class="add-section-wrap">
        <button id="addSectionButton" class="add-section-button" type="button">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5.5v13M5.5 12h13" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/></svg>
          <span>Add section</span>
        </button>
        <div id="addSectionMenu" class="add-section-menu" hidden></div>
      </div>
      <a class="add-section-button" href="{{ route('admin.pages.edit', $page) }}">Back to page settings</a>
    </aside>
    <main class="editor">
      <section id="editPanel" class="panel" aria-hidden="true">
        <div class="panel-head">
          <div>
            <strong id="activeName">Section</strong>
            <span id="saveState">All changes saved</span>
          </div>
          <button id="closePanel" class="icon-button close-button" type="button" aria-label="Close editor">&times;</button>
        </div>
        <div id="fields" class="fields"></div>
      </section>
      <section class="preview-wrap">
        <div class="preview-bar">
          <span>Live preview</span>
          <a href="{{ route('pages.show', $page->slug) }}" target="_blank">Open page</a>
        </div>
        <iframe id="preview" src="{{ route('admin.pages.customizer.preview', $page) }}?preview_ts={{ time() }}" title="Page preview"></iframe>
      </section>
    </main>
  </div>
  <script src="{{ asset('customizer/vendor/Sortable.min.js') }}?v={{ filemtime(public_path('customizer/vendor/Sortable.min.js')) }}"></script>
  <script src="{{ asset('customizer/customizer.js') }}?v={{ filemtime(public_path('customizer/customizer.js')) }}"></script>
</body>
</html>
