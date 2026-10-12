/**
 * RTL helpers for UpdraftCentral dashboard + modals.
 */
(function ($) {
  'use strict';

  var modalSelector =
    '#updraftcentral_modal_dialog, #updraftcentral_modal, .bootbox.modal, .modal';

  function applyModalRtl($root) {
    var $targets = $root && $root.length ? $root : $(modalSelector);
    $targets.each(function () {
      var $el = $(this);
      $el.attr('dir', 'rtl').css({ direction: 'rtl', 'text-align': 'right' });
      $el
        .find('.modal-dialog, .modal-content, .modal-header, .modal-body, .modal-footer, .bootbox-body, .modal-title')
        .attr('dir', 'rtl')
        .css({ direction: 'rtl', 'text-align': 'right' });
      $el
        .find(
          'textarea#updraftcentral_addsite_key, input[type="url"], input[name*="url"], input[name*="key"], code, pre, kbd'
        )
        .attr('dir', 'ltr')
        .css('text-align', 'left');
    });
  }

  function applyRtlRoot() {
    var $dash = $('#updraftcentral_dashboard');
    if ($dash.length) {
      $dash.attr('dir', 'rtl');
      $('html').attr('dir', 'rtl');
      $('body').addClass('ucp-rtl updraftcentral-persian');

      $(
        '#updraft-central-content-container, #updraft-central-content, #updraft-central-navigation-sidebar, .updraft-central-sidebar'
      )
        .attr('dir', 'rtl')
        .css({ direction: 'rtl', 'text-align': 'right' });

      $dash
        .find(
          'textarea#updraftcentral_addsite_key, input[type="url"], input[name*="url"], input[name*="key"], code, pre, kbd'
        )
        .attr('dir', 'ltr')
        .css('text-align', 'left');
    }

    applyModalRtl();
  }

  $(document).ready(applyRtlRoot);

  $(document).on(
    'updraftcentral_dashboard_loaded updraftcentral_module_loaded',
    applyRtlRoot
  );

  // Bootstrap / Bootbox modal lifecycle
  $(document).on('show.bs.modal shown.bs.modal', modalSelector, function () {
    applyModalRtl($(this));
  });

  if (typeof MutationObserver !== 'undefined') {
    var scheduled = false;
    var observer = new MutationObserver(function (mutations) {
      var needsModal = false;
      mutations.forEach(function (m) {
        if (!m.addedNodes) {
          return;
        }
        m.addedNodes.forEach(function (node) {
          if (node.nodeType !== 1) {
            return;
          }
          if (
            node.id === 'updraftcentral_modal_dialog' ||
            (node.classList &&
              (node.classList.contains('bootbox') || node.classList.contains('modal')))
          ) {
            needsModal = true;
          }
        });
      });

      if (scheduled) {
        return;
      }
      scheduled = true;
      window.setTimeout(function () {
        scheduled = false;
        applyRtlRoot();
        if (needsModal) {
          applyModalRtl();
        }
      }, 150);
    });

    $(document).ready(function () {
      observer.observe(document.body, { childList: true, subtree: true });
    });
  }
})(jQuery);
