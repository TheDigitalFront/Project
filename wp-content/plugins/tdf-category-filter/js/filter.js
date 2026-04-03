(function () {
	"use strict";

	var container = document.getElementById("tdf-filter");
	if (!container) return;

	var results = document.getElementById("tdf-filter-results");
	var loading = document.getElementById("tdf-filter-loading");
	var tabs = container.querySelectorAll(".tdf-filter__tab");
	var dateForm = document.getElementById("tdf-filter-dates");
	var clearBtn = document.getElementById("tdf-filter-clear");
	var perPage = container.getAttribute("data-per-page") || 6;

	var activeCat = "";
	var activeFrom = "";
	var activeTo = "";

	/* Find the initially active tab to set activeCat on load. */
	tabs.forEach(function (tab) {
		if (tab.classList.contains("is-active")) {
			activeCat = tab.getAttribute("data-cat") || "";
		}
	});

	/**
	 * Send a fetch() POST request to admin-ajax.php with the current
	 * filter state (category, date range, page number).
	 *
	 * Uses FormData to send parameters as POST body.
	 * The nonce is included for CSRF protection — verified server-side
	 * by wp_verify_nonce() in the tdf_filter_posts handler.
	 *
	 * On success, the grid HTML is swapped without a page reload.
	 * On error, a user-friendly message is shown in the results area.
	 */
	function fetchPosts(page) {
		/* Show loading spinner, hide results. */
		loading.setAttribute("aria-hidden", "false");
		loading.style.display = "flex";
		results.style.opacity = "0.4";

		var data = new FormData();
		data.append("action", "tdf_filter_posts");
		data.append("nonce", tdfFilter.nonce);
		data.append("cat", activeCat);
		data.append("from", activeFrom);
		data.append("to", activeTo);
		data.append("paged", page || 1);
		data.append("per_page", perPage);

		fetch(tdfFilter.ajaxUrl, {
			method: "POST",
			credentials: "same-origin",
			body: data,
		})
			.then(function (response) {
				if (!response.ok) throw new Error("Network error");
				return response.json();
			})
			.then(function (json) {
				if (json.success) {
					/* Swap the grid + pagination HTML from the AJAX response. */
					results.innerHTML = json.data.html;
					bindPaginationLinks();
				} else {
					results.innerHTML =
						'<div class="tdf-empty"><p>Error loading results. Please try again.</p></div>';
				}
			})
			.catch(function () {
				/* Handle network errors gracefully. */
				results.innerHTML =
					'<div class="tdf-empty"><p>Something went wrong. Please try again.</p></div>';
			})
			.finally(function () {
				/* Hide spinner, restore results opacity. */
				loading.setAttribute("aria-hidden", "true");
				loading.style.display = "none";
				results.style.opacity = "1";
			});
	}

	/* Category tab click — switch active tab and fetch filtered posts. */
	tabs.forEach(function (tab) {
		tab.addEventListener("click", function () {
			tabs.forEach(function (t) {
				t.classList.remove("is-active");
			});
			tab.classList.add("is-active");
			activeCat = tab.getAttribute("data-cat") || "";
			fetchPosts(1);
		});
	});

	/* Date form submit — read date inputs and fetch filtered posts. */
	if (dateForm) {
		dateForm.addEventListener("submit", function (e) {
			e.preventDefault();
			activeFrom = dateForm.querySelector('[name="from"]').value;
			activeTo = dateForm.querySelector('[name="to"]').value;
			fetchPosts(1);
		});
	}

	/* Clear dates button — reset date inputs and re-fetch. */
	if (clearBtn) {
		clearBtn.addEventListener("click", function () {
			activeFrom = "";
			activeTo = "";
			dateForm.querySelector('[name="from"]').value = "";
			dateForm.querySelector('[name="to"]').value = "";
			clearBtn.remove();
			fetchPosts(1);
		});
	}

	/**
	 * Bind click handlers to pagination links inside the AJAX-loaded HTML.
	 * Called after every successful AJAX response because the pagination
	 * links are re-rendered each time.
	 */
	function bindPaginationLinks() {
		var links = results.querySelectorAll(".tdf-pagination a");
		links.forEach(function (link) {
			link.addEventListener("click", function (e) {
				e.preventDefault();
				var href = link.getAttribute("href");
				var match = href.match(/paged?[=/](\d+)/);
				var page = match ? parseInt(match[1], 10) : 1;
				fetchPosts(page);
				container.scrollIntoView({ behavior: "smooth", block: "start" });
			});
		});
	}

	/* Bind pagination on initial page load. */
	bindPaginationLinks();
})();
