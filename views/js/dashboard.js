/*jslint browser: true*/
/*global $, jQuery, alert, google*/
// slightly modified myreports.js fix for map in dynamic content
// http://goo.gl/A2rh2I
var map = [];

$(document).ready(function () {
    "use strict";
	var totals;
	
	$('#content-submited').load('resources/submitedReports.php', function () {
		totals = parseInt($('#content-submited').find('#totalopen').html(), 10);
		// init bootpag
		$('#page-selection-submited').bootpag({
			total: totals,
			page: 1,
			maxVisible: 4
		}).on("page", function (event, num) {
            		$("#content-submited").load('resources/submitedReports.php', {page: num});
		});

		$('.report-modal1').on('shown.bs.modal', function (e) {
			var x = e.currentTarget.id, that,
                lat, long, mapOptions, marker, center;
			if (map[x] === undefined) {
				// find coordinates
				that = $(this);
				lat = that.find('span[data-lat1]').data('lat1');
				long = that.find('span[data-long1]').data('long1');

				mapOptions = {
					center: new google.maps.LatLng(lat, long),
					zoom: 16,
					disableDefaultUI: true
				};
				map[x] = new google.maps.Map(document.getElementById('map' + x), mapOptions);
				marker = new google.maps.Marker({
					position: new google.maps.LatLng(lat, long),
					map: map[x]
				});
			} else {
				center = map[x].getCenter();
				google.maps.event.trigger(map[x], "resize");
				map[x].setCenter(center);
			}
		});

                if ($(this).find('tbody tr').length === 1) {
                    $(this).find('tbody tr.warning').removeClass('hidden');
                }
	});
	
	$('#content-open').load('resources/openReports.php', function () {
		totals = parseInt($('#content-open').find('#totalopen').html(), 10);

		// init bootpag
		$('#page-selection-open').bootpag({
			total: totals,
			page: 1,
			maxVisible: 4
		}).on("page", function (event, num) {
            $("#content-open").load('resources/openReports.php', {page: num});
		});

		$('.report-modal').on('shown.bs.modal', function (e) {
			var i = e.currentTarget.id, that,
                lat, long, mapOptions, marker, center;
			if (map[i] === undefined) {
				// find coordinates
				that = $(this);
				lat = that.find('span[data-lat]').data('lat');
				long = that.find('span[data-long]').data('long');

				mapOptions = {
					center: new google.maps.LatLng(lat, long),
					zoom: 16,
					disableDefaultUI: true
				};
				map[i] = new google.maps.Map(document.getElementById('map' + i), mapOptions);
				marker = new google.maps.Marker({
					position: mapOptions.center,
					map: map[i]
				});
			} else {
				center = map[i].getCenter();
				google.maps.event.trigger(map[i], "resize");
				map[i].setCenter(center);
			}
		});

                if ($(this).find('tbody tr').length === 1) {
                    $(this).find('tbody tr.warning').removeClass('hidden');
                }
	});

	$('#content-closed').load('resources/closedReports.php', function () {
		totals = parseInt($('#content-closed').find('#totalclosed').html(), 10);

		 //init bootpag
		$('#page-selection-closed').bootpag({
			total: totals,
			page: 1,
			maxVisible: 4
		}).on("page", function (event, num) {
            $("#content-closed").load('resources/closedReports.php', {page: num});
		});

                if ($(this).find('tbody tr').length === 1) {
                    $(this).find('tbody tr.warning').removeClass('hidden');
                }
	});

	$(document).on("click", ".reportClosebtn", function () {
		console.log("onClick reportClosebtn....");
		
		/* Loipon o logos gia tin parakatw asxhmia einai o eksis:
		 * otan epestrefa ta dynamika modals meta tin epilogi selidas
		 * den mporousa na parw to form pou eixa ekei, kai oti tropos
		 * kai na dokimaza itan xalia. Opote afairesa to form apo ta modals
		 * kai evala apla elements. Etsi pairnw apla ta dedomena gia to post
		 * apo ta elements kai kataskeuazw ena form to opoio ginetai
		 * non-ajax post sto dashboard.php.
		 * */
		var modalform = $(this).parent().parent(),
		    id = modalform.find('span[data-id]').data('id'),
            comment = modalform.find('textarea').val(),
            post_data = { report_id: id, comment: comment, markClosed: 1 };

		var form='<form id="closeReportfrm" method="post" action="dashboard.php" role="form">' +
			'<input name="report_id" value="' + id + '">' +
			'<textarea name="comment">' + comment + '</textarea>' +
			'<input name="markClosed">' +
			'</form>';
		$(document.body).append(form);
		$('#closeReportfrm').submit();
	});
	
	$(document).on("click", ".reportOpenBtn", function () {
		var modalform = $(this).parent().parent(),
		    id = modalform.find('span[data-id]').data('id'),
            post_data = { report_id: id};

		var form='<form id="openReportfrm" method="POST" action="dashboard.php" role="form">' +
			'<input name="report_id" value="' + id + '">'+
			'<input name="markOpen">' +
			'</form>';
		$(document.body).append(form);
		$('#openReportfrm').submit();
	});

	//delegate event to document in case of newlly added categories
	$(document).on("click", ".editable", function () {
		$(this).attr('contentEditable', true).focus();
        $(this).blur(function () {
            $(this).attr('contentEditable', false);
            var newName = $(this).text(), id, post_data;
            if (newName) {
                id = $(this).parent().attr('id');
                post_data = "newName=" + newName + "&category_id=" + id;
                $.post("dashboard.php", post_data);
            }
        });
	});

	//delegate event to document in case of newlly added categories
	$(document).on("click", ".catRemove", function () {
		var cat_id = $(this).siblings().attr('id'),
            post_data = "del_cat_id=" + cat_id;
		$.post("dashboard.php", post_data);
		$(this).parent().remove();
	});

	$('.delUser').click(function () {
		var uid = $(this).parent().siblings("td.uid").text();
		$('<form method="post" action="dashboard.php" role="form">' +
			'<input name="del_user_id" value="' + uid + '">' +
			'</form>').submit();
	});

	$(document).on("submit", "#categoryForm", function (e) {
		e.preventDefault();
		var catName = $(this).find('#categoryNameId').val(),
            post_data = $(this).serialize() + "&categorySubmit=" + encodeURIComponent(1);

		// empty category name field
		$(this).find('#categoryNameId').val('');

		// check if category name is empty
		if (/^$|^\s+$/.test(catName)) {
			return;
		}

		$.post("dashboard.php", post_data)
			.done(function (data) {
				$('#categories').append(data);
			});
	});


	$(document).on("click", ".editUser", function () {
	//$('.editUser').click(function() {
		var username = $(this).parent().siblings("td.name").text(),
            usermail = $(this).parent().siblings("td.email").text(),
            userfullname = $(this).parent().siblings("td.fname").text(),
            userphone = $(this).parent().siblings("td.phone").text(),
            post_data;

		//handle the click of the nav edit for the same user
		if (!username) {
			username = $(document).find('.user-img').text();
		}
		username = username.trim();

		post_data = { user_name: username, user_email: usermail, user_edit: 1, user_fullname: userfullname, user_phone: userphone };
		$.post("edit.php", post_data)
		    .done(function (data) {
                var content = $(data)[35]; //mou exei spasei ta neura giati to .find('#edit-header') den douleuei
                $('#modalUserForm').empty().append(content);
            });
	});
});
