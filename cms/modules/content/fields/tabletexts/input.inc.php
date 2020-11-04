	function tabletexts($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$columns = explode("\n",$this->fields[$field]['column']);
	 
		$array = array();
		if(!empty($_POST[$field.'_1'])) {
			foreach($_POST[$field.'_1'] as $key=>$val) {
				for ($x=1; $x<=count($columns); $x++) {
					$array[$key][$field.'_'.$x] = $_POST[$field.'_'.$x][$key];
				}
			}
		}
		$array = array2string($array);
		return $array;
	}