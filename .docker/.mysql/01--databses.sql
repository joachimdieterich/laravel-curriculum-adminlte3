# create databases
CREATE DATABASE IF NOT EXISTS `ileaplus`;
CREATE DATABASE IF NOT EXISTS `ileaplus_test`;

# create root user and grant rights
CREATE USER 'ilea_user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON *.* TO 'ilea_user'@'%';
