<?php

namespace Carbon_Field_Innerblocks;

use Carbon_Fields\Field\Field;


/**
 * Class Innerblock_Field
 * Custom Carbon Fields field for Gutenberg InnerBlocks.
 *
 * @package CFInnerBlocks
 */
class Innerblock_Field extends Field
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
   * Prepare the field type for use
   * Called once per field type when activated
   */
  public static function field_type_activated() {
    $dir = \Carbon_Field_Innerblocks\DIR . '/languages/';
		$locale = \get_locale();
		$path = $dir . $locale . '.mo';
		\load_textdomain('carbon-field-innerblocks', $path);
  }

  /**
   * Enqueue JS and CSS assets for the field.
   * 
   * @return void
   */
  public static function admin_enqueue_scripts() {
    $root_uri = \Carbon_Fields\Carbon_Fields::directory_to_url( dirname(__DIR__) );

    // Enqueue field styles
    \wp_enqueue_style(
      'cfib-innerblock-field',
      $root_uri . '/assets/block.css',
      [],
      filemtime( dirname(__DIR__) . '/assets/block.css' )
    );

    // Enqueue field scripts
    \wp_enqueue_script(
      'cfib-innerblock-field',
      $root_uri . '/assets/block.js',
      ['carbon-fields-core', 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components'],
      filemtime( dirname(__DIR__) . '/assets/block.js' )
    );

    // Set script translations
    \wp_set_script_translations('cfib-innerblock-field', 'carbon-field-innerblocks', dirname(__DIR__) . '/languages');
  }
}
