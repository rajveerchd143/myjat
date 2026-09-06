document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("myjat-membership-form");

    if (!form) {
        return;
    }


    // =====================================================
    // Registration Steps
    // =====================================================

    const steps = Array.from(
        form.querySelectorAll(".myjat-registration-step")
    );

    const progressSteps = Array.from(
        form.querySelectorAll(".myjat-progress-step")
    );

    let currentStep = 1;


    // =====================================================
    // Show Step
    // =====================================================

    function showStep(stepNumber) {

        currentStep = stepNumber;

        steps.forEach(function (step) {

            const number = Number(step.dataset.step);

            step.classList.toggle(
                "is-active",
                number === stepNumber
            );

        });


        progressSteps.forEach(function (step) {

            const number = Number(step.dataset.step);

            step.classList.toggle(
                "is-active",
                number === stepNumber
            );

            step.classList.toggle(
                "is-complete",
                number < stepNumber
            );

        });


        form.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }


    // =====================================================
    // Validate Current Step
    // =====================================================

    function validateCurrentStep() {

        const current =
            form.querySelector(
                '.myjat-registration-step[data-step="' +
                currentStep +
                '"]'
            );

        if (!current) {
            return true;
        }


        const fields =
            current.querySelectorAll(
                "input, select, textarea"
            );


        for (const field of fields) {

            if (!field.checkValidity()) {

                field.reportValidity();

                return false;
            }

        }


        return true;
    }


    // =====================================================
    // NEXT BUTTON
    // =====================================================

    form.querySelectorAll(".myjat-btn-next")
        .forEach(function (button) {

            button.addEventListener("click", function () {

                if (!validateCurrentStep()) {
                    return;
                }


                if (currentStep < steps.length) {

                    showStep(
                        currentStep + 1
                    );

                }

            });

        });


    // =====================================================
    // BACK BUTTON
    // =====================================================

    form.querySelectorAll(".myjat-btn-prev")
        .forEach(function (button) {

            button.addEventListener("click", function () {

                if (currentStep > 1) {

                    showStep(
                        currentStep - 1
                    );

                }

            });

        });


        // =====================================================
// DOB - DD/MM/YYYY
// =====================================================

const dobDisplay =
    document.getElementById("dob_display");

const dobHidden =
    document.getElementById("dob");


if (dobDisplay && dobHidden) {

    function updateDOB() {

        let value =
            dobDisplay.value.replace(/\D/g, "");

        if (value.length > 8) {
            value = value.substring(0, 8);
        }


        if (value.length > 4) {

            dobDisplay.value =
                value.substring(0, 2) + "/" +
                value.substring(2, 4) + "/" +
                value.substring(4);

        } else if (value.length > 2) {

            dobDisplay.value =
                value.substring(0, 2) + "/" +
                value.substring(2);

        } else {

            dobDisplay.value = value;

        }


        const match =
            dobDisplay.value.match(
                /^(\d{2})\/(\d{2})\/(\d{4})$/
            );


        if (!match) {

            dobHidden.value = "";

            return;
        }


        const day = Number(match[1]);
        const month = Number(match[2]);
        const year = Number(match[3]);


        const date =
            new Date(
                year,
                month - 1,
                day
            );


        if (
            date.getFullYear() === year &&
            date.getMonth() === month - 1 &&
            date.getDate() === day
        ) {

            dobHidden.value =
                year + "-" +
                String(month).padStart(2, "0") + "-" +
                String(day).padStart(2, "0");

        } else {

            dobHidden.value = "";

        }

    }


    dobDisplay.addEventListener(
        "input",
        updateDOB
    );


    form.addEventListener(
        "submit",
        function (event) {

            updateDOB();


            if (!dobHidden.value) {

                event.preventDefault();

                alert(
                    "कृपया जन्म तिथि dd/mm/yyyy प्रारूप में सही भरें।"
                );

                dobDisplay.focus();

            }

        }
    );

}


// =====================================================
// SAME ADDRESS
// =====================================================

const sameAddress =
    document.getElementById("same-address");

const currentAddress =
    document.getElementById("current_address");

const permanentAddress =
    document.getElementById("permanent_address");


if (
    sameAddress &&
    currentAddress &&
    permanentAddress
) {

    sameAddress.addEventListener(
        "change",
        function () {

            if (this.checked) {

                permanentAddress.value =
                    currentAddress.value;

            } else {

                permanentAddress.value = "";

            }

        }
    );


    currentAddress.addEventListener(
        "input",
        function () {

            if (sameAddress.checked) {

                permanentAddress.value =
                    currentAddress.value;

            }

        }
    );

}
    // =====================================================
    // PHOTO PREVIEW
    // =====================================================

    const photo =
        document.getElementById("photo");

    const preview =
        document.getElementById(
            "myjat-photo-preview"
        );


    if (photo && preview) {

        photo.addEventListener(
            "change",
            function () {

                const file =
                    this.files[0];


                if (!file) {

                    preview.src = "";
                    preview.style.display = "none";

                    return;
                }


                if (!file.type.startsWith("image/")) {

                    this.value = "";

                    preview.src = "";
                    preview.style.display = "none";

                    return;
                }


                const reader =
                    new FileReader();


                reader.onload = function (event) {

                    preview.src =
                        event.target.result;

                    preview.style.display =
                        "block";

                };


                reader.readAsDataURL(file);

            }
        );

    }


    // =====================================================
    // Start
    // =====================================================

    showStep(1);

});