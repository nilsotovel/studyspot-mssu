// Shows a list of error messages in the red box
function showErrors(errors) {
    let box = document.getElementById("error-box");
    box.classList.remove("success");
    box.innerHTML = errors.join("<br>");
    box.style.display = "block";
}

// Shows one success message in the same box, styled green
function showSuccess(message) {
    let box = document.getElementById("error-box");
    box.classList.add("success");
    box.textContent = message;
    box.style.display = "block";
}

// ----- Login page -----
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

    // Messages from login.php / register.php after a redirect
    let loginParams = new URLSearchParams(window.location.search);

    if (loginParams.has("error")) {
        showErrors(["Incorrect username or password."]);
    }
    if (loginParams.has("registered")) {
        showSuccess("Account created! You can log in now.");
    }
}

// ----- Register page -----
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

    // Messages from register.php after a redirect
    let registerParams = new URLSearchParams(window.location.search);
    let registerError = registerParams.get("error");

    if (registerError === "taken") {
        showErrors(["That username is already taken."]);
    } else if (registerError === "mismatch") {
        showErrors(["Passwords do not match."]);
    } else if (registerError === "invalid") {
        showErrors(["Username must be 3-50 characters and password at least 6."]);
    }
}

// ----- Dashboard and edit page: review form -----
let reviewForm = document.getElementById("review-form");

if (reviewForm) {
    reviewForm.addEventListener("submit", function (event) {
        let errors = [];
        let spot = document.getElementById("spot_id").value;
        let rating = document.querySelector('input[name="rating"]:checked');
        let text = document.getElementById("review_text").value.trim();

        if (spot === "") {
            errors.push("Please choose a study spot.");
        }
        if (rating === null) {
            errors.push("Please pick a star rating.");
        }
        if (text === "") {
            errors.push("Please write a review.");
        }

        if (errors.length > 0) {
            event.preventDefault();
            showErrors(errors);
        }
    });
}

// ----- Dashboard: confirm before deleting -----
let deleteForms = document.querySelectorAll(".delete-form");

for (let i = 0; i < deleteForms.length; i++) {
    deleteForms[i].addEventListener("submit", function (event) {
        if (!confirm("Delete this review?")) {
            event.preventDefault();
        }
    });
}
