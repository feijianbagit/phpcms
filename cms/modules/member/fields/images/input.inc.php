	function images($field, $value) {
		$pictures_id = $this->input->post($field.'_id');
		$pictures_title = $this->input->post($field.'_title');
		$pictures_description = $this->input->post($field.'_description');
		$array = $temp = array();
		if(!empty($pictures_id)) {
			foreach($pictures_id as $key=>$pic) {
				$temp['id'] = $pictures_id[$key];
				$temp['title'] = str_replace(array('"',"'"),'`',$pictures_title[$key]);
				$temp['description'] = str_replace(array('"',"'"),'`',$pictures_description[$key]);
				$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
