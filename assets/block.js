
// Carbon Fields InnerBlockField JS

const { InnerBlocks } = wp.blockEditor;
const { createElement: el, Fragment } = wp.element;
const { __ } = wp.i18n;

window.CarbonFields = window.CarbonFields || {};
window.CarbonFields.fields = window.CarbonFields.fields || {};


/**
 * InnerBlockField JS implementation for Carbon Fields.
 * - Shows a placeholder when empty (editor only)
 * - All styling is handled via CSS, not inline
 * - Uses WordPress i18n for translation
 */
window.CarbonFields.fields.innerblock = class {
  constructor(args) {
    this.args = args;
    this.field = null;
  }

  /**
   * Render the field in the Gutenberg editor.
   * @param {Object} field - The field instance
   * @returns {Element}
   */
  render(field) {
    this.field = field;

    const {
      allowedBlocks = null,
      template = [],
      templateLock = false,
      orientation = 'vertical',
      placeholder = 'Add blocks here...',
    } = this.args;

    // Show placeholder only if editing and InnerBlocks is empty
    // Use a wrapper Fragment to allow both placeholder and InnerBlocks
    return el(
      'div',
      {
        className: 'cfib-innerblock-field'
      },
      el(Fragment, null,
        el(InnerBlocks, {
          allowedBlocks,
          template,
          templateLock,
          orientation,
          renderAppender: InnerBlocks.ButtonBlockAppender
        }),
        el('div', {
          className: 'cfib-innerblock-placeholder',
          style: { display: 'none' }, // Will be toggled by editor CSS/JS
          'aria-hidden': 'true'
        }, placeholder)
      )
    );
  }
};
