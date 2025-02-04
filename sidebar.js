function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function (event) {
    if (!event.target.matches(".dropdown-toggle")) {
        const dropdowns = document.querySelectorAll(".dropdown-menu, .nav-treeview");
        dropdowns.forEach(function (dropdown) {
            dropdown.style.display = "none";
        });
    }
});