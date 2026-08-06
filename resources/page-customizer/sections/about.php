<section class="section-pad alt" id="about">
  <div class="container">
    <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
    <h2><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em></h2>
    <div class="about-layout">
      <div class="frame-art" aria-hidden="true"><span></span></div>
      <div class="about-copy">
        <?php foreach (lines($data['body'] ?? '') as $paragraph): ?>
          <p><?= esc($paragraph) ?></p>
        <?php endforeach; ?>
        <h3 class="eyebrow compact"><?= esc($data['serve_title'] ?? '') ?></h3>
        <ul class="two-col">
          <?php foreach (lines($data['serve_items'] ?? '') as $item): ?>
            <li><?= esc($item) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>
