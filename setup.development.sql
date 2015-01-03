create table entries(
	id integer primary key autoincrement,
	year integer,
	month integer,
	day integer,
	description text,
	estimated float,
	real float,
	status integer
);