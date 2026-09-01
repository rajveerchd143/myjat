// ==================================================
// MY JAT — Header & Footer
// ==================================================

(() => {

    // --------------------------------------------------
    // Mobile Header Menu
    // --------------------------------------------------

    const toggle = document.querySelector(".myjat-menu-toggle");
    const nav = document.querySelector(".myjat-nav");

    if (toggle && nav) {

        toggle.addEventListener("click", () => {

            const isOpen = nav.classList.toggle("is-open");

            toggle.setAttribute(
                "aria-expanded",
                isOpen ? "true" : "false"
            );

        });

        nav.querySelectorAll("a").forEach(link => {

            link.addEventListener("click", () => {

                nav.classList.remove("is-open");

                toggle.setAttribute(
                    "aria-expanded",
                    "false"
                );

            });

        });

        document.addEventListener("click", (event) => {

         if (!nav.contains(event.target) && !toggle.contains(event.target)) {

        nav.classList.remove("is-open");
        toggle.classList.remove("is-active");

        toggle.setAttribute("aria-expanded", "false");

         }

        });

    }

})();