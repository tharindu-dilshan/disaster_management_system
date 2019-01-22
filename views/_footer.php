<html>
<head>
<link href='views/css/newsslider.css' rel='stylesheet' type='text/css'>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="views/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDw3MQncM0DCAMROUC7Ow5wyllismsuwP8"sensor=false"></script>
    <script type="text/javascript" src="views/js/initmap.js"></script>
	<script  src="views/js/newsslider.js"></script>
</head>
<body>
    <div id="map-canvas"></div>

	<!-- Modal -->
	<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 id="modalUserHeader" class="modal-title" id="myModalLabel">Edit Profile</h4>
		</div>
		<div id="modalUserForm" class="modal-body">
		</div>
		</div> <!-- close modal-content -->
	</div> <!-- close modal-dialog -->
	</div> <!-- close modal -->
	<div id="stats" style="position: absolute;top:0px;width:100%;">
	</div>
	
	<center><p style="color:#E96D65;border-bottom:5px solid #E96D65;font-size:30px;padding-top:9px">NEWS</p><center>
	
	<div id="slider-main">
    <div id="slides">
        <ul>
		<li class="slide">
				<div class="headingContainer">
                    <h3 class="heading">Landslide Watch level -1 Colour Code Yellow 0530hrs ,30-11-2017</h3>
                </div>
				
                <div class="newsContainer">
                    <p class="news">Since the rainfall within the past 24 hours has exceeded 150mm and if the rains continue evacuate to a safe location to avoid 
				risk of landslides, slope failures, rock falls, cutting failures and ground subsidence may occur in following areas.
				<br>
                Boraluwage Ayina , Bulutota ,Ranhotikanda Iththakanda ,Ulinduwa and Buthkanda in Kolonna DS Division in Ratnapura 
				District Haldulmulla and Bandarawela DS Division in Badulla District
				<br>
                Laggala Pallegama DS Division in Matale District
				<br>
                Waleboda , Boltumbe, Maratenna, Udagama and Pidaligan-arawa GN Divisions in Imbulpe DS Division Ratnapura District
				<br>
                Elpitiya DS Division in Galle District
				<br>
                Pitabeddara , Kotapala DS Divisions in Matara District</p>
					
                </div>
            </li>
            <li class="slide">
				<div class="headingContainer">
                    <h3 class="heading">Weather forecast for 04th December 2017 issued at 05.30 a.m. On 04th December 2017</h3>
                </div>
				
                <div class="newsContainer">
                    <p class="news">The low pressure area in the South Andaman Islands is likely to develop further. Therefore,
				wind condition over the island and surrounding sea areas (particularly Northern, Eastern and Southern sea areas)
				may increase from 5th of December.Showers or thundershowers will occur at times in Northern, North-Central, 
				Eastern and Uva provinces.Showers or thundershowers will occur elsewhere after 2.00p.m.Fairly heavy showers 
				(above 75mm) can be expected in Eastern, Northern and North-central provinces.Fairly strong gusty winds (up to 50kmph)
				can be expected in the Western, Sabaragamuwa and Uva provinces and in Ampara and Batticaloa districts.
                    </p>
					
                </div>
                
            </li>
            <li class="slide">
				<div class="headingContainer">
                    <h3 class="heading">Weather forecast for 01 December 2017 -issued at 05.30 AM on 01 December 2017.</h3>
                </div>
				
                <div class="newsContainer">
                    <p class="news">The cyclone named OCKHI in the Arabian Sea is now located at about 600km to the west of Colombo and moving further 
				away from the island. Hence it’s effect for the country is expected to be lessen gradually.
				Showers or thundershowers will occur over most parts of the island.
				Heavy falls (above 100mm) can be expected at some places in the Northern, North-central, Uva, Southern, Western, 
				Sabaragamuwa and Central provinces.
				Fairly strong gusty winds up to 50kmph can be expected in the Western, Southern and Sabaragamuwa provinces.
                    </p>
					
                </div>
            </li>
        </ul>
    </div>
    <div class="page-footer page-footer-red">
        <p style="color:white">© 2018 ABC Technology. All Rights Reserved.</p>
    </div>
	<div class="btn-bar" style="display:none">
    <div id="buttons"><a id="prev" href="#"><</a><a id="next" href="#">></a> </div></div>
    </div>
  </body>
</html>
