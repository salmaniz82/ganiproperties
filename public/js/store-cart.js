(() => {
  const drawer = document.getElementById("quickCart");
  const overlay = document.querySelector("[data-cart-overlay]");
  if (!drawer || !overlay) return;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const formatPkr = (amount) => `PKR ${new Intl.NumberFormat("en-PK").format(amount)}`;
  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
    "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;"
  })[character]);

  const setOpen = (open) => {
    drawer.classList.toggle("is-open", open);
    overlay.classList.toggle("is-open", open);
    drawer.setAttribute("aria-hidden", String(!open));
    document.querySelectorAll(".js-cart-trigger").forEach((trigger) => trigger.setAttribute("aria-expanded", String(open)));
    document.body.style.overflow = open ? "hidden" : "";
  };

  const setLoading = (loading) => drawer.classList.toggle("is-loading", loading);
  const showMessage = (message, error = false) => {
    const element = drawer.querySelector("[data-cart-message]");
    element.hidden = false;
    element.textContent = message;
    element.classList.toggle("error", error);
    window.setTimeout(() => { element.hidden = true; }, 3000);
  };

  const render = (cart) => {
    document.querySelectorAll("[data-cart-count]").forEach((element) => element.textContent = cart.item_count);
    drawer.querySelector("[data-drawer-count]").textContent = cart.item_count;
    drawer.querySelector("[data-cart-subtotal]").textContent = cart.subtotal_formatted;
    drawer.querySelector("[data-cart-items]").innerHTML = cart.items.length ? cart.items.map((item) => `
      <article class="quick-cart__item" data-cart-key="${escapeHtml(item.key)}" data-update-url="${escapeHtml(item.update_url)}" data-remove-url="${escapeHtml(item.remove_url)}" data-quantity="${item.quantity}" data-max="${item.available_stock}">
        <a href="${escapeHtml(item.url)}"><img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}"></a>
        <div><a href="${escapeHtml(item.url)}"><h4>${escapeHtml(item.name)}</h4></a><p>${escapeHtml(item.unit_price_formatted)}</p><div class="quick-cart__quantity"><button data-drawer-quantity="-1" type="button">−</button><span>${item.quantity}</span><button data-drawer-quantity="1" type="button">+</button></div></div>
        <div class="quick-cart__line"><strong>${escapeHtml(item.line_total_formatted)}</strong><button data-drawer-remove type="button" aria-label="Remove item">×</button></div>
      </article>`).join("") : '<div class="quick-cart__empty">Your cart is currently empty.</div>';
  };

  const request = async (url, options = {}) => {
    const response = await fetch(url, {
      ...options,
      headers: { Accept: "application/json", "X-CSRF-TOKEN": csrf, "X-Requested-With": "XMLHttpRequest", ...(options.headers || {}) }
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || "Unable to update cart.");
    return data;
  };

  document.addEventListener("submit", async (event) => {
    const form = event.target.closest(".js-ajax-add-cart");
    if (!form) return;
    event.preventDefault();
    const button = form.querySelector("button[type=submit], button:not([type])");
    button?.setAttribute("disabled", "disabled");
    setLoading(true);
    try {
      const data = await request(form.action, { method: "POST", body: new FormData(form) });
      render(data.cart);
      showMessage(data.message);
      setOpen(true);
    } catch (error) {
      showMessage(error.message, true);
      setOpen(true);
    } finally {
      button?.removeAttribute("disabled");
      setLoading(false);
    }
  });

  drawer.addEventListener("click", async (event) => {
    if (event.target.closest(".quick-cart__close")) return setOpen(false);
    const item = event.target.closest(".quick-cart__item");
    if (!item) return;

    const remove = event.target.closest("[data-drawer-remove]");
    const quantityButton = event.target.closest("[data-drawer-quantity]");
    if (!remove && !quantityButton) return;

    setLoading(true);
    try {
      if (remove) {
        const data = await request(item.dataset.removeUrl, { method: "DELETE" });
        render(data.cart);
      } else {
        const quantity = Math.max(0, Math.min(Number(item.dataset.max), Number(item.dataset.quantity) + Number(quantityButton.dataset.drawerQuantity)));
        const data = await request(item.dataset.updateUrl, { method: "PUT", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ quantity }) });
        render(data.cart);
      }
    } catch (error) {
      showMessage(error.message, true);
    } finally {
      setLoading(false);
    }
  });

  document.querySelectorAll(".js-cart-trigger").forEach((trigger) => trigger.addEventListener("click", (event) => { event.preventDefault(); setOpen(true); }));
  overlay.addEventListener("click", () => setOpen(false));
  document.addEventListener("keydown", (event) => { if (event.key === "Escape") setOpen(false); });
})();
