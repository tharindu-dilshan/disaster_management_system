/*jslint browser: true*/
/*global $, jQuery, alert, google*/
$(window).load(function () {
    "use strict";
    function updateMarker(pos) {
        $('#debug').text(pos);
       
        $('#lat_field1').attr("value",pos.lat());
        $('#lon_field1').attr("value",pos.lng());
    }

    function loadMap(coords) {
        var mapOptions = {
            center: new google.maps.LatLng(coords.lat, coords.long),
            zoom: 11,
            disableDefaultUI: true
        }, map, marker;

        $('#debug').text(mapOptions.center);
        $('#lat_field1').attr("value",coords.lat);
        $('#lon_field1').attr("value",coords.long);

        map = new google.maps.Map($('#map-container')[0], mapOptions);
        marker = new google.maps.Marker({
            draggable: true,
            position: mapOptions.center,
            map: map,
            title: 'Report Position'
        });

        google.maps.event.addListener(marker, 'dragend', function () {
            updateMarker(marker.getPosition());
        });
    }

    function getCoords(position) {
        var lat = position.coords.latitude,
            long = position.coords.longitude;

        loadMap({
            lat: lat,
            long: long
        });
    }

    var greeceLatLng = {
        lat: 6.92,
        long: 79.82
    };
    function geolocationError(err) {
        loadMap(greeceLatLng);
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(getCoords, geolocationError);
    } else {
        loadMap(greeceLatLng);
    }

    $('#add-file-input').click(function () {
        var size = $('input[type="file"]').size();
        if (size < 4) {
            $(this).parent().append('<input class="upload" type="file" accept="image/*;capture=camera" name="images[]">');
        }
    });
});

/*$("#ajaxform").submit(function (e) {
    "use strict";
    var formObj = $(this),
        formURL = formObj.attr("action"),
        formData = new FormData(this);
    $.ajax({
        url: formURL,
        type: 'POST',
        data:  formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data, textStatus, jqXHR) {
            var content = $(data).find(".popup");
            $("#errors").empty().prepend(content);

            content[0].style.opacity = 0;

            //Make sure the initial state is applied.
            window.getComputedStyle(content[0]).opacity;

            content[0].style.opacity = 1;

            if ($(data).find(".popup").hasClass("alert-info")) {
                document.getElementById("ajaxform").reset();
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    });
    e.preventDefault(); //STOP default action
});*/

$(document).on("click", ".editUser", function () {
    "use strict";
    var username = $(document).find('.user-img').text().trim(),
        post_data = { user_name: username, user_edit: 1 };

	$.post("edit.php", post_data)
	    .done(function (data) {
	        var content = $(data)[35];
	        $('#modalUserForm').empty().append(content);
        });
});
