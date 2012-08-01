create table kronos_users(id integer unsigned not null primary key auto_increment, email varchar(100), password varchar(100), screen_name varchar(100), UNIQUE(email));

-- Password utilise l'algorithme "sha1". 
-- Le mot de passe par défaut est "asdF1234".
-- sha1('asdF1234') == F5C9A1604A136728870E8A7150044CEE5FDF8A0C
INSERT INTO kronos_users(email, password, screen_name) VALUES('admin@kronos-web.com', 'F5C9A1604A136728870E8A7150044CEE5FDF8A0C', 'Admin, Admin');
INSERT INTO kronos_users(email, password, screen_name) VALUES('schenard@kronos-web.com', 'F5C9A1604A136728870E8A7150044CEE5FDF8A0C', 'Simon, Chenard');
INSERT INTO kronos_users(email, password, screen_name) VALUES('nvanheu@kronos-web.com', 'F5C9A1604A136728870E8A7150044CEE5FDF8A0C', 'Nicolas, Vanheuverzwijn');
INSERT INTO kronos_users(email, password, screen_name) VALUES('jesus@kronos-web.com', 'F5C9A1604A136728870E8A7150044CEE5FDF8A0C', 'Jesus, Christ');
