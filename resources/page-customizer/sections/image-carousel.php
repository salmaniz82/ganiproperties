<?php $slides = $data['slides'] ?? []; ?>
<section class="image-carousel" data-image-carousel>
  <?php foreach ($slides as $index => $slide): ?>
    <?php $background = trim((string) ($slide['background_image'] ?? '')); ?>
    <article
      class="image-carousel-slide<?= $index === 0 ? ' active' : '' ?>"
      data-carousel-slide
      <?= $background !== '' ? ' style="--slide-image: url(\'' . esc($background) . '\');"' : '' ?>>
      <div class="container image-carousel-content">
        <p class="eyebrow line-before"><?= esc($slide['title'] ?? '') ?></p>
        <h2><?= esc($slide['heading'] ?? '') ?></h2>
      </div>
    </article>
  <?php endforeach; ?>
  <?php if (count($slides) > 1): ?>
    <div class="image-carousel-dots" aria-label="Carousel slides">
      <?php foreach ($slides as $index => $slide): ?>
        <button type="button" class="<?= $index === 0 ? 'active' : '' ?>" data-carousel-dot aria-label="Show slide <?= esc($index + 1) ?>"></button>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
