<script type="text/javascript">

$(document).ready(function() {
	$("#map").dialog( {
		height: 500,
		width: 750,
		modal: true,
		autoOpen: false
		} );
$(".vaccine_map").click(function(){
	$('#map').dialog('option', 'title', $(this).attr("map_title"));
	$("#map").dialog('open');
	load_vaccine_map($(this).attr("id")); 
	
});
 
//load();
});


  //<![CDATA[
    var map;
    var markers = [];
    var infoWindow;
    var locationSelect;
    var markerCluster; 
    
    function sum_population(markers, numStyles) {
  	  var index = 0;
	  var count = markers.length;
	  var total_stock = 0;
	  for(var x = 0; x<count;x++){
		var stock = parseInt(markers[x].stock);
		total_stock += stock;
	  }
	  var dv = total_stock;
	  while (dv !== 0) {
	    dv = parseInt(dv / 10, 10);
	    index++;
	  }
	  index = Math.min(index, numStyles);
	  return {
	    text: total_stock,
	    index: index
	  };
    }
 

    	
    function load_vaccine_map(vaccine) {
       markers = [];
        
      map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(0.35156,37.913818),
        mapTypeId: 'roadmap',
        mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
      });
      var opt = { minZoom: 6 };
      map.setOptions(opt);
      infoWindow = new google.maps.InfoWindow();
      var url = '<?php echo base_url()."country_stock_map/plot/"?>'+vaccine;
		downloadUrl(url, function(data) {
       var xml = parseXml(data);
       var markerNodes = xml.documentElement.getElementsByTagName("marker");
       var bounds = new google.maps.LatLngBounds();
       for (var i = 0; i < markerNodes.length; i++) {
         var name = markerNodes[i].getAttribute("name");
         var facility_id = markerNodes[i].getAttribute("facility_id");
         var stock = markerNodes[i].getAttribute("stock");
         var latlng = new google.maps.LatLng(
              parseFloat(markerNodes[i].getAttribute("lat")),
              parseFloat(markerNodes[i].getAttribute("lng")));
         createMarker(latlng, name, stock,facility_id);
         bounds.extend(latlng);  	    
       }
       map.fitBounds(bounds);
       markerCluster = new MarkerClusterer(map, markers,{ zoomOnClick: false });
       markerCluster.setCalculator(sum_population);
       google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {
           var covered_markers = cluster.getMarkers();
           
			var uniqueness = [];
           for (marker in covered_markers)
           {
           if(uniqueness.indexOf(covered_markers[marker].facility_id) == -1){
               uniqueness.push(covered_markers[marker].facility_id);
           }
           }
           if(uniqueness.length == 1){
           google.maps.event.trigger(covered_markers[0], 'click'); 
           }
   	});
          
      });	
   }
    
    

   function clearLocations() {
     infoWindow.close();
     for (var i = 0; i < markers.length; i++) {
       markers[i].setMap(null);
     }
     markers.length = 0;

     locationSelect.innerHTML = "";
     var option = document.createElement("option");
     option.value = "none";
     option.innerHTML = "See all results:";
     locationSelect.appendChild(option);
   }
    function createMarker(latlng, name, stock, facility_id) {
      var html = "<b>" + name + "</b> <br><b>Stock Held: </b>"+stock+" doses";
      var marker = new google.maps.Marker({
        map: map,
        position: latlng,
        facility_id: facility_id,
        stock: stock
      });      
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
      markers.push(marker); 
    }


    function downloadUrl(url, callback) { 
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request.responseText, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function parseXml(str) {
      if (window.ActiveXObject) {
        var doc = new ActiveXObject('Microsoft.XMLDOM');
        doc.loadXML(str);
        return doc;
      } else if (window.DOMParser) {
        return (new DOMParser).parseFromString(str, 'text/xml');
      }
    }

    function doNothing() {}
    function cleanup(){};

    //]]>
</script> 
<div id="summaries"> 


<div id="notification_panel">
<div id="notification_panel_image"></div>
<div id="notification_panel_text">
Values in <b style="color:red">RED</b> indicate vaccine levels that will <b>NOT</b> last the store for at least a <b>MONTH</b>
</div>
</div> 

<table border="0" class="data-table" style="margin:0 auto 0 auto;">
<tr>
<th rowspan="2">Store</th>
<th colspan = "<?php echo count($vaccines);?>">Vaccine</th>
</tr>
<tr>
<?php 
foreach($vaccines as $vaccine){?>
	<th style="background-color:<?php echo '#'.$vaccine->Tray_Color;?>; color:white;"><?php echo $vaccine->Name;?></th>
	 
<?php 
}
?>
</tr>
<tr>
<td><a class="link" href="<?php echo site_url('disbursement_management/drill_down/2/0');?>"> National Central Store </a> </td>
<?php  
foreach($vaccines as $vaccine){

$population = str_replace(",","",$national_stocks[$vaccine->id][1]);
$monthly_requirement =  ceil(($vaccine->Doses_Required*$population*$vaccine->Wastage_Factor)/12);
if($national_stocks[$vaccine->id][0] >= $monthly_requirement){?>
<td style="color:green"><?php echo $national_stocks[$vaccine->id][0];?></td>
<?php }
else{?>
<td style="color:red"><?php echo $national_stocks[$vaccine->id][0];?></td>
<?php }
?>
	
	 
<?php 
}
?>
</tr>

<?php 
foreach($regional_stores as $regional_store){?>
<tr>
<td>  <a class="link" href="<?php echo site_url('disbursement_management/drill_down/0/'.$regional_store->id);?>"> <?php echo $regional_store->name;?> </a>  </td>
<?php foreach($vaccines as $vaccine){
$population = str_replace(",","",$regional_stocks[$vaccine->id][$regional_store->id][1]);
$monthly_requirement =  ceil(($vaccine->Doses_Required*$population*$vaccine->Wastage_Factor)/12);

if($regional_stocks[$vaccine->id][$regional_store->id][0]>=$monthly_requirement){?>
<td style="color:green"><?php echo $regional_stocks[$vaccine->id][$regional_store->id][0];?></td>
<?php }
else{?>
<td style="color:red"><?php echo $regional_stocks[$vaccine->id][$regional_store->id][0];?></td>
<?php }
?>

<?php }
?>
</tr>
<?php }
?>

<tr>
<td><b>Coverage Map</b></td>
<?php 
foreach($vaccines as $vaccine){?>
	<td>
	<a href="#" class="link vaccine_map" map_title="<?php echo "Country Distribution for ".$vaccine->Name." Vaccine"?>" id="<?php echo $vaccine->id;?>">Click to View Map</a>
	</td>
	 
<?php 
}
?>
</tr>
</table> 
</div>
<style type="text/css">
label,input {
	display: block;
}

#map {
	background-color: white !important;
	background: url('<?php echo base_url()."Images/loading.gif";?>')
		no-repeat center;
}
</style>
<div
	id="map"
	style="width: 700px; height: 500px; margin: 5px auto 5px auto;"></div>
<input type="hidden"
	id="vaccine_name_label" />
 
