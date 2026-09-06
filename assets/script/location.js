document.addEventListener("DOMContentLoaded", function () {

    const state =
        document.getElementById("myjat-state");

    const district =
        document.getElementById("myjat-district");

    const block =
        document.getElementById("myjat-block");

    const village =
        document.getElementById("myjat-village");

    const pincode =
        document.getElementById("myjat-pincode");


    if (
        !state ||
        !district ||
        !block ||
        !village
    ) {
        return;
    }


    if (
        !window.myjatLocation ||
        !window.myjatLocation.ajax_url
    ) {
        return;
    }


    const ajaxUrl =
        window.myjatLocation.ajax_url;


    // =====================================================
    // Helper
    // =====================================================

    function resetSelect(select, text) {

        select.innerHTML = "";

        const option =
            document.createElement("option");

        option.value = "";
        option.textContent = text;

        select.appendChild(option);

        select.disabled = false;
    }


    function setLoading(select) {

        select.innerHTML = "";

        const option =
            document.createElement("option");

        option.value = "";
        option.textContent =
            "लोड हो रहा है...";

        select.appendChild(option);

        select.disabled = true;
    }


    function loadLocation(
        type,
        data,
        select,
        placeholder
    ) {

        setLoading(select);


        const body =
            new URLSearchParams();

        body.append(
            "action",
            "myjat_location"
        );

        body.append(
            "type",
            type
        );


        Object.keys(data).forEach(
            function (key) {

                body.append(
                    key,
                    data[key]
                );

            }
        );


        fetch(
            ajaxUrl,
            {
                method: "POST",

                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body:
                    body.toString()
            }
        )

        .then(function (response) {

            if (!response.ok) {
                throw new Error(
                    "Location request failed"
                );
            }

            return response.json();

        })

        .then(function (result) {

            resetSelect(
                select,
                placeholder
            );


            if (
                !result.success ||
                !Array.isArray(result.data)
            ) {
                return;
            }


            result.data.forEach(
                function (item) {

                    const option =
                        document.createElement(
                            "option"
                        );

                    option.value = item;
                    option.textContent = item;

                    select.appendChild(
                        option
                    );

                }
            );

        })

        .catch(function () {

            resetSelect(
                select,
                placeholder
            );

        });

    }


    // =====================================================
    // STATE → DISTRICT
    // =====================================================

    state.addEventListener(
        "change",
        function () {

            const value =
                this.value.trim();


            resetSelect(
                district,
                "जिला चुनें"
            );

            resetSelect(
                block,
                "पहले जिला चुनें"
            );

            resetSelect(
                village,
                "पहले ब्लॉक चुनें"
            );


            if (!value) {
                return;
            }


            loadLocation(
                "district",
                {
                    parent: value
                },
                district,
                "जिला चुनें"
            );

        }
    );


    // =====================================================
    // DISTRICT → BLOCK
    // =====================================================

    district.addEventListener(
        "change",
        function () {

            const value =
                this.value.trim();


            resetSelect(
                block,
                "ब्लॉक चुनें"
            );

            resetSelect(
                village,
                "पहले ब्लॉक चुनें"
            );


            if (!value) {
                return;
            }


            loadLocation(
                "block",
                {
                    parent: value
                },
                block,
                "ब्लॉक चुनें"
            );

        }
    );


    // =====================================================
    // BLOCK → VILLAGE / PANCHAYAT
    // =====================================================

    block.addEventListener(
        "change",
        function () {

            const value =
                this.value.trim();


            resetSelect(
                village,
                "ग्राम / पंचायत चुनें"
            );


            if (!value) {
                return;
            }


            loadLocation(
                "village",
                {
                    state: state.value,
                    district: district.value,
                    block: value
                },
                village,
                "ग्राम / पंचायत चुनें"
            );

        }
    );


    // =====================================================
    // VILLAGE → PINCODE
    // =====================================================

    village.addEventListener(
        "change",
        function () {

            const value =
                this.value.trim();


            if (!pincode) {
                return;
            }


            if (!value) {

                pincode.value = "";

                return;
            }


            pincode.value =
                "लोड हो रहा है...";

            pincode.readOnly = true;


            const body =
                new URLSearchParams();


            body.append(
                "action",
                "myjat_location"
            );

            body.append(
                "type",
                "pincode"
            );

            body.append(
                "state",
                state.value
            );

            body.append(
                "district",
                district.value
            );

            body.append(
                "block",
                block.value
            );

            body.append(
                "village",
                value
            );


            fetch(
                ajaxUrl,
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/x-www-form-urlencoded"
                    },

                    body:
                        body.toString()
                }
            )

            .then(function (response) {

                if (!response.ok) {
                    throw new Error(
                        "PIN request failed"
                    );
                }

                return response.json();

            })

            .then(function (result) {

                if (
                    result.success &&
                    result.data
                ) {

                    pincode.value =
                        result.data;

                } else {

                    pincode.value = "";

                }

            })

            .catch(function () {

                pincode.value = "";

            })

            .finally(function () {

                pincode.readOnly = false;

            });

        }
    );

});