<section class="split-content-card-section section">
    <div class="split-content-card-copy">
        <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
        <h2><?= esc($data['title'] ?? '') ?></h2>
        <?php foreach (lines($data['body'] ?? '') as $paragraph): ?><p><?= esc($paragraph) ?></p><?php endforeach; ?>
    </div>
    <aside class="split-content-card-highlight">
        <?php if (! empty($data['mark'])): ?><span class="split-content-card-mark" aria-hidden="true"><?= esc($data['mark']) ?></span><?php endif; ?>
        <p class="eyebrow"><?= esc($data['card_eyebrow'] ?? '') ?></p>
        <h2><?= esc($data['card_title'] ?? '') ?></h2>
        <p><?= esc($data['card_body'] ?? '') ?></p>
        <?php if (! empty($data['button'])): ?><a href="<?= esc($data['button_url'] ?? route('contact')) ?>"><?= esc($data['button']) ?> <span aria-hidden="true">&#8594;</span></a><?php endif; ?>
    </aside>
</section>
