<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Simplee_geolocate_ft extends EE_Fieldtype {
	
		var $has_array_data = TRUE;
		var $info = array(
			'name' => 'SimplEE Geolocate',
			'version' => '1.0.1'
		);
		
		var $geo_fields = array(
			"address"			=> "",
			"number"			=> "",
			"street_number"		=> "",
			"street_name"		=> "",
			"neighborhood"		=> "",
			"city"				=> "",
			"state"				=> "",
			"county"			=> "",
			"zip"				=> "",
			"country"			=> "",
			"latitude"			=> "",
			"longitude"			=> ""
		);
		
		var $address_components = array(
			"subpremise"					=> "number",
			"street_number"					=> "street_number",
			"route"							=> "street_name",
			"neighborhood"					=> "neighborhood",
			"locality"						=> "city",
			"administrative_area_level_1"	=> "state",
			"administrative_area_level_2"	=> "county",
			"country"						=> "country",
			"postal_code"					=> "zip"
		);
		
		function install(){
			return array(
				'key' => ''
			);
		}
		
		function display_global_settings(){
			$key = $this->settings['key'];
			$out = form_label("Google API Key", "key");
			$out .= form_input("key", $key);
			return $out;
		}
	
		function save_global_settings(){
			return array_merge($this->settings, $_POST);
		}
		
		function display_settings($data){
			if($data['field_id']){
				ee()->table->add_row(
					"Update Past Entries?",
					form_dropdown('update_entries', array('no' => "No", 'yes' => "Yes"))
				);
			}
		}
		
		function save_settings($data){
			$update = ee()->input->post('update_entries');
			$field_id = "field_id_".$data['field_id'];
			$field_id_copy = $field_id; //"field_id_2"; USE THIS TO COPY OTHER FIELD DATA WITHOUT OVERWRITTING
			
			if($update == "yes" && $field_id){
				$query = ee()->db->where($field_id_copy." IS NOT NULL", null, false)->from("channel_data")->get();
				foreach($query->result_array() as $row){
					$entry_id = $row['entry_id'];
					$old = $row[$field_id_copy];
					
					if(base64_encode(base64_decode($old, true)) !== $old){
						//address is just saved as a string
						$data = $this->_get_address_data($old);
					} else {
						//update from previous version
						$data = unserialize(base64_decode($old));
						$data = $this->_get_address_data($data['address']);
					}
					$data = base64_encode(serialize($data));
					ee()->db->where("entry_id", $entry_id)->update("channel_data", array($field_id => $data));
				}
			}
			
		    return array();
		}
		
		function display_field($data){
			$data = ($data ? unserialize(base64_decode($data)) : $this->geo_fields);
			
			$name = 'field_id_'.$this->field_id;
			$value = $data['address'];
			
			return form_input(array(
				'id' 	=>	$name,
				'name' 	=> 	$name,
				'value'	=>	$value
			));
		}
		
		
		function save($address){
			//$data = ($data != '' ? unserialize(base64_decode($data)) : $this->geo_fields);
			$data = $this->_get_address_data($address);
			return base64_encode(serialize($data));
		}
		
		function _get_address_data($address){
			$data = $this->geo_fields;
			$data['address'] = $address;
			
			
			$gm_address = str_replace(' ','+',$address);
		    $geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$gm_address.'&sensor=false');
		    $output = json_decode($geocode);
		    
		    if($output->status != "OK"){
			    return $data;
		    }
		    
		    $result = $output->results[0];
		    foreach($result->address_components as $component){
			    $value = $component->long_name;
			    foreach($component->types as $type){
				    if( isset($this->address_components[$type]) ){
					    $data[$this->address_components[$type]] = $value;
				    }	
			    }
		    }
		     
		    $data['formatted'] = $result->formatted_address;  
		    $data['latitude'] = $result->geometry->location->lat;
		    $data['longitude'] = $result->geometry->location->lng;
		    
		    return $data;
		}
		
		
		function replace_tag($data, $params = array(), $tagdata = FALSE){
			$data = unserialize(base64_decode($data));
			return ee()->TMPL->parse_variables_row($tagdata, $data);
		}
		
		/*
		function display_cell( $data ){
	    	$data = ($data ? unserialize(base64_decode($data)) : $this->geo_fields);
			
			$name = 'field_id_'.$this->field_id;
			$value = $data['address'];
			
			return form_input(array(
				'id' 	=>	$name,
				'name' 	=> 	$name,
				'value'	=>	$value
			));
	    }
	    
	    function save_cell($address){
		    $data = $this->_get_address_data($address);
			return base64_encode(serialize($data));
	    }
	    */
	}