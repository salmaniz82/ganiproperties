<section class="section-pad" id="request">
  <div class="container request-layout">
    <div>
      <p class="eyebrow"><?= esc($data['eyebrow'] ?? '') ?></p>
      <h2><?= esc($data['title'] ?? '') ?> <em><?= esc($data['highlight'] ?? '') ?></em></h2>
      <p class="request-intro"><?= esc($data['intro'] ?? '') ?></p>
      <div class="contact-list">
        <p><span>Phone</span><?= esc($data['phone'] ?? '') ?></p>
        <p><span>Email</span><?= esc($data['email'] ?? '') ?></p>
        <p><span>Office</span><?= nl2br(esc($data['address'] ?? '')) ?></p>
      </div>
    </div>
    <form class="request-form">
      <label>Full name *<input type="text" placeholder="Your full name"></label>
      <label>Email *<input type="email" placeholder="your@email.com"></label>
      <label>Phone *<input type="tel" placeholder="+44 ..."></label>
      <label>Preferred contact<select><option>- Select</option><option>Phone</option><option>Email</option></select></label>
      <label class="wide">Service *<select><option>- Select a service</option><option>Airport transfers</option><option>Accommodation</option><option>Vehicle care</option></select></label>
      <label>Date<input type="date"></label>
      <label>Passengers / Guests<input type="number" min="1" value="1"></label>
      <label>Pickup / Location<input type="text" placeholder="e.g. Heathrow T5"></label>
      <label>Drop-off<input type="text" placeholder="e.g. Mayfair"></label>
      <label class="wide">Flight number<input type="text" placeholder="e.g. BA0294"></label>
      <label class="wide">Notes<textarea placeholder="Any additional requirements..."></textarea></label>
      <button class="button" type="submit"><?= esc($data['button'] ?? '') ?></button>
    </form>
  </div>
</section>
