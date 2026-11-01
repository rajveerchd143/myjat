/* ==================================================
   MY JAT — Header & Footer
   ================================================== */

(() => {
  // --------------------------------------------------
  // Mobile Header Menu
  // --------------------------------------------------

  const toggle = document.querySelector(".myjat-menu-toggle");
  const menu = document.querySelector(".myjat-header-menu");

  let touchStartX = 0;

  menu?.addEventListener(
    "touchstart",
    (event) => {
      touchStartX = event.changedTouches[0].screenX;
    },
    { passive: true },
  );

  menu?.addEventListener(
    "touchend",
    (event) => {
      const touchEndX = event.changedTouches[0].screenX;
      const swipeDistance = touchEndX - touchStartX;

      // Right swipe closes the menu
      if (swipeDistance > 70) {
        menu.classList.remove("is-open");
        toggle?.classList.remove("is-active");
        toggle?.setAttribute("aria-expanded", "false");
      }
    },
    { passive: true },
  );

  if (toggle && menu) {
    toggle.addEventListener("click", () => {
      const isOpen = menu.classList.toggle("is-open");

      toggle.classList.toggle("is-active", isOpen);
      toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });

    menu.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", () => {
        menu.classList.remove("is-open");
        toggle.classList.remove("is-active");
        toggle.setAttribute("aria-expanded", "false");
      });
    });

    document.addEventListener("click", (event) => {
      if (!menu.contains(event.target) && !toggle.contains(event.target)) {
        menu.classList.remove("is-open");
        toggle.classList.remove("is-active");
        toggle.setAttribute("aria-expanded", "false");
      }
    });
  }
})();
