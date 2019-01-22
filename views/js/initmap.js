/*jslint browser: true*/
/*global $, jQuery, alert, google*/
var map;
var markers = [];
var markersobj = [];
var oldMarkers = [];
var infowindow;
var markerBounds;
var greenIcon = 'views/images/icon_green.png';

function refreshStats() {
    "use strict";
	$('#stats').load('resources/stats.php', function () {
        $('#map-canvas').height('590px');
		setTimeout(refreshStats, 5000);
	});
}

$(document).ready(function () {
    "use strict";
	refreshStats();
});

function parseXML(url) {
    "use strict";
	// get markers info from xml page
	var request = window.XMLHttpRequest ? new XMLHttpRequest() : new window.ActiveXObject("Microsoft.XMLHTTP");
	request.open('GET', url, false);
	request.send(null);

	return request.responseXML;
}

function addMarker(p) {
    "use strict";
	var title = p.getAttribute("title"),
	    description = p.getAttribute("description"),
	    statusMsg = p.getAttribute("status"),
	    category = p.getAttribute("category"),
	    lat = p.getAttribute("latitude"),
	    lon = p.getAttribute("longitude"),
	    diff = p.getAttribute("timeDiff"),
	    latlng = new google.maps.LatLng(lat, lon),
        contentString,
        marker;

	markerBounds.extend(latlng);

	marker = new google.maps.Marker({
		position: latlng,
		map: map
	});

    markersobj.push(marker);

	if (statusMsg === "Closed") {
		marker.setIcon(greenIcon);
	}

	//add Infowindows
	contentString = '<div>' +
		'<h4>' + title + '</h4>' +
		'<div>' +
		'<p>' + description + '</p>' +
		'</div>' +
                '<small>Before ' + diff + '</small>' +
		'</div>';

	google.maps.event.addListener(marker, 'click', (function (contentString) {
		return function () {
			//map.setZoom(10);
			//map.setCenter(this.getPosition());
			infowindow.setContent(contentString);
			infowindow.open(map, this);
		};
	}(contentString)));

	return marker;
}

function markerExists(p) {
    "use strict";
	var i;
    for (i = 0; i < oldMarkers.length; i += 1) {
        if (oldMarkers[i].getAttribute('id') === p.getAttribute('id')) {
            return true;
        }
    }

    return false;
}

function markerChanged(p) {
    "use strict";
	var i;
    for (i = 0; i < oldMarkers.length; i += 1) {
        if (oldMarkers[i].getAttribute('id') === p.getAttribute('id')) {
            if (oldMarkers[i].getAttribute('status') !== p.getAttribute('status')) {
                return true;
            }
        }
    }

    return false;
}

function changeColorOnClose(p) {
    "use strict";
    var lat = p.getAttribute("latitude"),
	    lon = p.getAttribute("longitude"),
	    latlng = new google.maps.LatLng(lat, lon),
        tlatlng,
        i;

    for (i = 0; i < markersobj.length; i += 1) {
        tlatlng = markersobj[i].position;
        if (tlatlng.equals(latlng)) {
            markersobj[i].setIcon(greenIcon);
        }
    }
}

function clearMarkers() {
    "use strict";
    var lon, lat, tlatlng, latlng, i, j, old;
    //krataw epipleon ena markersobj oste na menoun ta markers
    //objects kai na ta ta kanw .setMap(null)osa pleon den einai sta last 20
    //an kai einai psilokaki ylopoihsh to markersobj einai active mono mexri
    //to epomeno refresh opote isos na min einai provlima
    //mou thymizei triti lykeiou auti i asximia(ginan 2 oi asximies)
    for (i = 0; i < markersobj.length; i += 1) {
        old = 1;
        tlatlng = markersobj[i].position;
        for(j = 0; j < markers.length; j += 1) {
            lon = markers[j].getAttribute("longitude");
	        lat = markers[j].getAttribute("latitude");
	        latlng = new google.maps.LatLng(lat, lon);

            if (tlatlng.equals(latlng)) {
                old = 0;
            }
        }
        if (old == 1) {
            markersobj[i].setMap(null);
        }
    }
}

function updateMarkers() {
    "use strict";
	var data = parseXML('resources/last.php'), i, p, marker, statusMsg, ret;
	markers = data.getElementsByTagName("marker");

	for (i = 0; i < markers.length; i += 1) {
		p = markers[i];
        statusMsg = p.getAttribute("status");
		//TODO check the marker status and change colour dynamically
		if (!markerExists(p)) {
			marker = addMarker(p);
			marker.setAnimation(google.maps.Animation.DROP);
			map.fitBounds(markerBounds);
		} else {
            //if exists replace color
            if (markerChanged(p)) {
                console.log('changed');
                changeColorOnClose(p);
            }
        }
	}

    //clear old markers
    clearMarkers();

	oldMarkers = markers;
	setTimeout(updateMarkers, 5000);
}

function initialize() {
    "use strict";
    var i, data, p, marker, mapOptions = {
		zoom: 8,
		disableDefaultUI: true
    };
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	infowindow = new google.maps.InfoWindow({
		maxWidth: 200
	});
	markerBounds = new google.maps.LatLngBounds();

	data = parseXML('resources/last.php');
	markers = data.getElementsByTagName("marker");

	for (i = 0; i < markers.length; i += 1) {
		p = markers[i];
		marker = addMarker(p);
		marker.setAnimation(google.maps.Animation.DROP);
	}


	map.fitBounds(markerBounds);
	oldMarkers = markers;
	setTimeout(updateMarkers, 5000);
}



google.maps.event.addDomListener(window, 'resize', initialize);
google.maps.event.addDomListener(window, 'load', initialize);

$(document).on("click", ".editUser", function () {
    "use strict";
    var username = $(document).find('.user-img').text().trim(),
	    post_data = { user_name: username, user_edit: 1 };
	$.post("edit.php", post_data)
	    .done(function (data) {
	        var content = $(data)[35]; //mou exei spasei ta neura giati to .find('#edit-header') den douleuei
	        $('#modalUserForm').empty().append(content);
	    });
});
