CREATE DATABASE pizza;

USE pizza;

CREATE TABLE user (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email varchar(320) UNIQUE NOT NULL,
    username varchar(24) NOT NULL,
    password varchar(255) NOT NULL,
    role ENUM('user', 'admin', 'deliverer') DEFAULT 'user',
    register_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pizza (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name varchar(100),
    description varchar(500),
    price FLOAT,
    deleted BOOLEAN DEFAULT FALSE
);

INSERT INTO user (
    email,
    username,
    password,
    role
) VALUES (
    'lolxd@email.com',
    'admin',
    '',
    'admin'
);

CREATE TABLE transactions (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pizza_id INT,
    receiver_id INT,
    buyer_id INT,
    price FLOAT,
    bought_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    received_time DATETIME,
    delivery_time DATETIME,
    prepared_time DATETIME,
    FOREIGN KEY (pizza_id) REFERENCES pizza (id),
    FOREIGN KEY (receiver_id) REFERENCES user (id),
    FOREIGN KEY (buyer_id) REFERENCES user (id)
);

INSERT INTO pizza (
    name,
    description,
    price
) VALUES (
    'Capriciosa',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vitae odio risus. Nam massa nunc, molestie et massa non, convallis euismod eros.',
    35.99
), (
    'Margherita',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vitae odio risus. Nam massa nunc, molestie et massa non, convallis euismod eros.',
    29.85
), (
    'Diavola',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vitae odio risus. Nam massa nunc, molestie et massa non, convallis euismod eros.',
    33.21
), (
    'Quattro Formagii',
    'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam vitae odio risus. Nam massa nunc, molestie et massa non, convallis euismod eros.',
    39.99
);