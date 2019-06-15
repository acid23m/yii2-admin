/**
 * Shows progress spinner.
 */
yii.spinner = (function ($) {
  let pub = {
    isActive: true,
    init () {
      $('body').on('click', '.js_show_progress', lBlock);
    }
  }

  /**
   * Shows loader
   */
  function lBlock () {
    const $loadingBlock = $('#loading-block');

    $loadingBlock.fadeIn('fast'); // show

    // hide
    setTimeout(() => {
      $loadingBlock.fadeOut('fast');
    }, 5000);
  }

  return pub;
})(jQuery);

/**
 * Fixes issue with MAILGO anchors.
 */
yii.mailgoAnchors = (function ($) {
  let pub = {
    isActive: true,
    init () {
      $('body').on('click', 'a[href^="#mailgo"]', e => {
        e.preventDefault();
      });
    }
  }

  return pub;
})(jQuery);
