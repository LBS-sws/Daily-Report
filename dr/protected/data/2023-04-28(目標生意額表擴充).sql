
-- ----------------------------
-- Table structure for swo_comparison_set
-- ----------------------------
ALTER TABLE swo_comparison_set ADD COLUMN month_type int(2) NOT NULL DEFAULT 1 COMMENT '季度 1:1至3月 4:4至6月 7:7至9月 10:10至12月' AFTER city;
ALTER TABLE swo_summary_set ADD COLUMN month_type int(2) NOT NULL DEFAULT 1 COMMENT '季度 1:1至3月 4:4至6月 7:7至9月 10:10至12月' AFTER city;
