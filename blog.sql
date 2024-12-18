CREATE DATABASE blog;
USE blog;


CREATE TABLE User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE, -- True for admins, false for regular users
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL, -- References the User table
    category_id INT,      -- References the Category table
    likes INT DEFAULT 0,  
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(id) ON DELETE SET NULL
);

CREATE TABLE Comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user_id INT NOT NULL,  -- References the User table
    post_id INT NOT NULL,  -- References the Post table
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES Post(id) ON DELETE CASCADE
);

CREATE TABLE Likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- References the User table
    post_id INT NOT NULL, -- References the Post table
    UNIQUE(user_id, post_id), -- Prevent duplicate likes by the same user on the same post

    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES Post(id) ON DELETE CASCADE
);

INSERT INTO User (username, email, password, is_admin) 
VALUES ('admin', 'admin@example.com', 'adminpass', TRUE); 

INSERT INTO Category (name, description)
VALUES 
    ('Technology', 'Posts about technology and gadgets'),
    ('Lifestyle', 'Posts about lifestyle and daily habits'),
    ('Travel', 'Posts about travel and destinations'),
    ('Pop Culture', 'Posts about Pop-Culture'),
    ('Personal', 'Posts about personal life');

INSERT INTO Post (title, content, user_id, category_id, likes)
VALUES 
    ('The Alps - Switzerland', 'Travelling through the Alps is like stepping into a living postcard, where towering snow-capped peaks meet lush green valleys and picturesque villages. From the iconic Matterhorn to the charming Swiss and French mountain towns, the Alps invite you to explore not just stunning landscapes, but also rich cultures and unforgettable cuisine. Every turn brings a new vista, making it a dream destination for nature lovers and thrill-seekers alike..', 1, 1, 1),
    ('Pokemon TCG', 'Whether you are collecting rare cards, participating in local tournaments, or trading with fellow enthusiasts, each event offers a chance to connect with other trainers and immerse yourself in the vibrant Pokémon universe', 1, 4, 1),
    ('Apple Bhua', 'While your iPhone might be pretty, Samsung’s out here being the actual MVP of the smartphone world.', 1, 1, 1);

INSERT INTO Comment (content, user_id, post_id)
VALUES 
    ('Ossamossam', 1, 1);

-- 11. Insert Example Likes
INSERT INTO Likes (user_id, post_id)
VALUES 
    (1, 1),
    (1, 2);
