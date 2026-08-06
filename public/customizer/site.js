const requestForm = document.querySelector(".request-form");

if (requestForm) {
  requestForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const existing = requestForm.querySelector(".form-success");
    if (existing) existing.remove();

    const message = document.createElement("p");
    message.className = "form-success";
    message.textContent = "Thank you. Your request is ready to be connected to your preferred backend or email handler.";

    requestForm.appendChild(message);
    requestForm.reset();
  });
}

document.querySelectorAll("[data-testimonials]").forEach((section) => {
  const cards = Array.from(section.querySelectorAll("[data-testimonial-card]"));
  const next = section.querySelector("[data-testimonial-next]");
  const prev = section.querySelector("[data-testimonial-prev]");
  let active = cards.findIndex((card) => card.classList.contains("active"));

  if (cards.length <= 1 || !next || !prev) return;
  if (active < 0) active = 0;

  const show = (index) => {
    cards[active].classList.remove("active");
    active = (index + cards.length) % cards.length;
    cards[active].classList.add("active");
  };

  next.addEventListener("click", () => show(active + 1));
  prev.addEventListener("click", () => show(active - 1));
});

document.querySelectorAll("[data-image-carousel]").forEach((carousel) => {
  const slides = Array.from(carousel.querySelectorAll("[data-carousel-slide]"));
  const dots = Array.from(carousel.querySelectorAll("[data-carousel-dot]"));
  let active = slides.findIndex((slide) => slide.classList.contains("active"));
  let timer;

  if (slides.length <= 1) return;
  if (active < 0) active = 0;

  const show = (index) => {
    slides[active].classList.remove("active");
    if (dots[active]) dots[active].classList.remove("active");
    active = (index + slides.length) % slides.length;
    slides[active].classList.add("active");
    if (dots[active]) dots[active].classList.add("active");
  };

  const start = () => {
    timer = window.setInterval(() => show(active + 1), 5000);
  };

  const restart = () => {
    window.clearInterval(timer);
    start();
  };

  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      show(index);
      restart();
    });
  });

  start();
});

document.querySelectorAll("[data-faq-section]").forEach((section) => {
  section.querySelectorAll("[data-faq-toggle]").forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const item = toggle.closest(".faq-item");
      const isOpen = item.classList.toggle("open");
      toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  });
});
