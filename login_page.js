function openModal() {
    document.getElementById("loginModal").style.display = "block";
}

function closeModal() {
    document.getElementById("loginModal").style.display = "none";
}

function showForm(formType) {
    document.getElementById("loginForm").classList.remove("active");
    document.getElementById("registerForm").classList.remove("active");

    if (formType === "login") {
        document.getElementById("loginForm").classList.add("active");
        document.querySelectorAll(".tab-buttons button")[0].classList.add("active");
        document.querySelectorAll(".tab-buttons button")[1].classList.remove("active");
    } else {
        document.getElementById("registerForm").classList.add("active");
        document.querySelectorAll(".tab-buttons button")[1].classList.add("active");
        document.querySelectorAll(".tab-buttons button")[0].classList.remove("active");
    }
}

// Close modal if clicked outside content
window.onclick = function(event) {
    if (event.target == document.getElementById("loginModal")) {
        closeModal();
    }
}