// Carbon Fields InnerBlockField + Auto-Injection for CF Blocks

const { InnerBlocks } = wp.blockEditor;
const { createElement: el, Fragment } = wp.element;

window.CarbonFields = window.CarbonFields || {};
window.CarbonFields.fields = window.CarbonFields.fields || {};

/**
 * Defines the sidebar field UI.
 */
window.CarbonFields.fields.innerblock = class {
  constructor(args) {
    this.args = args;
  }

  render(field) {
    const {
      allowedBlocks = null,
      template      = [],
      templateLock  = false,
      orientation   = 'vertical',
      placeholder   = 'Add blocks here…',
    } = this.args;

    // Show placeholder only if editing and InnerBlocks is empty
    // Use a wrapper Fragment to allow both placeholder and InnerBlocks
    return el(
      'div',
      { className: 'cfib-innerblock-field' },
      el(Fragment, null,
        el(InnerBlocks, {
          allowedBlocks,
          template,
          templateLock,
          orientation,
          renderAppender: InnerBlocks.ButtonBlockAppender,
        }),
        el('div', {
          className: 'cfib-innerblock-placeholder',
          'aria-hidden': 'true',
        }, placeholder)
      )
    );
  }
};

/**
 * Auto-inject inline InnerBlocks into any Carbon Fields block
 * that has one or more 'innerblock' fields defined.
 */
wp.domReady(() => {
  wp.blocks.getBlockTypes().forEach((block) => {
    const { name, edit } = block;
    // Check if CF has defined any 'innerblock' fields for this block
    const cfFields = window.CarbonFields.blockFields?.[name] || [];
    const innerFields = cfFields.filter(f => f.fieldType === 'innerblock');
    if (innerFields.length === 0) {
      return;
    }

    // Re-register the block with an augmented edit()
    wp.blocks.unregisterBlockType(name);
    wp.blocks.registerBlockType(name, {
      ...block,
      edit: (props) => {
        // Original CF-rendered UI (sidebar fields + preview)
        const original = edit(props);
        // Render one or more inline InnerBlocks wrappers
        const wrappers = innerFields.map((fieldDef) => {
          const args = fieldDef.args;
          return el(
            'div',
            { key: fieldDef.name, className: 'cfib-innerblock-field' },
            el(InnerBlocks, {
              allowedBlocks: args.allowedBlocks,
              template: args.template,
              templateLock: args.templateLock,
              orientation: args.orientation,
              renderAppender: InnerBlocks.ButtonBlockAppender,
            })
          );
        });
        return el(Fragment, null, original, el(Fragment, null, ...wrappers));
      }
    });
  });
});
