
###############2017-06-10#################
/*增加和会员剩余天数*/
alter table `user` add column vip_left_day int default 0;
/*修改已有和会员的天数为永久*/
update `user` set vip_left_day = -1 where vip_flg = 2;