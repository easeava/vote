<?php
// +----------------------------------------------------------------------
// | To Young [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://tys.pub All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: to-young <tthd@163.com>
// +----------------------------------------------------------------------

php_sapi_name() !== 'cli' && exit('run cli');

class Vote
{
    protected $id;
    protected $vote_url;
    protected $token_url;
    protected $cookie_dir = 'cookie/';
    protected $cookie_path;
    protected $token;
    protected $header;
    protected $ip;

    public function __construct($id, $vote_url = 'http://www.jinruijiang.com/vote', $token_url = 'http://www.jinruijiang.com/project/')
    {
        $this->id           = $id;
        $this->vote_url     = $vote_url;
        $this->token_url    = $token_url . $this->id;
        $this->cookie_path  = __DIR__ .'/'. $this->cookie_dir . $this->id;
        $this->ip();
        $this->header       = [
            "CLIENT-IP:{$this->ip}",
            "X-FORWARDED-FOR:{$this->ip}",
        ];
        $this->token();
    }

    public function start()
    {
        $data = [
            'qiye_id'   =>  $this->id,
            'type'      =>  1,
            'token'     =>  $this->token
        ];
        $this->header[] = 'X-Requested-With: XMLHttpRequest';
        $vote = $this->curl($data);
        $result = json_decode($vote, true);
        $result['status'] == '1' ? $this->log() : '';
        @unlink($this->cookie_path);
    }

    protected function ip()
    {
        $ip_long = [
            ['607649792', '608174079'], //36.56.0.0-36.63.255.255
            ['1038614528', '1039007743'], //61.232.0.0-61.237.255.255
            ['1783627776', '1784676351'], //106.80.0.0-106.95.255.255
            ['2035023872', '2035154943'], //121.76.0.0-121.77.255.255
            ['2078801920', '2079064063'], //123.232.0.0-123.235.255.255
            ['-1950089216', '-1948778497'], //139.196.0.0-139.215.255.255
            ['-1425539072', '-1425014785'], //171.8.0.0-171.15.255.255
            ['-1236271104', '-1235419137'], //182.80.0.0-182.92.255.255
            ['-770113536', '-768606209'], //210.25.0.0-210.47.255.255
            ['-569376768', '-564133889'], //222.16.0.0-222.95.255.255
        ];
        $rand_key = mt_rand(0, 9);
        $this->ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
    }

    protected function token()
    {
        $content = $this->curl();
        preg_match_all('/token:\'(.*)\'/', $content, $match);
        $this->token = $match[1][0];
    }

    protected function log()
    {
        $file = dirname(__FILE__) . "/log/{$this->id}.txt";
        $num = 1;
        if (file_exists($file)) {
            $num = intval(file_get_contents($file));
            $num++;
        }
        file_put_contents($file, $num);
    }

    protected function characet($data)
    {
    	if (! empty ( $data )) {
    		$fileType = mb_detect_encoding ( $data, array (
    				'UTF-8',
    				'GBK',
    				'GB2312',
    				'LATIN1',
    				'BIG5'
    		) );
    		if ($fileType != 'UTF-8') {
    			$data = mb_convert_encoding ( $data, 'UTF-8', $fileType );
    		}
    	}
    	return $data;
    }

    protected function curl(array $data = [])
    {
        $ch = curl_init();
        $url = empty($data) ? $this->token_url : $this->vote_url ;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_path); //读取cookie
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_path);
        }

        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        return $rs;
    }
}

$id = $argv[1];
$num = $argv[2];

for ($i=0; $i < $num; $i++) {
    $vote = new Vote($id);
    $vote->start();
    unset($vote);
}
