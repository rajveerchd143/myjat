

document.addEventListener("DOMContentLoaded", function () {

    if (typeof TomSelect === "undefined") {
        return;
    }

    /* ==========================
       STATE
    ========================== */

const state = new TomSelect("#myjat-state", {
    create: false,
    searchField: ["text"],
    placeholder: "राज्य चुनें",
    allowEmptyOption: true
});
    /* ==========================
       DISTRICT
    ========================== */

const district = new TomSelect("#myjat-district", {
    create: false,
    searchField: ["text"],
    placeholder: "पहले राज्य चुनें",
    allowEmptyOption: true
});
  /* ==========================
       Block
    ========================== */

const block = new TomSelect("#myjat-block", {
    create: false,
    placeholder: "पहले जिला चुनें",
    allowEmptyOption: true
});
/* ==========================
       Village
    ========================== */

 const village = new TomSelect("#myjat-village", {
    create: false,
    searchField: ["text"],
    placeholder: "पहले ब्लॉक चुनें",
    allowEmptyOption: true
});
    /* ==========================
       State Change
    ========================== */

state.on("change", function (value) {
        district.clear();
        district.clearOptions();
        district.addOption({
    value: "",
});
    

        district.refreshOptions(false);

        fetch(myjatLocation.ajax_url, {

            method: "POST",

            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },

            body:
                "action=myjat_location&type=district&parent=" +
                encodeURIComponent(value)

        })

        .then(response => response.json())

        .then(result => {

            district.clearOptions();

            if (!result.success) {
                return;
            }

            result.data.forEach(function (item) {

                district.addOption({
                    value: item,
                    text: item
                });

            });

            district.refreshOptions(false);

        });

    });

    /* ==========================
   District Change
========================== */

district.on("change", function (value) {

    block.clear();
    block.clearOptions();

    block.addOption({
        value: "",
        text: "लोड हो रहा है..."
    });

    block.refreshOptions(false);

    fetch(myjatLocation.ajax_url, {

        method: "POST",

        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },

        body:
            "action=myjat_location&type=block&parent=" +
            encodeURIComponent(value)

    })

    .then(response => response.json())

    .then(result => {

        block.clearOptions();

        if (!result.success) {
            return;
        }

        result.data.forEach(function (item) {

            block.addOption({
                value: item,
                text: item
            });

        });

        block.refreshOptions(false);

    });

});


/* ==========================
   Block Change
========================== */

block.on("change", function (value) {

    village.clear();
    village.clearOptions();

    village.addOption({
        value: "",
        text: "लोड हो रहा है..."
    });

    village.refreshOptions(false);

    fetch(myjatLocation.ajax_url, {

        method: "POST",

        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },

        body:
            "action=myjat_location" +
            "&type=village" +
            "&state=" + encodeURIComponent(state.getValue()) +
            "&district=" + encodeURIComponent(district.getValue()) +
            "&block=" + encodeURIComponent(value)

    })

    .then(response => response.json())

    .then(result => {

        village.clearOptions();

        if (!result.success) {
            return;
        }

        result.data.forEach(function (item) {

            village.addOption({
                value: item,
                text: item
            });

        });

        village.refreshOptions(false);

    });

});


/* ==========================
   Village change 
========================== */
village.on("change", function (value) {

fetch(myjatLocation.ajax_url, {

    method: "POST",

    headers: {
        "Content-Type": "application/x-www-form-urlencoded"
    },

    body:
        "action=myjat_location" +
        "&type=pincode" +
        "&state=" + encodeURIComponent(state.getValue()) +
        "&district=" + encodeURIComponent(district.getValue()) +
        "&block=" + encodeURIComponent(block.getValue()) +
        "&village=" + encodeURIComponent(value)

})

    .then(response => response.json())

    .then(result => {

        const pin = document.querySelector("#myjat-pincode");

        if (!pin) return;

if (result.success && result.data) {

    pin.value = result.data;

} else {

    pin.value = "";

}
pin.value = result.data || "";
pin.readOnly = false;
pin.classList.remove("myjat-readonly");


    });

});

});