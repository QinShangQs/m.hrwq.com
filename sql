
###############2017-06-17#################
/*增加建议留言表*/
create table leave_word(
	`id` int auto_increment primary key,
	`user_id` int not null comment '用户ID',
	`content` varchar(1000) not null comment '建议内容',
	`created_at` timestamp not null default '0000-00-00 00:00:00',
	`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleted_at` timestamp NULL DEFAULT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='建议留言表';

###############2017-06-13#################
update `user` set vip_left_day = '2018-06-13' where vip_flg = 2;


###############2017-06-10#################
/*增加和会员剩余天数*/
alter table `user` add column vip_left_day date;
/*修改已有和会员的天数为永久*/
update `user` set vip_left_day = '2099-12-31' where vip_flg = 2;

/*增加和会员天数记录表*/
create table user_point_vip(
	`id` int auto_increment primary key,
	`user_id` int not null comment '用户ID',
	`point_value` smallint not null comment '积分值',
	`source` tinyint not null comment '来源:1购买和会员、2使用会员卡、3推荐注册',
	`created_at` timestamp not null default '0000-00-00 00:00:00',
	`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`deleted_at` timestamp NULL DEFAULT NULL,
  `remark` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户和会员天数记录';


