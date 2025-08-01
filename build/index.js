/******/ (() => { // webpackBootstrap
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/**
 * Add custom media frame for Gemini Image Generation.
 */

/* global wp */
var frame = wp.media.view.MediaFrame.Select;
var __ = wp.i18n.__;
wp.media.view.MediaFrame.Select = frame.extend({
  initialize: function () {
    frame.prototype.initialize.apply(this, arguments);
    var State = wp.media.controller.State.extend({
      insert: function () {
        this.frame.close();
      }
    });
    this.states.add([new State({
      id: 'geminimedia',
      search: false,
      title: __('Gemini Image Generation')
    })]);
    this.on('content:render:geminimedia', this.renderGeminiMediaContent, this);
  },
  browseRouter: function (routerView) {
    routerView.set({
      upload: {
        text: wp.media.view.l10n.uploadFilesTitle,
        priority: 20
      },
      geminimedia: {
        text: __('Gemini Image Generation'),
        priority: 30
      },
      browse: {
        text: wp.media.view.l10n.mediaLibraryTitle,
        priority: 40
      }
    });
  },
  renderGeminiMediaContent: function () {
    var GeminiMediaContent = wp.media.View.extend({
      tagName: 'div',
      className: 'geminimedia-content',
      template: wp.template('geminimedia'),
      active: false,
      toolbar: null,
      frame: null
    });
    var view = new GeminiMediaContent();
    this.content.set(view);
  }
});
/******/ })()
;
//# sourceMappingURL=index.js.map