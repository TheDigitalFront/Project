/**
 * TDF Breaking News v2 — slide ticker with progress bar.
 * Vanilla JS, no dependencies.
 */
(function () {
	'use strict';

	var banner = document.querySelector('.tdf-banner');
	if (!banner) return;

	var slides      = banner.querySelectorAll('.tdf-banner__slide');
	var total       = slides.length;
	if (total === 0) return;

	var progressBar = banner.querySelector('.tdf-banner__progress-bar');
	var counterEl   = banner.querySelector('.tdf-banner__current');
	var speed       = (typeof tdfBN !== 'undefined' && tdfBN.speed) ? parseInt(tdfBN.speed, 10) : 3000;
	var current     = 0;
	var paused      = false;
	var elapsed     = 0;
	var rafId       = null;
	var lastTime    = null;

	function updateCounter() {
		if (counterEl) {
			counterEl.textContent = String(current + 1).padStart(2, '0');
		}
	}

	function goTo(index) {
		var prev = current;
		current = ((index % total) + total) % total;
		if (prev === current) return;

		// Exit old.
		slides[prev].classList.remove('is-active');
		slides[prev].classList.add('is-exiting');
		setTimeout(function () {
			slides[prev].classList.remove('is-exiting');
		}, 380);

		// Enter new.
		slides[current].classList.add('is-active');

		updateCounter();
		resetProgress();
	}

	function next() { goTo(current + 1); }
	function prev() { goTo(current - 1); }

	// Progress bar driven by requestAnimationFrame for smoothness.
	function resetProgress() {
		elapsed = 0;
		lastTime = null;
		if (progressBar) {
			progressBar.style.width = '0%';
		}
	}

	function tick(now) {
		rafId = requestAnimationFrame(tick);

		if (!lastTime) { lastTime = now; return; }
		if (paused) { lastTime = now; return; }

		elapsed += now - lastTime;
		lastTime = now;

		var pct = Math.min((elapsed / speed) * 100, 100);
		if (progressBar) {
			progressBar.style.width = pct + '%';
		}

		if (elapsed >= speed) {
			next();
		}
	}

	// Pause on hover.
	banner.addEventListener('mouseenter', function () { paused = true; });
	banner.addEventListener('mouseleave', function () { paused = false; });

	// Buttons.
	banner.querySelectorAll('.tdf-banner__arrow').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var dir = parseInt(btn.getAttribute('data-dir'), 10);
			if (dir === -1) prev(); else next();
		});
	});

	// Init.
	updateCounter();
	rafId = requestAnimationFrame(tick);
})();
