<section class="section-pad" id="services">
  <div class="container">
    <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
    <h2><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em></h2>
    <p class="section-copy"><?= esc($data['text'] ?? '') ?></p>
    <div class="service-grid bordered-grid">
      <?php foreach (($data['cards'] ?? []) as $card): ?>
        <article>
          <span class="mini-number"><?= esc($card['number'] ?? '') ?></span>
          <h3><?= esc($card['title'] ?? '') ?></h3>
          <p><?= esc($card['text'] ?? '') ?></p>
          <ul>
            <?php foreach (lines($card['bullets'] ?? '') as $bullet): ?>
              <li><?= esc($bullet) ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
