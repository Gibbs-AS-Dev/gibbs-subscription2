
// *************************************************************************************************
// *** class GibbsLeafletMap
// *************************************************************************************************

class GibbsLeafletMap
{
// *************************************************************************************************
// *** Constructor.
// *************************************************************************************************

constructor (mapContainerId, initialCoordinates = [0, 0], initialZoom = 13)
{
  var tileProperties;

  // Set the default zoom level at which the location map will be displayed. Values are defined by
  // OpenStreetMap.
  this._defaultZoomLevel = 14;
  // Set the maximum zoom level available on the location map.
  this._maxZoomLevel = 19;

  // Initialise the Leaflet map. The map is displayed in the container with the given ID.
  this._map = L.map(mapContainerId).setView(initialCoordinates, initialZoom);

  // Add the OpenStreetMap map tile layer. The copyright notice is required.
  tileProperties =
    {
      maxZoom: this._maxZoomLevel,
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    };
  this._tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    tileProperties);
  this._tileLayer.addTo(this._map);

}

// *************************************************************************************************
// *** Public methods.
// *************************************************************************************************

displayAddress(address)
{
  // Clear all currently displayed marker layers.
  this._map.eachLayer(this._removeMarker.bind(this));

  // Use Nominatim API to get the coordinates for the address. If found, display a map marker at the
  // address, and center the map on that location.
  fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURI(address))
    .then(Utility.extractJson)
    .catch(this._logNominatimError.bind(this))
    .then(this._displayMarker.bind(this))
    .catch(this._logNominatimError.bind(this));
}

// *************************************************************************************************
// *** Property servicing methods.
// *************************************************************************************************
// Return the Leaflet map. The map property is read-only.
get map()
{
  return this._map;
}

// *************************************************************************************************
// Return the Leaflet map tile layer. The tileLayer property is read-only.
get tileLayer()
{
  return this._tileLayer;
}

// *************************************************************************************************
// Return the default zoom level property.
get defaultZoomLevel()
{
  return this._defaultZoomLevel;
}

// *************************************************************************************************
// Set the default zoom level property.
set defaultZoomLevel(newValue)
{
  this._defaultZoomLevel = Utility.getPositiveInteger(newValue, this._defaultZoomLevel);
}

// *************************************************************************************************
// Get the max zoom level property.
get maxZoomLevel()
{
  return this._maxZoomLevel;
}

// *************************************************************************************************
// Set the max zoom level property.
set maxZoomLevel(newValue)
{
  this._maxZoomLevel = Utility.getPositiveInteger(newValue, this._maxZoomLevel);
  // Write the new max zoom level to the map tile layer, then redraw the map to reflect the
  // change.
  this._tileLayer.options.maxZoom = newValue;
  this._redrawMap();
}

// *************************************************************************************************
// *** Protected methods.
// *************************************************************************************************

_redrawMap()
{
  this._map.eachLayer(
    function (layer)
    {
      layer.redraw();
    });
}

// *************************************************************************************************

_removeMarker(layer)
{
  if (layer instanceof L.Marker)
    this._map.removeLayer(layer);
}

// *************************************************************************************************
// Display a marker at the given map location, and center the map on that location. locations is a
// Javascript array, where each entry is an object which should include lat and lon fields. The
// first location in the list will be used.
_displayMarker(locations)
{
  var lat, lon, marker;
  
  if (locations && locations[0])
  {
    lat = locations[0].lat;
    lon = locations[0].lon;

    // Add a marker at the specified coordinates.
    marker = L.marker([lat, lon]);
    marker.addTo(this._map);
    // Add a popup showing the address.
    // marker.bindPopup(`<b>${address}</b>`).openPopup();
    // Center the map on the marker.
    this._map.setView([lat, lon], this._defaultZoomLevel);
  }
  else
    console.error('Unable to retrieve address coordinates.');
}

// *************************************************************************************************

_logNominatimError(error)
{
  console.error('Error fetching data from Nominatim API: ' + error);
}

// *************************************************************************************************

}
