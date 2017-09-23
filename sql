###############2017-07-10#################
/*将会员卡激活但是没有会员天数的用户增加天数*/
update `user` set vip_left_day = '2018-07-12' where vip_flg = 2 and vip_left_day is null
###############2017-07-09#################
/*爱心大使指导师分成*/
update income_scale set `value` = 'a:3:{s:7:"p_scale";s:2:"60";s:7:"t_scale";s:2:"40";s:7:"a_scale";s:1:"0";}'
where `key` = 3;

/*爱心大使合伙人分成*/
insert into income_scale(`key`,`value`,`created_at`,`updated_at`)
values(4,'a:3:{s:7:"p_scale";s:2:"40";s:7:"t_scale";s:2:"60";s:7:"a_scale";s:1:"0";}','2017-07-08 15:36:00','2017-07-08 15:36:00');

###############2017-07-09#################
alter table `order` add column lover_id int default 0 comment '爱心大使用户ID';

###############2017-07-08#################
/*爱心大使指导师分成*/
insert into income_scale(`key`,`value`,`created_at`,`updated_at`)
values(3,'a:3:{s:7:"p_scale";s:1:"0";s:7:"t_scale";s:2:"40";s:7:"a_scale";s:2:"60";}','2017-07-08 15:36:00','2017-07-08 15:36:00');

###############2017-07-06#################
alter table `user` add column lover_id int default 0 comment '爱心大使用户ID';
alter table `user` add column lover_time datetime comment '爱心大使ID登记时间';

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


###############2017-09-23#################
/*增加和会员注册时间*/
alter table `user` add column `register_at` datetime default null comment '手机号注册时间';

