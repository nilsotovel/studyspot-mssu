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
let registerForm = document.getElementById("register-form");

if (registerForm) {
    registerForm.addEventListener("submit", function (event) {
        let errors = [];
        let username = document.getElementById("username").value.trim();
        let password = document.getElementById("password").value;
        let confirm = document.getElementById("confirm_password").value;

        if (username === "") {
            errors.push("Please enter a username.");
        } else if (username.length < 3) {
            errors.push("Username must be at least 3 characters.");
        }

        if (password === "") {
            errors.push("Please enter a password.");
        } else if (password.length < 6) {
            errors.push("Password must be at least 6 characters.");
        }

        if (password !== confirm) {
            errors.push("Passwords do not match.");
        }

        if (errors.length > 0) {
            event.preventDefault();
            showErrors(errors);
        }
    });
}