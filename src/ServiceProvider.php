<?php

namespace CFInnerBlocks;

class ServiceProvider
{
  public static function boot()
  {
    // Register custom field type with Carbon Fields
    add_action('carbon_fields_register_fields', function () {
      if (!class_exists(InnerBlockField::class)) {
        require_once __DIR__ . '/InnerBlockField.php';
      }

      \Carbon_Fields\Carbon_Fields::extend('innerblock', InnerBlockField::class);
    });
  }
}
