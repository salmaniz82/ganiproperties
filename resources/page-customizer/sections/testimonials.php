<?php $items = $data['items'] ?? []; ?>
<section class="section-pad testimonials-section" data-testimonials>
  <div class="container">
    <p class="eyebrow">Testimonials</p>
    <h2>Quiet words from <em>people we support.</em></h2>
    <div class="testimonial-shell">
      <?php foreach ($items as $index => $item): ?>
        <article class="testimonial-card<?= $index === 0 ? ' active' : '' ?>" data-testimonial-card>
          <p class="testimonial-review">“<?= esc($item['review'] ?? '') ?>”</p>
          <div>
            <h3><?= esc($item['name'] ?? '') ?></h3>
            <span><?= esc($item['designation'] ?? '') ?></span>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (count($items) > 1): ?>
        <div class="testimonial-controls" aria-label="Testimonials controls">
          <button type="button" data-testimonial-prev aria-label="Previous testimonial">‹</button>
          <button type="button" data-testimonial-next aria-label="Next testimonial">›</button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
