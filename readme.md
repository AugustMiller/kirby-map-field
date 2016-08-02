# Map Field

I've found that adding location data to [Kirby CMS](http://getkirby.com) forms to be super useful.

Unfortunately, this isn't one of the many fields available to us, out of the box.

## Features

- Familiar Google Maps UI
- Discrete storage of location name, latitude and longitude
- Geocoding of location names and addresses
- Repositionable marker (in case search doesn't nail it)
- Support for multiple `map` fields per form
- Support for `map` fields within `structure` fields
- Support for `map` fields in file forms
- Easy to implement (See "Getting Started", below)
- Customizable initial position and zoom— globally and on a per-field basis

![Kirby Map Field Screenshot](https://github.com/AugustMiller/kirby-map-field/raw/master/map-field.png)

## Getting Started

If you like the command line, adding this to your project is super easy.

Be sure you have a `plugins` folder in your `site` folder, then:

```sh
cd /path/to/your/project
git submodule add https://github.com/AugustMiller/kirby-map-field.git site/plugins/map
```

It's important that the folder be named `map`, because kirby looks for the plugin class's definition in a PHP file with the same name as the folder.

You can also directly [download](https://github.com/AugustMiller/kirby-map-field/archive/master.zip) an archive of the current project state, rename the folder to `map`, and add it to the `site/plugins` folder of your project.

Once you've added the plugin, you can add a `map` field to your blueprints, like this:

```yml
fields:
  location:
    label: Location
    type: map
    center:
      lat: 45.5230622
      lng: -122.6764816
      zoom: 9
    help: >
      Move the pin wherever you'd like, or search for a location!
```

The `center` key allows you to customize the initial position and zoom level of the map interface.

You can also set global defaults, in your `config.php`:

```php
c::set('map.defaults.lat', 45.5230622);
c::set('map.defaults.lng', -122.6764816);
c::set('map.defaults.zoom', 9);
```

These options will be overridden by any set on individual fields. Without either configured, it will default to hard-coded values.

## Keys

Google recently announced that all usage must be accompanied by a valid [browser key](https://developers.google.com/maps/documentation/javascript/get-api-key). This means that in order to use the Maps and Geocoding APIs, you must apply for a key in Google's Developer Console/API Manager, and add it to your installation's configuration file:

```php
c::set('map.key', 'your-browser-key');
```

Access to the Maps API is free up to 25,000 loads per day. Be aware that you will need to manually enable _both_ the JS Maps API _and_ the Geocoding API individually.

## Usage

The Map Field stores data in YAML.

You must manually transform the field to an associative array by calling the [`yaml` field method](https://getkirby.com/docs/cheatsheet/field-methods/yaml).

The resulting array can be used just like any other:

```php
$page->location()->yaml()['lat'];
// Or!
$location = $page->location()->yaml();
echo $location['lng']; # => -122.6764816
```

Properties `address`, `lat` and `lng` should exist in the decoded object, but may be empty.

Kirby creator Bastian Allgeier recently created the [Geo Plugin](https://github.com/getkirby-plugins/geo-plugin), which is a great toolkit for working with coordinates. Check it out!

:deciduous_tree:
