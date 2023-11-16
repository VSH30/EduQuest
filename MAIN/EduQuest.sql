-- Active: 1675490431180@@127.0.0.1@3306@cq
CREATE DATABASE CQ;
USE CQ;
CREATE TABLE Users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  user_type VARCHAR(10) NOT NULL,
  dept VARCHAR(15) NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  pass VARCHAR(100) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO Users(username,user_type,dept,name,email,pass) VALUES('vsh','student','bca','Vishal Hanchnoli','vsh@gmail.com','vsh123');
INSERT INTO Users(username,user_type,dept,name,email,pass) VALUES('rp','student','bca','Rohit Prajapati','rp@gmail.com','rp123');
INSERT INTO Users(username,user_type,dept,name,email,pass) VALUES('vsh07','admin','bca','VSH','VSH@gmail.com','vsh07');
CREATE TABLE Question (
  question_id INT AUTO_INCREMENT PRIMARY KEY,
  qid VARCHAR(50) UNIQUE NOT NULL,
  user_id INT REFERENCES Users(user_id),
  title VARCHAR(255) NOT NULL,
  imgfile VARCHAR (10),
  datetime DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE Views  (
  qid VARCHAR(50) REFERENCES Question(qid),
  user_id INT REFERENCES Users(user_id),
  PRIMARY KEY (qid, user_id)
);
CREATE TABLE Answer (
  answer_id INT AUTO_INCREMENT PRIMARY KEY,
  aid VARCHAR(50) UNIQUE NOT NULL,
  question_id INT REFERENCES Question(question_id),
  user_id INT REFERENCES Users(user_id),
  imgfile VARCHAR(20),
  datetime DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE Likes(
  answer_id INT REFERENCES Answer(answer_id),
  user_id INT REFERENCES Users(user_id),
  PRIMARY KEY (answer_id, user_id)
);
