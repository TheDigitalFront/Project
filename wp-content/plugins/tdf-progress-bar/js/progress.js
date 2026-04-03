(function () {
  "use strict"; /* enables strict mode to catch common JS errors like undeclared variables */

  var bar = document.getElementById(
    "tdf-reading-progress",
  ); /* gets the progress bar element injected by wp_footer */
  if (!bar) {
    return; /* exits immediately if the bar element is not found — prevents errors on pages where the plugin didn't output it */
  }

  var ticking = false; /* flag used to prevent multiple animation frames firing at once on rapid scroll events */

  function updateBar() {
    var scrollY =
      window.scrollY ||
      window.pageYOffset; /* scrollY is the distance scrolled from the top — pageYOffset is the fallback for older browsers */
    var docHeight =
      document.body.scrollHeight; /* total height of the entire page content */
    var winHeight =
      window.innerHeight; /* height of the visible browser window */
    var scrollable =
      docHeight -
      winHeight; /* the actual scrollable distance — total height minus what's already visible */

    var progress =
      scrollable > 0
        ? Math.min(
            100,
            Math.round((scrollY / scrollable) * 100),
          ) /* calculates percentage scrolled, capped at 100 so the bar never overflows */
        : 0; /* fallback to 0 if the page is too short to scroll */

    bar.style.width =
      progress +
      "%"; /* sets the bar width as a percentage of the page scrolled */
    bar.setAttribute(
      "aria-valuenow",
      progress,
    ); /* updates the ARIA value so screen readers can announce the current progress */
    ticking = false; /* resets the flag so the next scroll event can trigger another animation frame */
  }

  window.addEventListener(
    "scroll",
    function () {
      if (!ticking) {
        /* only queues a new animation frame if one isn't already pending — prevents performance issues on fast scrolling */
        requestAnimationFrame(
          updateBar,
        ); /* defers the update to the next browser repaint for smooth performance */
        ticking = true; /* sets the flag to block any further frames until this one completes */
      }
    },
    {
      passive: true,
    } /* passive: true tells the browser this listener won't call preventDefault(), allowing smoother scrolling */,
  );

  updateBar(); /* runs once on load so the bar reflects the correct position if the user refreshes mid-page */
})();
