<?php
defined('IN_CMS') or exit('No permission resources.');

error_reporting(E_ERROR);
/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-7-18
 * Time: 上午11: 32
 * UEditor编辑器通用上传类
 */
class Uploader
{
    private $fileField; //文件域名
    private $file; //文件上传对象
    private $base64; //文件上传对象
    private $config; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_SYSTEM_TYPE_NOT_ALLOWED" => "文件类型被系统禁止上传",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确",
        "INVALID_URL" => "非法 URL",
        "INVALID_IP" => "非法 IP"
    );

    /**
     * 构造函数
     * @param string $fileField 表单名称
     * @param array $config 配置项
     * @param bool $base64 是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     */
    public function __construct($fileField, $config, $type = "upload")
    {
        // 禁止以下文件上传
        $this->notallowed = array('php', 'php3', 'php4', 'asp', 'jsp', 'aspx', 'exe', 'sh', 'phtml', 'dll', 'cer', 'asa', 'shtml', 'shtm', 'asax', 'cgi', 'fcgi', 'pl');
        $this->fileField = $fileField;
        $this->config = $config;
        $this->type = $type;
        $this->siteid = intval($this->config['siteid'])== 0 ? 1 : intval($this->config['siteid']);
        $this->module = $this->config['module'] ? $this->config['module'] : 'content';
        $this->catid = $this->config['catid'];
        $this->userid = $this->config['userid'];
        $this->is_wm = $this->config['is_wm'];
        $this->image_reduce = $this->config['image_reduce'];
        
        if (!$this->userid) {
            // 错误提示
            $this->stateInfo = '请登录在操作';
            return;
        }
        
        if ($type == "remote") {
            $this->saveRemote();
        } else if($type == "base64") {
            $this->upBase64();
        } else {
            $this->upFile();
        }

        $this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = iconv('unicode', 'utf-8', $this->stateMap['ERROR_TYPE_NOT_ALLOWED']);
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    private function upFile()
    {
        $file = $this->file = $_FILES[$this->fileField];
        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return;
        }
        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            return;
        } else if (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");
            return;
        } else if (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMPFILE");
            return;
        }

        $this->oriName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }

        // 安全检测
        $rt = $this->_safe_check(trim($this->getFileExt(), '.'), file_get_contents($file["tmp_name"]), 1);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }

        //移动文件
        if (!(move_uploaded_file($file["tmp_name"], $this->filePath) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_MOVE");
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
            pc_base::load_sys_class('image');
            $image = new image();
            // 图片处理
            if (is_image($this->filePath)) {
                // 图片压缩处理
                if ($this->image_reduce) {
                    $image->reduce($this->filePath, $this->image_reduce);
                }
                if($this->siteid) {
                    $siteinfo = getcache('sitelist', 'commons');
                    $config = string2array($siteinfo[$this->siteid]['setting']);
                    $watermark_enable = $config['watermark_enable'];
                    $watermark = $config['ueditor'] ? 1 : intval($this->is_wm);
                }
                // 强制水印
                if ($watermark_enable && $watermark) {
                    $config['source_image'] = $this->filePath;
                    $config['dynamic_output'] = false;
                    $image->watermark($config);
                }
            }
        }
        // 存储附件
        $this->save_attach();
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    private function upBase64()
    {
        $base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);

        $this->oriName = $this->config['oriName'];
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        // 安全检测
        $rt = $this->_safe_check(trim($this->getFileExt(), '.'), $img, 1);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
            pc_base::load_sys_class('image');
            $image = new image();
            // 图片处理
            if (is_image($this->filePath)) {
                // 图片压缩处理
                if ($this->image_reduce) {
                    $image->reduce($this->filePath, $this->image_reduce);
                }
                if($this->siteid) {
                    $siteinfo = getcache('sitelist', 'commons');
                    $config = string2array($siteinfo[$this->siteid]['setting']);
                    $watermark_enable = $config['watermark_enable'];
                    $watermark = $config['ueditor'] ? 1 : intval($this->is_wm);
                }
                // 强制水印
                if ($watermark_enable && $watermark) {
                    $config['source_image'] = $this->filePath;
                    $config['dynamic_output'] = false;
                    $image->watermark($config);
                }
            }
        }
        // 存储附件
        $this->save_attach();

    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    private function saveRemote()
    {
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace("&amp;", "&", $imgUrl);

        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return;
        }
        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo("INVALID_URL");
            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo("INVALID_IP");
            return;
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], "image")) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);

        $this->oriName = $m ? $m[1]:"";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }

        // 安全检测
        $rt = $this->_safe_check(trim($this->getFileExt(), '.'), $img, 1);
        if (!$rt['code']) {
            $this->stateInfo = $rt['msg'];
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo("ERROR_CREATE_DIR");
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo("ERROR_DIR_NOT_WRITEABLE");
            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
            pc_base::load_sys_class('image');
            $image = new image();
            // 图片处理
            if (is_image($this->filePath)) {
                // 图片压缩处理
                if ($this->image_reduce) {
                    $image->reduce($this->filePath, $this->image_reduce);
                }
                if($this->siteid) {
                    $siteinfo = getcache('sitelist', 'commons');
                    $config = string2array($siteinfo[$this->siteid]['setting']);
                    $watermark_enable = $config['watermark_enable'];
                    $watermark = $config['ueditor'] ? 1 : intval($this->is_wm);
                }
                // 强制水印
                if ($watermark_enable && $watermark) {
                    $config['source_image'] = $this->filePath;
                    $config['dynamic_output'] = false;
                    $image->watermark($config);
                }
            }
        }
        // 存储附件
        $this->save_attach();

    }

    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getFullName()
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date("Y-y-m-d-H-i-s"));
        $format = $this->config["pathFormat"];
        $format = str_replace("{yyyy}", $d[0], $format);
        $format = str_replace("{yy}", $d[1], $format);
        $format = str_replace("{mm}", $d[2], $format);
        $format = str_replace("{dd}", $d[3], $format);
        $format = str_replace("{hh}", $d[4], $format);
        $format = str_replace("{ii}", $d[5], $format);
        $format = str_replace("{ss}", $d[6], $format);
        $format = str_replace("{time}", $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName, 0, strrpos($this->oriName, '.'));
        $oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
        $format = str_replace("{filename}", $oriName, $format);

        //替换随机字符串
        $randNum = substr(md5(SYS_TIME.$oriName), rand(0, 20), 15); // 随机新名字
        if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
            $format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
        }

        if($this->fileType){
            $ext = $this->fileType;
        } else {
            $ext = $this->getFileExt();
        }
        return $format . $ext;
    }

    /**
     * 获取文件名
     * @return string
     */
    private function getFileName () {
        return substr($this->filePath, strrpos($this->filePath, '/') + 1);
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    private function getFilePath()
    {
        $fullname = $this->fullName;
        $rootPath = $_SERVER['DOCUMENT_ROOT'];

        if (substr($fullname, 0, 1) != '/') {
            $fullname = '/' . $fullname;
        }

        return $rootPath . $fullname;
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config["allowFiles"]);
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function  checkSize()
    {
        return $this->fileSize <= ($this->config["maxSize"]);
    }

    // 安全验证
    public function _safe_check($file_ext, $data, $is_ext = 1) {

        // 检查系统保留文件格式
        if ($is_ext) {
            if (in_array($file_ext, $this->notallowed)) {
                return dr_return_data(0, $this->getStateInfo("ERROR_SYSTEM_TYPE_NOT_ALLOWED"));
            } elseif (!$file_ext) {
                return dr_return_data(0, L('无法读取文件扩展名'));
            }
        }

        // 验证扩展名格式
        if (!preg_match('/^[a-z0-9]+$/i', $file_ext)) {
            return dr_return_data(0, L('此文件扩展名'.$file_ext.'不安全，禁止上传'));
        }

        // 验证伪装图片
        if (in_array($file_ext, array('jpg', 'jpeg', 'png', 'gif'))) {
            $data = strtolower($data);
            if (strlen($data) < 100) {
                return dr_return_data(0, L('图片文件不规范'));
            } elseif (strpos($data, '<?php') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传'));
            } elseif (strpos($data, 'eval(') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传'));
            } elseif (strpos($data, '.php') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传'));
            } elseif (strpos($data, 'base64_decode(') !== false) {
                return dr_return_data(0, L('此图片不安全，禁止上传'));
            }
        }

        return dr_return_data(1, 'ok');
    }

    /**
     * 存储归档
     * @return bool
     */
    private function save_attach()
    {
        $this->att_db = pc_base::load_model('attachment_model');
        $uploadedfile['module'] = $this->module;
        $uploadedfile['catid'] = $this->catid;
        $uploadedfile['siteid'] = $this->siteid;
        $uploadedfile['userid'] = $this->userid;
        $uploadedfile['uploadtime'] = SYS_TIME;
        $uploadedfile['uploadip'] = ip();
        $uploadedfile['status'] = pc_base::load_config('system','attachment_stat') ? 0 : 1;
        $uploadedfile['authcode'] = md5(str_replace(WEB_PATH.'uploadfile/','',$this->fullName));
        $uploadedfile['filename'] = $this->oriName;
        $uploadedfile['filepath'] = str_replace(WEB_PATH.'uploadfile/','',$this->fullName);
        $uploadedfile['filesize'] = $this->fileSize;
        $uploadedfile['fileext'] = str_replace('.','',$this->fileType);
        $uploadedfile['isimage'] = in_array(str_replace('.','',$this->fileType), array('gif', 'jpg', 'jpeg', 'png', 'bmp')) ? 1 : 0;
        $this->att_db->insert($uploadedfile);
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            "state" => $this->stateInfo,
            "url" => $this->fullName,
            "title" => $this->fileName,
            "original" => $this->oriName,
            "type" => $this->fileType,
            "size" => $this->fileSize
        );
    }

}