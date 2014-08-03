var map, sidebar;

function getViewport() {
  if (sidebar.isVisible()) {
    map.setActiveArea({
      position: "absolute",
      top: "0px",
      left: $(".leaflet-sidebar").css("width"),
      right: "0px",
      height: $("#map").css("height")
    });
  } else {
    map.setActiveArea({
      position: "absolute",
      top: "0px",
      left: "0px",
      right: "0px",
      height: $("#map").css("height")
    });
  }
}

$('.cityRow').on('click', function()
{
    showCityInfo($(this).data('cityslug'));
});
function showCityInfo(citySlug)
{
    var city = CityFactory.getCityFromStorageBySlug(citySlug);

    $('#cityInfoTitle').html(city.getTitle());
    $('#cityInfoPunchLine').html(city.getPunchLine());
    $('#cityInfoDescription').html(city.getDescription());

    if (city.countSocialMediaLinks() > 0)
    {
        var html = '';

        if (city.getUrl())
        {
            html += '<button type="button" class="btn btn-default" href="' + city.getUrl() + '">WWW</button>';
        }

        if (city.getFacebook())
        {
            html += '<button type="button" class="btn btn-default" href="' + city.getFacebook() + '">facebook</button>';
        }

        if (city.getTwitter())
        {
            html += '<button type="button" class="btn btn-default" href="' + city.getTwitter() + '">twitter</button>';
        }

        $('#cityInfoSocialMedia').html(html);
    }

    var ride = RideFactory.getRideFromStorageBySlug(citySlug);

    if (ride)
    {
        var imageFilename = Url.getUrlPrefix() + 'images/ride/' + slug + '/' + ride.getId() + '.jpg';

        if (Url.fileExists(imageFilename))
        {
            $('#cityModalTabNextRideJumbotron').show();
            $('#cityModalTabNextRideTitle').hide();

            $('#cityModalTabNextRideJumbotron').css('background-image', 'url(' + imageFilename + ')');
        }
        else
        {
            alert('ride image failure');
            $('#cityModalTabNextRideJumbotron').hide();
            $('#cityModalTabNextRideTitle').show();
        }

        $('#cityModalTabNextRideJumbotronTitle').html(ride.getTitle());
        $('#cityModalTabNextRideTitle').html(ride.getTitle());
        $('#cityModalTabNextRideDescription').html(ride.getDescription());

        $('#cityModalTabNextRideLocation span').html(ride.getLocation());
        $('#cityModalTabNextRideDate time').html(ride.getFormattedDate());
        $('#cityModalTabNextRideTime time').html(ride.getFormattedTime());
    }

    $('#cityInfoModal').modal('show');

    map.setView([city.getLatitude(), city.getLongitude()], 15);
}

if (document.body.clientWidth <= 767) {
    var isCollapsed = true;
} else {
    var isCollapsed = false;
    /*sidebar.show();*/
}

function initApp()
{
    var tileLayerObjects = TileLayerFactory.getTileLayers();
    var tileLayers = new Array();
    var standardTileLayer = null;

    for (var index in tileLayerObjects)
    {
        var tileLayerObject = tileLayerObjects[index];
        var tileLayer = L.tileLayer(tileLayerObject.getAddress(), {foo: 'bar'});

        tileLayers[tileLayerObject.getTitle()] = tileLayer;

        if (tileLayerObject.getStandard())
        {
            standardTileLayer = tileLayer;
        }
    }


    var criticalmassIcon = L.icon({
        iconUrl: '/bundles/calderacriticalmasscore/images/marker/criticalmassblue.png',
        iconSize: [25, 41],
        iconAnchor: [13, 41],
        popupAnchor: [0, -36],
        shadowUrl: '/bundles/calderacriticalmasscore/images/marker/defaultshadow.png',
        shadowSize: [41, 41],
        shadowAnchor: [13, 41]
    });

    var cities = CityFactory.getAllCities();
    var markerArray = new Array();

    for (var index in cities)
    {
        var city = cities[index];
        var html = '<tr class="cityRow" data-cityslug="' + city.getCitySlug() + '" style="cursor: pointer;"><td class="cityName">' + city.getCity() + '<i class="fa fa-chevron-right pull-right"></i></td></tr>';

        $('#cityList').append(html);

        $('.cityRow').on('click', function()
        {
            showCityInfo($(this).data('cityslug'));
        });

        var marker = L.marker([city.getLatitude(), city.getLongitude()], { icon: criticalmassIcon, citySlug: city.getCitySlug() });
        marker.on('click', function()
        {
            showCityInfo(this.options.citySlug);
        });

        markerArray.push(marker);
    }

    var markerGroup = L.layerGroup(markerArray);

    map = L.map('map', { zoomControl: false, attributionControl: false, layers: [markerGroup]});

    map.setView([53.5554952, 9.9436765], 13);

    standardTileLayer.addTo(map);

    sidebar = L.control.sidebar("sidebar", {
        closeButton: true,
        position: "left"
    }).on("shown", function () {
        getViewport();
    }).on("hidden", function () {
        getViewport();
    }).addTo(map);

    if (document.body.clientWidth <= 767)
    {
        var isCollapsed = true;
    }
    else
    {
        var isCollapsed = false;
        sidebar.show();
    }


    var zoomControl = L.control.zoom({
        position: "topright"
    }).addTo(map);



    var heatmapGroup = L.layerGroup([L.tileLayer("https://www.criticalmass.cm/images/heatmap/123123123/{z}/{x}/{y}.png", {
        maxZoom: 18
    })]);


    getViewport();

    var mapPositions = new MapPositions(map);
    mapPositions.startLoop();

    var layerControl = L.control.groupedLayers(tileLayers, {
        "Critical Mass": {
            "Städte": markerGroup,
            "Teilnehmer": mapPositions.layerGroup,
            "Heatmaps": heatmapGroup
        }
    }, {
        collapsed: false
    });
    layerControl.addTo(map);
}