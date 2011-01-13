<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED_EXTPLUGIN]
Code=carbase
Name=Car Database
Description=Car database for users
Version=1.1.2
Date=2009-jun-03
Author=Trustmaster
Copyright=
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
th_x=01:string::150:Max. thumbnail width
th_y=02:string::113:Max. thumbnail height
views=03:radio::1:Count views
phrow=04:string::4:Photos per row
th_b=05:string::0:Thumbnail border
[END_SED_EXTPLUGIN_CONFIG]
==================== */

defined('SED_CODE') || die("Wrong URL.");

if ($action == 'install')
{
	$sql = <<<SQL
CREATE TABLE IF NOT EXISTS sed_cars_models (
	mod_id INT NOT NULL AUTO_INCREMENT,
	mod_name VARCHAR(255) NOT NULL,
	PRIMARY KEY(mod_id),
	KEY(mod_name)
);

CREATE TABLE IF NOT EXISTS sed_cars_engines (
	eng_id INT NOT NULL AUTO_INCREMENT,
	eng_name VARCHAR(255) NOT NULL,
	PRIMARY KEY(eng_id),
	KEY(eng_name)
);

CREATE TABLE IF NOT EXISTS sed_cars_owned (
	car_id INT NOT NULL AUTO_INCREMENT,
	car_owner INT NOT NULL,
	car_model INT NOT NULL,
	car_built SMALLINT,
	car_bought SMALLINT,
	car_engine INT NOT NULL,
	car_fuel ENUM('diesel', 'gasoline') NOT NULL DEFAULT 'gasoline',
	car_cylinders TINYINT NOT NULL DEFAULT 4,
	car_volume SMALLINT,
	car_valves TINYINT,
	car_power SMALLINT,
	car_rpm SMALLINT,
	car_gearbox ENUM('man', 'auto') NOT NULL DEFAULT 'man',
	car_gears TINYINT,
	car_descr TEXT,
	car_added DATETIME NOT NULL,
	car_views INT NOT NULL DEFAULT 0,
	PRIMARY KEY(car_id),
	KEY (car_owner)
);

CREATE TABLE IF NOT EXISTS sed_cars_photos (
	ph_id INT NOT NULL AUTO_INCREMENT,
	ph_car INT NOT NULL,
	ph_ext ENUM('jpg', 'png', 'gif'),
	ph_width SMALLINT NOT NULL DEFAULT 0,
	ph_height SMALLINT NOT NULL DEFAULT 0,
	ph_size SMALLINT NOT NULL DEFAULT 0,
	ph_name VARCHAR(200),
	ph_com TEXT,
	PRIMARY KEY(ph_id),
	KEY(ph_car)
)
SQL;
	$queries = explode(';', $sql);
	foreach ($queries as $query)
	{
		sed_sql_query($query);
	}
}
elseif ($action == 'uninstall')
{
	$sql = <<<SQL
DROP TABLE IF EXISTS sed_cars_models;
DROP TABLE IF EXISTS sed_cars_engines;
DROP TABLE IF EXISTS sed_cars_owned;
DROP TABLE IF EXISTS sed_cars_photos;
SQL;
	$queries = explode(';', $sql);
	foreach ($queries as $query)
	{
		// sed_sql_query($query); UNCOMMENT TO REMOVE DATA
	}
}
?>