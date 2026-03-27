CREATE TABLE users(
    id int AUTO_INCREMENT PRIMARY KEY,
    name varchar(50),
    email varchar(100)UNIQUE ,
    password varchar(100),
)



CREATE TABLE categories(
    id int AUTO_INCREMENT PRIMARY KEY,
    name varchar(100)
)


CREATE TABLE prompts(
    id int AUTO_INCREMENT PRIMARY KEY,
    title varchar(100),
    content TEXT,
    category_id int,
    user_id int,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(category_id) REFERENCES categories(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
)

INSERT INTO categories(name)
VALUES ('Code'),('SQL'),('Marketing')