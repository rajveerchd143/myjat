/* =========================================================
   MY JAT - Photo Gallery
   ========================================================= */

document.addEventListener("DOMContentLoaded", function () {

    const gallery = document.querySelector(".myjat-gallery");

    if (!gallery) {
        return;
    }

    const track = gallery.querySelector(".myjat-gallery-track");
    const items = Array.from(
        gallery.querySelectorAll(".myjat-gallery-item")
    );

    const prev = gallery.querySelector(".myjat-gallery-prev");
    const next = gallery.querySelector(".myjat-gallery-next");

    if (!track || !items.length) {
        return;
    }

    let index = 0;
    let timer = null;
    let paused = false;


    /* =====================================================
       Visible Photos
       ===================================================== */

    function visibleCount() {

        if (window.innerWidth <= 600) {
            return 1;
        }

        if (window.innerWidth <= 900) {
            return 2;
        }

        return 4;
    }


    /* =====================================================
       Move Gallery
       ===================================================== */

    function moveTo(newIndex) {

        const count = visibleCount();

        const maxIndex = Math.max(
            0,
            items.length - count
        );

        if (newIndex > maxIndex) {
            newIndex = 0;
        }

        if (newIndex < 0) {
            newIndex = maxIndex;
        }

        index = newIndex;

        const item = items[0];

        const itemWidth =
            item.getBoundingClientRect().width;

        const gap =
            parseFloat(
                getComputedStyle(track).gap
            ) || 0;

        track.style.transform =
            "translateX(-" +
            ((itemWidth + gap) * index) +
            "px)";
    }


    /* =====================================================
       Auto Move
       ===================================================== */

    function stopAutoMove() {

        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }


    function startAutoMove() {

        stopAutoMove();

        if (items.length <= visibleCount()) {
            return;
        }

        timer = setInterval(function () {

            if (!paused) {
                moveTo(index + 1);
            }

        }, 5000);
    }


    /* =====================================================
       Arrow Controls
       ===================================================== */

    if (prev) {

        prev.addEventListener("click", function () {

            moveTo(index - 1);

            startAutoMove();

        });

    }


    if (next) {

        next.addEventListener("click", function () {

            moveTo(index + 1);

            startAutoMove();

        });

    }


    /* =====================================================
       Pause on Hover
       ===================================================== */

    gallery.addEventListener(
        "mouseenter",
        function () {
            paused = true;
        }
    );


    gallery.addEventListener(
        "mouseleave",
        function () {
            paused = false;
        }
    );


    /* =====================================================
       Mobile Touch
       ===================================================== */

    gallery.addEventListener(
        "touchstart",
        function () {
            paused = true;
        },
        {
            passive: true
        }
    );


    gallery.addEventListener(
        "touchend",
        function () {
            paused = false;
        },
        {
            passive: true
        }
    );


    /* =====================================================
       Resize
       ===================================================== */

    window.addEventListener(
        "resize",
        function () {

            moveTo(
                Math.min(
                    index,
                    Math.max(
                        0,
                        items.length - visibleCount()
                    )
                )
            );

            startAutoMove();

        }
    );


    /* =====================================================
       Lightbox
       ===================================================== */

    const lightbox =
        document.querySelector(
            ".myjat-gallery-lightbox"
        );

    const lightboxImage =
        lightbox
            ? lightbox.querySelector(
                ".myjat-gallery-lightbox-image"
            )
            : null;

    const lightboxCaption =
        lightbox
            ? lightbox.querySelector(
                ".myjat-gallery-lightbox-caption"
            )
            : null;

    const lightboxClose =
        lightbox
            ? lightbox.querySelector(
                ".myjat-gallery-lightbox-close"
            )
            : null;


    function closeLightbox() {

        if (!lightbox) {
            return;
        }

        lightbox.classList.remove("is-open");

        lightbox.setAttribute(
            "aria-hidden",
            "true"
        );

        document.body.style.overflow = "";

    }


    items.forEach(function (item) {

        item.addEventListener(
            "click",
            function () {

                if (!lightbox || !lightboxImage) {
                    return;
                }

                const image =
                    item.getAttribute(
                        "data-gallery-image"
                    ) || "";

                const caption =
                    item.getAttribute(
                        "data-gallery-caption"
                    ) || "";


                lightboxImage.src = image;

                lightboxImage.alt =
                    caption ||
                    "MY JAT Gallery";


                if (lightboxCaption) {

                    lightboxCaption.textContent =
                        caption;

                }


                lightbox.classList.add(
                    "is-open"
                );

                lightbox.setAttribute(
                    "aria-hidden",
                    "false"
                );

                document.body.style.overflow =
                    "hidden";

            }
        );

    });


    if (lightboxClose) {

        lightboxClose.addEventListener(
            "click",
            closeLightbox
        );

    }


    if (lightbox) {

        lightbox.addEventListener(
            "click",
            function (event) {

                if (event.target === lightbox) {
                    closeLightbox();
                }

            }
        );

    }


    document.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Escape") {
                closeLightbox();
            }

        }
    );


    /* =====================================================
       Start Gallery
       ===================================================== */

    moveTo(0);

    startAutoMove();

});