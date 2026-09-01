// ==================================================
// MYJAT Background Slideshow
// Lightweight true crossfade
// ==================================================

(() => {

    const section = document.body;

    if (!section) {
        return;
    }

    const images = [
        "/assets/images/background/back-1.webp",
        "/assets/images/background/back-2.webp",
        "/assets/images/background/back-3.webp",
        "/assets/images/background/back-4.webp",
        "/assets/images/background/back-5.webp"
    ];

    let current = 0;
    let next = 1;
    let transitioning = false;

    // Initial load: ONLY first image.
    section.style.setProperty(
        "--myjat-bg-current",
        `url("${images[current]}")`
    );

    section.style.setProperty(
        "--myjat-bg-next",
        "none"
    );

    function changeBackground() {

        if (transitioning) {
            return;
        }

        transitioning = true;

        const image = new Image();

        image.onload = () => {

            // Put the fully loaded image into hidden layer.
            section.style.setProperty(
                "--myjat-bg-next",
                `url("${images[next]}")`
            );

            // Give browser time to paint it.
            requestAnimationFrame(() => {

                requestAnimationFrame(() => {

                    // True crossfade.
                    section.classList.add("myjat-bg-fade");

                    setTimeout(() => {

                        /*
                         * DO NOT replace current image here.
                         *
                         * Both layers are already showing the
                         * same image after the fade.
                         *
                         * We simply reset the opacity state.
                         */

                        section.classList.remove(
                            "myjat-bg-fade"
                        );

                        /*
                         * Keep the new image as the current
                         * visual layer and prepare the variables
                         * for the next cycle.
                         */
                        section.style.setProperty(
                            "--myjat-bg-current",
                            `url("${images[next]}")`
                        );

                        section.style.setProperty(
                            "--myjat-bg-next",
                            "none"
                        );

                        current = next;
                        next = (current + 1) % images.length;

                        transitioning = false;

                    }, 2050);

                });

            });

        };

        image.onerror = () => {
            transitioning = false;
        };

        // Load ONLY when needed.
        image.src = images[next];
    }

    setInterval(changeBackground, 9000);

})();

// ==================================================
// Mouse and Leaf
// ==================================================

(() => {

    const glow = document.createElement("div");
    glow.className = "myjat-mouse-glow";
    document.body.appendChild(glow);

    let x = window.innerWidth / 2;
    let y = window.innerHeight / 2;
    let tx = x;
    let ty = y;

    document.addEventListener("mousemove", (e) => {
        tx = e.clientX;
        ty = e.clientY;
    }, { passive: true });

    function animate() {

        x += (tx - x) * 0.08;
        y += (ty - y) * 0.08;

        glow.style.transform =
            `translate(${x - 250}px, ${y - 250}px)`;

        requestAnimationFrame(animate);
    }

    animate();


    // ==================================================
    // Floating Leaves
    // ==================================================

    const leaves = document.createElement("div");
    leaves.className = "myjat-leaves";

    const leafImages = [
        "/assets/images/background/leaf-1.png",
        "/assets/images/background/leaf-2.png"
    ];

    for (let i = 0; i < 6; i++) {

        const leaf = document.createElement("div");
        leaf.className = "myjat-leaf";

        leaf.style.backgroundImage =
            `url("${leafImages[i % 2]}")`;

        leaves.appendChild(leaf);
    }

    document.body.prepend(leaves);


    // ==================================================
    // Site Background Canvas
    // ==================================================

    const canvas = document.createElement("div");
    canvas.className = "myjat-site-bg";

    document.body.prepend(canvas);


    // ==================================================
    // Light Rays
    // ==================================================

    const rays = document.createElement("div");
    rays.className = "myjat-light-rays";

    for (let i = 0; i < 3; i++) {

        const light = document.createElement("div");
        light.className = "myjat-light";

        rays.appendChild(light);
    }

    document.body.prepend(rays);

})();