var geocoder;

$(document).ready(function(){
	geocoder = new google.maps.Geocoder();
});

function SimpleeGeolocate(field_id){
	$("#message_field_id_" + field_id).html("Looking up location...");

	var address = $("#address_field_id_" + field_id).val();
	geocoder.geocode( { 'address': address}, function(results, status) {
    	if (status == google.maps.GeocoderStatus.OK) {
        	var pos = results[0].geometry.location;
        	var lat = pos.lat();
        	var lng = pos.lng();
            $("#latitude_field_id_" + field_id).val(lat);
            $("#longitude_field_id_" + field_id).val(lng);
            $("#message_field_id_" + field_id).html("Location Found: (" + lat + ", " + lng + ").");
        } 
	});
}

function GEEolocateMatrix(p){
	p.closest("#message_field_id_" + field_id).html("Looking up location...");

	var address = p.closest("#address_field_id_" + field_id).val();
	geocoder.geocode( { 'address': address}, function(results, status) {
    	if (status == google.maps.GeocoderStatus.OK) {
        	var pos = results[0].geometry.location;
        	var lat = pos.lat();
        	var lng = pos.lng();
            p.closest("#latitude_field_id_" + field_id).val(lat);
            p.closest("#longitude_field_id_" + field_id).val(lng);
            p.closest("#message_field_id_" + field_id).html("Location Found: (" + lat + ", " + lng + ").");
        } 
	});
}