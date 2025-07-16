const { InnerBlocks } = wp.blockEditor;
const { createElement: el } = wp.element;

window.CarbonFields = window.CarbonFields || {};
window.CarbonFields.fields = window.CarbonFields.fields || {};

window.CarbonFields.fields.innerblock = class {
  constructor(args) {
    this.args = args;
    this.field = null;
  }

  render(field) {
    this.field = field;

    const {
      allowedBlocks = null,
      template = [],
      templateLock = false,
      orientation = 'vertical',
    } = this.args;

    return el(
      'div',
      {
        className: 'cfib-innerblock-field',
        style: {
          border: '1px dashed #aaa',
          padding: '10px',
          marginBottom: '10px',
          background: '#fafafa'
        }
      },
      el(InnerBlocks, {
        allowedBlocks,
        template,
        templateLock,
        orientation,
      })
    );
  }
};
