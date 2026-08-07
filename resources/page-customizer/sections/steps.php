<?php if (($data['variant'] ?? '') === 'timeline'): ?>
<section class="process-steps-section section">
  <div class="process-steps-heading"><p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p><h2><?= esc($data['title'] ?? '') ?></h2><p><?= esc($data['intro'] ?? '') ?></p></div>
  <ol><?php foreach (($data['items'] ?? []) as $item): ?><li><span><?= esc($item['number'] ?? '') ?></span><div><h3><?= esc($item['title'] ?? '') ?></h3><p><?= esc($item['text'] ?? '') ?></p></div></li><?php endforeach; ?></ol>
  <?php if (! empty($data['note'])): ?><p class="process-steps-note"><?= esc($data['note']) ?></p><?php endif; ?>
</section>
<?php else: ?>
<section class="section-pad alt" id="how">
  <div class="container">
    <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
    <h2><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em> Nothing else.</h2>
    <div class="step-grid bordered-grid">
      <?php foreach (($data['items'] ?? []) as $item): ?>
        <article>
          <span class="big-number"><?= esc($item['number'] ?? '') ?></span>
          <h3><?= esc($item['title'] ?? '') ?></h3>
          <p><?= esc($item['text'] ?? '') ?></p>
        </article>
      <?php endforeach; ?>
    </div>
    <a class="button" href="#request"><?= esc($data['cta'] ?? '') ?></a>
  </div>
</section>
<?php endif; ?>
