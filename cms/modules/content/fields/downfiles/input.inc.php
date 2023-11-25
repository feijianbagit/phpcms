	function downfiles($field, $value) {
		$files_id = $this->input->post($field.'_id');
		$files_title = $this->input->post($field.'_title');
		$files_description = $this->input->post($field.'_description');
		$array = $temp = array();
		if(!empty($files_id)) {
			foreach($files_id as $key=>$file) {
					$temp['id'] = $files_id[$key];
					$temp['title'] = $files_title[$key];
					$temp['description'] = $files_description[$key];
					$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
