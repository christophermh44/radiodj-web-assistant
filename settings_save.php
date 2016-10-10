<?php include_once 'init.php';

Conf::set("radio_name", $_POST["radio_name"]);
Conf::set("language", $_POST["language"]);
Conf::set("welcome_message", $_POST["welcome_message"]);
Conf::set("footer_data", $_POST["footer_data"]);
Conf::set("db_base", $_POST["db_base"]);
Conf::set("db_host", $_POST["db_host"]);
Conf::set("db_port", $_POST["db_port"]);
Conf::set("db_user", $_POST["db_user"]);
Conf::set("db_pass", $_POST["db_pass"]);
Conf::set("ftp_host", $_POST["ftp_host"]);
Conf::set("ftp_port", $_POST["ftp_port"]);
Conf::set("ftp_user", $_POST["ftp_user"]);
Conf::set("ftp_pass", $_POST["ftp_pass"]);
Conf::set("ftp_ssl", $_POST["ftp_ssl"]);
Conf::set("ftp_bind", $_POST["ftp_bind"]);
Conf::set("mc_channel", $_POST["mc_channel"]);
Conf::set("mc_clientid", $_POST["mc_clientid"]);
Conf::set("mc_clientsecret", $_POST["mc_clientsecret"]);

header('Location: settings.php');