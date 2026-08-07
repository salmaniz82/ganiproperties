<section class="feature-panel">
    <div class="section feature-panel-inner">
        <div><p class="eyebrow eyebrow-light"><?= esc($data['eyebrow'] ?? '') ?></p><h2><?= esc($data['title'] ?? '') ?></h2><p><?= esc($data['body'] ?? '') ?></p><?php if (! empty($data['button'])): ?><a class="button button-white" href="<?= esc($data['button_url'] ?? route('contact')) ?>"><?= esc($data['button']) ?></a><?php endif; ?></div>
        <ul><?php foreach (($data['items'] ?? []) as $item): ?><li><svg><use href="#icon-check"/></svg><div><strong><?= esc($item['title'] ?? '') ?></strong><span><?= esc($item['text'] ?? '') ?></span></div></li><?php endforeach; ?></ul>
    </div>
</section>
