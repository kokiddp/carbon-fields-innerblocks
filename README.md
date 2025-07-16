# Carbon Fields InnerBlocks

This is a Composer package that extends [Carbon Fields](https://carbonfields.net) to support `InnerBlocks` inside Gutenberg blocks.

## Features

- Gutenberg `InnerBlocks` integration
- Support for multiple innerblock fields per block
- Unlimited nested innerblocks
- Configurable attributes:
  - `allowed_blocks()`
  - `template()`
  - `template_lock()`
  - `orientation()`
- Prevents use outside Gutenberg (e.g. options pages)

---

## Installation

```bash
composer require kokiddp/carbon-fields-innerblocks
```

In your theme or plugin bootstrap:

```php
use CFInnerBlocks\ServiceProvider;

add_action('after_setup_theme', function () {
    \Carbon_Fields\Carbon_Fields::boot();
    ServiceProvider::boot();
});
```

---

## Usage Example

```php
use Carbon_Fields\Block;
use Carbon_Fields\Field;
use CFInnerBlocks\InnerBlockField;

Block::make('Content Block')
    ->add_fields([
        Field::make('text', 'section_title', 'Section Title'),
        Field::make('innerblock', 'main_content', 'Main Content')
            ->set_allowed_blocks(['core/paragraph', 'core/image'])
            ->set_template([
                ['core/paragraph', ['placeholder' => 'Start typing...']]
            ])
            ->set_template_lock(false)
            ->set_orientation('vertical'),
        Field::make('innerblock', 'sidebar_content', 'Sidebar Content')
    ])
    ->set_render_callback(function ($fields, $attributes, $inner_blocks) {
        echo '<div class="my-block">';
        echo '<h2>' . esc_html($fields['section_title']) . '</h2>';
        echo '<div class="main">' . $inner_blocks . '</div>';
        echo '<div class="sidebar">' . $inner_blocks . '</div>';
        echo '</div>';
    });
```

---

## Limitations

- This field only works inside Gutenberg block contexts
- It will log an error and render nothing if used in options, users, terms, etc.

---

## License

MIT © Gabriele Coquillard
