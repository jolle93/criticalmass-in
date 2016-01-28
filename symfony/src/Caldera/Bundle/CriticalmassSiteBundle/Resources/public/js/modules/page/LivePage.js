define(['Map', 'Container', 'CityEntity', 'RideEntity', 'NoLocationRideEntity', 'MapLayerControl', 'MapLocationControl', 'MapPositions'], function() {
    LivePage = function () {
        this._initContainer();
        this._initMap();
        this._initLive();
        this._initLayers();
        this._initLayerControl();
        this._initLocationControl();
        this._initOfflineCallback();
        this._startLive();
    };

    LivePage.prototype._map = null;
    LivePage.prototype._rideContainer = null;
    LivePage.prototype._cityContainer = null;
    LivePage.prototype._layers = [];
    LivePage.prototype._offlineCallbackShown = false;

    LivePage.prototype._initContainer = function() {
        this._rideContainer = new Container();
        this._cityContainer = new Container();
    };

    LivePage.prototype._initMap = function() {
        $('#map').css('top', $('nav#navigation').css('height'));

        this._map = new Map('map', []);
    };

    LivePage.prototype._initLive = function() {
        this._mapPositions = new MapPositions();

        this._mapPositions.addToControl(this._layers, 'Teilnehmer');
    };

    LivePage.prototype._initLayers = function() {
        this._rideContainer.addToMap(this._map);
        this._mapPositions.addToMap(this._map);
        //this._map.addLayer(this._rideContainer.getLayer());
        //this._map.addLayer(this._cityContainer.getLayer());
        //this._map.addLayer(this._mapPositions.getLayer());
    };

    LivePage.prototype._initLayerControl = function() {
        this._rideContainer.addToControl(this._layers, 'Tour');
        //this._cityContainer.addToControl(this._layers, 'Städte');

        this._layerControl = new MapLayerControl();
        this._layerControl.setLayers(this._layers);
        this._layerControl.init();
        this._layerControl.addTo(this._map);
    };

    LivePage.prototype._initLocationControl = function() {
        this._locationControl = new MapLocationControl();
        this._locationControl.init();
        this._locationControl.addTo(this._map);
    };

    LivePage.prototype._initOfflineCallback = function() {
        this._mapPositions.setOfflineCallback(this.offlineCallback);
    };

    LivePage.prototype.offlineCallback = function() {
        if (!this._offlineCallbackShown) {
            $('#offlineModal').modal();

            this._offlineCallbackShown = true;
        }
    };

    LivePage.prototype.addCity = function(city, title, slug, description, latitude, longitude) {
        var cityEntity = new CityEntity(city, title, slug, description, latitude, longitude);

        this._cityContainer.addEntity(cityEntity);
    };

    LivePage.prototype.addRide = function(title, description, latitude, longitude, location, date, time, weatherForecast) {
        var rideEntity = new RideEntity(title, description, latitude, longitude, location, date, time, weatherForecast);

        this._rideContainer.addEntity(rideEntity);
    };

    LivePage.prototype.addNoLocationRide = function(title, description, latitude, longitude, location, date, time, weatherForecast) {
        var rideEntity = new NoLocationRideEntity(title, description, latitude, longitude, location, date, time, weatherForecast);

        this._rideContainer.addEntity(rideEntity);
    };

    LivePage.prototype._startLive = function() {
        this._mapPositions.start();
    };

    LivePage.prototype.setFocus = function() {
        var bounds = this._rideContainer.getBounds();

        this._map.fitBounds(bounds);
    };

    return LivePage;
});