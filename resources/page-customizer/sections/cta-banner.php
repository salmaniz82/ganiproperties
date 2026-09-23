<?php $variant = preg_replace('/[^a-z0-9_-]/i', '', (string) ($data['variant'] ?? '')); ?>
<section class="cta-banner<?= $variant !== '' ? ' cta-banner--'.esc($variant) : '' ?>">
    <div><p class="eyebrow eyebrow-light"><?= esc($data['eyebrow'] ?? '') ?></p><h2><?= esc($data['title'] ?? '') ?></h2><p><?= esc($data['body'] ?? '') ?></p></div>
    <?php if (! empty($data['button'])): ?><a class="button button-white" href="<?= esc($data['button_url'] ?? route('contact')) ?>"><?= esc($data['button']) ?></a><?php endif; ?>
    <?php if (! empty($data['phone'])): ?><a class="phone" href="tel:<?= esc(preg_replace('/\D+/', '', (string) $data['phone'])) ?>"><svg class="icon"><use href="#icon-phone"/></svg><?= esc($data['phone']) ?></a><?php endif; ?>
</section>
