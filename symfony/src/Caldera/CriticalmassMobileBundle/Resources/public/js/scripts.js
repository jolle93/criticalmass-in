/**
 * Ruft im Hintergrund die Logout-URL auf und beendet die Sitzung des Benutzers.
 */
function logout()
{
    $.ajax({
        type : 'GET',
        url : '/app_dev.php/logout',
        success : function(data){
            alert(data);
        }
    });
}

function loadAllCities()
{
    $.ajax({
        type: 'GET',
        url: '/app_dev.php/api/cities/listall',
        cache: false,
        success: function(data)
        {
            for (index in data.cities)
            {
                var city = data.cities[index];
                var listItem = document.createElement('li');

                $(listItem).append('<h3>' + city.title + '</h3>');

                if (city.description != null)
                {
                    $(listItem).append('<p>' + city.description + '</p>');
                }

                $('#cityList').append(listItem);
            }

            $('#cityList').listview('refresh');
        }
    });
}