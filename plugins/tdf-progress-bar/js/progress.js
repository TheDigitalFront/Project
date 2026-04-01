(function () {
  "use strict";

  var bar = document.getElementById("tdf-reading-progress");
  if (!bar) {
    return;
  }

  var ticking = false;

  function updateBar() {
    var scrollY = window.scrollY || window.pageYOffset;
    var docHeight = document.body.scrollHeight;
    var winHeight = window.innerHeight;
    var scrollable = docHeight - winHeight;

    var progress =
      scrollable > 0
        ? Math.min(100, Math.round((scrollY / scrollable) * 100))
        : 0;

    bar.style.width = progress + "%";
    bar.setAttribute("aria-valuenow", progress);
    ticking = false;
  }

  window.addEventListener(
    "scroll",
    function () {
      if (!ticking) {
        requestAnimationFrame(updateBar);
        ticking = true;
      }
    },
    { passive: true },
  );

  updateBar();
})();
