<section class="card-grid-section">
    <div class="section">
        <div class="card-grid-heading"><div><p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p><h2><?= esc($data['title'] ?? '') ?></h2></div><p><?= esc($data['intro'] ?? '') ?></p></div>
        <div class="service-grid customizer-card-grid">
            <?php foreach (($data['cards'] ?? []) as $card): ?><article class="service-card"><span class="service-number"><?= esc($card['number'] ?? '') ?></span><svg><use href="#icon-check"/></svg><h3><?= esc($card['title'] ?? '') ?></h3><p><?= esc($card['text'] ?? '') ?></p></article><?php endforeach; ?>
        </div>
    </div>
</section>
