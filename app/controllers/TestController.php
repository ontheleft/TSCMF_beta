<?php
/**
 * Created by PhpStorm.
 * User: Ming
 * Date: 2014/11/20
 * Time: 17:53
 */

class TestController extends BaseController {

    public function anyIndex()
    {
        echo '二维数组如下：'.'<br / >';
        for($i=0; $i<=5; $i++)
        {
            $arr[$i]['val'] = mt_rand(1, 100);
            $arr[$i]['num'] = mt_rand(1, 100);
        }
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
        echo '从二维数组中抽出键为val，单独成另一个数组：'.'<br / >';
        foreach ($arr as $key => $row)
        {
            $vals[$key] = $row['val'];
            $nums[$key] = $row['num'];
        }

        echo '<pre>';
        print_r($vals);
        echo '</pre>';
        echo '对其进行排序：'.'<br / >';
        array_multisort($nums,SORT_DESC,$arr );
        echo '<pre>';
        print_r($vals);
        echo '</pre>';
        echo '结果：'.'<br / >';
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
        exit();
    }
} 