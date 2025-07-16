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
    if (!\is_admin() || !function_exists('get_current_screen')) {
      return;
    }

    $screen = \get_current_screen();
    if (!$screen || strpos($screen->base, 'post') === false) {
      \add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>Carbon Fields InnerBlockField can only be used in Gutenberg block contexts.</p></div>';
      });
      return;
    }

    $asset_url  = rtrim(\CFInnerBlocks\ServiceProvider::get_asset_url(), '/');
    $asset_path = rtrim(\CFInnerBlocks\ServiceProvider::get_asset_path(), '/');

    $js_file = $asset_path . '/assets/block.js';
    $js_ver  = file_exists($js_file) ? filemtime($js_file) : null;
    $css_file = $asset_path . '/assets/block.css';
    $css_ver  = file_exists($css_file) ? filemtime($css_file) : null;

    \wp_enqueue_script(
      'cfib-innerblock-field',
      $asset_url . '/assets/block.js',
      ['wp-blocks', 'wp-editor', 'wp-element', 'wp-components', 'wp-block-editor'],
      $js_ver,
      true
    );

    \wp_enqueue_style(
      'cfib-innerblock-field',
      $asset_url . '/assets/block.css',
      [],
      $css_ver
    );
  }
}
