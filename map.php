<?php # Kirby Map Plugin

$kirby->set('field', 'map', __DIR__ . '/fields/map');

$kirby->set('route', [
  'pattern' => 'maps/key',
  'action'  => function() {
    if ( site()->user() ) {
      return response::json([
        'key' => c::get('map.key', '')
      ]);
    } else {
      return response::json([
        'error' => 'Please log in to use the maps integration.'
      ], 403);
    }
  }
]);
