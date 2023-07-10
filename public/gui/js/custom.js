window.addEventListener("resize", function() {
	"use strict"; window.location.reload();
});

document.addEventListener("DOMContentLoaded", function () {
	// Prevent closing from click inside dropdown
	document.querySelectorAll('.dropdown-menu').forEach(function (element) {
		element.addEventListener('click', function (e) {
			e.stopPropagation();
		});
	})

	// Make it as accordion for smaller screens
	if (window.innerWidth < 992) {
		// Close all inner dropdowns when parent is closed
		document.querySelectorAll('.navbar .dropdown').forEach(function (everydropdown) {
			everydropdown.addEventListener('hidden.bs.dropdown', function () {
				// After dropdown is hidden, then find all submenus
				this.querySelectorAll('.submenu').forEach(function (everysubmenu) {
					// Hide every submenu as well
					everysubmenu.style.display = 'none';
				});
			})
		});
		document.querySelectorAll('.dropdown-menu a').forEach(function (element) {
			element.addEventListener('click', function (e) {
				let nextEl = this.nextElementSibling;
				if (nextEl && nextEl.classList.contains('submenu')) {
					// Prevent opening link if link needs to open dropdown
					e.preventDefault();
					console.log(nextEl);
					if (nextEl.style.display == 'block') {
						nextEl.style.display = 'none';
					} else {
						nextEl.style.display = 'block';
					}

				}
			});
		})
		// End if inner width
	}
});
