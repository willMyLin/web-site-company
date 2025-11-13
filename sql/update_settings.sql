-- 添加统计数据设置项
-- 如果数据库中已有这些设置，此脚本会更新它们；如果没有，则插入新记录

INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('company_intro', '交个朋友成立于1995年，深耕企业数字化服务28年。我们专注于网站建设、品牌设计与数字营销，为客户提供从策划到落地的一站式互联网解决方案，助力企业在数字时代实现品牌价值提升与业务增长。', '公司简介'),
('stat_years', '29', '公司成立年数'),
('stat_assets', '15', '公司资产（亿元）'),
('stat_cities', '100', '业务覆盖城市数'),
('stat_cases', '4000', '实施案例数'),
('stat_branches', '30', '分公司及办事机构数'),
('stat_provinces', '26', '业务覆盖省份数')
ON DUPLICATE KEY UPDATE 
    `setting_value` = VALUES(`setting_value`),
    `description` = VALUES(`description`);
