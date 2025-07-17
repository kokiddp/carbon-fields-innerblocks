<?php
use Carbon_Fields\Carbon_Fields;
use Carbon_Field_Innerblocks\Innerblock_Field;

define('Carbon_Field_Innerblocks\\DIR', __DIR__);

Carbon_Fields::extend(Innerblock_Field::class, function($container) {
	return new Innerblock_Field($container['arguments']['type'], $container['arguments']['name'], $container['arguments']['label']);
});