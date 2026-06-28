$(document).ready(function() {

    var currencyArr = ["INR", "EUR", "JPY", "GBP", "USD", "AED"];
    var currencyShowArr = ["Rupee", "Euro", "Yen", "Pound", "Dollar", "Dirham"];

    const currencyLevels = {
        INR: ["", "thousand", "lakh", "crore"],
        EUR: ["", "thousand", "million", "billion"],
        JPY: ["", "thousand", "million", "billion"],
        GBP: ["", "thousand", "million", "billion"],
        USD: ["", "thousand", "million", "billion"],
        AED: ["", "thousand", "million", "billion"]
    };


    function numberToWords(num, currencyValue) {
        var currency = currencyArr[currencyValue];
        var currencyText = currencyShowArr[currencyValue];
        if (isNaN(num) || num === "") return "Invalid number";

        const belowTwenty = [
            "", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten",
            "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"
        ];
        const tens = ["", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"];

        function convertToWords(n) {
            if (n === 0) return "";
            if (n < 20) return belowTwenty[n];
            if (n < 100) {
                return tens[Math.floor(n / 10)] + (n % 10 ? " " + belowTwenty[n % 10] : "");
            }
            return (
                belowTwenty[Math.floor(n / 100)] +
                " hundred" +
                (n % 100 ? " and " + convertToWords(n % 100) : "")
            );
        }

        function splitNumberIndian(n) {
            const parts = [];
            parts.push(n % 1000); // Last 3 digits (units place)
            n = Math.floor(n / 1000);

            while (n > 0) {
                parts.push(n % 100); // Next 2 digits
                n = Math.floor(n / 100);
            }
            return parts.reverse();
        }

        function splitNumberWestern(n) {
            const parts = [];
            while (n > 0) {
                parts.push(n % 1000);
                n = Math.floor(n / 1000);
            }
            return parts.reverse();
        }

        const levels = currencyLevels[currency] || ["", "thousand", "million", "billion"];
        const splitNumber = currency === "INR" ? splitNumberIndian : splitNumberWestern;

        const parts = splitNumber(num);
        let result = "";

        for (let i = 0; i < parts.length; i++) {
            if (parts[i] > 0) {
                result +=
                    (result ? " " : "") +
                    convertToWords(parts[i]) +
                    (levels[parts.length - 1 - i] ? " " + levels[parts.length - 1 - i] : "");
            }
        }

        return result.trim() + " " + currencyText;
    }

    window.numberToWords = numberToWords;

});