<?php $background = trim((string) ($data['background_image'] ?? '')); ?>
<section class="page-banner customizer-banner" aria-labelledby="customizer-banner-title"<?= $background !== '' ? ' style="background-image:url(\''.esc($background).'\')"' : '' ?>>
    <div class="page-banner-shade"></div>
    <div class="page-banner-content">
        <?php if (! empty($data['breadcrumb'])): ?><nav class="breadcrumbs" aria-label="Breadcrumb"><a href="<?= esc(route('home')) ?>">Home</a><span aria-hidden="true">/</span><span><?= esc($data['breadcrumb']) ?></span></nav><?php endif; ?>
        <p class="eyebrow eyebrow-light"><?= esc($data['eyebrow'] ?? '') ?></p>
        <h1 id="customizer-banner-title"><?= esc($data['title'] ?? '') ?></h1>
        <p><?= esc($data['body'] ?? '') ?></p>
    </div>
</section>
