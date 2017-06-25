<?php class MapField extends InputField {
  public function __construct() {
    $this->type = 'map';
    $this->icon = 'map-marker';
    $this->label = l::get('fields.map.label', 'Place');
    $this->placeholder = l::get('fields.map.placeholder', 'Address or Location');
    $this->map_settings = array(
      'lat' => c::get('map.defaults.lat', 45.5230622),
      'lng' => c::get('map.defaults.lng', -122.6764816),
      'zoom' => c::get('map.defaults.zoom', 1)
    );
  }

  static public $assets = array(
    'js' => array(
      'map.js'
    ),
    'css' => array(
      'map.css'
    )
  );

  public function defaults () {
    if (isset($this->center) && is_array($this->center)) {
      $this->center = array_merge($this->map_settings, $this->center);
    } else {
      $this->center = $this->map_settings;
    }
  }

  public function content () {
    $this->defaults();

    $field = new Brick('div');
    $field->addClass('field-multipart field-map cf');

    # Add each
    $field->append($this->input());
    $field->append($this->button_search());
    $field->append($this->map());
    $field->append($this->input_lat());
    $field->append($this->input_lng());
    $field->append($this->input_zoom());

    # Concatenate & Return
    return $field;
  }

  # Location Input & Search
  public function input () {
    # Use `BaseField`'s setup
    $input = parent::input();

    # Provide a hook for the Panel's form initialization. This is a jQuery method, defined in assets/js/map.js
    $input->data('field', 'mapField');

    # Container
    $location_container = new Brick('div');
    $location_container->addClass('field-content input-map');

    # Field
    $input->addClass('input-address');
    $input->attr('name', $this->name() . '[address]');
    $input->val($this->pick('address'));

    # Combine & Ship It
    $location_container->append($input);
    $location_container->append($this->icon());

    return $location_container;
  }

  # Search Button
  private function button_search () {
    # Wrapper
    $search_container = new Brick('div');
    $search_container->addClass('field-content input-search input-button');

    # Button
    $search_button = new Brick('input');
    $search_button->attr('type', 'button');
    $search_button->val(l::get('fields.map.locate', 'Locate'));
    $search_button->addClass('btn btn-rounded locate-button');

    # Combine & Ship It
    $search_container->append($search_button);

    return $search_container;
  }

  # Latitude Input
  private function input_lat () {
    # Wrapper
    $lat_content = new Brick('div');
    $lat_content->addClass('field-content field-lat');

    # Input (Locked: We use the map UI to update these)
    $lat_input = new Brick('input');
    $lat_input->attr('tabindex', '-1');
    $lat_input->attr('readonly', true);
    $lat_input->attr('name', $this->name() . '[lat]');
    $lat_input->addClass('input input-split-left input-is-readonly map-lat');
    $lat_input->attr('placeholder', l::get('fields.map.latitude', 'Latitude'));
    $lat_input->val($this->pick('lat'));

    # Combine & Ship It
    $lat_content->append($lat_input);

    return $lat_content;
  }

  # Longitude Input
  private function input_lng () {
    # Wrapper
    $lng_content = new Brick('div');
    $lng_content->addClass('field-content field-lng');

    # Input (Locked: We use the map UI to update these)
    $lng_input = new Brick('input');
    $lng_input->attr('tabindex', '-1');
    $lng_input->attr('readonly', true);
    $lng_input->attr('name', $this->name() . '[lng]');
    $lng_input->addClass('input input-split-right input-is-readonly map-lng');
    $lng_input->attr('placeholder', l::get('fields.map.longitude', 'Longitude'));
    $lng_input->val($this->pick('lng'));

    # Combine & Ship It
    $lng_content->append($lng_input);

    return $lng_content;
  }

  # Zoom Input
  private function input_zoom () {
    # Wrapper
    $zoom_content = new Brick('div');
    $zoom_content->addClass('field-content field-zoom hidden');

    # Input (Locked: We use the map UI to update these)
    $zoom_input = new Brick('input');
    $zoom_input->attr('tabindex', '-1');
    $zoom_input->attr('readonly', true);
    $zoom_input->attr('type', 'hidden');
    $zoom_input->attr('name', $this->name() . '[zoom]');
    $zoom_input->addClass('input input-is-readonly map-zoom');
    $zoom_input->attr('placeholder', l::get('fields.map.zoom', 'Zoom'));
    $zoom_input->val($this->pick('zoom'));

    # Combine & Ship It
    $zoom_content->append($zoom_input);

    return $zoom_content;
  }

  # Map
  public function map () {
    $map_content = new Brick('div');
    $map_content->addClass('field-content field-google-map-ui input');
    $map_content->data($this->center);

    return $map_content;
  }

  public function pick ($key = null) {
    $data = $this->value();
    if ( $key && isset($data[$key]) ) {
      return $data[$key];
    } else {
      return null;
    }
  }

  public function value() {
    return (array)yaml::decode($this->value);
  }

  public function result() {
    # Get Incoming data, which should be a nested object containing `lat`, `lng`, `zoom`, and `address` properties
    $input = parent::result();

    # Store as Yaml.
    return yaml::encode($input);

    # This ends up as a text block when stored inside a Structure field. Really, it's plain text anywhere it's stored— but the effect is only noticeable there. The truth is that Structure fields are stored as "plain text," as-is, which may be the only way to legitimately implement nested structures. For example, how do we "stop" YAML from being parsed at a certain hierarchical level?
  }
}
