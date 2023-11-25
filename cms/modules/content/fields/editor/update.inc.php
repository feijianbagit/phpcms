function editor($field, $value) {
	$attachment_db = pc_base::load_model('attachment_model');
	$attachment_db->api_update($GLOBALS['downloadfiles'],'c-'.$this->data['catid'].'-'.$this->id,1);
	$value = str_replace(' style=""', '', $value);
	return html2code($value);
}