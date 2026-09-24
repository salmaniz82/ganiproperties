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

  var updatesDialog = document.getElementById("updates-dialog");
  var updatesOpener = document.querySelector("[data-updates-open]");
  if (updatesDialog && updatesOpener) {
    var updatesForm = updatesDialog.querySelector("[data-updates-form]");
    var updatesFormContent = updatesDialog.querySelector("[data-updates-form-content]");
    var updatesFeedback = updatesDialog.querySelector("[data-updates-feedback]");
    var updatesSuccess = updatesDialog.querySelector("[data-updates-success]");
    var updatesSuccessMessage = updatesDialog.querySelector("[data-updates-success-message]");
    var updatesSubmit = updatesForm.querySelector('[type="submit"]');
    var updatesCloseTimer;
    var updatesSubmitting = false;

    updatesOpener.addEventListener("click", function () { updatesDialog.showModal(); });
    updatesDialog.querySelector("[data-updates-close]").addEventListener("click", function () { updatesDialog.close(); });
    updatesDialog.addEventListener("click", function (event) {
      if (event.target === updatesDialog) updatesDialog.close();
    });
    updatesDialog.addEventListener("close", function () {
      window.clearTimeout(updatesCloseTimer);
      updatesForm.reset();
      updatesFeedback.hidden = true;
      updatesFeedback.textContent = "";
      updatesSuccess.hidden = true;
      updatesFormContent.hidden = false;
      updatesOpener.focus();
    });

    updatesForm.addEventListener("submit", async function (event) {
      event.preventDefault();
      if (updatesSubmitting) return;
      updatesSubmitting = true;
      updatesSubmit.disabled = true;
      updatesSubmit.textContent = "Registering...";
      updatesFeedback.hidden = true;

      try {
        var response = await fetch(updatesForm.action, {
          method: "POST",
          body: new FormData(updatesForm),
          headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
        });
        var result = await response.json();
        if (! response.ok) {
          var fieldErrors = result.errors ? Object.values(result.errors) : [];
          throw new Error(fieldErrors.length ? fieldErrors[0][0] : (result.message || "Registration failed. Please try again."));
        }
        if (!updatesDialog.open) return;
        updatesSuccessMessage.textContent = result.message;
        updatesFormContent.hidden = true;
        updatesSuccess.hidden = false;
        updatesCloseTimer = window.setTimeout(function () { updatesDialog.close(); }, 3200);
      } catch (error) {
        if (updatesDialog.open) {
          updatesFeedback.textContent = error.message || "Registration failed. Please try again.";
          updatesFeedback.hidden = false;
        }
      } finally {
        updatesSubmitting = false;
        updatesSubmit.disabled = false;
        updatesSubmit.textContent = "Register for updates";
      }
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

    if (sectionSelect) sectionSelect.addEventListener("change", function () {
      var section = sectionSelect.value;
      var currentSection = form.getAttribute("data-current-section");
      if (currentSection !== "all" && section !== currentSection) {
        var destination = form.getAttribute("data-" + section + "-url");
        if (destination) {
          window.location.assign(destination);
          return;
        }
      }
      updateFilter();
    });
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
