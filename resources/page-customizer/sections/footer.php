<?php
$header = $allSections['header'] ?? [];
$request = $allSections['request'] ?? [];
$suppliers = $allSections['suppliers'] ?? [];
$supplierNames = array_map(function ($item) {
    return $item['name'] ?? '';
}, array_slice(($suppliers['items'] ?? []), 0, 8));
$footerSuppliers = lines($data['suppliers'] ?? implode("\n", array_filter($supplierNames)));
$footerLinks = $data['links'] ?? [
    ['label' => 'About', 'href' => '#about'],
    ['label' => 'Contact', 'href' => '#contact'],
    ['label' => 'Request', 'href' => '#request']
];
?>
<footer class="footer">
  <div class="container footer-grid">
    <div>
      <h2><?= esc($data['brand_name'] ?? ($header['brand_name'] ?? '')) ?></h2>
      <p class="eyebrow"><?= esc($data['brand_location'] ?? ($header['brand_location'] ?? '')) ?></p>
      <p><?= esc($data['description'] ?? 'A discreet concierge service arranging transfers, car hire, accommodation, translators and lifestyle reservations through trusted UK suppliers.') ?></p>
    </div>
    <div>
      <h3><?= esc($data['contact_heading'] ?? 'Contact') ?></h3>
      <p><?= esc($data['phone'] ?? ($request['phone'] ?? '')) ?></p>
      <p><?= esc($data['email'] ?? ($request['email'] ?? '')) ?></p>
      <p><?= nl2br(esc($data['address'] ?? ($request['address'] ?? ''))) ?></p>
    </div>
    <div>
      <h3><?= esc($data['suppliers_heading'] ?? 'Trusted suppliers') ?></h3>
      <?php foreach ($footerSuppliers as $supplier): ?>
        <p><?= esc($supplier) ?></p>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="container footer-bottom">
    <span><?= esc($data['copyright'] ?? '© 2026 KP Consultancy Limited. All rights reserved.') ?></span>
    <?php foreach ($footerLinks as $link): ?>
      <a href="<?= esc($link['href'] ?? '#') ?>"><?= esc($link['label'] ?? '') ?></a>
    <?php endforeach; ?>
  </div>
</footer>
