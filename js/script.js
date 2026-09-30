function showErrors(errors) {
    let box = document.getElementById("error-box");
    box.innerHTML = errors.join("<br>");
    box.style.display = "block";
}

let loginForm = document.getElementById("login-form");

if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
        let errors = [];
        let username = document.getElementById("username").value.trim();
        let password = document.getElementById("password").value;

        if (username === "") {
            errors.push("Please enter your username.");
        }
        if (password === "") {
            errors.push("Please enter your password.");
        }

        if (errors.length > 0) {
            event.preventDefault();
            showErrors(errors);
        }
    });
}