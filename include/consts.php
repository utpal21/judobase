<?php
    /*---------------------------------------------------
        Project Name:        Kirari Report System
        Developement:        
        Author:              Ken
        Date:                2015/02/10
    ---------------------------------------------------*/

// error
define("ERR_OK",                            '0');
define("ERR_SQL",                           '1');
define("ERR_INVALID_PKEY",                  '2');
define("ERR_NODATA",                        '3');

define("ERR_FAILLOGIN",                     '4');
define("ERR_ALREADYLOGIN",                  '5');

define("ERR_INVALID_REQUIRED",              '9');

define("ERR_NOPRIV",                        '10');
define("ERR_NOT_LOGINED",                   '11');
define("ERR_FAIL_UPLOAD",                   '12');

define("ERR_INVALID_IMAGE",                 '13');
define("ERR_INVALID_PDF",                   '14');
define("ERR_USER_LOCKED",                   '15');
define("ERR_USER_UNACTIVATED",              '16');
define("ERR_DUPLICATE_LOGINID",             '17');

define("ERR_ALREADYINSTALLED",              '23');

define("ERR_COULDNOT_CONNECT",              '24');
define("ERR_BLACKIP",                       '25');
define("ERR_NOTFOUND_PAGE",                 '27');

define("ERR_INVALID_OLDPWD",                '28');

define("ERR_ALREADY_USING_USER_NAME",       '29');
define("ERR_ALREADY_USING_EMAIL",           '30');

define("ERR_DELUSER",                       '31');
define("ERR_DELATCCAT",                     '32');

define("ERR_CANTCONNECT",                   '33');
define("ERR_NEWINSERTED",                   '34');

define("ERR_INVALID_ACTIVATE_KEY",          '35');
define("ERR_ACTIVATE_EXPIRED",              '36');
define("ERR_INVALID_EMAIL",                 '37');

define("ERR_NOTFOUND_USER",                 '38');
define("ERR_NOTFOUND_SHOP",                 '39');
define("ERR_NOTFOUND_PLAN",                 '40');
define("ERR_NOTFOUND_PLAYER",              	'41');
define("ERR_NOTFOUND_FACILITY",             '42');
define("ERR_NOTFOUND_CAREOFFICE",           '43');
define("ERR_NOTFOUND_CAREMAN",              '44');
define("ERR_NOTFOUND_CLINIC",               '45');
define("ERR_NOTFOUND_DOCTOR",               '46');
define("ERR_NOTFOUND_REPORT",               '47');
define("ERR_NOTFOUND_PRESCRIPTION",         '48');
define("ERR_NOTFOUND_PRESCRIPTION_PDF",     '49');
define("ERR_FAILOPENFILE",                  '50');

define("ERR_SEND_IGNORED",                  '52');
define("ERR_NOTFOUND_REPORTPDF",            '53');
define("ERR_NOTSETTED_TO_EMAIL",            '54');
define("ERR_NOTSETTED_TO_FAX",              '55');
define("ERR_FAIL_SEND_EMAIL",               '56');
define("ERR_FAIL_SEND_FAX",                 '57');
define("ERR_ONCE_IN_A_WEEK",                '58');
define("ERR_NOTFOUND_HISTORY",              '59');
define("ERR_NOTFOUND_RESULT",               '60');
define("ERR_NOTFOUND_TOURNAMENT",           '61');
define("ERR_ALEADY_EXIST_SAME_RESULT",		'62');

// code
define("CODE_UTYPE",                        0);
define("CODE_GENDER",                          1);
define("CODE_PAGE",                         2);
define("CODE_LANG",                         3);
define("CODE_LOCK",                         4);
define("CODE_ENABLE",                       5);
define("CODE_ATCSTATE",                     6);
define("CODE_LOGTYPE",                      7);
define("CODE_ALARMTYPE",                    8);
define("CODE_FRMSTATE",                     11);
define("CODE_EDITORTYPE",                   12);
define("CODE_ACTIVATE",                     15);

define("UTYPE_NONE",                        0);
define("UTYPE_ADMIN",                       1); // 管理者
define("UTYPE_REVIEW",                      2); // レビュー担当者
define("UTYPE_COARCH",                      4); // コーチ
define("UTYPE_LOGINUSER",                   UTYPE_ADMIN | UTYPE_REVIEW | UTYPE_COARCH);

define("PREFIX_CLINIC",                     "d");
define("PREFIX_CAREOFFICE",                 "c");
define("PREFIX_FACILITY",                   "f");

define("GENDER_MAN",                           1); //　男性
define("GENDER_WOMAN",                         2); // 女性

define("PAGE_HOME",                         0); // ホームページ

define("LANG_JA_JP",                        "ja_jp"); // 日本語

define("UNLOCKED",                          0); // 解除
define("LOCKED",                            1); // ロック

define("DISABLED",                          0); // 不可
define("ENABLED",                           1); // 可能

define("LOGTYPE_ACCESS",                    1); // アクセス
define("LOGTYPE_OPERATION",                 2); // 操作
define("LOGTYPE_WARNING",                   4); // 警告
define("LOGTYPE_ERROR",                     8); // エラー
define("LOGTYPE_DEBUG",                     16); // デバッグ

define("EDITORTYPE_INLINE",                 0);
define("EDITORTYPE_EXPERT",                 1);

define("ACTIONTYPE_HTML",                   0);
define("ACTIONTYPE_AJAXJSON",               1);
define("ACTIONTYPE_AJAXHTML",               2);

define("UNACTIVATED",                       0);
define("ACTIVATED",                         1);

?>