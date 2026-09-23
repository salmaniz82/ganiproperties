<section class="tag-panel-section">
    <div class="section tag-panel-inner">
        <div>
            <p class="eyebrow eyebrow-light"><?= esc($data['eyebrow'] ?? '') ?></p>
            <h2><?= esc($data['title'] ?? '') ?></h2>
            <p><?= esc($data['body'] ?? '') ?></p>
        </div>
        <ul aria-label="<?= esc($data['list_label'] ?? 'Highlighted items') ?>">
            <?php foreach (($data['items'] ?? []) as $item): ?>
                <?php if (! empty($item['label'])): ?><li><?= esc($item['label']) ?></li><?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
