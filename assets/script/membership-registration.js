document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("myjat-membership-form");

  if (!form) {
    return;
  }

  // =====================================================
  // Registration Steps
  // =====================================================

  const steps = Array.from(form.querySelectorAll(".myjat-registration-step"));

  const progressSteps = Array.from(
    form.querySelectorAll(".myjat-progress-step"),
  );

  let currentStep = 1;

  // =====================================================
  // Show Step
  // =====================================================

  function showStep(stepNumber) {
    currentStep = stepNumber;

    steps.forEach(function (step) {
      const number = Number(step.dataset.step);

      step.classList.toggle("is-active", number === stepNumber);
    });

    progressSteps.forEach(function (step) {
      const number = Number(step.dataset.step);

      step.classList.toggle("is-active", number === stepNumber);

      step.classList.toggle("is-complete", number < stepNumber);
    });

    form.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }

  // =====================================================
  // Validate Current Step
  // =====================================================

  function validateCurrentStep() {
    const current = form.querySelector(
      '.myjat-registration-step[data-step="' + currentStep + '"]',
    );

    if (!current) {
      return true;
    }

    const fields = current.querySelectorAll("input, select, textarea");

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

  form.querySelectorAll(".myjat-btn-next").forEach(function (button) {
    button.addEventListener("click", function () {
      if (!validateCurrentStep()) {
        return;
      }

      if (currentStep < steps.length) {
        showStep(currentStep + 1);
      }
    });
  });

  // =====================================================
  // BACK BUTTON
  // =====================================================

  form.querySelectorAll(".myjat-btn-prev").forEach(function (button) {
    button.addEventListener("click", function () {
      if (currentStep > 1) {
        showStep(currentStep - 1);
      }
    });
  });

  // =====================================================
  // DOB - DD/MM/YYYY
  // =====================================================

  const dobDisplay = document.getElementById("dob_display");

  const dobHidden = document.getElementById("dob");

  if (dobDisplay && dobHidden) {
    function updateDOB() {
      let value = dobDisplay.value.replace(/\D/g, "");

      if (value.length > 8) {
        value = value.substring(0, 8);
      }

      if (value.length > 4) {
        dobDisplay.value =
          value.substring(0, 2) +
          "/" +
          value.substring(2, 4) +
          "/" +
          value.substring(4);
      } else if (value.length > 2) {
        dobDisplay.value = value.substring(0, 2) + "/" + value.substring(2);
      } else {
        dobDisplay.value = value;
      }

      const match = dobDisplay.value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);

      if (!match) {
        dobHidden.value = "";

        return;
      }

      const day = Number(match[1]);
      const month = Number(match[2]);
      const year = Number(match[3]);

      const date = new Date(year, month - 1, day);

      if (
        date.getFullYear() === year &&
        date.getMonth() === month - 1 &&
        date.getDate() === day
      ) {
        dobHidden.value =
          year +
          "-" +
          String(month).padStart(2, "0") +
          "-" +
          String(day).padStart(2, "0");
      } else {
        dobHidden.value = "";
      }
    }

    dobDisplay.addEventListener("input", updateDOB);

    form.addEventListener("submit", function (event) {
      updateDOB();

      if (!dobHidden.value) {
        event.preventDefault();

        alert("कृपया जन्म तिथि dd/mm/yyyy प्रारूप में सही भरें।");

        dobDisplay.focus();
      }
    });
  }

  // =====================================================
  // SAME ADDRESS
  // =====================================================

  const sameAddress = document.getElementById("same-address");

  const currentAddress = document.getElementById("current_address");

  const permanentAddress = document.getElementById("permanent_address");

  if (sameAddress && currentAddress && permanentAddress) {
    sameAddress.addEventListener("change", function () {
      if (this.checked) {
        permanentAddress.value = currentAddress.value;
      } else {
        permanentAddress.value = "";
      }
    });

    currentAddress.addEventListener("input", function () {
      if (sameAddress.checked) {
        permanentAddress.value = currentAddress.value;
      }
    });
  }

  // =====================================================
  // PHOTO PREVIEW
  // =====================================================

  const photo = document.getElementById("photo");

  const preview = document.getElementById("myjat-photo-preview");

  if (photo && preview) {
    photo.addEventListener("change", function () {
      const file = this.files[0];

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

      const reader = new FileReader();

      reader.onload = function (event) {
        preview.src = event.target.result;

        preview.style.display = "block";
      };

      reader.readAsDataURL(file);
    });
  }

  // =====================================================
  // MEMBERSHIP PLAN / SANKALP
  // =====================================================

  const membershipPlan =
    document.getElementById("membership_plan_id");

  const membershipPlanInfo =
    document.getElementById("membership-plan-info");

  const sankalpContent =
    document.getElementById("membership-sankalp-content");

  const sankalpDeclaration =
    document.getElementById("sankalp_declaration");

  const pratigyaDeclaration =
    document.getElementById("pratigya_declaration");

  if (membershipPlan) {

    function updateMembershipPlan() {

      const selected =
        membershipPlan.options[
          membershipPlan.selectedIndex
        ];

      // -----------------------------------------------
      // No plan selected
      // -----------------------------------------------

      if (!selected || !selected.value) {

        if (membershipPlanInfo) {
          membershipPlanInfo.textContent =
            "कृपया सदस्यता प्रकार चुनें।";
        }

        if (sankalpContent) {
          sankalpContent.style.display = "none";
        }

        if (sankalpDeclaration) {
          sankalpDeclaration.required = false;
        }

        if (pratigyaDeclaration) {
          pratigyaDeclaration.required = true;
        }

        return;
      }

      // -----------------------------------------------
      // Plan data
      // -----------------------------------------------

      const amount =
        selected.dataset.amount || "";

      const validityType =
        selected.dataset.validityType || "";

      const validityValue =
        Number(
          selected.dataset.validityValue || 0
        );

      const requiresSankalp =
        selected.dataset.requiresSankalp === "1";

      const requiresOrdinary =
        selected.dataset.requiresOrdinary === "1";

      // -----------------------------------------------
      // Validity
      // -----------------------------------------------

      let validity = "";

      if (validityType === "lifetime") {

        validity = "आजीवन";

      } else if (
        validityType === "years" &&
        validityValue > 0
      ) {

        validity =
          validityValue + " वर्ष";
      }

      // -----------------------------------------------
      // Plan info
      // -----------------------------------------------

      let info = "";

      if (amount) {

        info =
          "सदस्यता शुल्क: ₹" +
          Number(amount).toLocaleString("en-IN") +
          "/-";
      }

      if (validity) {

        info +=
          " · " + validity;
      }

      if (requiresOrdinary) {

        info +=
          " · साधारण सदस्य होना आवश्यक";
      }

      if (membershipPlanInfo) {

        membershipPlanInfo.textContent =
          info || "कृपया सदस्यता प्रकार चुनें।";
      }

      // -----------------------------------------------
      // Sankalp visibility
      // -----------------------------------------------

      if (sankalpContent) {

        sankalpContent.style.display =
          requiresSankalp ? "" : "none";
      }

      // -----------------------------------------------
      // Sankalp declaration
      // -----------------------------------------------

      if (sankalpDeclaration) {

        sankalpDeclaration.required =
          requiresSankalp;
      }

      // -----------------------------------------------
      // Pratigya declaration
      // -----------------------------------------------

      if (pratigyaDeclaration) {

        pratigyaDeclaration.required = true;
      }
    }

    membershipPlan.addEventListener(
      "change",
      updateMembershipPlan
    );

    updateMembershipPlan();
  }

  // =====================================================
  // RAZORPAY PAYMENT
  // =====================================================

  const paymentOrder = document.getElementById("myjat-payment-order-id");

  const paymentAmount = document.getElementById("myjat-payment-amount");

  const paymentCurrency = document.getElementById("myjat-payment-currency");

  const submitButton = form.querySelector(".myjat-btn-submit");

  // =====================================================
  // OPEN RAZORPAY
  // =====================================================

  function openRazorpay() {
    if (!paymentOrder || !paymentAmount || !paymentCurrency || !submitButton) {
      return;
    }

    if (typeof Razorpay === "undefined") {
      console.error("Razorpay Checkout script is not loaded.");

      return;
    }

    const options = {
      key: window.MYJAT_RAZORPAY_KEY_ID,

      amount: Number(paymentAmount.value),

      currency: paymentCurrency.value,

      name: "अखिल भारतवर्षीय जाट महासभा",

      description: "सदस्यता शुल्क",

      order_id: paymentOrder.value,

      handler: function (response) {
        const verifyData = {
          razorpay_payment_id: response.razorpay_payment_id,

          razorpay_order_id: response.razorpay_order_id,

          razorpay_signature: response.razorpay_signature,
        };

        submitButton.disabled = true;

        submitButton.textContent = "Payment verify हो रहा है...";

        fetch("/membership-payment-verify/", {
          method: "POST",

          headers: {
            "Content-Type": "application/json",
          },

          body: JSON.stringify(verifyData),
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (result) {
            if (result.success) {
              window.location.href = result.redirect || "/register/";

              return;
            }

            submitButton.disabled = false;

            submitButton.textContent = "शुल्क जमा करें और आगे बढ़ें →";

            alert(result.message || "Payment verification असफल हुआ।");
          })
          .catch(function () {
            submitButton.disabled = false;

            submitButton.textContent = "शुल्क जमा करें और आगे बढ़ें →";

            alert(
              "Payment verification के दौरान समस्या हुई। कृपया पुनः प्रयास करें।",
            );
          });
      },

      modal: {
        ondismiss: function () {
          submitButton.disabled = false;

          submitButton.textContent = "शुल्क जमा करें और आगे बढ़ें →";
        },
      },

      prefill: {
        name: document.getElementById("full_name")?.value || "",

        email: document.getElementById("email")?.value || "",

        contact: document.getElementById("mobile_no")?.value || "",
      },
    };

    submitButton.disabled = true;

    submitButton.textContent = "Payment खुल रहा है...";

    const razorpay = new Razorpay(options);

    razorpay.on("payment.failed", function () {
      submitButton.disabled = false;

      submitButton.textContent = "शुल्क जमा करें और आगे बढ़ें →";

      alert("Payment असफल हुआ। कृपया पुनः प्रयास करें।");
    });

    razorpay.open();
  }

  // =====================================================
  // PAYMENT FORM SUBMIT
  // =====================================================

  if (paymentOrder && paymentAmount && paymentCurrency && submitButton) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      if (!validateCurrentStep()) {
        return;
      }

      openRazorpay();
    });
  }

  // =====================================================
  // START
  // =====================================================

  showStep(1);

  // =====================================================
  // AUTO OPEN RAZORPAY AFTER ORDER CREATION
  // =====================================================

  if (paymentOrder && paymentAmount && paymentCurrency && submitButton) {
    if (typeof Razorpay !== "undefined") {
      openRazorpay();
    } else {
      window.addEventListener(
        "load",
        function () {
          if (typeof Razorpay !== "undefined") {
            openRazorpay();
          } else {
            console.error("Razorpay Checkout script is still not loaded.");
          }
        },
        { once: true },
      );
    }
  }
});
