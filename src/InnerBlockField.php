<?php

namespace CFInnerBlocks;

use Carbon_Fields\Field\Field;

class InnerBlockField extends Field
{
  protected string $field_type = 'innerblock';

  protected array $settings = [
    'allowedBlocks' => null,
    'template' => [],
    'templateLock' => false,
    'orientation' => 'vertical',
  ];

  public function set_allowed_blocks(array $blocks): self
  {
    $this->settings['allowedBlocks'] = $blocks;
    return $this;
  }

  public function set_template(array $template): self
  {
    $this->settings['template'] = $template;
    return $this;
  }

  public function set_template_lock($lock): self
  {
    $this->settings['templateLock'] = $lock;
    return $this;
  }

  public function set_orientation(string $orientation): self
  {
    $this->settings['orientation'] = $orientation;
    return $this;
  }

  public function to_json($load): array
  {
    $load = parent::to_json($load);
    return array_merge($load, $this->settings);
  }

  public function get_value(): string
  {
    return '';
  }

  public function export_value()
  {
    return null;
  }

  public function enqueue_assets(): void
  {
    if (!is_admin() || !function_exists('get_current_screen')) {
      return;
    }

    $screen = get_current_screen();
    if (!$screen || strpos($screen->base, 'post') === false) {
      return;
    }

    wp_enqueue_script(
      'cfib-innerblock-field',
      plugin_dir_url(__DIR__) . '../assets/block.js',
      ['wp-blocks', 'wp-editor', 'wp-element', 'wp-components', 'wp-block-editor'],
      filemtime(__DIR__ . '/../assets/block.js'),
      true
    );
  }
}
