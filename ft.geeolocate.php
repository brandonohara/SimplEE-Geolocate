<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Geeolocate_ft extends EE_Fieldtype {
	
		var $info = array(
			'name' => 'GEEolocate',
			'version' => '1.0.0'
		);
		
		var $geo_fields = array(
			'address' => '',
			'latitude' => 0.0, 
			'longitude' => 0.0
		);
		
		var $has_array_data = TRUE;
		
		function _include_head(){
			$themes = defined('URL_THIRD_THEMES') ? URL_THIRD_THEMES : get_instance()->config->slash_item('theme_folder_url').'third_party/';
			$themes = $themes.'geeolocate/';
			
			ee()->cp->add_to_head('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>');
			ee()->cp->add_to_head('<link type="text/css" rel="stylesheet" href="'.$themes.'css/cp.css" media="screen" />');
			ee()->cp->add_to_head('<script type="text/javascript" src="'.$themes.'javascript/cp.js"></script>');
		}
		
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
		
		function display_field($data){
			$out = '';
			$key = $this->settings['key'];
			$data = ($data ? unserialize(base64_decode($data)) : $this->geo_fields);
			$this->_include_head();
			
	
			foreach($data as $key => $value){
				$name = $key.'_field_id_'.$this->field_id;
				
				$out .= "<div class='geeolocate_field' ".($key != "address" ? "style='display: none;'" : "").">";
				//$out .= form_label(ucfirst($key), $name);
				$out .= form_input(array(
					'id' 	=>	$name,
					'name' 	=> 	$name,
					'value'	=>	$value
				));
				$out .= "</div>";
			}
			
			$out .= "<div class='geeolocate_field'>";
			$out .= "<a onclick='GEEolocate(".$this->field_id.")' class='geeolocate_button'>Geolocate</a>";
			$out .= "</div>";
			$out .= "<div style='clear: both;'></div>";
			$out .= "<p class='geeolocate_message' id='message_field_id_".$this->field_id."'></p>";
			
			return $out;
		}
		
		function save($data){
			$data = array();
			foreach($this->geo_fields as $key => $value){
				$data[$key] = ee()->input->post($key."_field_id_".$this->field_id);
			}
			return base64_encode(serialize($data));
		}
		
		
		function replace_tag($data, $params = array(), $tagdata = FALSE){
			$data = unserialize(base64_decode($data));
			
			if($tagdata){
				return ee()->TMPL->parse_variables_row($tagdata, $data);
			} else
				return "Address: ".$data['address']." (".$data['longitude'].", ".$data['longitude'].")";
		}
		
		
		
		/* ========================================================
		=========================   MATRIX   ======================
		
		function randomString($length = 10) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, strlen($characters) - 1)];
		    }
		    return $randomString;
		}
		
		function display_cell( $data ){
	    	$out = '';
			$key = $this->settings['key'];
			$data = ($data ? unserialize(base64_decode($data)) : $this->geo_fields);
			$this->_include_head();
			
			foreach($data as $key => $value){
				$name = $key.'_field_id_'.$this->field_id;
				
				$out .= "<div class='geolocatee_field' ".($key != "address" ? "style='display: none;'" : "").">";
				//$out .= form_label(ucfirst($key), $name);
				$out .= form_input(array(
					'id' 	=>	$name,
					'name' 	=> 	$name,
					'value'	=>	$value
				));
				$out .= "</div>";
			}
			
			$out .= "<div class='geolocatee_field'>";
			$out .= "<a onclick='GeolocatEEMatrix($(this).parent())' class='geolocatee_button'>Geolocate</a>";
			$out .= "</div>";
			$out .= "<div style='clear: both;'></div>";
			$out .= form_hidden($this->field_name, implode("|", $data))
			$out .= "<p class='geolocatee_message' id='message_field_id_".$unique."'>".implode("|", $data)."</p>";
			
			return $out;
	    }
	    
	    function save_cell($data){
		    $data = explode("|", $data);
			return base64_encode(serialize($data));
	    }
		=========================================================== */
	}