MapPage = function(pageIdentifier)
{
    this.pageIdentifier = pageIdentifier;
}

MapPage.prototype = new AppPage();

MapPage.prototype.constructor = MapPage;

MapPage.prototype.map = null;

MapPage.prototype.initPage = function()
{
    if (this.map == null)
    {
        this.map = new Map('map', this.getCitySlug(), this);
    }
};

MapPage.prototype.refreshGpsGauge = function(quality)
{
    if ((quality >= 0) && (quality <= 25))
    {
        $('#gpsquality').html('sehr gut (' + quality + ' Meter)');
    }
    else if ((quality > 25) && (quality <= 70))
    {
        $('#gpsquality').html('annehmbar (' + quality + ' Meter)');
    }
    else
    {
        $('#gpsquality').html('unbrauchbar (' + quality + ' Meter)');
    }
};

MapPage.prototype.isGpsActivated = function()
{
    var gpsSender = $("select#flip-gps-sender")[0].selectedIndex;

    if (gpsSender == 1)
    {
        return true;
    }

    return false;
};

MapPage.prototype.isAutoFollow = function()
{
    return $('#' + this.pageIdentifier + 'select#autoFollow')[0].selectedIndex;
};

MapPage.prototype.setAutoFollow = function(autoFollow)
{
    $('#' + this.pageIdentifier + 'select#autoFollow').val(autoFollow ? 'on' : 'off').slider('refresh');
};