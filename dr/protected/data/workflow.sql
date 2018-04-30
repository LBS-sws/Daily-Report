CREATE DATABASE workflow CHARACTER SET utf8 COLLATE utf8_general_ci;

GRANT SELECT, INSERT, UPDATE, DELETE ON workflow.* TO 'swuser'@'localhost' IDENTIFIED BY 'swisher168';

use workflow;

DROP TABLE IF EXISTS wf_process;
CREATE TABLE wf_process(
	id int unsigned not null auto_increment primary key,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_user;
CREATE TABLE wf_user(
	id int unsigned not null auto_increment primary key,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_process_admin;
CREATE TABLE wf_process_admin(
	process_id int unsigned not null,
	user_id int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
DROP TABLE IF EXISTS wf_group;
CREATE TABLE wf_group(
	id int unsigned not null auto_increment primary key,
	process_id int unsigned not null,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_group_member;
CREATE TABLE wf_group_member(
	group_id int unsigned not null,
	user_id int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request;
CREATE TABLE wf_request(
	id int unsigned not null auto_increment primary key,
	process_id int unsigned not null,
	current_state int unsigned not null,
	user_id int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request_data;
CREATE TABLE wf_request_data(
	id int unsigned not null auto_increment primary key,
	request_id int unsigned not null,
	data_name varchar(300) not null,
	data_value varchar(5000),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request_note;
CREATE TABLE wf_request_note(
	id int unsigned not null auto_increment primary key,
	request_id int unsigned not null,
	note varchar(5000),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_state_type;
CREATE TABLE wf_state_type(
	id int unsigned not null auto_increment primary key,
	name varchar(255) not null
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO wf_state(name) VALUES
('Start'),
('Normal'),
('Complete'),
('Denied'),
('Cancelled')
;

DROP TABLE IF EXISTS wf_state;
CREATE TABLE wf_state(
	id int unsigned not null auto_increment primary key,
	state_type int unsigned not null,
	process_id int unsigned not null,
	name varchar(255),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
DROP TABLE IF EXISTS wf_transition;
CREATE TABLE wf_transition(
	id int unsigned not null auto_increment primary key,
	process_id int unsigned not null,
	current_state int unsigned not null,
	next_state int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_action_type;
CREATE TABLE wf_action_type(
	id int unsigned not null auto_increment primary key,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO wf_action_type(name) VALUES
('Approve'),
('Deny'),
('Cancel'),
('Restart'),
('Resolve')
;

DROP TABLE IF EXISTS wf_action;
CREATE TABLE wf_action(
	id int unsigned not null auto_increment primary key,
	action_type_id int unsigned not null,
	process_id int unsigned not null,
	name varchar(255),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_transition_action;
CREATE TABLE wf_transition_action(
	transition_id int unsigned not null,
	action_id int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_activity_type;
CREATE TABLE wf_activity_type(
	id int unsigned not null auto_increment primary key,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO wf_activity_type(name) VALUES
('Add Note'),
('Send Email'),
('Add Stakeholders'),
('Remove Stakeholders')
;

DROP TABLE IF EXISTS wf_activity;
CREATE TABLE wf_activity(
	id int unsigned not null auto_increment primary key,
	activity_type_id int unsigned not null,
	process_id int unsigned not null,
	name varchar(255),
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_target;
CREATE TABLE wf_target(
	id int unsigned not null auto_increment primary key,
	name varchar(255) not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO wf_target(name) VALUES
('Requester'),
('Stakeholders'),
('Group Members'),
('Process Admins')
;

DROP TABLE IF EXISTS wf_activity_target;
CREATE TABLE wf_activity_target(
	id int unsigned not null auto_increment primary key,
	activity_type_id int unsigned not null,
	activity_id int unsigned not null,
	target_id int unsigned not null,
	group_id int unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wf_request_action;
CREATE TABLE wf_request_action(
	id int unsigned not null auto_increment primary key,
	request_id int unsigned not null,
	action_id int unsigned not null,
	transition_id int unsigned not null,
	is_active tinyint unsigned not null,
	is_complete tinyint unsigned not null,
	lcd timestamp default CURRENT_TIMESTAMP,
	lud timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
