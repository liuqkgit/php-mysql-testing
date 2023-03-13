-- creat a table for testing
DROP TABLE IF EXISTS `order_test`;
CREATE TABLE `order_test` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `oid` varchar(30) NOT NULL DEFAULT '' COMMENT '订单号',
  `order_ctime` bigint(20) NOT NULL DEFAULT '0',
  `orderBaseInfo` text COMMENT '订单基础信息',
  `orderItemInfo` text COMMENT '订单商品信息',
  `orderRefundList` text COMMENT '订单退款列表',
  `orderLogisticsInfo` text COMMENT '订单物流信息',
  `orderNote` text COMMENT '订单备注',
  `orderAddress` text COMMENT '订单地址信息',
  `orderStepInfo` text COMMENT '订单阶段信息',
  `orderCpsInfo` text COMMENT '订单分销信息',
  `orderDeliveryInfo` text COMMENT '订单发货信息',
  `async_time` int(11) NOT NULL DEFAULT '0' COMMENT '同步时间',
  PRIMARY KEY (`id`),
  KEY `order_oid_IDX` (`oid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


-- change MySQL ENGINE
ALTER TABLE order_test ENGINE=MyISAM;
