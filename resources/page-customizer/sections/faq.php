<?php $items = $data['items'] ?? []; ?>
<section class="section-pad faq-section" id="faq" data-faq-section>
  <div class="container">
    <p class="eyebrow"><?= esc($data['eyebrow'] ?? 'FAQ') ?></p>
    <h2><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em></h2>
    <div class="faq-list">
      <?php foreach ($items as $index => $item): ?>
        <article class="faq-item<?= $index === 0 ? ' open' : '' ?>">
          <button type="button" class="faq-question" data-faq-toggle aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>">
            <span><?= esc($item['question'] ?? '') ?></span>
            <span class="faq-icon" aria-hidden="true"></span>
          </button>
          <div class="faq-answer">
            <p><?= esc($item['answer'] ?? '') ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
