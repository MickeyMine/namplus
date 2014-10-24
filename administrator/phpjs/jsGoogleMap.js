var map;
var markersArray = [];

function initialize() {

    var posX, posY;

    if (document.getElementById(positionX).value != "" && document.getElementById(positionY).value != "") {
        posX = document.getElementById(positionX).value;
        posY = document.getElementById(positionY).value
    }
    else {
        posX = 106.67350709438324;
        posY = 10.75912771916204;
    }   

    var mapOptions = {
        zoom: 13,
        center: new google.maps.LatLng(posY, posX),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById('map'),
        mapOptions);
    

    var location;
    if(document.getElementById(positionX).value!="" && document.getElementById(positionY).value!="")
    {
        location = new google.maps.LatLng(posY, posX);
        setMarkers(map, location);
    }
    
 //   setMarkers(map, location);

   // google.maps.event.addDomListener(document.getElementById('map'), 'click', showPositionMap);
    google.maps.event.addListener(map, 'click', showPositionMap);
}

function setMarkers(map, locations) {
    var image = 'phpimages/iconpoint.png';
 
    var beachMarker = new google.maps.Marker({
        position: locations,
        map: map,
        icon: image
    });

    markersArray.push(beachMarker);     // add maker to array


}
// Deletes all markers in the array by removing references to them
function deleteOverlays() {
    if (markersArray) {
        for (i in markersArray) {
            markersArray[i].setMap(null);
        }
        markersArray.length = 0;
    }
}

function showPositionMap(event) {
  //  alert(event.latLng.lng());// tọa độ x

    //   alert(event.latLng.lat());// tọa độ y
    deleteOverlays();
    var location = new google.maps.LatLng(event.latLng.lat(), event.latLng.lng());
    setMarkers(map, location);
    document.getElementById(positionX).value = event.latLng.lng();
    document.getElementById(positionY).value = event.latLng.lat();

}

google.maps.event.addDomListener(window, 'load', initialize);

