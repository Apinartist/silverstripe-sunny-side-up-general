<?php

/* This file gives SilverStripe information about the environment that it's running in */

/* Environment mode: change this from 'dev' to 'live' when you are going live with the site. */
define('SS_ENVIRONMENT_TYPE', 'dev');

/* This defines a default database user */
define('SS_DATABASE_SERVER', 'localhost');
define('SS_DATABASE_USERNAME', 'root');
define('SS_DATABASE_PASSWORD', 'root');

define('SS_DEFAULT_ADMIN_USERNAME','admin');
define('SS_DEFAULT_ADMIN_PASSWORD','test');
/* The database class to use, MySQLDatabase, MSSQLDatabase, etc. defaults to MySQLDatabase */
define('SS_DATABASE_CLASS','MySQLDatabase');
