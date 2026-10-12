/**
 * Small RTL helpers for UpdraftCentral dashboard DOM that CSS alone cannot fix.
 */
(function ($) {
  'use strict';

  function applyRtlRoot() {
    var $dash = $('#updraftcentral_dashboard');
    if (!$dash.length) {
      return;
    }

    $dash.attr('dir', 'rtl');
    $('html').attr('dir', 'rtl');
    $('body').addClass('ucp-rtl updraftcentral-persian');

    // Keep technical inputs LTR.
    $dash
      .find(
        'textarea#updraftcentral_addsite_key, input[type="url"], input[name*="url"], input[name*="key"], code, pre, kbd'
      )
      .attr('dir', 'ltr')
      .css('text-align', 'left');
  }

  $(document).ready(applyRtlRoot);

  // UpdraftCentral is a SPA — re-apply after major UI swaps.
  $(document).on(
    'updraftcentral_dashboard_loaded updraftcentral_module_loaded',
    applyRtlRoot
  );

  // Mutation fallback when custom events are absent.
  if (typeof MutationObserver !== 'undefined') {
    var scheduled = false;
    var observer = new MutationObserver(function () {
      if (scheduled) {
        return;
      }
      scheduled = true;
      window.setTimeout(function () {
        scheduled = false;
        applyRtlRoot();
      }, 200);
    });

    $(document).ready(function () {
      var node = document.getElementById('updraftcentral_dashboard');
      if (node) {
        observer.observe(node, { childList: true, subtree: true });
      }
    });
  }
})(jQuery);
