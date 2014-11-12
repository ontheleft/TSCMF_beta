<?php
/*//=================================
//
//	Curl 操作类 [更新时间: 2014-8-4]
//
//===================================*/
/*

发送post数据:
	
	$curl = new Curl();
	
	//设置参数[可选]:
	$parameters['browser'] = 'ie8'; //设置浏览器类型(PC浏览器: ie6,ie8,firefox(默认), 手机浏览器：iphone,android,nokia)
	$parameters['ajax'] = true; //是否模拟ajax方式提交(默认true)
	$parameters['followLocation'] = false; //是否将服务器服务器返回的"Location:"放在header中递归的返回给服务器(默认false)
	$parameters['cookieFile'] = 'cookie.txt';//指定cookie文件(如果没有特别的指定则默认会自动生成cookie文件)
	$curl->set($parameters);
	
	//设置http请求头(必须是数组类型)如果遇到一些奇怪的http请求头参数,可以在这里手动设置
	//返回成功设置的信息个数, 如:
	$httpheader = array(
						'X-MicrosoftAjax'=>'Delta=true',
						'Content-Type'=>'application/x-www-form-urlencoded; charset=UTF-8',
					);
	$num = $curl->setHeader($httpheader);
	
	//设置cookie[可选]:
	$curl->setCookie($key,$cookies);
	
	//post提交,取得返回内容数组
	$arr = $curl->post('url', post数据数组);
	
	//get提交,取得返回内容数组
	$arr = $curl->get('url');
	
	//save抓取,抓取文件并保存到本地
	$bool = $curl->save('url','filepath');
	
	//取得存放cookie的文件及路径
	$path = $curl->getCookieFile();
	
	//查看cookie
	echo $curl->printCookie();
	
	//清除cookie
	$bool = $curl->clearCookie();
	
['info']	
	* "url" //资源网络地址
    * "content_type" //内容编码
    * "http_code" //HTTP状态码
    * "header_size" //header的大小
    * "request_size" //请求的大小
    * "filetime" //文件创建时间
    * "ssl_verify_result" //SSL验证结果
    * "redirect_count" //跳转技术  
    * "total_time" //总耗时
    * "namelookup_time" //DNS查询耗时
    * "connect_time" //等待连接耗时
    * "pretransfer_time" //传输前准备耗时
    * "size_upload" //上传数据的大小
    * "size_download" //下载数据的大小
    * "speed_download" //下载速度
    * "speed_upload" //上传速度
    * "download_content_length"//下载内容的长度
    * "upload_content_length" //上传内容的长度  
    * "starttransfer_time" //开始传输的时间
    * "redirect_time"//重定向耗时
*/

class Curl
{
	private $curl; //CURL句柄
	private $cookiefile=''; //临时存放cookie的文件
	private $postData = array(); //post的数据
	private $file = ''; //抓取文件将保存的本地路径
	private $HttpHeaders = array(); //如果遇到一些奇怪的http请求头参数,可以在这里手动设置
	
	//所有的浏览器类型
	private $browser = array(
					//PC浏览器
					'ie6'=>'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)',
					'ie8'=>'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; CIBA)',
					'firefox'=>'Mozilla/5.0 (Windows NT 5.1; rv:13.0) Gecko/20100101 Firefox/13.0.1',
					//手机浏览器
					'iphone'=>'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16',
					'android'=>'Mozilla/5.0 (Linux; U; Android 2.2; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
					'nokia'=>'Mozilla/5.0 (SymbianOS/9.4; Series60/5.0 NokiaN97-1/20.0.019; Profile/MIDP-2.1 Configuration/CLDC-1.1) AppleWebKit/525 (KHTML, like Gecko) BrowserNG/7.1.18124',
				);
	private $Parameters = array(
					'browser'=> 'firefox', //默认的模拟浏览器类型
					'ajax'=> true, //默认使用模拟ajax方式提交
					'followLocation'=>false, //是否将服务器服务器返回的"Location:"放在header中递归的返回给服务器
				);
	
	//构造化函数
	function __construct($parameters=array())
	{
		if(!empty($parameters)){
			$this->set($parameters); //设置参数
		}
	}
	
	//析构函数
	function __destruct()
	{	}
	
	/*
	//设置参数:
		$parameters['browser'] //设置浏览器类型(ie6,ie8,firefox)
		$parameters['ajax'] //是否模拟ajax方式提交(true,false)
		$parameters['cookieFile'] //指定cookie文件(如果没有特别的指定则默认会自动生成cookie文件)
		$parameters['followLocation'] //是否将服务器服务器返回的"Location:"放在header中递归的返回给服务器
	*/
	public function set($parameters=array())
	{
		//设置浏览器类型
		if(isset($parameters['browser'])){
			$browser = trim($parameters['browser']);
			if(in_array($browser,array_keys($this->browser))){
                $this->Parameters['browser'] = $browser;
            }
		}
		//是否将服务器服务器返回的"Location:"放在header中递归的返回给服务器
		if(isset($parameters['followLocation'])){
			$this->Parameters['followLocation'] = (bool)$parameters['followLocation'];
		}else{
			$this->Parameters['followLocation'] = false;
		}
		//指定cookie文件
		if(isset($parameters['cookieFile']) && trim($parameters['cookieFile'])!=''){
			$this->cookiefile = $parameters['cookieFile'];
		}
		$this->getCookieFile(); //初始化cookie存放的文件
		
		//设置是否模拟ajax方式提交
		if(isset($parameters['ajax'])){
			$this->Parameters['ajax'] = (bool)$parameters['ajax'];
		}else{
			$this->Parameters['ajax'] = true;
		}
	}
	
	//设置http请求头,返回成功设置的信息个数
	//参数： array $headers [$key]=>$val
	public function setHeader($Headers=array())
	{
		if(!is_array($Headers)){
			return false;
		}
		$i = 0;
		foreach($Headers as $key=>$val){
			$this->HttpHeaders[$key] = $val;
			$i++;
		}
		return $i;
	}
	
	//清空已经设置的请求头
	public function clearHeader()
	{
		$this->HttpHeaders = array();
	}
	
	//post数据到指定的url, 取得url页面输出的内容。
	//参数: $url url地址, $data 要post提交的数据数组
	/*例: $data = array(
					//post数据:
					"post变量名称1" => "值1",
					"post变量名称2" => "值2",
					//上传文件:
					"upload" => "@C:/wamp/www/test.zip", //要上传的本地文件地址
				)
	*/
	public function post($url='',$data=array())
	{
		//网址
		$url = trim($url);
		if($url==''){ return NULL; }
		//post提交的数据
		if(is_array($data) && count($data)){
			foreach($data as $key=>$value){ 
				$this->postData[$key] = $value;
			}
		}else{
			$this->postData = $data;
		}
		//post方式
		$redata = $this->play($url,'post');
		//清除数据
		$this->postData = array();
		//返回数据
		return $redata;
	}
	
	//get方式, 取得url页面输出的内容。
	//参数: $url url地址
	public function get($url='')
	{
		//网址
		$url = trim($url);
		if(empty($url)){
			return NULL;
		}
		return $this->play($url,'get'); //get方式
	}

	//save方式,抓取文件并保存到指定路径
	//参数: $url url地址, $file 要保存的文件路径
	public function save($url,$file)
	{
		$file = trim($file);
		if(empty($file)){
			return false;
		}
		$this->file = $file; //设置文件路径
		return $this->play($url,'save'); //保存文件
	}

	//执行curl
	private function play($url,$type='get')
	{
		$url = str_replace('&amp;','&',$url);
		$this->curl = curl_init(); //初始化CURL句柄
		//设置参数
		//==================
		curl_setopt($this->curl, CURLOPT_URL, $url); //设置请求的URL
		//设为TRUE把curl_exec()结果转化为字串，而不是直接输出
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, true);
		//HTTP请求User-Agent:头
		curl_setopt($this->curl, CURLOPT_USERAGENT, $this->browser[$this->Parameters['browser']]);
		
		//post方式
		if(trim($type)=='post'){
			curl_setopt($this->curl, CURLOPT_POST, 1);//启用POST提交
			curl_setopt($this->curl, CURLOPT_POSTFIELDS,self::toHttpData($this->postData)); //设置POST提交的字符串
		}else{
			curl_setopt($this->curl, CURLOPT_HTTPGET, 1);//启用GET提交
		}
		
		//设置HTTP请求头(默认值)
		$httpheader = array(
			'Accept-Language'=>'zh-cn,zh;q=0.5',
			'Connection'=>'keep-alive',
			'Cache-Control'=>'no-cache',
		);
		//默认设置上一次的网址做为来源网址
		!empty($this->Parameters['Referer']) && $httpheader['Referer'] = $this->Parameters['Referer'];
		//是否模拟ajax方式提交
		$this->getP('ajax') && $httpheader['X-Requested-With'] = 'XMLHttpRequest';
		//组合http请求头数组
		foreach($this->HttpHeaders as $key=>$val){
			$httpheader[trim($key)] = trim($val);
		}
		//生成curl可以使用的http请求头数组
		$HttpHeaders = array();
		foreach($httpheader as $key=>$val){
			$HttpHeaders[] = "$key: $val";
		}
		curl_setopt($this->curl,CURLOPT_HTTPHEADER,$HttpHeaders);//设置HTTP头信息
		
		//设定为不验证证书和HOST
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		//设置文件cookie
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->getCookieFile());
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->getCookieFile());
		
		//是否开启follow location
		if($this->getP('followLocation')){
			curl_setopt($this->curl, CURLOPT_AUTOREFERER, 1); //当根据Location:重定向时，自动设置header中的Referer:信息
			curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1); //启用时会将服务器服务器返回的"Location:"放在header中递归的返回给服务器
			curl_setopt($this->curl, CURLOPT_MAXREDIRS, 10); //允许的最大转向数,在设置了CURLOPT_FOLLOWLOCATION才有效
		}
		
		//其它设置
		if($type!='save'){
			curl_setopt($this->curl, CURLOPT_HEADER,1);//设为TRUE在输出中包含头信息
		}
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 600); //设置连接超时的秒数
		//curl_setopt($this->curl, CURLOPT_ENCODING, ''); //header中“Accept-Encoding: ”部分的内容，支持的编码格式为："identity"，"deflate"，"gzip"。如果设置为空字符串，则表示支持所有的编码格式
		//curl_setopt($this->curl, CURLOPT_FAILONERROR, 1); // 启用时显示HTTP状态码，默认行为是忽略编号小于等于400的HTTP信息
		//curl_setopt($this->curl, CURLOPT_PORT, 80); //设置端口
		
		//保存文件
		if($type=='save' && $this->file!=''){
			$fp = fopen($this->file, 'w');//输出文件
			curl_setopt($this->curl, CURLOPT_FILE, $fp);//设置输出文件的位置，值是一个资源类型，默认为STDOUT (浏览器)。
			$re = curl_exec($this->curl); //执行预定义的CURL
			fclose($fp);
			return $re;
		}

		//执行
		//==================
		//得到请求信息
		$Request['url'] = $url;
		$Request['header'] = implode("\r\n",$HttpHeaders);
		trim($type)=='post' && $Request['postData'] = $this->postData;
		
		//得到响应信息
		$http = curl_exec($this->curl); //执行预定义的CURL
		$headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE); //取得响应头大小
		$Response['info'] = curl_getinfo($this->curl); //得到响应信息的特性
		$Response['header'] = substr($http, 0, $headerSize);//得到响应头
		$Response['content'] = substr($http, $headerSize);//得到响应正文
		$errorNo = curl_errno($this->curl); //错误编号
		$errorMsg = curl_error($this->curl); //错误信息
		curl_close($this->curl); //释放curl句柄
		
		//返回值
		//=================
		$revalue = array(
			'httpcode' => $Response['info']['http_code'],
			'errorNo' => $errorNo,
			'errorMsg' => $errorMsg,
			'cookiefile'=> $this->getCookieFile(),
			//'cookie'=> $this->getHttpCookie($Response['header']), //取得页头中的cookie
			'Location'=>$this->getHttpLocation($Response['header']), //将要跳转的网址
			'Request' => $Request,
			'Response'=>$Response,
			'cookieList'=>$this->printCookie(), //取得cookie文件中的内容
		);
		
		$this->Parameters['Referer'] = $url; //记录来源网址
		return $revalue;
	}
	
	//取得存放cookie的文件及路径
	public function getCookieFile()
	{
		if(trim($this->cookiefile)==''){
			$this->cookiefile = tempnam('./temp','CexClass_Cookie');
		}
		return $this->cookiefile;
	}
	
	//清除cookie (删除cookie文件)
	public function clearCookie()
	{
		return is_file($this->getCookieFile()) && unlink($this->getCookieFile());
	}
	
	//取得cookie值
	public function getCookie($key='')
	{
        $cookies = array();

		//解释cookie文件
		$lines = file($this->getCookieFile());
		if(!is_array($lines)){
			return array();
		}
		foreach($lines as $line){
			$line = trim($line);
			if(!empty($line) && '#'!==$line{0}){
				$r = explode("\t",$line);
				if(isset($r[5])){
					$cookies[trim($r[5])] = array_map('trim',array(
						'domain'=>$r[0],
						'flag'=>$r[1],
						'path'=>$r[2],
						'secure'=>$r[3],
						'expires'=>$r[4],
						'name'=>$r[5],
						'value'=>$r[6],
					));
				}
			}
		}
		//返回cookie数据(数组)
		if(empty($key)){
			return $cookies;
		}else{
			return is_array($cookies[$key])?$cookies[$key]:array();
		}
	}
	
	//设置cookie
	public function setCookie($key,$cookie=array())
	{
		$key = trim($key);
		if(empty($key) || !is_array($cookie) || empty($cookie)){
			return false;
		}
		//处理数据
		if(empty($cookie['domain']) || empty($cookie['name']) || empty($cookie['value'])){
			return false;
		}
		empty($cookie['flag']) && $cookie['flag'] = 'FALSE';
		empty($cookie['path']) && $cookie['path'] = '/';
		empty($cookie['secure']) && $cookie['secure'] = 'FALSE';
		empty($cookie['expires']) && $cookie['expires'] = '0';
		
		//读取cookie文件
		$lines = file($this->getCookieFile());
		if(!is_array($lines)){
			return false;
		}
		$lines = array_map('trim',$lines);
		//1.如果存在，则删除所在指定的行
		foreach($lines as $k=>$line){
			$r = explode("\t",$line);
			if(trim($r[5])==$key){
				unset($lines[$k]);
			}
		}
		//2.尾部加入要设置的cookie
        !isset($cookieText) && $cookieText='';
		$lines[] = $cookieText.implode("\t",array(
			'domain'=>$cookie['domain'],
			'flag'=>$cookie['flag'],
			'path'=>$cookie['path'],
			'secure'=>$cookie['secure'],
			'expires'=>$cookie['expires'],
			'name'=>$cookie['name'],
			'value'=>$cookie['value'],
		));
		
		return file_put_contents($this->getCookieFile(),implode("\r\n",$lines));
	}
	
	//删除指定的cookie
	//设置cookie
	public function delCookie($key)
	{
		$key = trim($key);
		if(empty($key)){
			return false;
		}
		//读取cookie文件
		$lines = file($this->getCookieFile());
		if(!is_array($lines)){
			return false;
		}
		$lines = array('trim',$lines);
		//如果存在，则删除所在指定的行
		foreach($lines as $k=>$line){
			$r = explode("\t",$line);
			if(trim($r[5])==$key){
				unset($lines[$k]);
			}
		}
		
		return file_put_contents($this->getCookieFile(),implode("\r\n",$lines));
	}
	
	//输出查看cookie文件里的内容
	//主要用于调试
	public function printCookie()
	{
		return file_get_contents($this->getCookieFile());
	}

	//根据名称取得参数值
	//参数: $name 参数名称
	private function getP($name='')
	{
		$name = trim($name);
		if($name==''){
			return $this->Parameters;
		}else{
			return $this->Parameters[$name];
		}
	}
	
	
	//取得http头用的Location跳转网址
	private function getHttpLocation($http='')
	{
		if($http==''){ return '';}
		
		$goUrl = array();
		preg_match_all('/Location: [^\n]*\n/i',$http."\n",$goUrl);
		if(count($goUrl[0])){
			$goUrl = trim(str_replace('Location:','',$goUrl[0][0]));
		}else{
			$goUrl = '';
		}
		return $goUrl;
	}
	
	//支持二级数组,用于curl的数据在进行POST提交之前先进行的转换
	public static function toHttpData($data)
	{
		$data = json_decode(json_encode($data),1);
		if(!is_array($data)){
			return trim($data);
		}
		//如果包含了文件则直接返回此数组数据
		foreach($data as $v){
			if(!is_array($v) && strlen($v) &&$v{0}=='@'){
				return $data;
			}
		}
		//数据中未包含文件，则对数据进行URL-encoded处理，以便可以支持二维数组
		$d = '';
		foreach($data as $key=>&$item){
			if(!is_array($item)){
				$d .= '&'.urlencode($key).'='.urlencode($item);
			}else{
				foreach($item as $k2=>$v2){
					if(!is_array($v2)){
						if(trim((int)$k2)==trim($k2)){
							$d .= '&'.urlencode($key).'='.urlencode($v2);
						}else{
							$d .= '&'.urlencode($key.'['.$k2.']').'='.urlencode($v2);
						}
					}
				}
			}
		}
		return trim($d,'&');
	}
	
	//返回的信息
	public static function ErrMsg($msgcode)
	{
		//[Informational 1xx]
		$httpCode['0']='Unable to access';
		$httpCode['100']='Continue';
		$httpCode['101']='Switching Protocols';

		//[Successful 2xx]
		$httpCode['200']='OK';
		$httpCode['201']='Created';
		$httpCode['202']='Accepted';
		$httpCode['203']='Non-Authoritative Information';
		$httpCode['204']='No Content';
		$httpCode['205']='Reset Content';
		$httpCode['206']='Partial Content';

		//[Redirection 3xx]
		$httpCode['300']='Multiple Choices';
		$httpCode['301']='Moved Permanently';
		$httpCode['302']='Found';
		$httpCode['303']='See Other';
		$httpCode['304']='Not Modified';
		$httpCode['305']='Use Proxy';
		$httpCode['306']='(Unused)';
		$httpCode['307']='Temporary Redirect';

		//[Client Error 4xx]
		$httpCode['400']='Bad Request';
		$httpCode['401']='Unauthorized';
		$httpCode['402']='Payment Required';
		$httpCode['403']='Forbidden';
		$httpCode['404']='Not Found';
		$httpCode['405']='Method Not Allowed';
		$httpCode['406']='Not Acceptable';
		$httpCode['407']='Proxy Authentication Required';
		$httpCode['408']='Request Timeout';
		$httpCode['409']='Conflict';
		$httpCode['410']='Gone';
		$httpCode['411']='Length Required';
		$httpCode['412']='Precondition Failed';
		$httpCode['413']='Request Entity Too Large';
		$httpCode['414']='Request-URI Too Long';
		$httpCode['415']='Unsupported Media Type';
		$httpCode['416']='Requested Range Not Satisfiable';
		$httpCode['417']='Expectation Failed';

		//[Server Error 5xx]
		$httpCode['500']='Internal Server Error';
		$httpCode['501']='Not Implemented';
		$httpCode['502']='Bad Gateway';

		$httpCode['503']='Service Unavailable';
		$httpCode['504']='Gateway Timeout';
		$httpCode['505']='HTTP Version Not Supported';

		return $httpCode[$msgcode];
	}
	
}

/*
附录: [curl_setopt 函数设置项明细]

常用设置选项布尔值选项
CURLOPT_AUTOREFERER：当根据Location:重定向时，自动设置header中的Referer:信息
CURLOPT_BINARYTRANSFER：在启用CURLOPT_RETURNTRANSFER时候将获取数据返回
CURLOPT_COOKIESESSION：标志为新的cookie会话，忽略之前设置的cookie会话
CURLOPT_CRLF：将Unix系统的换行符转换为Dos换行符
CURLOPT_DNS_USE_GLOBAL_CACHE：使用全局的DNS缓存
CURLOPT_FAILONERROR：忽略返回错误
CURLOPT_FILETIME：获取请求文档的修改日期，该日期可以用curl_getinfo()获取。
CURLOPT_FOLLOWLOCATION：紧随服务器返回的所有重定向信息
CURLOPT_FORBID_REUSE：当进程处理完毕后强制关闭会话，不再缓存供重用
CURLOPT_FRESH_CONNECT：强制建立一个新的会话，而不是重用缓存的会话
CURLOPT_HEADER：在返回的输出中包含响应头信息
CURLOPT_HTTPGET：设置HTTP请求方式为GET
CURLOPT_HTTPPROXYTUNNEL：经由一个HTTP代理建立连接
CURLOPT_NOBODY：返回的输出中不包含文档信息.
CURLOPT_NOPROGRESS：禁止进程级别传输，PHP自动设为真
CURLOPT_NOSIGNAL：忽略所有发往PHP的信息
CURLOPT_POST：设置POST方式提交数据，POST格式为application/x-www-form-urlencoded
CURLOPT_PUTTRUE：设置PUT方式上传文件，同时设置CURLOPT_INFILE和CURLOPT_INFILESIZE
CURLOPT_RETURNTRANSFER：返回字符串，而不是调用curl_exec()后直接输出
CURLOPT_SSL_VERIFYPEER：SSL验证开启
CURLOPT_UNRESTRICTED_AUTH：一直链接后面附加用户名和密码，同时设置CURLOPT_FOLLOWLOCATION
CURLOPT_UPLOAD：准备上传

整数值选项
CURLOPT_BUFFERSIZE：缓存大小
CURLOPT_CONNECTTIMEOUT：连接时间设置，默认0为无限制
CURLOPT_DNS_CACHE_TIMEOUT：内存中保存DNS信息的时间，默认2分钟
CURLOPT_INFILESIZE：上传至远程站点的文件尺寸
CURLOPT_LOW_SPEED_LIMIT：传输最低速度限制andabort.
CURLOPT_LOW_SPEED_TIME：传输时间限制
CURLOPT_MAXCONNECTS：最大持久连接数
CURLOPT_MAXREDIRS：最大转向数
CURLOPT_PORT：连接端口
CURLOPT_PROXYAUTH：代理服务器验证方式
CURLOPT_PROXYPORT：代理服务器端口
CURLOPT_PROXYTYPE：代理服务器类型
CURLOPT_TIMEOUT：CURL函数的最大执行时间

字符串选项
CURLOPT_COOKIE：HTTP头中set-cookie中的cookie信息
CURLOPT_COOKIEFILE：包含cookie信息的文件，cookie文件的格式可以是Netscape格式,或者只是HTTP头的格式
CURLOPT_COOKIEJAR：连接结束后保存cookie信息的文件
CURLOPT_CUSTOMREQUEST：自定义请求头，使用相对地址
CURLOPT_ENCODING：HTTP请求头中Accept-Encoding的值
CURLOPT_POSTFIELDS：POST格式提交的数据内容
CURLOPT_PROXY：代理通道
CURLOPT_PROXYUSERPWD：代理认证用户名和密码
CURLOPT_RANGE：返回数据的范围,以字节记
CURLOPT_REFERER：前向链接
CURLOPT_URL：要连接的URL地址，可以在curl_init()中设置
CURLOPT_USERAGENT：HTTP头中User-Agent的值
CURLOPT_USERPWD：连接种使用的验证信息

数组选项
CURLOPT_HTTP200ALIASES：200响应码数组，数组中的响应吗被认为是正确的响应
CURLOPT_HTTPHEADER：自定义请求头信息

只能是流句柄的选项：
CURLOPT_FILE：传输要写入的晚间句柄，默认是标准输出
CURLOPT_INFILE：传输要读取的文件句柄
CURLOPT_STDERR：作为标准错误输出的一个替换选项
CURLOPT_WRITEHEADER：传输头信息要写入的文件

回调函数选项
CURLOPT_HEADERFUNCTION：拥有两个参数的回调函数，第一个是参数是会话句柄，第二是HTTP响应头信息的字符串。使用此回调函数，将自行处理响应头信息。响应头信息按行返回。设置返回值为字符串长度。
CURLOPT_READFUNCTION：拥有两个参数的回调函数，第一个是参数是会话句柄，第二是HTTP响应头信息的字符串。使用此函数，将自行处理返回的数据。返回值为数据尺寸。
CURLOPT_WRITEFUNCTION：拥有两个参数的回调函数，第一个是参数是会话句柄，第二是HTTP响应头信息的字符串。使用此回调函数，将自行处理响应头信息。响应头信息是整个字符串。设置返回值为字符串长度。

*/
?>