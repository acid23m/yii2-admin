/**
 * Created by Poyarkov S. <webmaster.cipa at gmail dot com>.
 */

/**
 * Show progress spinner.
 */
yii.spinner = (function ($) {
  let pub = {
    isActive: true,
    init () {
      $('body').on('click', '.js_show_progress', lBlock);
    }
  }

  /**
   * Show loader
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
