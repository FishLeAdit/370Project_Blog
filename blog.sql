-- SQL Script to Create Blog Database

-- 1. Create the Database
CREATE DATABASE blog;
USE blog;

-- 2. User Table
CREATE TABLE User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE, -- True for admins, false for regular users
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Category Table
CREATE TABLE Category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Post Table
CREATE TABLE Post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL, -- References the User table
    category_id INT,      -- References the Category table
    likes INT DEFAULT 0,  -- New column to store the number of likes
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(id) ON DELETE SET NULL
);

-- 5. Comment Table
CREATE TABLE Comment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    user_id INT NOT NULL,  -- References the User table
    post_id INT NOT NULL,  -- References the Post table
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES Post(id) ON DELETE CASCADE
);

-- 6. Likes Table
CREATE TABLE Likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- References the User table
    post_id INT NOT NULL, -- References the Post table
    UNIQUE(user_id, post_id), -- Prevent duplicate likes by the same user on the same post

    FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES Post(id) ON DELETE CASCADE
);

-- 7. Insert Default Admin (Example Data)
INSERT INTO User (username, email, password, is_admin) 
VALUES ('admin', 'admin@example.com', 'hashed_password', TRUE); -- Replace 'hashed_password' with actual hashed password

-- 8. Insert Example Categories
INSERT INTO Category (name, description)
VALUES 
    ('Technology', 'Posts about technology and gadgets'),
    ('Lifestyle', 'Posts about lifestyle and daily habits'),
    ('Travel', 'Posts about travel and destinations'),
    ('Pop Culture', 'Posts about Pop-Culture'),
    ('Personal', 'Posts about personal life');

-- 9. Insert Example Posts
INSERT INTO Post (title, content, user_id, category_id, likes)
VALUES 
    ('First Post', 'This is the content of the first post.', 1, 1, 10),
    ('Life Tips', 'Here are some tips for a better life.', 1, 2, 5);

-- 10. Insert Example Comments
INSERT INTO Comment (content, user_id, post_id)
VALUES 
    ('Great post! Looking forward to more.', 1, 1);

-- 11. Insert Example Likes
INSERT INTO Likes (user_id, post_id)
VALUES 
    (1, 1),
    (1, 2);
