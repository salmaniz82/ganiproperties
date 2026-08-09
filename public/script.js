(function () {
  "use strict";

  var menuButton = document.querySelector(".menu-toggle");
  var mobileNavigation = document.querySelector(".mobile-nav");
  var notice = document.querySelector(".search-notice");
  var noticeTimer;

  function showNotice(message) {
    if (!notice) return;
    notice.textContent = message;
    notice.classList.add("is-visible");
    window.clearTimeout(noticeTimer);
    noticeTimer = window.setTimeout(function () { notice.classList.remove("is-visible"); }, 3200);
  }

  function closeMenu() {
    if (!menuButton || !mobileNavigation) return;
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.setAttribute("aria-label", "Open navigation");
    mobileNavigation.classList.remove("is-open");
  }

  if (menuButton && mobileNavigation) {
    menuButton.addEventListener("click", function () {
      var isOpen = menuButton.getAttribute("aria-expanded") === "true";
      menuButton.setAttribute("aria-expanded", String(!isOpen));
      menuButton.setAttribute("aria-label", isOpen ? "Open navigation" : "Close navigation");
      mobileNavigation.classList.toggle("is-open", !isOpen);
    });

    mobileNavigation.addEventListener("click", function (event) {
      if (event.target.tagName === "A") closeMenu();
    });

    document.addEventListener("click", function (event) {
      if (!mobileNavigation.contains(event.target) && !menuButton.contains(event.target)) closeMenu();
    });
  }

  document.querySelectorAll(".save-property").forEach(function (button) {
    button.addEventListener("click", function () {
      var isSaved = button.getAttribute("aria-pressed") === "true";
      button.setAttribute("aria-pressed", String(!isSaved));
      button.classList.toggle("is-saved", !isSaved);
    });
  });

  document.querySelectorAll("[data-property-filter]").forEach(function (form) {
    var sectionSelect = form.querySelector("[data-filter-section]");

    function setGroupState(selector, enabled) {
      form.querySelectorAll(selector).forEach(function (group) {
        group.hidden = !enabled;
        group.querySelectorAll("select, input").forEach(function (field) { field.disabled = !enabled; });
      });
    }

    function updateFilter() {
      var section = sectionSelect ? sectionSelect.value : "all";
      var isCommercial = section === "commercial";
      var priceSection = section === "all" ? "buy" : section;
      var destination = form.getAttribute("data-" + section + "-url") || form.getAttribute("data-home-url");

      form.setAttribute("action", destination);
      setGroupState("[data-filter-residential]", !isCommercial);
      setGroupState("[data-filter-commercial]", isCommercial);
      form.querySelectorAll("[data-filter-price]").forEach(function (group) {
        var enabled = group.getAttribute("data-filter-price") === priceSection;
        group.hidden = !enabled;
        group.querySelectorAll("select").forEach(function (field) { field.disabled = !enabled; });
      });
    }

    if (sectionSelect) sectionSelect.addEventListener("change", updateFilter);
    form.addEventListener("submit", updateFilter);
    updateFilter();
  });

  var galleryMainImage = document.querySelector(".gallery-main img");
  if (galleryMainImage) {
    document.querySelectorAll(".gallery-thumb").forEach(function (button) {
      button.addEventListener("click", function () {
        var image = button.getAttribute("data-gallery-image");
        if (image) galleryMainImage.setAttribute("src", image);
      });
    });
  }

  document.querySelectorAll(".share-property").forEach(function (button) {
    button.addEventListener("click", async function () {
      var shareData = { title: button.getAttribute("data-share-title") || document.title, url: window.location.href };
      try {
        if (navigator.share) {
          await navigator.share(shareData);
        } else if (navigator.clipboard) {
          await navigator.clipboard.writeText(shareData.url);
          showNotice("Property link copied to your clipboard.");
        }
      } catch (error) {
        if (error && error.name !== "AbortError") showNotice("Unable to share this property right now.");
      }
    });
  });

  var year = document.getElementById("year");
  if (year) year.textContent = new Date().getFullYear();
})();
