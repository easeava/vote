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
header("Content-type:text/html;charset=utf-8");

function curl()
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://www.jinruijiang.com/2016');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $rs = curl_exec($ch); //执行cURL抓取页面内容
    curl_close($ch);
    return $rs;
}

$content = curl();

preg_match_all('/<p class="tl">票数：(.*)<\/p>/i', $content, $match);
preg_match_all('/<h4><a href="\/project\/(\d+)" title="(.*)" /i', $content, $matchs);
$num = $match[1];
$title = $matchs[2];
$data = array_combine($num, $title);
krsort($data);
$i = 0;
foreach ($data as $key => $value) {
    $i++;
    echo $i.'----'.$value.'----'.$key."\n";
}
