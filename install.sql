CREATE TABLE `aw_user` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
   `user_name` VARCHAR(32) CHARACTER SET utf8 NOT NULL COMMENT '用户名',
   `user_email` VARCHAR(64) NOT NULL COMMENT '邮箱',
   `password` VARCHAR(64) NOT NULL COMMENT '密码',
   `avatar_url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '头像地址',
   `create_time` DATETIME NOT NULL COMMENT '注册时间',
   `update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
   `status` TINYINT(1) NOT NULL COMMENT '状态。-1：删除；0：禁用；1：正常',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`user_name`),
   UNIQUE KEY (`user_email`)
) COMMENT='用户表';

CREATE TABLE `aw_project` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(32) CHARACTER SET utf8 NOT NULL COMMENT '项目名称',
  `description` TEXT CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '项目描述',
  `base_url` VARCHAR(255) CHARACTER SET utf8 NOT NULL COMMENT '项目访问URL根目录',
  `logo_url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'logo地址',
  `amount` INT(8) NOT NULL COMMENT '接口的数量',
  `read_level` TINYINT(1) NOT NULL COMMENT '阅读权限',
  `user_id` INT(10) NOT NULL COMMENT '创建者',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改时间',
  `status` TINYINT(1) NOT NULL COMMENT '状态。-1：删除；0：草稿；1：公开',
  PRIMARY KEY (`id`)
) COMMENT='项目表';

CREATE TABLE `aw_project_member` (
  `user_id` INT(10) NOT NULL COMMENT '用户ID',
  `project_id` INT(10) NOT NULL COMMENT '项目ID',
  `rule` TINYINT(1) NOT NULL COMMENT '权限。0：所有用户可读；1：只有会员可读；2：只有自己可读。',
  KEY (`user_id`),
  KEY (`project_id`)
) COMMENT='项目成员';

CREATE TABLE `aw_interface` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `project_id` INT(10) NOT NULL COMMENT '所属项目ID',
  `title` VARCHAR(32) NOT NULL COMMENT '接口名称',
  `description` TEXT CHARACTER SET utf8 NOT NULL COMMENT '接口描述',
  `url` VARCHAR(255) NOT NULL COMMENT 'URL，除去BASE URL部分和group url部分',
  `method` VARCHAR(16) NOT NULL COMMENT '数据传输方式：1:POST|2:GET|3:PUT',
  `user_id` INT(10) NOT NULL COMMENT '创建者ID',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `status` TINYINT(1) NOT NULL COMMENT '状态。-1：删除；0：草稿；1：上线',
  `sort` SMALLINT(4) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`)
) COMMENT='接口表';

CREATE TABLE `aw_interface_parameter` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `interface_id` INT(10) NOT NULL COMMENT '接口ID',
  `parent_id` INT(10) NOT NULL DEFAULT '0' COMMENT '父字段ID',
  `field` VARCHAR(32) NOT NULL COMMENT '字段名',
  `title` VARCHAR(64) NOT NULL COMMENT '变量名',
  `data_type` VARCHAR(32) NOT NULL COMMENT '数据类型',
  `required` TINYINT(1) NOT NULL COMMENT '是否必须',
  `example` TINYTEXT NOT NULL COMMENT '示例值',
  `detail` TEXT NOT NULL COMMENT '详细描述',
  `sort` SMALLINT(4) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY (`interface_id`),
  KEY (`parent_id`)
) COMMENT='传入参数表';

CREATE TABLE `aw_interface_data` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `interface_id` INT(10) NOT NULL COMMENT '接口ID',
  `parent_id` INT(10) NOT NULL DEFAULT '0' COMMENT '父字段ID',
  `field` VARCHAR(32) NOT NULL COMMENT '字段名',
  `title` VARCHAR(64) NOT NULL COMMENT '变量名',
  `data_type` VARCHAR(32) NOT NULL COMMENT '数据类型',
  `require` TINYINT(1) NOT NULL COMMENT '是否必须',
  `example` TINYTEXT NOT NULL COMMENT '示例值',
  `detail` TEXT NOT NULL COMMENT '详细描述',
  `sort` SMALLINT(4) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`),
  KEY (`interface_id`),
  KEY (`parent_id`)
) COMMENT='返回值表';

CREATE TABLE `aw_interface_error` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `interface_id` INT(10) NOT NULL COMMENT '接口ID',
  `project_id` INT(10) NOT NULL COMMENT '项目ID',
  `code` INT(6) NOT NULL COMMENT '错误码',
  `msg` VARCHAR(100) NOT NULL COMMENT '错误提示',
  `reason` TEXT NOT NULL COMMENT '参数错误的原因',
  `solution` TEXT NOT NULL COMMENT '解决办法',
  `sort` SMALLINT(4) NOT NULL DEFAULT '0' COMMENT '排序。不过再统一列表中，按照code升序排',
  PRIMARY KEY (`id`)
) COMMENT='错误码表';

CREATE TABLE `aw_document` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `project_id` INT(10) NOT NULL COMMENT '所属项目ID',
  `title` VARCHAR(32) CHARACTER SET utf8 NOT NULL COMMENT '项目名称',
  `detail` TEXT CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '项目描述',
  `user_id` INT(10) NOT NULL COMMENT '创建者',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改时间',
  `status` TINYINT(1) NOT NULL COMMENT '状态。-1：删除；0：草稿；1：公开',
  `sort` SMALLINT(4) NOT NULL COMMENT '排序值',
  PRIMARY KEY (`id`)
) COMMENT='项目表';

CREATE TABLE `aw_comment` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `parent_id` INT(10) NOT NULL COMMENT '父评论ID',
  `user_id` INT(10) NOT NULL COMMENT '创建者',
  `detail` TEXT CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '项目描述',
  `create_time` DATETIME NOT NULL COMMENT '创建时间',
  `update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改时间',
  `status` TINYINT(1) NOT NULL COMMENT '状态。-1：删除；0：草稿；1：公开',
  PRIMARY KEY (`id`),
  KEY (`parent_id`),
  KEY (`tid`),
  KEY (`user_id`)
);