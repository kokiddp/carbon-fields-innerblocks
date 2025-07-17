<?php

namespace CFInnerBlocks;

/**
 * Class ServiceProvider
 * Registers the InnerBlock_Field with Carbon Fields.
 *
 * @package CFInnerBlocks
 */
class ServiceProvider
{
  /**
   * Boot the service provider and register the field type.
   *
   * @return void
   */
  public static function boot(): void
  {
    \add_action('carbon_fields_register_fields', function () {
      \Carbon_Fields\Carbon_Fields::extend('CFInnerBlocks\\Innerblock_Field', function($type, $name, $label) {
        return new Innerblock_Field($type, $name, $label);
      });
    });
  }
}
