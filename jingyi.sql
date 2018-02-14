/*
Navicat MySQL Data Transfer

Source Server         : gongsi
Source Server Version : 50717
Source Host           : inner.cdnhxx.com:3306
Source Database       : jingyi

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2017-04-02 10:13:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `admin`
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `admin` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pid` int(255) NOT NULL,
  `auth` text NOT NULL COMMENT '权限对应',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态 1|正常2|冰冻',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'admin', '超管', 'e10adc3949ba59abbe56e057f20f883e', '0', '', '1');
INSERT INTO `admin` VALUES ('4', '123456', '123456', 'e10adc3949ba59abbe56e057f20f883e', '1', '7', '1');
INSERT INTO `admin` VALUES ('7', 'test1', 'wang', 'e10adc3949ba59abbe56e057f20f883e', '1', '1,2,3,4,5,6,7,8', '1');

-- ----------------------------
-- Table structure for `airship`
-- ----------------------------
DROP TABLE IF EXISTS `airship`;
CREATE TABLE `airship` (
  `airship_id` int(255) NOT NULL AUTO_INCREMENT,
  `airship_title` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '飞船标题',
  `airship_content` text COLLATE utf8_bin NOT NULL COMMENT '飞船内容',
  `cityid` int(255) NOT NULL,
  `airship_hobby_id` varchar(500) COLLATE utf8_bin NOT NULL COMMENT '爱好id字符串（以,隔开）',
  `airship_hobby_name` varchar(500) COLLATE utf8_bin NOT NULL COMMENT '爱好名称字符串（以,隔开）',
  `airship_status` int(5) NOT NULL COMMENT '状态 0|起飞中 1|停靠中',
  `userid` int(255) NOT NULL COMMENT '用户id',
  `addtime` int(11) NOT NULL COMMENT '添加时间戳',
  PRIMARY KEY (`airship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='飞船信息表';

-- ----------------------------
-- Records of airship
-- ----------------------------

-- ----------------------------
-- Table structure for `airship_stops`
-- ----------------------------
DROP TABLE IF EXISTS `airship_stops`;
CREATE TABLE `airship_stops` (
  `airship_stops_id` int(255) NOT NULL AUTO_INCREMENT,
  `airship_id` int(255) NOT NULL COMMENT '飞船id',
  `userid` int(11) NOT NULL COMMENT '飞船发布者id',
  `stops_userid` int(255) NOT NULL COMMENT '停靠用户id',
  `addtime` int(11) NOT NULL COMMENT '停靠时间戳',
  `stops_status` int(5) NOT NULL COMMENT '停靠状态 1|停靠中2|以离开',
  `leavetime` int(11) NOT NULL COMMENT '离开时间戳',
  PRIMARY KEY (`airship_stops_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='飞船停靠信息表';

-- ----------------------------
-- Records of airship_stops
-- ----------------------------

-- ----------------------------
-- Table structure for `article`
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '标题',
  `content` text COLLATE utf8_bin NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='单篇文章';

-- ----------------------------
-- Records of article
-- ----------------------------
INSERT INTO `article` VALUES ('1', '注册协议', 0x266C743B702667743BE6B58BE8AF95266C743B2F702667743B);
INSERT INTO `article` VALUES ('2', '关于我们', 0x266C743B702667743BE585B3E4BA8EE68891E4BBAC266C743B2F702667743B);
INSERT INTO `article` VALUES ('3', '联系客服', 0x313233323133323131);

-- ----------------------------
-- Table structure for `backimg`
-- ----------------------------
DROP TABLE IF EXISTS `backimg`;
CREATE TABLE `backimg` (
  `back_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `addtime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`back_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='背景图表';

-- ----------------------------
-- Records of backimg
-- ----------------------------

-- ----------------------------
-- Table structure for `banner`
-- ----------------------------
DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `images` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '图片地址',
  `tui` int(10) NOT NULL COMMENT '排序（从小到大）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='banner信息表';

-- ----------------------------
-- Records of banner
-- ----------------------------
INSERT INTO `banner` VALUES ('1', './Public/upload/banner/2016-09-01/57c8000dcc558.png', '1');
INSERT INTO `banner` VALUES ('2', './Public/upload/banner/2016-09-01/57c800bae5cab.jpg', '1');
INSERT INTO `banner` VALUES ('4', './Public/upload/banner/2017-03-20/58cf826d03d09.jpg', '1');

-- ----------------------------
-- Table structure for `city`
-- ----------------------------
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `city_id` int(10) NOT NULL,
  `city_name` varchar(50) NOT NULL,
  `province_id` varchar(20) NOT NULL,
  `first_letter` varchar(20) DEFAULT NULL,
  `is_hot` int(10) NOT NULL DEFAULT '0',
  `state` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of city
-- ----------------------------
INSERT INTO `city` VALUES ('110100', '北京(市辖区)', '110000', 'bj', '0', '1');
INSERT INTO `city` VALUES ('110200', '北京(县)', '110000', 'bj', '0', '1');
INSERT INTO `city` VALUES ('120100', '天津(市辖区)', '120000', 'tj', '0', '1');
INSERT INTO `city` VALUES ('120200', '天津(县)', '120000', 'tj', '0', '1');
INSERT INTO `city` VALUES ('130100', '石家庄市', '130000', 'sjz', '0', '1');
INSERT INTO `city` VALUES ('130200', '唐山市', '130000', 'ts', '0', '1');
INSERT INTO `city` VALUES ('130300', '秦皇岛市', '130000', 'qhd', '0', '1');
INSERT INTO `city` VALUES ('130400', '邯郸市', '130000', 'hd', '0', '1');
INSERT INTO `city` VALUES ('130500', '邢台市', '130000', 'xt', '0', '1');
INSERT INTO `city` VALUES ('130600', '保定市', '130000', 'bd', '0', '1');
INSERT INTO `city` VALUES ('130700', '张家口市', '130000', 'zjk', '0', '1');
INSERT INTO `city` VALUES ('130800', '承德市', '130000', 'cd', '0', '1');
INSERT INTO `city` VALUES ('130900', '沧州市', '130000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('131000', '廊坊市', '130000', 'lf', '0', '1');
INSERT INTO `city` VALUES ('131100', '衡水市', '130000', 'hs', '0', '1');
INSERT INTO `city` VALUES ('140100', '太原市', '140000', 'ty', '0', '1');
INSERT INTO `city` VALUES ('140200', '大同市', '140000', 'dt', '0', '1');
INSERT INTO `city` VALUES ('140300', '阳泉市', '140000', 'yq', '0', '1');
INSERT INTO `city` VALUES ('140400', '长治市', '140000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('140500', '晋城市', '140000', 'zc', '0', '1');
INSERT INTO `city` VALUES ('140600', '朔州市', '140000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('140700', '晋中市', '140000', 'jz', '0', '1');
INSERT INTO `city` VALUES ('140800', '运城市', '140000', 'yc', '0', '1');
INSERT INTO `city` VALUES ('140900', '忻州市', '140000', 'xz', '0', '1');
INSERT INTO `city` VALUES ('141000', '临汾市', '140000', 'll', '0', '1');
INSERT INTO `city` VALUES ('141100', '吕梁市', '140000', 'll', '0', '1');
INSERT INTO `city` VALUES ('150100', '呼和浩特市', '150000', 'hh', '0', '1');
INSERT INTO `city` VALUES ('150200', '包头市', '150000', 'bt', '0', '1');
INSERT INTO `city` VALUES ('150300', '乌海市', '150000', 'wh', '0', '1');
INSERT INTO `city` VALUES ('150400', '赤峰市', '150000', 'cf', '0', '1');
INSERT INTO `city` VALUES ('150500', '通辽市', '150000', 'tl', '0', '1');
INSERT INTO `city` VALUES ('150600', '鄂尔多斯市', '150000', 'ee', '0', '1');
INSERT INTO `city` VALUES ('150700', '呼伦贝尔市', '150000', 'hl', '0', '1');
INSERT INTO `city` VALUES ('150800', '巴彦淖尔市', '150000', 'by', '0', '1');
INSERT INTO `city` VALUES ('150900', '乌兰察布市', '150000', 'wl', '0', '1');
INSERT INTO `city` VALUES ('152200', '兴安盟', '150000', 'xa', '0', '1');
INSERT INTO `city` VALUES ('152500', '锡林郭勒盟', '150000', 'xl', '0', '1');
INSERT INTO `city` VALUES ('152900', '阿拉善盟', '150000', 'al', '0', '1');
INSERT INTO `city` VALUES ('210100', '沈阳市', '210000', 'sy', '1', '1');
INSERT INTO `city` VALUES ('210200', '大连市', '210000', 'dl', '1', '1');
INSERT INTO `city` VALUES ('210300', '鞍山市', '210000', 'as', '0', '1');
INSERT INTO `city` VALUES ('210400', '抚顺市', '210000', 'fs', '0', '1');
INSERT INTO `city` VALUES ('210500', '本溪市', '210000', 'bx', '0', '1');
INSERT INTO `city` VALUES ('210600', '丹东市', '210000', 'dd', '0', '1');
INSERT INTO `city` VALUES ('210700', '锦州市', '210000', 'jz', '0', '1');
INSERT INTO `city` VALUES ('210800', '营口市', '210000', 'yk', '0', '1');
INSERT INTO `city` VALUES ('210900', '阜新市', '210000', 'fx', '0', '1');
INSERT INTO `city` VALUES ('211000', '辽阳市', '210000', 'ly', '0', '1');
INSERT INTO `city` VALUES ('211100', '盘锦市', '210000', 'pj', '0', '1');
INSERT INTO `city` VALUES ('211200', '铁岭市', '210000', 'tl', '0', '1');
INSERT INTO `city` VALUES ('211300', '朝阳市', '210000', 'cy', '0', '1');
INSERT INTO `city` VALUES ('211400', '葫芦岛市', '210000', 'hld', '0', '1');
INSERT INTO `city` VALUES ('220100', '长春市', '220000', 'cc', '0', '1');
INSERT INTO `city` VALUES ('220200', '吉林市', '220000', 'jl', '0', '1');
INSERT INTO `city` VALUES ('220300', '四平市', '220000', 'sp', '0', '1');
INSERT INTO `city` VALUES ('220400', '辽源市', '220000', 'ly', '0', '1');
INSERT INTO `city` VALUES ('220500', '通化市', '220000', 'th', '0', '1');
INSERT INTO `city` VALUES ('220600', '白山市', '220000', 'bs', '0', '1');
INSERT INTO `city` VALUES ('220700', '松原市', '220000', 'sy', '0', '1');
INSERT INTO `city` VALUES ('220800', '白城市', '220000', 'bc', '0', '1');
INSERT INTO `city` VALUES ('222400', '延边朝鲜族自治州', '220000', 'yb', '0', '1');
INSERT INTO `city` VALUES ('230100', '哈尔滨市', '230000', 'heb', '0', '1');
INSERT INTO `city` VALUES ('230200', '齐齐哈尔市', '230000', 'qq', '0', '1');
INSERT INTO `city` VALUES ('230300', '鸡西市', '230000', 'jx', '0', '1');
INSERT INTO `city` VALUES ('230400', '鹤岗市', '230000', 'hg', '0', '1');
INSERT INTO `city` VALUES ('230500', '双鸭山市', '230000', 'sy', '0', '1');
INSERT INTO `city` VALUES ('230600', '大庆市', '230000', 'dq', '0', '1');
INSERT INTO `city` VALUES ('230700', '伊春市', '230000', 'yc', '0', '1');
INSERT INTO `city` VALUES ('230800', '佳木斯市', '230000', 'jms', '0', '1');
INSERT INTO `city` VALUES ('230900', '七台河市', '230000', 'qth', '0', '1');
INSERT INTO `city` VALUES ('231000', '牡丹江市', '230000', 'mdj', '0', '1');
INSERT INTO `city` VALUES ('231100', '黑河市', '230000', 'hh', '0', '1');
INSERT INTO `city` VALUES ('231200', '绥化市', '230000', 'sh', '0', '1');
INSERT INTO `city` VALUES ('232700', '大兴安岭地区', '230000', 'dxal', '0', '1');
INSERT INTO `city` VALUES ('310100', '上海(市辖区)', '310000', 'sh', '0', '1');
INSERT INTO `city` VALUES ('310200', '上海(县)', '310000', 'sh', '0', '1');
INSERT INTO `city` VALUES ('320100', '南京市', '320000', 'nj', '0', '1');
INSERT INTO `city` VALUES ('320200', '无锡市', '320000', 'wx', '0', '1');
INSERT INTO `city` VALUES ('320300', '徐州市', '320000', 'xz', '0', '1');
INSERT INTO `city` VALUES ('320400', '常州市', '320000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('320500', '苏州市', '320000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('320600', '南通市', '320000', 'nt', '0', '1');
INSERT INTO `city` VALUES ('320700', '连云港市', '320000', 'lyg', '0', '1');
INSERT INTO `city` VALUES ('320800', '淮安市', '320000', 'ha', '0', '1');
INSERT INTO `city` VALUES ('320900', '盐城市', '320000', 'yc', '0', '1');
INSERT INTO `city` VALUES ('321000', '扬州市', '320000', 'yz', '0', '1');
INSERT INTO `city` VALUES ('321100', '镇江市', '320000', 'zj', '0', '1');
INSERT INTO `city` VALUES ('321200', '泰州市', '320000', 'tz', '0', '1');
INSERT INTO `city` VALUES ('321300', '宿迁市', '320000', 'sq', '0', '1');
INSERT INTO `city` VALUES ('330100', '杭州市', '330000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('330200', '宁波市', '330000', 'nb', '0', '1');
INSERT INTO `city` VALUES ('330300', '温州市', '330000', 'wz', '0', '1');
INSERT INTO `city` VALUES ('330400', '嘉兴市', '330000', 'jx', '0', '1');
INSERT INTO `city` VALUES ('330500', '湖州市', '330000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('330600', '绍兴市', '330000', 'sx', '0', '1');
INSERT INTO `city` VALUES ('330700', '金华市', '330000', 'jh', '0', '1');
INSERT INTO `city` VALUES ('330800', '衢州市', '330000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('330900', '舟山市', '330000', 'zs', '0', '1');
INSERT INTO `city` VALUES ('331000', '台州市', '330000', 'tz', '0', '1');
INSERT INTO `city` VALUES ('331100', '丽水市', '330000', 'ls', '0', '1');
INSERT INTO `city` VALUES ('340100', '合肥市', '340000', 'hf', '0', '1');
INSERT INTO `city` VALUES ('340200', '芜湖市', '340000', 'wh', '0', '1');
INSERT INTO `city` VALUES ('340300', '蚌埠市', '340000', 'bb', '0', '1');
INSERT INTO `city` VALUES ('340400', '淮南市', '340000', 'hn', '0', '1');
INSERT INTO `city` VALUES ('340500', '马鞍山市', '340000', 'mas', '0', '1');
INSERT INTO `city` VALUES ('340600', '淮北市', '340000', 'hb', '0', '1');
INSERT INTO `city` VALUES ('340700', '铜陵市', '340000', 'tl', '0', '1');
INSERT INTO `city` VALUES ('340800', '安庆市', '340000', 'aq', '0', '1');
INSERT INTO `city` VALUES ('341000', '黄山市', '340000', 'hs', '0', '1');
INSERT INTO `city` VALUES ('341100', '滁州市', '340000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('341200', '阜阳市', '340000', 'fy', '0', '1');
INSERT INTO `city` VALUES ('341300', '宿州市', '340000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('341400', '巢湖市', '340000', 'ch', '0', '1');
INSERT INTO `city` VALUES ('341500', '六安市', '340000', 'la', '0', '1');
INSERT INTO `city` VALUES ('341600', '亳州市', '340000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('341700', '池州市', '340000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('341800', '宣城市', '340000', 'xc', '0', '1');
INSERT INTO `city` VALUES ('350100', '福州市', '350000', 'fz', '0', '1');
INSERT INTO `city` VALUES ('350200', '厦门市', '350000', 'xm', '0', '1');
INSERT INTO `city` VALUES ('350300', '莆田市', '350000', 'ft', '0', '1');
INSERT INTO `city` VALUES ('350400', '三明市', '350000', 'sm', '0', '1');
INSERT INTO `city` VALUES ('350500', '泉州市', '350000', 'qz', '0', '1');
INSERT INTO `city` VALUES ('350600', '漳州市', '350000', 'zz', '0', '1');
INSERT INTO `city` VALUES ('350700', '南平市', '350000', 'np', '0', '1');
INSERT INTO `city` VALUES ('350800', '龙岩市', '350000', 'ly', '0', '1');
INSERT INTO `city` VALUES ('350900', '宁德市', '350000', 'nd', '0', '1');
INSERT INTO `city` VALUES ('360100', '南昌市', '360000', 'nc', '0', '1');
INSERT INTO `city` VALUES ('360200', '景德镇市', '360000', 'jdz', '0', '1');
INSERT INTO `city` VALUES ('360300', '萍乡市', '360000', 'px', '0', '1');
INSERT INTO `city` VALUES ('360400', '九江市', '360000', 'jj', '0', '1');
INSERT INTO `city` VALUES ('360500', '新余市', '360000', 'xy', '0', '1');
INSERT INTO `city` VALUES ('360600', '鹰潭市', '360000', 'yt', '0', '1');
INSERT INTO `city` VALUES ('360700', '赣州市', '360000', 'gz', '0', '1');
INSERT INTO `city` VALUES ('360800', '吉安市', '360000', 'ja', '0', '1');
INSERT INTO `city` VALUES ('360900', '宜春市', '360000', 'yc', '0', '1');
INSERT INTO `city` VALUES ('361000', '抚州市', '360000', 'fz', '0', '1');
INSERT INTO `city` VALUES ('361100', '上饶市', '360000', 'sx', '0', '1');
INSERT INTO `city` VALUES ('370100', '济南市', '370000', 'jn', '0', '1');
INSERT INTO `city` VALUES ('370200', '青岛市', '370000', 'qd', '0', '1');
INSERT INTO `city` VALUES ('370300', '淄博市', '370000', 'zb', '0', '1');
INSERT INTO `city` VALUES ('370400', '枣庄市', '370000', 'zz', '0', '1');
INSERT INTO `city` VALUES ('370500', '东营市', '370000', 'dy', '0', '1');
INSERT INTO `city` VALUES ('370600', '烟台市', '370000', 'yt', '0', '1');
INSERT INTO `city` VALUES ('370700', '潍坊市', '370000', 'lf', '0', '1');
INSERT INTO `city` VALUES ('370800', '济宁市', '370000', 'jn', '0', '1');
INSERT INTO `city` VALUES ('370900', '泰安市', '370000', 'ta', '0', '1');
INSERT INTO `city` VALUES ('371000', '威海市', '370000', 'wh', '0', '1');
INSERT INTO `city` VALUES ('371100', '日照市', '370000', 'rz', '0', '1');
INSERT INTO `city` VALUES ('371200', '莱芜市', '370000', 'lw', '0', '1');
INSERT INTO `city` VALUES ('371300', '临沂市', '370000', 'ly', '0', '1');
INSERT INTO `city` VALUES ('371400', '德州市', '370000', 'dz', '0', '1');
INSERT INTO `city` VALUES ('371500', '聊城市', '370000', 'lc', '0', '1');
INSERT INTO `city` VALUES ('371600', '滨州市', '370000', 'bz', '0', '1');
INSERT INTO `city` VALUES ('371700', '荷泽市', '370000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('410100', '郑州市', '410000', 'zz', '0', '1');
INSERT INTO `city` VALUES ('410200', '开封市', '410000', 'kf', '0', '1');
INSERT INTO `city` VALUES ('410300', '洛阳市', '410000', 'ly', '0', '1');
INSERT INTO `city` VALUES ('410400', '平顶山市', '410000', 'pds', '0', '1');
INSERT INTO `city` VALUES ('410500', '安阳市', '410000', 'ay', '0', '1');
INSERT INTO `city` VALUES ('410600', '鹤壁市', '410000', 'hb', '0', '1');
INSERT INTO `city` VALUES ('410700', '新乡市', '410000', 'xx', '0', '1');
INSERT INTO `city` VALUES ('410800', '焦作市', '410000', 'jz', '0', '1');
INSERT INTO `city` VALUES ('410900', '濮阳市', '410000', 'py', '0', '1');
INSERT INTO `city` VALUES ('411000', '许昌市', '410000', 'xc', '0', '1');
INSERT INTO `city` VALUES ('411100', '漯河市', '410000', 'lh', '0', '1');
INSERT INTO `city` VALUES ('411200', '三门峡市', '410000', 'smx', '0', '1');
INSERT INTO `city` VALUES ('411300', '南阳市', '410000', 'ny', '0', '1');
INSERT INTO `city` VALUES ('411400', '商丘市', '410000', 'sq', '0', '1');
INSERT INTO `city` VALUES ('411500', '信阳市', '410000', 'xy', '0', '1');
INSERT INTO `city` VALUES ('411600', '周口市', '410000', 'zk', '0', '1');
INSERT INTO `city` VALUES ('411700', '驻马店市', '410000', 'zmd', '0', '1');
INSERT INTO `city` VALUES ('420100', '武汉市', '420000', 'wh', '0', '1');
INSERT INTO `city` VALUES ('420200', '黄石市', '420000', 'hs', '0', '1');
INSERT INTO `city` VALUES ('420300', '十堰市', '420000', 'sy', '0', '1');
INSERT INTO `city` VALUES ('420500', '宜昌市', '420000', 'yc', '0', '1');
INSERT INTO `city` VALUES ('420600', '襄樊市', '420000', 'xf', '0', '1');
INSERT INTO `city` VALUES ('420700', '鄂州市', '420000', 'ez', '0', '1');
INSERT INTO `city` VALUES ('420800', '荆门市', '420000', 'xm', '0', '1');
INSERT INTO `city` VALUES ('420900', '孝感市', '420000', 'xg', '0', '1');
INSERT INTO `city` VALUES ('421000', '荆州市', '420000', 'zj', '0', '1');
INSERT INTO `city` VALUES ('421100', '黄冈市', '420000', 'hg', '0', '1');
INSERT INTO `city` VALUES ('421200', '咸宁市', '420000', 'xn', '0', '1');
INSERT INTO `city` VALUES ('421300', '随州市', '420000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('422800', '恩施土家族苗族自治州', '420000', 'es', '0', '1');
INSERT INTO `city` VALUES ('429000', '省直辖行政单位', '420000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('430100', '长沙市', '430000', 'cs', '0', '1');
INSERT INTO `city` VALUES ('430200', '株洲市', '430000', 'zz', '0', '1');
INSERT INTO `city` VALUES ('430300', '湘潭市', '430000', 'xt', '0', '1');
INSERT INTO `city` VALUES ('430400', '衡阳市', '430000', 'hy', '0', '1');
INSERT INTO `city` VALUES ('430500', '邵阳市', '430000', 'sy', '0', '1');
INSERT INTO `city` VALUES ('430600', '岳阳市', '430000', 'yy', '0', '1');
INSERT INTO `city` VALUES ('430700', '常德市', '430000', 'cd', '0', '1');
INSERT INTO `city` VALUES ('430800', '张家界市', '430000', 'zjj', '0', '1');
INSERT INTO `city` VALUES ('430900', '益阳市', '430000', 'yy', '0', '1');
INSERT INTO `city` VALUES ('431000', '郴州市', '430000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('431100', '永州市', '430000', 'yz', '0', '1');
INSERT INTO `city` VALUES ('431200', '怀化市', '430000', 'hh', '0', '1');
INSERT INTO `city` VALUES ('431300', '娄底市', '430000', 'ld', '0', '1');
INSERT INTO `city` VALUES ('433100', '湘西土家族苗族自治州', '430000', 'xx', '0', '1');
INSERT INTO `city` VALUES ('440100', '广州市', '440000', 'gz', '0', '1');
INSERT INTO `city` VALUES ('440200', '韶关市', '440000', 'sg', '0', '1');
INSERT INTO `city` VALUES ('440300', '深圳市', '440000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('440400', '珠海市', '440000', 'zh', '0', '1');
INSERT INTO `city` VALUES ('440500', '汕头市', '440000', 'st', '0', '1');
INSERT INTO `city` VALUES ('440600', '佛山市', '440000', 'fs', '0', '1');
INSERT INTO `city` VALUES ('440700', '江门市', '440000', 'jm', '0', '1');
INSERT INTO `city` VALUES ('440800', '湛江市', '440000', 'zj', '0', '1');
INSERT INTO `city` VALUES ('440900', '茂名市', '440000', 'mm', '0', '1');
INSERT INTO `city` VALUES ('441200', '肇庆市', '440000', 'zq', '0', '1');
INSERT INTO `city` VALUES ('441300', '惠州市', '440000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('441400', '梅州市', '440000', 'mz', '0', '1');
INSERT INTO `city` VALUES ('441500', '汕尾市', '440000', 'sw', '0', '1');
INSERT INTO `city` VALUES ('441600', '河源市', '440000', 'hy', '0', '1');
INSERT INTO `city` VALUES ('441700', '阳江市', '440000', 'yj', '0', '1');
INSERT INTO `city` VALUES ('441800', '清远市', '440000', 'qy', '0', '1');
INSERT INTO `city` VALUES ('441900', '东莞市', '440000', 'dw', '0', '1');
INSERT INTO `city` VALUES ('442000', '中山市', '440000', 'zs', '0', '1');
INSERT INTO `city` VALUES ('445100', '潮州市', '440000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('445200', '揭阳市', '440000', 'jy', '0', '1');
INSERT INTO `city` VALUES ('445300', '云浮市', '440000', 'yf', '0', '1');
INSERT INTO `city` VALUES ('450100', '南宁市', '450000', 'nn', '0', '1');
INSERT INTO `city` VALUES ('450200', '柳州市', '450000', 'lz', '0', '1');
INSERT INTO `city` VALUES ('450300', '桂林市', '450000', 'gl', '0', '1');
INSERT INTO `city` VALUES ('450400', '梧州市', '450000', 'wz', '0', '1');
INSERT INTO `city` VALUES ('450500', '北海市', '450000', 'bh', '0', '1');
INSERT INTO `city` VALUES ('450600', '防城港市', '450000', 'fc', '0', '1');
INSERT INTO `city` VALUES ('450700', '钦州市', '450000', 'rz', '0', '1');
INSERT INTO `city` VALUES ('450800', '贵港市', '450000', 'gg', '0', '1');
INSERT INTO `city` VALUES ('450900', '玉林市', '450000', 'yl', '0', '1');
INSERT INTO `city` VALUES ('451000', '百色市', '450000', 'bs', '0', '1');
INSERT INTO `city` VALUES ('451100', '贺州市', '450000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('451200', '河池市', '450000', 'hc', '0', '1');
INSERT INTO `city` VALUES ('451300', '来宾市', '450000', 'lb', '0', '1');
INSERT INTO `city` VALUES ('451400', '崇左市', '450000', 'cz', '0', '1');
INSERT INTO `city` VALUES ('460100', '海口市', '460000', 'hk', '0', '1');
INSERT INTO `city` VALUES ('460200', '三亚市', '460000', 'sy', '0', '1');
INSERT INTO `city` VALUES ('469000', '省直辖县级行政单位', '460000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('500100', '重庆(市辖区)', '500000', 'cq', '0', '1');
INSERT INTO `city` VALUES ('500200', '重庆(县)', '500000', 'cq', '0', '1');
INSERT INTO `city` VALUES ('500300', '重庆(市)', '500000', 'cq', '0', '1');
INSERT INTO `city` VALUES ('510100', '成都市', '510000', 'cd', '0', '1');
INSERT INTO `city` VALUES ('510300', '自贡市', '510000', 'zg', '0', '1');
INSERT INTO `city` VALUES ('510400', '攀枝花市', '510000', 'pzh', '0', '1');
INSERT INTO `city` VALUES ('510500', '泸州市', '510000', 'lz', '0', '1');
INSERT INTO `city` VALUES ('510600', '德阳市', '510000', 'dy', '0', '1');
INSERT INTO `city` VALUES ('510700', '绵阳市', '510000', 'jy', '0', '1');
INSERT INTO `city` VALUES ('510800', '广元市', '510000', 'gy', '0', '1');
INSERT INTO `city` VALUES ('510900', '遂宁市', '510000', 'sn', '0', '1');
INSERT INTO `city` VALUES ('511000', '内江市', '510000', 'nj', '0', '1');
INSERT INTO `city` VALUES ('511100', '乐山市', '510000', 'ls', '0', '1');
INSERT INTO `city` VALUES ('511300', '南充市', '510000', 'nc', '0', '1');
INSERT INTO `city` VALUES ('511400', '眉山市', '510000', 'ms', '0', '1');
INSERT INTO `city` VALUES ('511500', '宜宾市', '510000', 'yb', '0', '1');
INSERT INTO `city` VALUES ('511600', '广安市', '510000', 'ga', '0', '1');
INSERT INTO `city` VALUES ('511700', '达州市', '510000', 'dz', '0', '1');
INSERT INTO `city` VALUES ('511800', '雅安市', '510000', 'ya', '0', '1');
INSERT INTO `city` VALUES ('511900', '巴中市', '510000', 'bz', '0', '1');
INSERT INTO `city` VALUES ('512000', '资阳市', '510000', 'zy', '0', '1');
INSERT INTO `city` VALUES ('513200', '阿坝藏族羌族自治州', '510000', 'ab', '0', '1');
INSERT INTO `city` VALUES ('513300', '甘孜藏族自治州', '510000', 'gm', '0', '1');
INSERT INTO `city` VALUES ('513400', '凉山彝族自治州', '510000', 'ls', '0', '1');
INSERT INTO `city` VALUES ('520100', '贵阳市', '520000', 'gy', '0', '1');
INSERT INTO `city` VALUES ('520200', '六盘水市', '520000', 'lp', '0', '1');
INSERT INTO `city` VALUES ('520300', '遵义市', '520000', 'zy', '0', '1');
INSERT INTO `city` VALUES ('520400', '安顺市', '520000', 'as', '0', '1');
INSERT INTO `city` VALUES ('522200', '铜仁地区', '520000', 'tr', '0', '1');
INSERT INTO `city` VALUES ('522300', '黔西南布依族苗族自治州', '520000', 'jx', '0', '1');
INSERT INTO `city` VALUES ('522400', '毕节地区', '520000', 'bj', '0', '1');
INSERT INTO `city` VALUES ('522600', '黔东南苗族侗族自治州', '520000', 'jd', '0', '1');
INSERT INTO `city` VALUES ('522700', '黔南布依族苗族自治州', '520000', 'jn', '0', '1');
INSERT INTO `city` VALUES ('530100', '昆明市', '530000', 'km', '0', '1');
INSERT INTO `city` VALUES ('530300', '曲靖市', '530000', 'qj', '0', '1');
INSERT INTO `city` VALUES ('530400', '玉溪市', '530000', 'yx', '0', '1');
INSERT INTO `city` VALUES ('530500', '保山市', '530000', 'bs', '0', '1');
INSERT INTO `city` VALUES ('530600', '昭通市', '530000', 'zt', '0', '1');
INSERT INTO `city` VALUES ('530700', '丽江市', '530000', 'lj', '0', '1');
INSERT INTO `city` VALUES ('530800', '思茅市', '530000', 'sm', '0', '1');
INSERT INTO `city` VALUES ('530900', '临沧市', '530000', 'lc', '0', '1');
INSERT INTO `city` VALUES ('532300', '楚雄彝族自治州', '530000', 'cx', '0', '1');
INSERT INTO `city` VALUES ('532500', '红河哈尼族彝族自治州', '530000', 'hh', '0', '1');
INSERT INTO `city` VALUES ('532600', '文山壮族苗族自治州', '530000', 'ws', '0', '1');
INSERT INTO `city` VALUES ('532800', '西双版纳傣族自治州', '530000', 'xs', '0', '1');
INSERT INTO `city` VALUES ('532900', '大理白族自治州', '530000', 'dl', '0', '1');
INSERT INTO `city` VALUES ('533100', '德宏傣族景颇族自治州', '530000', 'dh', '0', '1');
INSERT INTO `city` VALUES ('533300', '怒江傈僳族自治州', '530000', 'nj', '0', '1');
INSERT INTO `city` VALUES ('533400', '迪庆藏族自治州', '530000', 'dq', '0', '1');
INSERT INTO `city` VALUES ('540100', '拉萨市', '540000', 'ls', '0', '1');
INSERT INTO `city` VALUES ('542100', '昌都地区', '540000', 'cd', '0', '1');
INSERT INTO `city` VALUES ('542200', '山南地区', '540000', 'sn', '0', '1');
INSERT INTO `city` VALUES ('542300', '日喀则地区', '540000', 'rg', '0', '1');
INSERT INTO `city` VALUES ('542400', '那曲地区', '540000', 'nq', '0', '1');
INSERT INTO `city` VALUES ('542500', '阿里地区', '540000', 'al', '0', '1');
INSERT INTO `city` VALUES ('542600', '林芝地区', '540000', 'lz', '0', '1');
INSERT INTO `city` VALUES ('610100', '西安市', '610000', 'xa', '0', '1');
INSERT INTO `city` VALUES ('610200', '铜川市', '610000', 'tc', '0', '1');
INSERT INTO `city` VALUES ('610300', '宝鸡市', '610000', 'bj', '0', '1');
INSERT INTO `city` VALUES ('610400', '咸阳市', '610000', 'xy', '0', '1');
INSERT INTO `city` VALUES ('610500', '渭南市', '610000', 'wn', '0', '1');
INSERT INTO `city` VALUES ('610600', '延安市', '610000', 'ya', '0', '1');
INSERT INTO `city` VALUES ('610700', '汉中市', '610000', 'hz', '0', '1');
INSERT INTO `city` VALUES ('610800', '榆林市', '610000', 'yl', '0', '1');
INSERT INTO `city` VALUES ('610900', '安康市', '610000', 'ak', '0', '1');
INSERT INTO `city` VALUES ('611000', '商洛市', '610000', 'sl', '0', '1');
INSERT INTO `city` VALUES ('620100', '兰州市', '620000', 'lz', '0', '1');
INSERT INTO `city` VALUES ('620200', '嘉峪关市', '620000', 'jy', '0', '1');
INSERT INTO `city` VALUES ('620300', '金昌市', '620000', 'jc', '0', '1');
INSERT INTO `city` VALUES ('620400', '白银市', '620000', 'by', '0', '1');
INSERT INTO `city` VALUES ('620500', '天水市', '620000', 'ts', '0', '1');
INSERT INTO `city` VALUES ('620600', '武威市', '620000', 'ww', '0', '1');
INSERT INTO `city` VALUES ('620700', '张掖市', '620000', 'zy', '0', '1');
INSERT INTO `city` VALUES ('620800', '平凉市', '620000', 'pl', '0', '1');
INSERT INTO `city` VALUES ('620900', '酒泉市', '620000', 'jq', '0', '1');
INSERT INTO `city` VALUES ('621000', '庆阳市', '620000', 'qy', '0', '1');
INSERT INTO `city` VALUES ('621100', '定西市', '620000', 'dx', '0', '1');
INSERT INTO `city` VALUES ('621200', '陇南市', '620000', 'ln', '0', '1');
INSERT INTO `city` VALUES ('622900', '临夏回族自治州', '620000', 'lx', '0', '1');
INSERT INTO `city` VALUES ('623000', '甘南藏族自治州', '620000', 'gn', '0', '1');
INSERT INTO `city` VALUES ('630100', '西宁市', '630000', 'xn', '0', '1');
INSERT INTO `city` VALUES ('632100', '海东地区', '630000', 'hd', '0', '1');
INSERT INTO `city` VALUES ('632200', '海北藏族自治州', '630000', 'hb', '0', '1');
INSERT INTO `city` VALUES ('632300', '黄南藏族自治州', '630000', 'hn', '0', '1');
INSERT INTO `city` VALUES ('632500', '海南藏族自治州', '630000', 'hn', '0', '1');
INSERT INTO `city` VALUES ('632600', '果洛藏族自治州', '630000', 'gl', '0', '1');
INSERT INTO `city` VALUES ('632700', '玉树藏族自治州', '630000', 'ys', '0', '1');
INSERT INTO `city` VALUES ('632800', '海西蒙古族藏族自治州', '630000', 'hx', '0', '1');
INSERT INTO `city` VALUES ('640100', '银川市', '640000', 'yc', '0', '1');
INSERT INTO `city` VALUES ('640200', '石嘴山市', '640000', 'sz', '0', '1');
INSERT INTO `city` VALUES ('640300', '吴忠市', '640000', 'wz', '0', '1');
INSERT INTO `city` VALUES ('640400', '固原市', '640000', 'gy', '0', '1');
INSERT INTO `city` VALUES ('640500', '中卫市', '640000', 'zw', '0', '1');
INSERT INTO `city` VALUES ('650100', '乌鲁木齐市', '650000', 'wl', '0', '1');
INSERT INTO `city` VALUES ('650200', '克拉玛依市', '650000', 'kl', '0', '1');
INSERT INTO `city` VALUES ('652100', '吐鲁番地区', '650000', 'tl', '0', '1');
INSERT INTO `city` VALUES ('652200', '哈密地区', '650000', 'hm', '0', '1');
INSERT INTO `city` VALUES ('652300', '昌吉回族自治州', '650000', 'lc', '0', '1');
INSERT INTO `city` VALUES ('652700', '博尔塔拉蒙古自治州', '650000', 'be', '0', '1');
INSERT INTO `city` VALUES ('652800', '巴音郭楞蒙古自治州', '650000', 'by', '0', '1');
INSERT INTO `city` VALUES ('652900', '阿克苏地区', '650000', 'ak', '0', '1');
INSERT INTO `city` VALUES ('653000', '克孜勒苏柯尔克孜自治州', '650000', 'kz', '0', '1');
INSERT INTO `city` VALUES ('653100', '喀什地区', '650000', 'gs', '0', '1');
INSERT INTO `city` VALUES ('653200', '和田地区', '650000', 'ht', '0', '1');
INSERT INTO `city` VALUES ('654000', '伊犁哈萨克自治州', '650000', 'yl', '0', '1');
INSERT INTO `city` VALUES ('654200', '塔城地区', '650000', 'tc', '0', '1');
INSERT INTO `city` VALUES ('654300', '阿勒泰地区', '650000', 'al', '0', '1');
INSERT INTO `city` VALUES ('659000', '省直辖行政单位', '650000', 'sz', '0', '1');

-- ----------------------------
-- Table structure for `country_mobile_prefix`
-- ----------------------------
DROP TABLE IF EXISTS `country_mobile_prefix`;
CREATE TABLE `country_mobile_prefix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(255) DEFAULT NULL COMMENT '国家名称',
  `mobile_prefix` varchar(255) DEFAULT NULL COMMENT '区号',
  `area` varchar(255) DEFAULT NULL COMMENT '所在的洲',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=428 DEFAULT CHARSET=utf8 COMMENT='国际电话号码区号';

-- ----------------------------
-- Records of country_mobile_prefix
-- ----------------------------
INSERT INTO `country_mobile_prefix` VALUES ('214', '中国', '86', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('215', '香港', '852', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('216', '澳门', '853', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('217', '台湾', '886', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('218', '马来西亚', '60', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('219', '印度尼西亚', '62', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('220', '菲律宾', '63', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('221', '新加坡', '65', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('222', '泰国', '66', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('223', '日本', '81', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('224', '韩国', '82', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('225', '塔吉克斯坦', '7', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('226', '哈萨克斯坦', '7', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('227', '越南', '84', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('228', '土耳其', '90', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('229', '印度', '91', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('230', '巴基斯坦', '92', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('231', '阿富汗', '93', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('232', '斯里兰卡', '94', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('233', '缅甸', '95', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('234', '伊朗', '98', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('235', '亚美尼亚', '374', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('236', '东帝汶', '670', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('237', '文莱', '673', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('238', '朝鲜', '850', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('239', '柬埔寨', '855', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('240', '老挝', '856', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('241', '孟加拉国', '880', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('242', '马尔代夫', '960', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('243', '黎巴嫩', '961', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('244', '约旦', '962', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('245', '叙利亚', '963', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('246', '伊拉克', '964', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('247', '科威特', '965', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('248', '沙特阿拉伯', '966', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('249', '也门', '967', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('250', '阿曼', '968', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('251', '巴勒斯坦', '970', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('252', '阿联酋', '971', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('253', '以色列', '972', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('254', '巴林', '973', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('255', '卡塔尔', '974', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('256', '不丹', '975', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('257', '蒙古', '976', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('258', '尼泊尔', '977', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('259', '土库曼斯坦', '993', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('260', '阿塞拜疆', '994', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('261', '乔治亚', '995', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('262', '吉尔吉斯斯坦', '996', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('263', '乌兹别克斯坦', '998', '亚洲');
INSERT INTO `country_mobile_prefix` VALUES ('264', '英国', '44', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('265', '德国', '49', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('266', '意大利', '39', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('267', '法国', '33', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('268', '俄罗斯', '7', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('269', '希腊', '30', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('270', '荷兰', '31', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('271', '比利时', '32', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('272', '西班牙', '34', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('273', '匈牙利', '36', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('274', '罗马尼亚', '40', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('275', '瑞士', '41', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('276', '奥地利', '43', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('277', '丹麦', '45', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('278', '瑞典', '46', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('279', '挪威', '47', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('280', '波兰', '48', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('281', '圣马力诺', '223', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('282', '匈牙利', '336', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('283', '南斯拉夫', '338', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('284', '直布罗陀', '350', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('285', '葡萄牙', '351', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('286', '卢森堡', '352', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('287', '爱尔兰', '353', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('288', '冰岛', '354', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('289', '阿尔巴尼亚', '355', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('290', '马耳他', '356', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('291', '塞浦路斯', '357', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('292', '芬兰', '358', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('293', '保加利亚', '359', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('294', '立陶宛', '370', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('295', '拉脱维亚', '371', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('296', '爱沙尼亚', '372', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('297', '摩尔多瓦', '373', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('298', '安道尔共和国', '376', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('299', '乌克兰', '380', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('300', '南斯拉夫', '381', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('301', '克罗地亚', '385', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('302', '斯洛文尼亚', '386', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('303', '波黑', '387', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('304', '马其顿', '389', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('305', '梵蒂冈', '396', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('306', '捷克', '420', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('307', '斯洛伐克', '421', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('308', '列支敦士登', '423', '欧洲');
INSERT INTO `country_mobile_prefix` VALUES ('309', '秘鲁', '51', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('310', '墨西哥', '52', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('311', '古巴', '53', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('312', '阿根廷', '54', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('313', '巴西', '55', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('314', '智利', '56', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('315', '哥伦比亚', '57', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('316', '委内瑞拉', '58', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('317', '福克兰群岛', '500', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('318', '伯利兹', '501', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('319', '危地马拉', '502', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('320', '萨尔瓦多', '503', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('321', '洪都拉斯', '504', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('322', '尼加拉瓜', '505', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('323', '哥斯达黎加', '506', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('324', '巴拿马', '507', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('325', '圣彼埃尔', '508', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('326', '海地', '509', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('327', '瓜德罗普', '590', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('328', '玻利维亚', '591', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('329', '圭亚那', '592', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('330', '厄瓜多尔', '593', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('331', '法属圭亚那', '594', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('332', '巴拉圭', '595', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('333', '马提尼克', '596', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('334', '苏里南', '597', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('335', '乌拉圭', '598', '南美洲');
INSERT INTO `country_mobile_prefix` VALUES ('336', '埃及', '20', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('337', '南非', '27', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('338', '摩洛哥', '212', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('339', '阿尔及利亚', '213', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('340', '突尼斯', '216', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('341', '利比亚', '218', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('342', '冈比亚', '220', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('343', '塞内加尔', '221', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('344', '毛里塔尼亚', '222', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('345', '马里', '223', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('346', '几内亚', '224', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('347', '科特迪瓦', '225', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('348', '布基拉法索', '226', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('349', '尼日尔', '227', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('350', '多哥', '228', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('351', '贝宁', '229', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('352', '毛里求斯', '230', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('353', '利比里亚', '231', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('354', '塞拉利昂', '232', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('355', '加纳', '233', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('356', '尼日利亚', '234', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('357', '乍得', '235', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('358', '中非', '236', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('359', '喀麦隆', '237', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('360', '佛得角', '238', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('361', '圣多美', '239', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('362', '普林西比', '239', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('363', '赤道几内亚', '240', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('364', '加蓬', '241', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('365', '刚果', '242', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('366', '扎伊尔', '243', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('367', '安哥拉', '244', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('368', '几内亚比绍', '245', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('369', '阿森松', '247', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('370', '塞舌尔', '248', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('371', '苏丹', '249', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('372', '卢旺达', '250', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('373', '埃塞俄比亚', '251', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('374', '索马里', '252', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('375', '吉布提', '253', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('376', '肯尼亚', '254', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('377', '坦桑尼亚', '255', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('378', '乌干达', '256', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('379', '布隆迪', '257', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('380', '莫桑比克', '258', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('381', '赞比亚', '260', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('382', '马达加斯加', '261', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('383', '留尼旺岛', '262', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('384', '津巴布韦', '263', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('385', '纳米比亚', '264', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('386', '马拉维', '265', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('387', '莱索托', '266', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('388', '博茨瓦纳', '267', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('389', '斯威士兰', '268', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('390', '科摩罗', '269', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('391', '圣赫勒拿', '290', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('392', '厄立特里亚', '291', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('393', '阿鲁巴岛', '297', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('394', '法罗群岛', '298', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('395', '摩纳哥', '377', '非洲');
INSERT INTO `country_mobile_prefix` VALUES ('396', '澳大利亚', '61', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('397', '新西兰', '64', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('398', '关岛', '671', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('399', '瑙鲁', '674', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('400', '汤加', '676', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('401', '所罗门群岛', '677', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('402', '瓦努阿图', '678', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('403', '斐济', '679', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('404', '科克群岛', '682', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('405', '纽埃岛', '683', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('406', '东萨摩亚', '684', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('407', '西萨摩亚', '685', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('408', '基里巴斯', '686', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('409', '图瓦卢', '688', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('410', '科科斯岛', '619162', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('411', '诺福克岛', '6723', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('412', '圣诞岛', '619164', '大洋洲');
INSERT INTO `country_mobile_prefix` VALUES ('413', '美国', '1', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('414', '加拿大', '1', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('415', '夏威夷', '1808', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('416', '阿拉斯加', '1907', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('417', '格陵兰岛', '299', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('418', '中途岛', '1808', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('419', '威克岛', '1808', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('420', '维尔京群岛', '1809', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('421', '波多黎各', '1809', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('422', '巴哈马', '1809', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('423', '安圭拉岛', '1809', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('424', '圣卢西亚', '1809', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('425', '巴巴多斯', '1809', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('426', '牙买加', '1876', '北美洲');
INSERT INTO `country_mobile_prefix` VALUES ('427', '南极洲', '64672', '南极洲');

-- ----------------------------
-- Table structure for `hobby`
-- ----------------------------
DROP TABLE IF EXISTS `hobby`;
CREATE TABLE `hobby` (
  `hobbyid` int(255) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `hobbyname` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '兴趣爱好分类 名称',
  `addtime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`hobbyid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='爱好分类信息表';

-- ----------------------------
-- Records of hobby
-- ----------------------------
INSERT INTO `hobby` VALUES ('1', null, '旅游', null);
INSERT INTO `hobby` VALUES ('2', null, '摄影', null);

-- ----------------------------
-- Table structure for `option`
-- ----------------------------
DROP TABLE IF EXISTS `option`;
CREATE TABLE `option` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `adminid` int(255) NOT NULL COMMENT '管理员id',
  `content` text COLLATE utf8_bin NOT NULL COMMENT '操作记录',
  `addtime` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='管理员操作记录';

-- ----------------------------
-- Records of option
-- ----------------------------
INSERT INTO `option` VALUES ('1', '1', 0xE5889BE5BBBAE4BA86E794A8E688B7E38082E8B4A6E58FB7E4B8BA3A3138323032383535353130E38082, '1490685440');
INSERT INTO `option` VALUES ('2', '1', 0xE5889BE5BBBAE4BA86E794A8E688B7E38082E8B4A6E58FB7E4B8BA3A3138323038313837303031E38082, '1490691200');

-- ----------------------------
-- Table structure for `profession_sign`
-- ----------------------------
DROP TABLE IF EXISTS `profession_sign`;
CREATE TABLE `profession_sign` (
  `pro_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `addtime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`pro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='职业标签表';

-- ----------------------------
-- Records of profession_sign
-- ----------------------------

-- ----------------------------
-- Table structure for `star_sign`
-- ----------------------------
DROP TABLE IF EXISTS `star_sign`;
CREATE TABLE `star_sign` (
  `star_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `addtime` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`star_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='星球标签表';

-- ----------------------------
-- Records of star_sign
-- ----------------------------

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `userid` int(255) NOT NULL AUTO_INCREMENT,
  `countrynum` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '国家区号',
  `tel` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '账号（手机号）',
  `password` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '登陆密码',
  `headimg` varchar(500) COLLATE utf8_bin NOT NULL COMMENT '用户头像',
  `addtime` int(11) NOT NULL COMMENT '注册时间（时间戳）',
  `user_hx` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '用户环信好友账号',
  `user_hx_airship` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '用户环信空间站账号',
  `user_hx_password` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '用户环信密码',
  `status` int(3) NOT NULL COMMENT '账户状态  1|正常 2|冻结',
  `pid` int(255) NOT NULL COMMENT '邀请者id',
  `pcon` text COLLATE utf8_bin NOT NULL COMMENT '层级树，使用#userid|',
  `paypassword` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '钱包支付密码',
  `memberstatus` int(2) NOT NULL DEFAULT '0' COMMENT '会员状态 0|非会员 1|会员',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户信息表';

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '+86', '18208187006', 'e10adc3949ba59abbe56e057f20f883e', '', '1485155268', '', '', '', '1', '0', '', 'e10adc3949ba59abbe56e057f20f883e', '0');
INSERT INTO `user` VALUES ('2', '+86', '18208187007', 'e10adc3949ba59abbe56e057f20f883e', '', '1485155282', '', '', '', '1', '1', 0x613162, 'e10adc3949ba59abbe56e057f20f883e', '0');
INSERT INTO `user` VALUES ('3', '+86', '18208187008', 'e10adc3949ba59abbe56e057f20f883e', '', '1485155304', '', '', '', '2', '2', 0x613162613262, 'e10adc3949ba59abbe56e057f20f883e', '0');
INSERT INTO `user` VALUES ('4', '+86', '18208187009', 'e10adc3949ba59abbe56e057f20f883e', '', '1485155304', '', '', '', '1', '3', 0x613162613262613362, 'e10adc3949ba59abbe56e057f20f883e', '0');
INSERT INTO `user` VALUES ('5', '+86', '18208187019', 'e10adc3949ba59abbe56e057f20f883e', '', '1485155304', '', '', '', '1', '4', 0x613162613262613362613462, 'e10adc3949ba59abbe56e057f20f883e', '0');
INSERT INTO `user` VALUES ('6', '+86', '18208187011', 'e10adc3949ba59abbe56e057f20f883e', '', '1485155304', '', '', '', '1', '2', 0x613162613262, 'e10adc3949ba59abbe56e057f20f883e', '1');
INSERT INTO `user` VALUES ('7', '+86', '18208187012', 'e10adc3949ba59abbe56e057f20f883e', '', '1485155304', '', '', '', '1', '6', 0x613162613262613662, 'e10adc3949ba59abbe56e057f20f883e', '1');
INSERT INTO `user` VALUES ('8', '+86', '18202855510', 'e10adc3949ba59abbe56e057f20f883e', '', '1490685389', '', '', '', '1', '0', '', '', '0');
INSERT INTO `user` VALUES ('10', '+86', '18208187001', 'e10adc3949ba59abbe56e057f20f883e', '', '1490691200', '', '', '', '1', '0', '', '', '0');

-- ----------------------------
-- Table structure for `user_planet`
-- ----------------------------
DROP TABLE IF EXISTS `user_planet`;
CREATE TABLE `user_planet` (
  `planet_id` int(255) NOT NULL AUTO_INCREMENT,
  `planet_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '星球名称',
  `planet_summary` text COLLATE utf8_bin NOT NULL COMMENT '星球简介',
  `userid` int(255) NOT NULL COMMENT '用户id',
  `planet_style` enum('1','2','3','4') COLLATE utf8_bin NOT NULL COMMENT '星球样式',
  `province_id` int(255) NOT NULL COMMENT '省id',
  `city_id` int(255) NOT NULL COMMENT '市id',
  `city_name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '城市名称',
  `musicfile` varchar(255) COLLATE utf8_bin NOT NULL COMMENT '背景音乐地址',
  `growth_value` int(255) NOT NULL COMMENT '成长值',
  `addtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`planet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户星球信息表';

-- ----------------------------
-- Records of user_planet
-- ----------------------------
INSERT INTO `user_planet` VALUES ('1', '我的星球名字', 0xE68891E79A84E6989FE79083E58685E5AEB9, '1', '1', '1', '1', '北京', '你猜哇', '0', '1490680235');
