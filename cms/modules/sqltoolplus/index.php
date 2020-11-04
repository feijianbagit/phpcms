<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_sys_class('model', '', 0);
class index extends admin {
	public function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('module_model');
	}
	
	public function init() {
		$plugin_menus = array();
		$info = $this->db->get_one(array('module'=>'sqltoolplus'));
		extract($info);
		$plugin_menus[] =array('name'=>$info['name'],'url'=>'init','status'=>'1');
		$meun_total = count($plugin_menus);;
		$setting = string2array($info['setting']);
		if(is_array($setting)) {
			foreach($setting as $m) {
				$plugin_menus[] = array('name'=>$m['name'],'extend'=>1,'url'=>$m['url']);
				$mods[] = $m['url'];
			}
		}
		include $this->admin_tpl('plugin_setting');
	}

	public function sqlquery() {
		$plugin_menus = array();
		$info = $this->db->get_one(array('module'=>'sqltoolplus'));
		extract($info);
		$plugin_menus[] =array('name'=>$info['name'],'url'=>'init','status'=>'1');
		$meun_total = count($plugin_menus);;
		$setting = string2array($info['setting']);
		if(is_array($setting)) {
			foreach($setting as $m) {
				$plugin_menus[] = array('name'=>$m['name'],'extend'=>1,'url'=>$m['url']);
				$mods[] = $m['url'];
			}
		}
		$database = pc_base :: load_config('database');
		//echo '<pre>';var_dump(get_class_methods('mysql'));
		if (isset($_POST['pluginsubmit'])) {
			$pdo_name = $_POST['pdo_select'];
			$this -> db_charset = $database[$pdo_name]['charset'];
			$this -> db_tablepre = $database[$pdo_name]['tablepre'];
			$this -> db = db_factory :: get_instance($database) -> get_database($pdo_name);
			$sqls = new_stripslashes($_POST['sqls']);
			$sqls = trim($sqls);
			if ($sqls == '') {
				showmessage(L('sql_empty'), HTTP_REFERER);
			} 
			$handle = $this -> _sql_execute($sqls);
			if ($handle) {
				if(preg_match('/^SELECT /i',$sqls)){
					echo L('sql_successfully').'<br /><pre>',var_export($this -> db -> fetch_next(),true),'</pre><br /><a href="',HTTP_REFERER,'">'.L('return').'</a>';
					$this -> db -> lastqueryid = null;
					exit;
				}
				showmessage(L('sql_success'), HTTP_REFERER);
			} else {
				showmessage(L('sql_failure'), HTTP_REFERER);
			} 
		} else {
			foreach($database as $name => $value) {
				$pdos[$name] = $value['database'] . '[' . $value['hostname'] . ']';
			} 
			include $this->admin_tpl('sqlquery_admin');
		} 
	} 

	public function sqlreplace() {
		$plugin_menus = array();
		$info = $this->db->get_one(array('module'=>'sqltoolplus'));
		extract($info);
		$plugin_menus[] =array('name'=>$info['name'],'url'=>'init','status'=>'1');
		$meun_total = count($plugin_menus);;
		$setting = string2array($info['setting']);
		if(is_array($setting)) {
			foreach($setting as $m) {
				$plugin_menus[] = array('name'=>$m['name'],'extend'=>1,'url'=>$m['url']);
				$mods[] = $m['url'];
			}
		}
		$database = pc_base :: load_config('database');
		if (isset($_POST['pluginsubmit'])) {
			$pdo_name = $_POST['pdo_select'];
			$this -> db_charset = $database[$pdo_name]['charset'];
			$this -> db_tablepre = $database[$pdo_name]['tablepre'];
			$this -> db = db_factory :: get_instance($database) -> get_database($pdo_name);
			if (!strlen($_POST['search_rule'])) {
				showmessage(L('select_where'), HTTP_REFERER);
			} 
			if (!$_POST['db_table'] || !preg_match('/^[\w]+$/', $_POST['db_table'])) {
				showmessage(L('select_table'), HTTP_REFERER);
			} 
			if (!$_POST['db_field'] || !preg_match('/^[\w]+$/', $_POST['db_field'])) {
				showmessage(L('select_field'), HTTP_REFERER);
			} 
			if ($_POST['sql_where']) {
				$_sql = ' AND ' . new_stripslashes($_POST['sql_where']);
			} else {
				$_sql = '';
			} 
			if ($_POST['replace_type'] == 2) {
				$sql = "UPDATE `{$_POST['db_table']}` SET `{$_POST['db_field']}`=REPLACE(`{$_POST['db_field']}`,'{$_POST['search_rule']}','{$_POST['replace_data']}') WHERE `{$_POST['db_field']}` LIKE '%{$_POST['search_rule']}%'$_sql;";

				$handle = $this -> _sql_execute($sql);
				if ($handle) {
					showmessage(L('replace_success'), HTTP_REFERER);
				} else {
					showmessage(L('replace_failure'), HTTP_REFERER);
				} 
			} else {
				if (!$_POST['db_pr_field'] || !preg_match('/^[\w]+$/', $_POST['db_pr_field'])) {
					showmessage(L('select_pr_field'), HTTP_REFERER);
				} 
				$ck_pr = $this -> db -> get_primary($_POST['db_table']);
				if ($ck_pr && $ck_pr != $_POST['db_pr_field']) {
					showmessage(L('select_is_fieldfield') . $ck_pr . L('pleasereselect'), HTTP_REFERER);
				} 
				if ($_POST['replace_type'] == 1) {
					$search_rule = str_replace(array('\\\\%', '\\\\_'), array('\\%', '\\_'),$_POST['search_rule']);
					$sql = "LIKE '{$search_rule}'";
				} else {
					$search_rule = str_replace(array('\\', '\'', '.*?', '.+?'), array('\\\\', '\\\'', '.*', '.+'), $_POST['search_rule']);
					$sql = "REGEXP '{$search_rule}'";
				} 
				$sql = "SELECT `{$_POST['db_pr_field']}`,`{$_POST['db_field']}` FROM `{$_POST['db_table']}` WHERE `{$_POST['db_field']}` {$sql}$_sql";
				$handle = $this -> db -> query($sql);
				$success = $failse = 0;
				if ($handle) {
					$search_rule = str_replace('/', '\\/', new_stripslashes($_POST['search_rule']));
					if ($_POST['replace_type'] == 1) {
						$search_rule = str_replace(array('\\%', '\\_'), array('[(P)]', '[(U)]'), $search_rule);
						$search_rule = preg_quote($search_rule);
						$search_rule = str_replace(array('%', '_'), array('(.*?)', '(.)'), $search_rule);
						$search_rule = str_replace(array('\\[\\(P\\)\\]', '\\[\\(U\\)\\]'), array('%', '_'), $search_rule);
					} 
					$_POST['replace_data']=new_stripslashes($_POST['replace_data']);
					while ($r = $this -> db -> fetch_next()) {
						$preg = preg_replace('/' . $search_rule . '/i', $_POST['replace_data'], $r[$_POST['db_field']]);
						//var_dump(preg_match('/' . $search_rule . '/i',$r[$_POST['db_field']]));exit;
						$preg = new_addslashes($preg);
						$id = $r[$_POST['db_pr_field']];
						$sql = "UPDATE `{$_POST['db_table']}` SET `{$_POST['db_field']}`='$preg' WHERE `{$_POST['db_pr_field']}`='$id'$_sql;";
						if ($this -> _sql_execute($sql)) {
							$success++;
						} else {
							$failse++;
						} 
						$this -> db -> lastqueryid = $handle;
					} 
				} 
				showmessage(L('replacefinished') . $success . L('replace_successmonths') . $failse . L('months'), HTTP_REFERER);
			} 
		} else {
			foreach($database as $name => $value) {
				$pdos[$name] = $value['database'] . '[' . $value['hostname'] . ']';
			} 
			include $this->admin_tpl('sqlreplace_admin');
		} 
	} 

	public function dbtpatition() {
		$plugin_menus = array();
		$info = $this->db->get_one(array('module'=>'sqltoolplus'));
		extract($info);
		$plugin_menus[] =array('name'=>$info['name'],'url'=>'init','status'=>'1');
		$meun_total = count($plugin_menus);;
		$setting = string2array($info['setting']);
		if(is_array($setting)) {
			foreach($setting as $m) {
				$plugin_menus[] = array('name'=>$m['name'],'extend'=>1,'url'=>$m['url']);
				$mods[] = $m['url'];
			}
		}
		//$database = pc_base :: load_config('database');
		$model = getcache('model','commons');//var_dump($model);exit;
		if (isset($_POST['pluginsubmit'])) {
			if(isset($_POST['modelid'])){
				$_POST['modelid']=intval($_POST['modelid']);
				$_POST['dbtp_range']=intval($_POST['dbtp_range']);
				$_POST['dbtp_num']=intval($_POST['dbtp_num']);
				if(!isset($model[$_POST['modelid']]))showmessage(L('modelnotexist'), HTTP_REFERER);
				$m	=&	$model[$_POST['modelid']];
				$db =	new sqltoolplus($m['tablename']);
				
				$sql="SHOW VARIABLES LIKE '%partition%';";
				$handle = $db -> query($sql);
				$enabled = false;
				if($handle){
					while($r=$db -> fetch_next()){
						if($r['Variable_name']=='have_partitioning' && $r['Value']=='YES'){
							$enabled = true;
							break;
						}
					}
				}
				if(!$enabled)showmessage(L('unfortunately'), HTTP_REFERER);
				if(!$db->is_patitioned()){
					if($_POST['dbtp_num']<1){
						$r	=	$db -> get_one('', 'MAX(`id`) AS max');
						$end=	ceil($r['max']/$_POST['dbtp_range']);
					}else{//$end=5;$_POST['dbtp_range']=30;
						$end= $_POST['dbtp_num'];
					}
					$sql="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}` PARTITION BY RANGE (`id`)(";
					$sql2="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}_data` PARTITION BY RANGE (`id`)(";
					$_sql='PARTITION p0 VALUES LESS THAN ('.($_POST['dbtp_range']).'),';
					for($i=1;$i<=$end;$i++) {
						$_sql.='PARTITION p'.$i.' VALUES LESS THAN ('.($_POST['dbtp_range']*($i+1)).'),';
					}
					$_sql.='PARTITION pmax VALUES LESS THAN MAXVALUE';
					$_sql.=');';
				}else{
					$pt=$db -> get_patition_info();
					//$pt["part"]="p0"  $pt["expr"]="`id`" $pt["descr"]="100000" $pt["table_rows"]="0"
					$number = preg_replace('|^p|i','',end(explode(',',str_replace(',pmax','',$pt['partitions']))));
					$db -> query("EXPLAIN PARTITIONS SELECT * FROM `{$db->db_tablepre}{$m['tablename']}` WHERE `id`=(SELECT MAX(`id`) FROM `{$db->db_tablepre}{$m['tablename']}` LIMIT 1)");
					$r	=	$db->fetch_next();
					//var_dump($r);var_dump($pt['partitions']);exit($number);
					
					if($r['partitions']!='pmax'){
						showmessage(L('createanew'), HTTP_REFERER,6000);
					}

					$sql="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}` REORGANIZE PARTITION pmax INTO (";
					$sql2="ALTER TABLE `{$db->db_tablepre}{$m['tablename']}_data` REORGANIZE PARTITION pmax INTO (";
					$_sql='';
					$start=$number+1;
					$end=$start+$_POST['dbtp_num'];
					for($i=$start;$i<$end;$i++) {
						$_sql.='PARTITION p'.$i.' VALUES LESS THAN ('.($pt['descr']*($i+1)).'),';
					}
					$_sql.="PARTITION pmax VALUES LESS THAN MAXVALUE";
					$_sql.=");";
				}
				//exit;
				if($db -> query($sql.$_sql) && $db -> query($sql2.$_sql)){
					showmessage(L('success'), HTTP_REFERER);
				}else{
					showmessage(L('failure'), HTTP_REFERER);
				}
			}
		} else {
			$model_array=array();
			foreach($model as $_m) {
				$db =	new sqltoolplus($_m['tablename']);
				$model_array[$_m['modelid']] = $_m['name'] . '(' . $_m['tablename'] . ')'.($db->is_patitioned()&&($pt=$db->get_patition_info())?'已有分区:每个分区'.$pt['descr'].'条记录':'');
			}
			include $this->admin_tpl('dbtpatition_admin');
		} 
	}
	/**
	 * 执行SQL
	 * 
	 * @param string $sql 要执行的sql语句
	 */
	private function _sql_execute($sql) {
		$sqls = $this -> _sql_split($sql);
		if (is_array($sqls)) {
			foreach($sqls as $sql) {
				if (trim($sql) != '') {
					$handle = $this -> db -> query($sql);
					if (!$handle) return false;
				} 
			} 
		} else {
			$handle = $this -> db -> query($sqls);
		} 
		return $handle ? true : false ;
	} 

	/**
	 * 分割SQL语句
	 * 
	 * @param string $sql 要执行的sql语句
	 */
	private function _sql_split($sql) {
		$database = pc_base :: load_config('database');
		$db_charset = $database['default']['charset'];
		if ($this -> db -> version() > '4.1' && $db_charset) {
			$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=" . $db_charset, $sql);
		} 
		$sql = str_replace("\r", "\n", $sql);
		$ret = array();
		$num = 0;
		$queriesarray = explode(";\n", trim($sql));
		unset($sql);
		foreach($queriesarray as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			$queries = array_filter($queries);
			foreach($queries as $query) {
				$str1 = substr($query, 0, 1);
				if ($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
			} 
			$num++;
		} 
		return($ret);
	} 

	public function ajax_get_dbtable() {
		$name = preg_replace('/[\W]+/', '', $_GET['name']);
		if (empty($name)) $name = 'MM_LOCALHOST';
		$dbsrc = getcache('dbsource', 'commons');
		pc_base :: load_model('get_model', 0);
		$table_list = array();
		if (!empty($name) && $name != 'MM_LOCALHOST' && isset($dbsrc[$name])) {
			$get_db = new get_model($dbsrc, $name);
			$s = $get_db -> list_tables();
			foreach ($s as $key => $val) {
				$table_list[$val]['tablename'] = $val;
			} 
		} elseif ($name == 'MM_LOCALHOST') {
			$get_db = new get_model();
			$r = $get_db -> list_tables();
			foreach ($r as $key => $val) {
				$table_list[$val]['tablename'] = $val;
			} 
		} 
		$results = json_encode($table_list);
		if ($_GET['callback']) {
			echo $_GET['callback'], '(', $results, ')';
		} else {
			echo $results;
		} 
	} 

	public function ajax_get_fields() {
		$name = preg_replace('/[\W]+/', '', $_GET['name']);
		$table = preg_replace('/[\W]+/', '', $_GET['tables']);
		if (empty($name)) $name = 'MM_LOCALHOST';
		$dbsrc = getcache('dbsource', 'commons');
		pc_base :: load_model('get_model', 0);
		$fields = array();
		if (!empty($name) && $name != 'MM_LOCALHOST' && isset($dbsrc[$name])) {
			$get_db = new get_model($dbsrc, $name);
			$get_db -> sql_query('SHOW COLUMNS FROM `' . $table . '`');
			while ($d = $get_db -> fetch_next()) {
				$d['field'] = $d['Field'];
				$d['Type'] = preg_split('/[\s()]+/', $d['Type']);
				$d['type'] = $d['Type'][0];
				$fields[$d['Field']] = $d;
			} 
		} elseif ($name == 'MM_LOCALHOST') {
			$get_db = new get_model();
			$get_db -> sql_query('SHOW COLUMNS FROM ' . $table);
			while ($d = $get_db -> fetch_next()) {
				$d['field'] = $d['Field'];
				$d['Type'] = preg_split('/[\s()]+/', $d['Type']);
				$d['type'] = $d['Type'][0];
				$fields[$d['Field']] = $d;
			} 
		} 
		$results = json_encode($fields);
		if ($_GET['callback']) {
			echo $_GET['callback'], '(', $results, ')';
		} else {
			echo $results;
		} 
	} 
}

class sqltoolplus extends model {
	public $table_name = '';
	public function __construct($table) {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = $table;
		parent::__construct();
	}

	function get_patition_info($table=''){
		$table=!$table?$this->table_name:$this->db_tablepre.$table;
		$sql='SELECT partition_name part,partition_expression expr,partition_description descr,table_rows FROM INFORMATION_SCHEMA.partitions WHERE TABLE_SCHEMA=schema()  AND TABLE_NAME=\''.$table.'\'';
		$this->query($sql);
		$P=$this->fetch_next();
		$sql='EXPLAIN PARTITIONS SELECT * FROM `'.$table.'`;';
		$this->query($sql);
		$P=@array_merge($P,$this->fetch_next());
		return $P;
	}

	function get_table_status($table='',$db=''){
		$table=!$table?$this->table_name:$this->db_tablepre.$table;
		$sql='SHOW TABLE STATUS';
		if($db)$sql.=' FROM `'.$db.'`';
		$sql.=' LIKE \''.$table.'\'';
		$handle=$this->query($sql);
		return $handle ? $this->fetch_next() : false;
	}

	function is_patitioned($table='',$db='') {
		$return = $this->get_table_status($table,$db);
		return $return && $return['Create_options']=='partitioned';
	}

	public function fetch_next() {
		return $this->db->fetch_next();
	}
}
?>