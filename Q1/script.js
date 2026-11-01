document.getElementById("form").onsubmit = function(e) {
    e.preventDefault();
    let name = document.getElementById("name").value.trim();
    let roll = document.getElementById("roll").value.trim();
    let email = document.getElementById("email").value.trim();
    let course = document.getElementById("course").value;
    let password = document.getElementById("password").value;
    let message = document.getElementById("message")
    if (!name || !roll || !email || !course || !password) {
        message.style.color = "red";
        message.innerText = "Please fill all required fields.";
    }
    else if (!email.includes("@") || !email.includes(".")) {
        message.style.color = "red";
        message.innerText = "Enter a valid email.";
    }
    else if (password.length < 8) {
        message.style.color = "red";
        message.innerText = "Password must have 8 characters.";
    }
    else {
        message.style.color = "green";
        message.innerText = "Registration successful!";
    }
};
