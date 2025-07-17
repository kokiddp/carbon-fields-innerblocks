<?php

namespace CFInnerBlocks;

/**
 * Class ServiceProvider
 * Registers the InnerBlockField with Carbon Fields and handles asset paths.
 *
 * @package CFInnerBlocks
 */
class ServiceProvider
{
  protected static string $asset_url = '';
  protected static string $asset_path = '';

  /**
   * Boot the service provider and register the field type.
   * Automatically resolves asset URL/path if not provided.
   *
   * @param string|null $asset_url
   * @param string|null $asset_path
   * 
   * @return void
   */
  public static function boot(?string $asset_url = null, ?string $asset_path = null): void
  {
    if ($asset_url === null || $asset_path === null) {
      // Automatically resolve asset URL and path
      $ref = new \ReflectionClass(self::class);
      $base_dir = realpath(dirname($ref->getFileName(), 2));
      self::$asset_path = $base_dir;
      self::$asset_url = self::resolve_base_url($base_dir);
    } else {
      self::$asset_path = realpath(rtrim($asset_path, '/'));
      self::$asset_url = rtrim($asset_url, '/');
    }

    \add_action('carbon_fields_register_fields', function () {
      if (! class_exists(Innerblock_Field::class)) {
        require_once __DIR__ . '/Innerblock_Field.php';
      }
      \Carbon_Fields\Carbon_Fields::extend('innerblock', Innerblock_Field::class);
    });

    // Enqueue our editor integration script
    \add_action('enqueue_block_editor_assets', function () {
      $url = self::$asset_url;
      $path = self::$asset_path;
      $js = "$url/assets/block.js";
      $ver = file_exists("$path/assets/block.js") ? filemtime("$path/assets/block.js") : null;
      \wp_enqueue_script('cfib-editor-integration', $js, [
        'wp-blocks',
        'wp-element',
        'wp-block-editor',
        'wp-components',
      ], $ver, true);
    });
  }

  /**
   * Get the asset URL.
   * 
   * @return string
   */
  public static function get_asset_url(): string
  {
    return self::$asset_url;
  }

  /**
   * Get the asset path.
   * 
   * @return string
   */
  public static function get_asset_path(): string
  {
    return self::$asset_path;
  }

  /**
   * Attempt to resolve asset base URL from known WP paths.
   * 
   * @param string $base_dir
   * 
   * @return string
   */
  protected static function resolve_base_url(string $base_dir): string
  {
    $base_dir = rtrim(realpath($base_dir), DIRECTORY_SEPARATOR);

    // Child theme
    if (function_exists('get_stylesheet_directory')) {
      $child_dir = realpath(\get_stylesheet_directory());
      if ($child_dir && strpos($base_dir, $child_dir) === 0) {
        $rel = substr($base_dir, strlen($child_dir));
        return \get_stylesheet_directory_uri() . str_replace(DIRECTORY_SEPARATOR, '/', $rel);
      }
    }

    // Parent theme
    if (function_exists('get_template_directory')) {
      $parent_dir = realpath(\get_template_directory());
      if ($parent_dir && strpos($base_dir, $parent_dir) === 0) {
        $rel = substr($base_dir, strlen($parent_dir));
        return \get_template_directory_uri() . str_replace(DIRECTORY_SEPARATOR, '/', $rel);
      }
    }

    // Plugin directory
    if (defined('WP_PLUGIN_DIR')) {
      $plug_dir = realpath(WP_PLUGIN_DIR);
      if ($plug_dir && strpos($base_dir, $plug_dir) === 0) {
        $rel = substr($base_dir, strlen($plug_dir));
        return \plugins_url(str_replace(DIRECTORY_SEPARATOR, '/', $rel));
      }
    }

    // MU-plugin directory
    if (defined('WPMU_PLUGIN_DIR')) {
      $mu_dir = realpath(WPMU_PLUGIN_DIR);
      if ($mu_dir && strpos($base_dir, $mu_dir) === 0) {
        $rel = substr($base_dir, strlen($mu_dir));
        return \content_url('/mu-plugins' . str_replace(DIRECTORY_SEPARATOR, '/', $rel));
      }
    }

    // Fallback on content
    if (defined('WP_CONTENT_DIR')) {
      $content_dir = realpath(WP_CONTENT_DIR);
      if ($content_dir && strpos($base_dir, $content_dir) === 0) {
        $rel = substr($base_dir, strlen($content_dir));
        return \content_url(str_replace(DIRECTORY_SEPARATOR, '/', $rel));
      }
    }

    // If nothing matches, return empty string
    error_log("'carbon-fields-innerblocks' - could not resolve asset URL for base directory: $base_dir");
    return '';
  }
}
