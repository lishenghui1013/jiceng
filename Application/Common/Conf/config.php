﻿<?php

return array(
    'URL_MODEL' => 2,

    'APP_VERSION' => 'v1.0',
    'APP_NAME'    => '基层名医',

    'USER_ADMINISTRATOR' => array(1,2),
    'AUTH_KEY' => 'I&TC{pft>L,C`wFQ>&#ROW>k{Kxlt1>ryW(>r<#R',

    'COMPANY_NAME' => 'ApiAdmin开发维护团队',

    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES' => array(
        'wiki/:hash'  => 'Home/Wiki/apiField',
        'api/:hash'   => 'Home/Api/index',
        'wikiList'    => 'Home/Wiki/apiList',
        'errorList'   => 'Home/Wiki/errorCode',
        'calculation' => 'Home/Wiki/calculation'
    ),

    'LANG_SWITCH_ON' => true,   // 开启语言包功能
    'LANG_LIST'      => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'   => 'l', // 默认语言切换变量

    /* 数据库设置 */
    'DB_TYPE'        => 'mysql',     // 数据库类型
    'DB_HOST'        => 'localhost',     // 服务器地址
    'DB_NAME'        => 'jiceng',          // 数据库名
    'DB_USER'        => 'root',      // 用户名
    'DB_PWD'         => 'root'     ,     // 密码
	'TMPL_CACHE_ON' => false,//禁止模板编译缓存
    'HTML_CACHE_ON' => false,//禁止静态缓存 
	'MODULE_ALLOW_LIST' => array ('Home','Admin'),
    'DEFAULT_MODULE' => 'Home',//默认访问模块
	'URL_MODEL'=>2,//可重写url模式
	'LOG_RECORD' => true,
    'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR,notice,info,debug', 
	'APP_URL'=>'/jiceng/',

);

