<header class="site-header">
  <a class="brand" href="#top" aria-label="Home">
    <strong><?= esc($data['brand_name'] ?? '') ?></strong>
    <span><?= esc($data['brand_location'] ?? '') ?></span>
  </a>
  <nav class="nav" aria-label="Primary navigation">
    <?php foreach (($data['nav'] ?? []) as $item): ?>
      <a href="<?= esc($item['href'] ?? '#') ?>"><?= esc($item['label'] ?? '') ?></a>
    <?php endforeach; ?>
    <a class="outline-button" href="#request"><?= esc($data['button_label'] ?? 'Request') ?></a>
  </nav>
</header>
