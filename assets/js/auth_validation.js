document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    const emailInput = document.querySelector("input[name='email']");

    form.addEventListener("submit", function(event) {
        if (emailInput.value.trim() === "") {
            event.preventDefault(); // Batalkan pengiriman jika kosong
            alert("Please enter your email address first!");
            emailInput.focus();
        }
    });
});