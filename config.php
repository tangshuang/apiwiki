<?php

define('DB_HOST','localhost');
define('DB_NAME','');
define('DB_USER','root');
define('DB_PASSWORD','');
define('DB_PORT','3306');
define('DB_PREFIX','aw_');

define('AUTH_SALT','fsdfjlasf.239*asdjfkl');

define('NOW_TIME',$_SERVER['REQUEST_TIME']);
define('NOW_DATE',date('Y-m-d',$_SERVER['REQUEST_TIME']));
define('NOW_DATETIME',date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']));
