CREATE TABLE persons (
	id varchar(255) NOT NULL,
	name text NOT NULL,
	ins text NOT NULL,
	url text,
	location varchar(4),
	display int DEFAULT '1',
	active_from varchar(4),
	active_to varchar(4) DEFAULT '----'

);

ALTER TABLE persons
ADD PRIMARY KEY (id);

CREATE TABLE groups (
	id varchar(255) NOT NULL,
	name text NOT NULL,
	cp text,
	pp text,
	web text,
	region varchar(4),
	started varchar(4),
	ended varchar(4) DEFAULT '----',
	country varchar(2)
)

ALTER TABLE groups
ADD PRIMARY KEY (id);
