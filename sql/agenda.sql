CREATE TABLE kronos_agenda(
	id integer unsigned not null primary key auto_increment, 
        fk_kronos_users integer unsigned not null,
	created datetime not null,
	modified datetime,
	notes varchar(500),
	type varchar(50),
	subject varchar(200),
	private enum('Y','N'),
	time_start datetime not null,
	time_end datetime not null,
	uri varchar(600)
);
