<?php

namespace CFInnerBlocks;

use Carbon_Fields\Field\Field;


/**
 * Class InnerBlockField
 * Custom Carbon Fields field for Gutenberg InnerBlocks.
 *
 * @package CFInnerBlocks
 */
class InnerBlockField extends Field
{
  /**
   * Field type identifier.
   *
   * @var string
   */
  protected string $field_type = 'innerblock';

  /**
   * Field settings for JS export.
   *
   * @var array
   */
  protected array $settings = [
    'allowedBlocks' => null,
    'template' => [],
    'templateLock' => false,
    'orientation' => 'vertical',
  ];

  /**
   * Set allowed blocks for InnerBlocks.
   *
   * @param array $blocks
   * @return $this
   */
  public function set_allowed_blocks(array $blocks): self
  {
    $this->settings['allowedBlocks'] = $blocks;
    return $this;
  }

  /**
   * Set template for InnerBlocks.
   *
   * @param array $template
   * @return $this
   */
  public function set_template(array $template): self
  {
    $this->settings['template'] = $template;
    return $this;
  }

  /**
   * Set template lock for InnerBlocks.
   *
   * @param mixed $lock
   * @return $this
   */
  public function set_template_lock($lock): self
  {
    $this->settings['templateLock'] = $lock;
    return $this;
  }

  /**
   * Set orientation for InnerBlocks.
   *
   * @param string $orientation
   * @return $this
   */
  public function set_orientation(string $orientation): self
  {
    $this->settings['orientation'] = $orientation;
    return $this;
  }

  /**
   * Export settings to JS.
   *
   * @param mixed $load
   * @return array
   */
  public function to_json($load): array
  {
    $settings = \apply_filters('cfib_innerblock_field_settings', $this->settings, $this);
    $load = parent::to_json($load);
    return array_merge($load, $settings);
  }

  /**
   * Get field value (always empty, content is in blocks).
   *
   * @return string
   */
  public function get_value(): string
  {
    return '';
  }

  /**
   * Export value (null, as content is in blocks).
   *
   * @return null
   */
  public function export_value()
  {
    return null;
  }

  /**
   * Enqueue JS and CSS assets for the field.
   *
   * @return void
   */
  public function enqueue_assets(): void
  {
    // Only load in Gutenberg editor context
    if (!\is_admin() || !function_exists('get_current_screen')) {
      return;
    }

    // Gutenberg context check
    $screen = \get_current_screen();    
    $is_gutenberg = isset($screen->is_block_editor) ? $screen->is_block_editor : (strpos($screen->base, 'post') !== false);
    if (!$is_gutenberg) {
      \add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>InnerBlockField can only be used within Gutenberg block contexts.</p></div>';
      });
      return;
    }

    $url  = ServiceProvider::get_asset_url();
    $path = ServiceProvider::get_asset_path();

    // Enqueue JS
    $js_file = "$path/assets/block.js";
    $js_ver = file_exists($js_file) ? filemtime($js_file) : null;
    if (!\wp_script_is('cfib-innerblock-field', 'enqueued')) {
      \wp_enqueue_script(
        'cfib-innerblock-field',
        "$url/assets/block.js",
        \apply_filters('cfib_innerblock_js_deps', [
          'wp-blocks',
          'wp-editor',
          'wp-element',
          'wp-components',
          'wp-block-editor'
        ]),
        $js_ver,
        true
      );
    }

    // Enqueue CSS
    $css_file = "$path/assets/block.css";
    $css_ver = file_exists($css_file) ? filemtime($css_file) : null;
    if (!\wp_style_is('cfib-innerblock-field', 'enqueued')) {
      \wp_enqueue_style(
        'cfib-innerblock-field',
        "$url/assets/block.css",
        \apply_filters('cfib_innerblock_css_deps', []),
        $css_ver
      );
    }
  }
}
