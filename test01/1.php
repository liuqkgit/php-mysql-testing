<?php

/**
 * 使用PHP直接执行该脚本，每次操作执行完只会会有500毫秒延迟再执行下一次操作。
 * 操作日志会打印在当前目录下。
 * 
 * 最初的测试结果是使用的真实的业务数据测出的。为此，正式上版之前，又写了一个生成随机数据的方法。
 */

$obj = new test();

$testArr1 = $obj->getTestArr(50);
$testArr2 = $obj->getTestArr(500);

$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertOne($testArr1, false);
}
$avg = $time / 10;
$logStr = "1.不使用事务，单条语句，插入50条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertOne($testArr2, false);
}
$avg = $time / 10;
$logStr = "2.不使用事务，单条语句，插入500条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMulti($testArr1, false);
}
$avg = $time / 10;
$logStr = "3.不使用事务，多条语句，插入50条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMulti($testArr2, false);
}
$avg = $time / 10;
$logStr = "4.不使用事务，多条语句，插入500条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertOne($testArr1, true);
}
$avg = $time / 10;
$logStr = "5.使用事务，单条语句，插入50条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertOne($testArr2, true);
}
$avg = $time / 10;
$logStr = "6.使用事务，单条语句，插入500条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMulti($testArr1, true);
}
$avg = $time / 10;
$logStr = "7.使用事务，多条语句，插入50条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMulti($testArr2, true);
}
$avg = $time / 10;
$logStr = "8.使用事务，多条语句，插入500条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMultiUsePrepare($testArr1, false);
}
$avg = $time / 10;
$logStr = "9.不使用事务，多条语句，使用prepare，插入50条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMultiUsePrepare($testArr2, false);
}
$avg = $time / 10;
$logStr = "10.不使用事务，多条语句，使用prepare，插入500条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMultiUsePrepare($testArr1, true);
}
$avg = $time / 10;
$logStr = "11.使用事务，多条语句，使用prepare，插入50条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


$time = 0;
for ($i = 0; $i < 10; ++$i) {
    $time += $obj->insertMultiUsePrepare($testArr2, true);
}
$avg = $time / 10;
$logStr = "12.使用事务，多条语句，使用prepare，插入500条数据，平均耗时：$avg 秒 \n\n";
file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);


class test
{
    public $dbh;

    public function __construct()
    {
        $dsn = 'mysql:dbname=test;host:127.0.0.1';
        try {
            $this->dbh = new PDO($dsn, 'root', 'root');
        } catch (PDOException $e) {
            echo 'Connection filed:' . $e->getMessage();
            exit;
        }
    }

    /**
     * 使用单条语句插入全部数据
     * @param array $testArr 待插入的数据
     * @param bool $useTrans 是否使用事务
     */
    function insertOne($testArr = [], $useTrans = false)
    {
        $bool = $this->dbh->exec('truncate order_test');
        if ($bool === false) {
            echo 'truncate order_test is wrong';
            exit();
        }

        $insNum = count($testArr);
        if ($insNum < 1) {
            echo "insert data is empty";
            exit();
        }

        $startMSTime = microtime(true);

        if ($useTrans) {
            $bb = $this->dbh->beginTransaction();
            if ($bb === false) {
                echo "begin transaction is failed. \n";
                exit();
            }
        }

        $sql = "INSERT INTO order_test 
            (oid,order_ctime,orderBaseInfo,orderItemInfo,
            orderRefundList,orderLogisticsInfo,orderNote,orderAddress,
            orderStepInfo,orderCpsInfo,orderDeliveryInfo,async_time) values";

        foreach ($testArr as $val) {
            $sql .= "('{$val['oid']}',{$val['order_ctime']},'{$val['orderBaseInfo']}','{$val['orderItemInfo']}',
            '{$val['orderRefundList']}','{$val['orderLogisticsInfo']}','{$val['orderNote']}','{$val['orderAddress']}',
            '{$val['orderStepInfo']}','{$val['orderCpsInfo']}','{$val['orderDeliveryInfo']}',unix_timestamp()),";
        }

        $sql = rtrim($sql, ',');
        $res = $this->dbh->exec($sql);
        if ($res === false) {
            echo 'an error occurred';
            $logStr = date('Y-m-d H:i:s') . "\n" . var_export($this->dbh->errorInfo(), true) . "\n\n";
            file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);
            if ($useTrans) {
                $this->dbh->rollBack();
            }
            exit();
        }

        if ($useTrans) {
            $dd = $this->dbh->commit();
            if ($dd === false) {
                echo "commit transaction is failed. \n";
                exit();
            }
        }

        $time = microtime(true) - $startMSTime;

        $logStr = $insNum . ' one sql ' . ($useTrans ? 'with' : 'without') . ' transaction. used time: ' . $time . " seconds\n";
        file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);
        echo $time . "\n";
        usleep(500000);
        return $time;
    }

    /**
     * 循环插入逐条数据
     * @param array $testArr 待插入的数据
     * @param bool $useTrans 是否使用事务
     */
    function insertMulti($testArr = [], $useTrans = false)
    {
        $bool = $this->dbh->exec('truncate order_test');
        if ($bool === false) {
            echo 'truncate order_test is wrong';
            exit();
        }

        $insNum = count($testArr);
        if ($insNum < 1) {
            echo "insert data is empty";
            exit();
        }

        $startMSTime = microtime(true);

        if ($useTrans) {
            $bb = $this->dbh->beginTransaction();
            if ($bb === false) {
                echo "begin transaction is failed. \n";
                exit();
            }
        }


        foreach ($testArr as $val) {
            $sql = "INSERT INTO order_test 
            (oid,order_ctime,orderBaseInfo,orderItemInfo,
            orderRefundList,orderLogisticsInfo,orderNote,orderAddress,
            orderStepInfo,orderCpsInfo,orderDeliveryInfo,async_time) values";

            $sql .= "('{$val['oid']}',{$val['order_ctime']},'{$val['orderBaseInfo']}','{$val['orderItemInfo']}',
            '{$val['orderRefundList']}','{$val['orderLogisticsInfo']}','{$val['orderNote']}','{$val['orderAddress']}',
            '{$val['orderStepInfo']}','{$val['orderCpsInfo']}','{$val['orderDeliveryInfo']}',unix_timestamp())";

            $res = $this->dbh->exec($sql);
            if ($res === false) {
                echo 'an error occurred';
                $logStr = date('Y-m-d H:i:s') . "\n" . var_export($this->dbh->errorInfo(), true) . "\n\n";
                file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);
                if ($useTrans) {
                    $this->dbh->rollBack();
                }
                exit();
            }
        }

        if ($useTrans) {
            $dd = $this->dbh->commit();
            if ($dd === false) {
                echo "commit transaction is failed. \n";
                exit();
            }
        }

        $time = microtime(true) - $startMSTime;

        $logStr = $insNum . ' multi sql ' . ($useTrans ? 'with' : 'without') . ' transaction. used time: ' . $time . " seconds\n";
        file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);
        echo $time . "\n";
        usleep(500000);
        return $time;
    }

    /**
     * 循环插入逐条数据--使用prepare预处理语句
     * @param array $testArr 待插入的数据
     * @param bool $useTrans 是否使用事务
     */
    function insertMultiUsePrepare($testArr = [], $useTrans = false)
    {
        $bool = $this->dbh->exec('truncate order_test');
        if ($bool === false) {
            echo 'truncate order_test is wrong';
            exit();
        }

        $insNum = count($testArr);
        if ($insNum < 1) {
            echo "insert data is empty";
            exit();
        }

        $startMSTime = microtime(true);

        if ($useTrans) {
            $bb = $this->dbh->beginTransaction();
            if ($bb === false) {
                echo "begin transaction is failed. \n";
                exit();
            }
        }

        $sql = "INSERT INTO order_test 
            (oid,order_ctime,orderBaseInfo,orderItemInfo,
            orderRefundList,orderLogisticsInfo,orderNote,orderAddress,
            orderStepInfo,orderCpsInfo,orderDeliveryInfo,async_time) values (?,?,?,?,?,?,?,?,?,?,?,unix_timestamp());";
        $sth = $this->dbh->prepare($sql);

        foreach ($testArr as $val) {
            $res = $sth->execute([
                $val['oid'], $val['order_ctime'], $val['orderBaseInfo'], $val['orderItemInfo'],
                $val['orderRefundList'], $val['orderLogisticsInfo'], $val['orderNote'], $val['orderAddress'],
                $val['orderStepInfo'], $val['orderCpsInfo'], $val['orderDeliveryInfo']
            ]);

            if ($res === false) {
                echo 'an error occurred';
                $logStr = date('Y-m-d H:i:s') . "\n" . var_export($this->dbh->errorInfo(), true) . "\n\n";
                file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);
                if ($useTrans) {
                    $this->dbh->rollBack();
                }
                exit();
            }
        }

        if ($useTrans) {
            $dd = $this->dbh->commit();
            if ($dd === false) {
                echo "commit transaction is failed. \n";
                exit();
            }
        }

        $time = microtime(true) - $startMSTime;

        $logStr = $insNum . ' multi sql ' . ($useTrans ? 'with' : 'without') . ' transaction. use prepare. used time: ' . $time . " seconds\n";
        file_put_contents('./timeTrace.log', $logStr, FILE_APPEND);
        echo $time . "\n";
        usleep(500000);
        return $time;
    }

    // 获取测试用数组
    public function getTestArr($num = 50)
    {
        $colArr = [
            'orderBaseInfo',
            'orderItemInfo',
            'orderRefundList',
            'orderLogisticsInfo',
            'orderNote',
            'orderAddress',
            'orderStepInfo',
            'orderCpsInfo',
            'orderDeliveryInfo',
        ];

        $testArr = []; // 测试用数组
        for ($i = 0; $i < $num; ++$i) {
            $tmp = [];
            $tmp['oid'] = uniqid('oid', true);
            $tmp['order_ctime'] = time();

            $totalByte = 4095;
            foreach ($colArr as $val) {
                if ($totalByte > 3) {
                    $randNum = mt_rand(1, intval($totalByte / 3));
                    $totalByte -= ($randNum * 3);
                    $tmp[$val] = $this->getRandZHStr($randNum);
                } else {
                    $tmp[$val] = '';
                }
            }
            $testArr[] = $tmp;
        }
        return $testArr;
    }

    // 获取指定数量的随机中文字符
    public function getRandZHStr($num = 1000)
    {
        $str = '';
        for ($i = 0; $i < $num; ++$i) {
            $str .= chr(mt_rand(0xB0, 0xD6)) . chr(mt_rand(0xA1, 0xFE));
        }
        return iconv('gb2312', 'utf-8', $str);
    }
}