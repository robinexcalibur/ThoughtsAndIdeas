Ideas and Thoughts Web App
--------------------------
This was a little note taking app I put together for my partner after completing Coursera's
Web Apps for Everyone course and wanting to make something of my own.

The user must login, afterwards they can access all of their "Ideas". Within each
"Idea" are several "Thoughts", which the user may add, remove, or edit. It's basically
notes organized into different groups. 

the sql to build the database was:

```
create database notes;
GRANT ALL ON notes.* TO 'robinthegreat'@'127.0.0.1' IDENTIFIED BY 'IamPrettyGreat';
GRANT ALL ON notes.* TO 'robinthegreat'@'localhost' IDENTIFIED BY 'IamPrettyGreat';

CREATE TABLE users (
  user_id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(128),
  password VARCHAR(128),
  PRIMARY KEY(user_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE users ADD INDEX(name);

CREATE TABLE ideas (
  id INTEGER NOT NULL AUTO_INCREMENT,
  user_id INTEGER NOT NULL,
  title TEXT,
  summary TEXT,
  PRIMARY KEY(id),
  FOREIGN KEY (user_id)
  REFERENCES users (user_id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE thoughts (
  id INTEGER NOT NULL AUTO_INCREMENT,
  idea_id INTEGER NOT NULL,
  title TEXT,
  body TEXT,
  PRIMARY KEY(id),
  FOREIGN KEY (idea_id) REFERENCES ideas(id)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO users (name,password)
VALUES ('Robin', '1a52e17fa899cf40fb04cfc42e6352f1');
```
