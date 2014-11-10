<?php

/**
 * Created by PhpStorm.
 * User: imboswell
 * Date: 14-4-10
 * Time: 下午7:48
 */
class Utils
{
	const URL_BASIC_DATA_API = 'http://api.basicdata.baicheng.com/api/';

	public static function is_from_internal()
	{
		$ip = self::get_real_ip();
		if ($ip === '0.0.0.0') {
			return false;
		}

        if($ip=='106.2.184.106'||$ip=='106.120.110.226')
        {
            return true;
        }
        if(substr($ip,0,8)=='42.62.69')
        {
            $ipLats = end(explode('.',$ip));
            if(intval($ipLats)>63 && intval($ipLats)<70)
            {
                return true;
            }
        }
		if (substr($ip, 0, 7) == '192.168') {
			return true;
		}

		return false;
	}

	/**
	 * @return string 返回请求的真实ip
	 */
	public static function get_real_ip()
	{
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');

		} elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		$pat = '/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/';
		if (preg_match($pat, $ip)) {
			return $ip;
		}

		return '0.0.0.0';
	}

	public static function request_api($method, $params)
	{
		// 从 cache 取
		$redis = Redis::connection();
		$key   = '1012_cache_' . md5($method . json_encode($params));
		if (($val = $redis->get($key)) !== null) {
			$val_response = json_decode($val, true);
			if ($val_response['code'] === 100000) {
				return $val_response['data'];
			}
		}

		// 从接口取
		$params   = urlencode(json_encode($params));
		$url      = self::URL_BASIC_DATA_API . $method . '?params=' . $params;
		$response = file_get_contents($url);
		if (!$response) {
			return false;
		}
		$result = json_decode($response, true);
		if ($result['code'] !== 100000) {
			return false;
		}

		// 更新 cache
		$redis->set($key, $response);
		$redis->expire($key, 800000);

		return $result['data'];
	}

	public static function generate_trade_no($mobile)
	{
		$num = crc32($mobile);

		return date('YmdHis') . '-' . $num . '-' . self::generate_nonce();
	}

	public static function generate_nonce()
	{
		return rand(100000, 999999);
	}

	public static function build_passport_request($api, $data)
	{
		$api         = 'http://apippt.baicheng.com' . $api;
		$request_arr = $data;
		$request     = $api . '?data=' . urlencode(json_encode($request_arr));

		return $request;
	}

	public static function build_pay($order)
	{
		$order_info   = $order->order_info;
		$out_trade_no = $order->trade_no;
		$subject      = $order_info['name'];
		$body         = $order_info['description'];
		$total_fee    = $order->total_fee;

		return array(
			'out_trade_no' => $out_trade_no,
			'subject'      => $subject,
			'body'         => $body,
			'total_fee'    => $total_fee,
		);
	}

	public static function NumToStr($num)
	{
		if (stripos($num, 'e') === false) return $num;
		$num    = trim(preg_replace('/[=\'"]/', '', $num, 1), '"'); //出现科学计数法，还原成字符串
		$result = "";
		while ($num > 0) {
			$v      = $num - floor($num / 10) * 10;
			$num    = floor($num / 10);
			$result = $v . $result;
		}

		return $result;
	}

	public static function debug($debug_info, $debug_level = 0)
	{
		echo '<pre>';
		if (is_array($debug_info)) {
			var_dump($debug_info);
		} else {
			echo $debug_info;
		}
		echo '</pre>';
		die();
	}

	/**
	 * @param $request_url
	 * @param array $request_params
	 * @param $method
	 * @return mixed|string
	 */
	public static function http_request($request_url, $request_params = array(), $method = 'POST')
	{
		// 初始化一个 cURL 对象
		$ch = curl_init();
		// 设置你需要抓取的URL
		curl_setopt($ch, CURLOPT_URL, $request_url);
		// 设置header
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//设置为POST方式
		if ($method === 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
		}
		//POST数据
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
		// 运行cURL，请求网页
		$response = curl_exec($ch);
		//出错则显示错误信息
		if (curl_errno($ch)) {
			return curl_error($ch);
		}
		// 关闭URL请求
		curl_close($ch);

		// 显示获得的数据
		return $response;
	}

	/**
	 * 生成 trace_id
	 * @return string
	 */
	public static function gen_trace_id()
	{
		return rand(111111, 999999) . (microtime(true) * 10000);
	}

	/**
	 * 向名字服务发起请求
	 * @param string $sub_system 子系统名称 参考 http://192.168.3.172/up/document/wikis/ApiErrorCode
	 * @param string $method 方法
	 * @param array $param_data 数据
	 * @return bool|mixed
	 */
	public static function ns_issue($sub_system, $method, array $param_data)
	{
		try {
			$trace_id = Utils::gen_trace_id();
			$from     = ServiceInjection::system('Passport');
			if (($to = ServiceInjection::system($sub_system)) === false) {
				throw new Exception('无系统', '1111111111');
			}
			$params = array(
				'params' => json_encode(array(
					'trace_id' => $trace_id,
					'data'     => $param_data
				))
			);

			$result = ServiceInjection::send($trace_id, $from, $to, $method, $params);

			return $result;
		} catch (Exception $ex) {
			return false;
		}
	}

	/*
	 *   格式化返回数据    使之适应bootstrap分页
	 */
	public static function wrapTableData($current_page, $total_page, $total_records, $key_name, $arrResults)
	{

		$arrRet = array(
			'page'    => $current_page,
			'total'   => $total_page,
			'records' => $total_records,
			'rows'    => array(),
		);

		foreach ($arrResults as $key => $value) {
			$tmp              = array(
				'id'   => $value[$key_name],
				'cell' => $value,
			);
			$arrRet['rows'][] = $tmp;
		}

		return json_encode($arrRet);
	}

	// 调用命名服务接口
	public static function callApi($api_provider, $method, $params)
	{

		// 特卖系统在名字服务中注册的关键字为Operations
		$from_id  = ServiceInjection::system('Operations');
		$to_id    = ServiceInjection::system($api_provider);
		$trace_id = "${from_id}_${to_id}_${method}";

		$result = ServiceInjection::send($trace_id, $from_id, $to_id, $method, $params);

		return $result;
	}

	// 统一返回
	public static function wrapReturn($code = 100000, $message = '', $data = array())
	{

		return json_encode(array(
			'code'    => $code,
			'message' => $message,
			'data'    => $data,
		));
	}

	/**
	 * @todo 获取大洲数据
	 * @author Justin.bj@msn.com
	 * @version $id
	 * @return array
	 *
	 */
	public static function getContinents()
	{
		$api_prefix = Config::get('custom.geography_api');
		$api        = $api_prefix . '/api/continents';
		$rs         = file_get_contents($api);
		$arr        = json_decode($rs, TRUE);
		if ($arr['code'] == Result::SUCCESS) {
			$tmp = array_pop($arr);

			return array_pop($tmp);
		} else {
			return array();
		}
	}

	/**
	 * @todo 获取国家数据
	 * @author Justin.bj@msn.com
	 * @version $id
	 * @return array
	 *
	 */
	public static function getCountries($continent_id)
	{
		$params     = '{"data":{"continent_id":"' . $continent_id . '"}}';
		$api_prefix = Config::get('custom.geography_api');
		$api        = $api_prefix . '/api/countries?params=' . $params;
		$rs         = file_get_contents($api);
		$arr        = json_decode($rs, TRUE);
		if ($arr['code'] == Result::SUCCESS) {
			$tmp = array_pop($arr);

			return array_pop($tmp);
		} else {
			return array();
		}
	}

	/**
	 * @todo 获取国外城市数据
	 * @author Justin.bj@msn.com
	 * @version $id
	 * @return array
	 *
	 */
	public static function getCitiesOut($country_code)
	{
		$params     = '{"data":{"country_code":"' . $country_code . '"}}';
		$api_prefix = Config::get('custom.geography_api');
		$api        = $api_prefix . '/api/cities_out?params=' . $params;
		$rs         = file_get_contents($api);
		$arr        = json_decode($rs, TRUE);
		if ($arr['code'] == Result::SUCCESS) {
			$tmp = array_pop($arr);

			return array_pop($tmp);
		} else {
			return array();
		}
	}

	public static function cacheWrap($key, callable $getData, $minutes = 10)
	{
		$enable_debug = Config::get('cache')['enable_debug'];

		if (Cache::has($key)) {
			if ($enable_debug) {
				Log::debug('cache hit(' . $key . ')' . ' ' . microtime(true));
			}

			$data = Cache::get($key);
		} else {
			if ($enable_debug) {
				Log::debug('cache miss(' . $key . ')' . ' ' . microtime(true));
			}

			$data = $getData();
			Cache::add($key, $data, $minutes);
		}
		if ($enable_debug) {
			Log::debug('cache finish(' . $key . ')' . ' ' . microtime(true));
		}

		return $data;
	}

    /**
     * @todo 发送邮件
     *
     * @param $params
     * $params = array(
     *           'recipients' => $this->email_arr,
     *           'subject'    => $time_str.'渠道订单数据统计',
     *           'data'       => $content,
     *           'path'       => $path,
    );
     * @return bool
     */
    public static function  sendMail($params){
        if(!is_array($params)||count($params)<3)
        {
            return FALSE;
        }
        Log::warning('send mail data:'.var_export($params,true));
        $rs = Mail::send('email.statistics', $params, function($message) use ($params)
        {
            $recipients = $params['recipients'];
            $message->to($recipients)->subject($params['subtitle']);
            if(isset($params['path'])&&$params['path'])
            {
                $message->attach($params['path']);
            }
        });
        return TRUE;
    }
    public static function getUtmSource()
    {
        return (isset($_COOKIE['Byecity_utm_source']))?$_COOKIE['Byecity_utm_source']:'';
    }

    /**
     * @todo 生成支付链接
     * @param $uid
     * @param $tradeID
     * @return string
     */
    public static function getPayUrl ($uid, $tradeID) {
        if(!$uid||!$tradeID)
        {
            return '###';
        }
        $orderInfo = BossService::GetTradeInfo($tradeID);
        $data = array(
            'user_id'               => $uid,
            'source'                => Config::get('service.single_pay_source'),
            'out_order_id'          => $tradeID,
            'sync_url'              => Config::get('service.single_sync_url')."/$tradeID",  // 同步回调地址
            'merchant_url'          => '', // 无线端, 此处无用
            'trade_ip'              => self::get_real_ip(),
            'verify_url'            => Config::get('service.single_verify_url')."/$tradeID",  // 获取支付验证信息地址
            'order_url'             => Config::get('service.single_order_url') . "/$tradeID",  // 订单详情页
            'is_merge_trade'        => Config::get('service.single_is_merge_trade'),  // 合并交易
            'sign_type'             => Config::get('service.single_secret_method'),
        );

        $num = 0;
        // 商品
        foreach ($orderInfo['products'] as $product) {
           $num = $num + intval($product['product_num']);
        }
        $data['merge_info'][] = array(
            'agent_id'         => Config::get('service.singel_pay_agent_id'),
            'async_url'        => Config::get('service.single_async_url'),
            'attach'           => '',
            'sub_out_order_id' => $tradeID,
            'subject'          => $product['product_name'],
            'body'             => '订单号: '.$tradeID.'    数量:' .$num.'张' ,//$product['product_name'],
        );
        Log::info('message',$data);
        // 签名
        $data['sign'] = \BC\Tools\PaySign::sign($data, Config::get('service.single_pay_key'));
        $params = array(
            'trace_id'  => time(),
            'data'      => $data,
        );
        $str = Config::get('service.single_pay_url') . "?params=" . urlencode(json_encode($params));
        return $str;
    }

    /**
     * @todo 用户预注册
     * @author Justin.W
     * @param string $mobile 手机号
     * @return mixed
     */
    public static function preSignUp($mobile)
    {
        $uid = '0';
        if (!$mobile)
        {
            return $uid;
        }
        $params['trace_id'] = self::gen_trace_id();
        $params['data']['cond']['username'] = $mobile;
        $params['data']['cond']['presignup_from'] = '5';
        $preURL = SINGLE_PRE_Sign_UP_URL.'?params='.json_encode($params);
        $rs = self::http_request($preURL);
        $rs = json_decode($rs,true);
        if($rs['code']=='100000') $uid = $rs['data']['uid'];
        //var_dump($rs);
        return $uid;
    }

    public static function sendDFS($tradeID)
    {
        if(!$tradeID) return false;
        $tradeInfo = BossService::GetTradeInfo($tradeID);
        $param = [];
        $paramStr = '';
        if(is_array($tradeInfo)&&count($tradeInfo))
        {
            $countDFS = 0;
            if(array_key_exists('products',$tradeInfo))
            {
                $products = $tradeInfo['products'];
                foreach($products as $key => $val)
                {
                    $param['coutry'] = $val['countryname'];
                    $paramStr .= "country=".$val['countryname'];
                    $param['city']  = '';
                    $paramStr .= "&city=";
                    $countDFS = $countDFS + intval($val['product_num']);
                    $param['pid'] = $val['product_id'];
                    $paramStr .= "&pid=".$val['product_id'];
                    $param['pname'] = $val['product_name'];
                    $paramStr .= "&pname=" .$val['product_name'];
                }
            }
            $param['source'] = SINGLE_ORDER_TYPE;
            $paramStr .= "&source=" . SINGLE_ORDER_TYPE;
            $param['mobile'] = $tradeInfo['contact_mobile'];
            $paramStr .= "&mobile=" . $tradeInfo['contact_mobile'];
            $param['key']    = strtoupper(md5('taobao'.$tradeInfo['contact_mobile']));
            $paramStr .= "&key=" . strtoupper(md5('taobao'.$tradeInfo['contact_mobile']));
            $param['mid']    = $tradeID;
            $paramStr .= "&mid=" . $tradeID;
            $param['count']  = $param['cnumber'] = $countDFS;
            $paramStr .= "&count=" . $countDFS;
            $paramStr .= "&cnumber=" . $countDFS;

        }

        $single_dfs_url = Config::get('service.single_dfs_url')."?".$paramStr;
        $rss = file_get_contents($single_dfs_url);
        return $rss;
    }


}
