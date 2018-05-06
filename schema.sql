CREATE DATABASE doingsdone
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;
	
	USE `doingsdone`;
	
	CREATE TABLE `tasks` (
	`id` INT AUTO_INCREMENT PRIMARY KEY,
	`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`done_at` DATETIME NOT NULL,
	`name` CHAR(128) NOT NULL,
	`file` CHAR(128) NOT NULL,
	`deadline` DATETIME NOT NULL,
	`user_id` INT NOT NULL,
	`project_id` INT NOT NULL
);

	CREATE INDEX task_name ON tasks(name);
	CREATE INDEX task_userid ON tasks(user_id);
	CREATE INDEX task_projectid ON tasks(project_id);

	CREATE TABLE `users` (
		`id` INT AUTO_INCREMENT PRIMARY KEY,
		`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`email` CHAR(128) NOT NULL,
		`name` CHAR(128) NOT NULL,
		`password` CHAR(64) NOT NULL,
		`contacts` CHAR(255) NOT NULL
	);

	CREATE INDEX users_name ON users(name);
	
	CREATE TABLE `projects` (
		`id` INT AUTO_INCREMENT PRIMARY KEY,
		`name` CHAR(128) NOT NULL,
		`user_id` INT NOT NULL
	);
		
	CREATE UNIQUE INDEX un_email ON users(email);
	CREATE INDEX projects_name ON projects(name);
	CREATE INDEX projects_userid ON projects(user_id);