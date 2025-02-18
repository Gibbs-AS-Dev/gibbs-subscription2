jQuery(document).ready(function () {
    var inputs = document.querySelectorAll(".gibbs_phonenumber");
    if (inputs && inputs.length > 0) {

        var lang_code = "no";
        if (jQuery("html").attr("lang") != undefined) {
            var langg = jQuery("html").attr("lang");
            lang_code = langg.split("-")[1];
            if (lang_code != "") {
                lang_code = lang_code.toLowerCase();
            }
        }

        // Phone number length mapping
        var phoneLengthMapping = {
            "af": { min: 9, max: 9 },
            "al": { min: 9, max: 9 },
            "dz": { min: 9, max: 9 },
            "ad": { min: 6, max: 6 },
            "ao": { min: 9, max: 9 },
            "ar": { min: 10, max: 10 },
            "au": { min: 9, max: 9 },
            "at": { min: 10, max: 13 },
            "bd": { min: 10, max: 10 },
            "be": { min: 8, max: 9 },
            "br": { min: 10, max: 11 },
            "ca": { min: 10, max: 10 },
            "cn": { min: 11, max: 11 },
            "dk": { min: 8, max: 8 },
            "eg": { min: 10, max: 10 },
            "fi": { min: 7, max: 12 },
            "fr": { min: 9, max: 9 },
            "de": { min: 7, max: 15 },
            "in": { min: 10, max: 10 },
            "id": { min: 9, max: 12 },
            "it": { min: 9, max: 10 },
            "jp": { min: 10, max: 11 },
            "mx": { min: 10, max: 10 },
            "nl": { min: 9, max: 9 },
            "no": { min: 8, max: 8 },
            "pk": { min: 10, max: 10 },
            "pl": { min: 9, max: 9 },
            "ru": { min: 10, max: 10 },
            "sa": { min: 9, max: 9 },
            "za": { min: 9, max: 9 },
            "es": { min: 9, max: 9 },
            "se": { min: 7, max: 13 },
            "ch": { min: 9, max: 9 },
            "tr": { min: 10, max: 10 },
            "gb": { min: 10, max: 10 },
            "us": { min: 10, max: 10 },
            "vn": { min: 9, max: 11 }
        };

        inputs.forEach(function (input) {
            var iti = window.intlTelInput(input, {
                initialCountry: lang_code,
                allowExtensions: true,
                formatOnDisplay: true,
                autoFormat: true,
                numberType: "MOBILE",
                preventInvalidNumbers: true,
                separateDialCode: true,
                utilsScript: mySiteData.stylesheet_uri + "/assets/js/utils.js?ver=5.7.2",
            });

            input.addEventListener("countrychange", function () {
                var dialCode = iti.getSelectedCountryData().dialCode;
                jQuery(".country_code").val(dialCode);
            });

            // Validate on input
            // input.addEventListener("keypress", function (e) {
            //     var countryData = iti.getSelectedCountryData();
            //     var countryCode = countryData.iso2;
            //     var maxLength = phoneLengthMapping[countryCode]?.max || 15; // Default to 15 if no mapping exists

            //     // Get current input value length
            //     var phoneNumber = input.value.replace(/\s/g, ""); // Remove whitespace

            //     if (phoneNumber.length >= maxLength && !e.metaKey && !e.ctrlKey) {
			// 		input.setCustomValidity("Incorrect number of digit only allow " + maxLength);
			// 		input.reportValidity();
            //     }else{
			// 		input.setCustomValidity("");
			// 	}
            // });

            // Validate on input
            input.addEventListener("input", function () {
                var countryData = iti.getSelectedCountryData();
                var countryCode = countryData.iso2;
                var phoneNumber = input.value.replace(/\s/g, ""); // Remove whitespace

                if (isValidPhoneNumber(phoneNumber, countryCode, iti)) {
                    input.setCustomValidity(""); // Valid number
                } else {
                    input.setCustomValidity("Invalid phone number for " + countryData.name);
                }
            });
            input.addEventListener("change", function () {
                input.reportValidity();
            });

            // Utility function to validate phone number length based on country
            function isValidPhoneNumber(phoneNumber, countryCode, itiInstance) {
                // Check if the number is valid based on intlTelInput's isValidNumber()
                if (!itiInstance.isValidNumber()) return false;

                // Get the length requirements from the mapping
                var lengthData = phoneLengthMapping[countryCode];
                if (lengthData) {
                    var nationalNumber = phoneNumber.replace(/\D/g, ""); // Remove non-digit characters
                    return nationalNumber.length >= lengthData.min && nationalNumber.length <= lengthData.max;
                }

                // Default fallback for countries not in the mapping
                return true;
            }

        });

        jQuery(".gibbs_phonenumber").keyup(function () {
            setTimeout(function () {
                jQuery(".gibbs_phonenumber").attr("placeholder", "");
            }, 500);
            setTimeout(function () {
                jQuery(".gibbs_phonenumber").attr("placeholder", "");
            }, 50);
        });

        jQuery(".gibbs_phonenumber").on("countrychange", function () {
            setTimeout(function () {
                jQuery(".gibbs_phonenumber").attr("placeholder", "");
            }, 500);
            setTimeout(function () {
                jQuery(".gibbs_phonenumber").attr("placeholder", "");
            }, 50);
        });
    }
});
