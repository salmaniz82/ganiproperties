<section class="image-content-section section">
    <div class="image-content-media"><img src="<?= esc($data['image'] ?? '') ?>" alt="<?= esc($data['image_alt'] ?? '') ?>" loading="lazy"><?php if (! empty($data['image_caption'])): ?><span><?= esc($data['image_caption']) ?></span><?php endif; ?></div>
    <div class="image-content-copy">
        <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
        <h2><?= esc($data['title'] ?? '') ?></h2>
        <?php foreach (lines($data['body'] ?? '') as $paragraph): ?><p><?= esc($paragraph) ?></p><?php endforeach; ?>
        <?php if (! empty($data['items'])): ?><div class="image-content-items"><?php foreach ($data['items'] as $item): ?><div><strong><?= esc($item['title'] ?? '') ?></strong><span><?= esc($item['text'] ?? '') ?></span></div><?php endforeach; ?></div><?php endif; ?>
    </div>
</section>
