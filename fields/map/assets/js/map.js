;(function ($) {
  var Loader, MapsLoader, MapField, loader;

  Loader = function () {
    this.url = 'https://www.google.com/jsapi';
    this.loaded = false;
    this.loading = false;
    this.queue = [];
  };

  Loader.prototype.load = function (after) {
    if ( this.loaded ) {
      after();
    } else if ( this.loading ) {
      this.queue.push(after);
    } else {
      this.queue.push(after);

      $.ajax({
        url: this.url,
        cache: true,
        dataType: 'script',
        beforeSend: (function (_loader) {
          return function () {
            _loader.loading = true;
          };
        })(this),
        success: (function (_loader) {
          return function () {
            _loader.loading = false;
            _loader.loaded = true;
            _loader.maps();
          }
        })(this)
      });
    }
  };

  Loader.prototype.maps = function () {
    $.ajax({
      url: '/maps/key',
      success: (function (_loader) {
        return function (data) {
          google.load('maps', '3', {
            other_params: 'key=' + data.key,
            callback: function () {
              _loader.dequeue();
            }
          });
        }
      })(this)
    })
  };

  Loader.prototype.dequeue = function () {
    for (var q = this.queue.length - 1; q >= 0; q--) {
      this.queue[q]();
      this.queue.unshift();
    }
  };

  /*
    Field
  */

  MapField = function (field) {
    // State
    this.is_active = false;

    // Field Components
    this.field = $(field);
    this.container = this.field.parents('.field-map');
    this.location_fields = {
      address: this.container.find('.input-address'),
      lat: this.container.find('.map-lat'),
      lng: this.container.find('.map-lng')
    };

    // Google Maps Interface
    this.map_canvas = this.container.find('.field-google-map-ui');
    this.settings = {
      map: {
        center: {
          lat: parseFloat(this.location_fields.lat.val() || this.map_canvas.data('lat')),
          lng: parseFloat(this.location_fields.lng.val() || this.map_canvas.data('lng'))
        },
        zoom: this.map_canvas.data('zoom') || 6,
        disableDefaultUI: true,
        scrollwheel: false,
        zoomControl: true,
        zoomControlOptions: {
          position: google.maps.ControlPosition.LEFT_TOP
        }
      }
    };

    this.init();
  };

  MapField.prototype.init = function () {
    this.map = new google.maps.Map(this.map_canvas.get(0), this.settings.map);
    this.geocoder = new google.maps.Geocoder();
    this.pin = new google.maps.Marker({
      position: new google.maps.LatLng(this.settings.map.center.lat, this.settings.map.center.lng),
      map: this.map,
      draggable: true
    });
    this.listen();
  };

  MapField.prototype.listen = function () {
    // Address Input
    this.location_fields.address.on('keydown', (function (_map) {
      return function (e) {
        
        if (e.keyCode == 13) {
          e.preventDefault();
          e.stopPropagation();
          _map.geocode();
        }
      }
    })(this));

    this.container.find('.locate-button').on('click', (function (_map) {
      return function (e) {
        _map.geocode();
      }
    })(this));

    google.maps.event.addListener(this.pin, 'dragend', (function(_map) {
      return function (e) {
        _map.geocode_result = _map.pin.getPosition();
        _map.update_position();
      }
    })(this));
  };

  MapField.prototype.geocode = function () {
    this.geocoder.geocode({'address': this.location_fields.address.val()}, (function (_map) {
      return function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          _map.geocode_result = results[0].geometry.location;
          _map.update_position();
        } else {
          alert('Sorry, the location couldn’t be found.');
        }
      }
    })(this));
  };

  MapField.prototype.update_position = function () {
    this.location_fields.lat.val(this.geocode_result.lat());
    this.location_fields.lng.val(this.geocode_result.lng());
    this.pin.setPosition(this.geocode_result);
    this.map.panTo(this.geocode_result);
  };

  loader = new Loader();

  $.fn.mapField = function () {
    loader.load((function (_fields) {
      return function () {
        for ( var f = 0; f < _fields.length; f++ ) {
          new MapField(_fields[f]);
        }
      }
    })(this));
  };

})(jQuery);
