<section class="section-pad" id="suppliers">
  <div class="container">
    <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
    <h2><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em></h2>
    <p class="section-copy"><?= esc($data['text'] ?? '') ?></p>
    <div class="supplier-grid bordered-grid">
      <?php foreach (($data['items'] ?? []) as $item): ?>
        <article>
          <span class="eyebrow compact"><?= esc($item['category'] ?? '') ?></span>
          <h3><?= esc($item['name'] ?? '') ?></h3>
          <p><?= esc($item['text'] ?? '') ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
