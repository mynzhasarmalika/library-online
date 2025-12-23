-- Seed data for Online Library
-- Run this after creating the database schema

-- Insert test users
INSERT INTO users (email, password, password_hash, role, first_name, last_name, created_at, updated_at) VALUES
('admin@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin', 'User', NOW(), NOW()),
('librarian@library.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'librarian', 'Library', 'Staff', NOW(), NOW()),
('john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'John', 'Doe', NOW(), NOW()),
('jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Jane', 'Smith', NOW(), NOW()),
('bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Bob', 'Johnson', NOW(), NOW());

-- Note: Password for all users is 'password'

-- Insert books
INSERT INTO books (title, author, description, genre, cover_image, book_file, created_at, updated_at) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'A classic American novel set in the Jazz Age, following the mysterious millionaire Jay Gatsby and his obsession with Daisy Buchanan. A tale of love, wealth, and the American Dream.', 'Fiction', NULL, NULL, NOW(), NOW()),

('1984', 'George Orwell', 'A dystopian social science fiction novel about totalitarian control, surveillance, and thought control. One of the most influential novels of the 20th century.', 'Science Fiction', NULL, NULL, NOW(), NOW()),

('To Kill a Mockingbird', 'Harper Lee', 'A gripping tale of racial injustice and childhood innocence in the American South. Follow Scout Finch as she learns about prejudice and morality.', 'Fiction', NULL, NULL, NOW(), NOW()),

('Pride and Prejudice', 'Jane Austen', 'A romantic novel of manners that follows the character development of Elizabeth Bennet, the dynamic protagonist who learns about the repercussions of hasty judgments.', 'Romance', NULL, NULL, NOW(), NOW()),

('The Catcher in the Rye', 'J.D. Salinger', 'A controversial novel about teenage rebellion and alienation. Follow Holden Caulfield as he navigates the complexities of growing up.', 'Fiction', NULL, NULL, NOW(), NOW()),

('The Lord of the Rings', 'J.R.R. Tolkien', 'An epic high fantasy trilogy about the quest to destroy the One Ring and save Middle-earth from the Dark Lord Sauron.', 'Fantasy', NULL, NULL, NOW(), NOW()),

('Harry Potter and the Philosopher''s Stone', 'J.K. Rowling', 'The first book in the Harry Potter series, following a young wizard as he discovers his magical heritage and attends Hogwarts School of Witchcraft and Wizardry.', 'Fantasy', NULL, NULL, NOW(), NOW()),

('The Hobbit', 'J.R.R. Tolkien', 'A fantasy novel about Bilbo Baggins, a hobbit who goes on an unexpected journey with a group of dwarves to reclaim their mountain home from a dragon.', 'Fantasy', NULL, NULL, NOW(), NOW()),

('The Da Vinci Code', 'Dan Brown', 'A mystery thriller that follows symbologist Robert Langdon as he investigates a murder in the Louvre Museum and uncovers a conspiracy.', 'Mystery', NULL, NULL, NOW(), NOW()),

('The Hunger Games', 'Suzanne Collins', 'A dystopian novel set in a post-apocalyptic world where teenagers fight to the death in an annual televised event.', 'Science Fiction', NULL, NULL, NOW(), NOW()),

('The Chronicles of Narnia', 'C.S. Lewis', 'A series of fantasy novels about children who discover a magical world called Narnia through a wardrobe.', 'Fantasy', NULL, NULL, NOW(), NOW()),

('The Alchemist', 'Paulo Coelho', 'A philosophical novel about a young Andalusian shepherd who travels from Spain to Egypt in search of treasure and discovers his personal legend.', 'Philosophy', NULL, NULL, NOW(), NOW()),

('The Kite Runner', 'Khaled Hosseini', 'A powerful story of friendship, betrayal, and redemption set against the backdrop of Afghanistan''s turbulent history.', 'Fiction', NULL, NULL, NOW(), NOW()),

('The Book Thief', 'Markus Zusak', 'A novel set in Nazi Germany, narrated by Death, about a young girl who steals books and shares them with others during World War II.', 'Historical Fiction', NULL, NULL, NOW(), NOW()),

('The Girl with the Dragon Tattoo', 'Stieg Larsson', 'A psychological thriller about a journalist and a hacker who investigate a decades-old disappearance.', 'Mystery', NULL, NULL, NOW(), NOW()),

('The Fault in Our Stars', 'John Green', 'A young adult novel about two teenagers who meet in a cancer support group and fall in love.', 'Romance', NULL, NULL, NOW(), NOW()),

('The Shining', 'Stephen King', 'A horror novel about a writer who becomes the winter caretaker of an isolated hotel and slowly descends into madness.', 'Horror', NULL, NULL, NOW(), NOW()),

('The Handmaid''s Tale', 'Margaret Atwood', 'A dystopian novel set in a totalitarian society where women are subjugated and used for reproduction.', 'Science Fiction', NULL, NULL, NOW(), NOW()),

('The Picture of Dorian Gray', 'Oscar Wilde', 'A philosophical novel about a man who remains young while his portrait ages, exploring themes of vanity and morality.', 'Fiction', NULL, NULL, NOW(), NOW()),

('The Adventures of Sherlock Holmes', 'Arthur Conan Doyle', 'A collection of short stories featuring the famous detective Sherlock Holmes and his friend Dr. Watson solving mysteries.', 'Mystery', NULL, NULL, NOW(), NOW());

-- Insert some ratings
INSERT INTO ratings (user_id, book_id, score, created_at, updated_at) VALUES
(2, 1, 5, NOW(), NOW()),
(2, 2, 5, NOW(), NOW()),
(2, 3, 4, NOW(), NOW()),
(3, 1, 4, NOW(), NOW()),
(3, 4, 5, NOW(), NOW()),
(3, 6, 5, NOW(), NOW()),
(4, 2, 5, NOW(), NOW()),
(4, 7, 5, NOW(), NOW()),
(4, 10, 4, NOW(), NOW()),
(2, 5, 4, NOW(), NOW()),
(3, 8, 5, NOW(), NOW()),
(4, 12, 4, NOW(), NOW());

-- Insert some favorites
INSERT INTO favorites (user_id, book_id, created_at, updated_at) VALUES
(2, 1, NOW(), NOW()),
(2, 2, NOW(), NOW()),
(2, 6, NOW(), NOW()),
(3, 4, NOW(), NOW()),
(3, 6, NOW(), NOW()),
(3, 7, NOW(), NOW()),
(4, 2, NOW(), NOW()),
(4, 7, NOW(), NOW()),
(4, 10, NOW(), NOW());

-- Insert some comments
INSERT INTO comments (book_id, user_id, comment, created_at, updated_at) VALUES
(1, 2, 'One of the greatest American novels ever written! The symbolism and themes are incredible.', NOW(), NOW()),
(1, 3, 'Beautiful prose and a tragic story. Fitzgerald really captured the essence of the Jazz Age.', NOW(), NOW()),
(2, 2, 'This book is more relevant than ever. A must-read for everyone concerned about privacy and freedom.', NOW(), NOW()),
(2, 4, 'Chilling and thought-provoking. Orwell was a visionary.', NOW(), NOW()),
(3, 2, 'A powerful story about justice and growing up. Atticus Finch is one of literature''s greatest characters.', NOW(), NOW()),
(6, 3, 'The world-building is incredible! Tolkien created an entire universe.', NOW(), NOW()),
(7, 4, 'The book that started it all! J.K. Rowling created magic with this series.', NOW(), NOW()),
(10, 4, 'Gripping and intense. Couldn''t put it down!', NOW(), NOW());

-- Insert some reading progress
INSERT INTO progress (user_id, book_id, last_page, progress_percentage, status, created_at, updated_at) VALUES
(2, 1, 150, 75, 'reading', NOW(), NOW()),
(2, 2, NULL, 100, 'completed', NOW(), NOW()),
(3, 4, 200, 80, 'reading', NOW(), NOW()),
(3, 6, NULL, 100, 'completed', NOW(), NOW()),
(4, 7, 50, 25, 'reading', NOW(), NOW()),
(4, 10, NULL, 100, 'completed', NOW(), NOW());

