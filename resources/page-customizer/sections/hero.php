<?php
$heroBackground = trim((string) ($data['background_image'] ?? 'assets/images/hero-mayfair-night.png'));
$heroStyle = $heroBackground !== '' ? "--hero-image: url('" . esc($heroBackground) . "');" : '';
?>
<section class="hero section-pad"<?= $heroStyle ? ' style="' . $heroStyle . '"' : '' ?>>
    <div class="container hero-inner">
      <p class="eyebrow line-before"><?= esc($data['eyebrow'] ?? '') ?></p>
      <h1><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em><br><?= esc($data['title_after'] ?? '') ?></h1>
      <p class="lead"><?= esc($data['body'] ?? '') ?></p>
      <div class="button-row">
        <a class="button" href="#request"><?= esc($data['primary_button'] ?? '') ?></a>
        <a class="text-button" href="#services"><?= esc($data['secondary_button'] ?? '') ?></a>
      </div>
    </div>
  </section>

  <section class="feature-strip">
    <div class="wide-grid feature-grid">
      <?php foreach (($data['features'] ?? []) as $feature): ?>
        <article>
          <span class="card-number"><?= esc($feature['number'] ?? '') ?></span>
          <div>
            <h3><?= esc($feature['title'] ?? '') ?></h3>
            <p><?= esc($feature['text'] ?? '') ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
