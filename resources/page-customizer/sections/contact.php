<?php $request = $allSections['request'] ?? []; ?>
<section class="section-pad alt" id="contact">
  <div class="container">
    <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
    <h2><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em></h2>
    <p class="section-copy"><?= esc($data['text'] ?? '') ?></p>
    <div class="contact-layout">
      <div class="contact-cards">
        <article><span>Phone</span><strong><?= esc($request['phone'] ?? '') ?></strong><small>UK & international</small></article>
        <article><span>Email</span><strong><?= esc($request['email'] ?? '') ?></strong><small>Replies within one business day.</small></article>
        <article><span>Office</span><strong><?= nl2br(esc($request['address'] ?? '')) ?></strong><small>Mayfair · By appointment.</small></article>
      </div>
      <div class="map-frame">
        <iframe
          src="https://www.google.com/maps?q=45+Albemarle+Street+London+W1S+4JL&output=embed"
          title="KP Consultancy Office Location"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>
  </div>
</section>
