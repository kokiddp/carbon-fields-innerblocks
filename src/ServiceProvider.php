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
   */
  public static function boot(?string $asset_url = null, ?string $asset_path = null): void
  {
    if ($asset_url === null || $asset_path === null) {
      $ref = new \ReflectionClass(self::class);
      $base_dir = dirname($ref->getFileName(), 2); // /carbon-fields-innerblocks
      $base_url = self::resolve_base_url($base_dir);

      self::$asset_path = $base_dir;
      self::$asset_url = $base_url;
    } else {
      self::$asset_path = rtrim($asset_path, '/');
      self::$asset_url = rtrim($asset_url, '/');
    }

    \add_action('carbon_fields_register_fields', function () {
      if (!class_exists(InnerBlockField::class)) {
        require_once __DIR__ . '/InnerBlockField.php';
      }

      \Carbon_Fields\Carbon_Fields::extend('innerblock', InnerBlockField::class);
    });
  }

  public static function get_asset_url(): string
  {
    return self::$asset_url;
  }

  public static function get_asset_path(): string
  {
    return self::$asset_path;
  }

  /**
   * Attempt to resolve asset base URL from known WP paths.
   */
  protected static function resolve_base_url(string $base_dir): string
  {
    if (function_exists('get_stylesheet_directory') && strpos($base_dir, get_stylesheet_directory()) === 0) {
      return content_url(str_replace(get_stylesheet_directory(), '/themes/' . get_stylesheet(), $base_dir));
    }

    if (function_exists('get_template_directory') && strpos($base_dir, get_template_directory()) === 0) {
      return content_url(str_replace(get_template_directory(), '/themes/' . get_template(), $base_dir));
    }

    if (defined('WP_PLUGIN_DIR') && strpos($base_dir, WP_PLUGIN_DIR) === 0) {
      return plugins_url(str_replace(WP_PLUGIN_DIR, '', $base_dir));
    }

    if (defined('WPMU_PLUGIN_DIR') && strpos($base_dir, WPMU_PLUGIN_DIR) === 0) {
      return content_url('/mu-plugins' . str_replace(WPMU_PLUGIN_DIR, '', $base_dir));
    }

    // fallback
    return content_url(str_replace(WP_CONTENT_DIR, '', $base_dir));
  }
}
