-- Добавление новых книг в базу данных
-- Выполните этот SQL файл для добавления 8 новых книг с переводами

-- 1. Abai Zholy
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'Abai Zholy',
    'Mukhtar Auezov',
    'A classic novel describing the life, philosophy, and social environment of the great Kazakh poet Abai Kunanbayev. The book reflects Kazakh traditions, culture, and moral values.',
    'Novel',
    '978-601-01-0001-1',
    1942,
    'Kazakh State Publishing House',
    'Kazakh',
    NOW(),
    NOW()
);

SET @book_id_1 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_1, 'en', 'Abai Zholy', 'Mukhtar Auezov', 'A classic novel describing the life, philosophy, and social environment of the great Kazakh poet Abai Kunanbayev. The book reflects Kazakh traditions, culture, and moral values.', 'Novel', NOW(), NOW()),
(@book_id_1, 'ru', 'Путь Абая', 'Мухтар Ауэзов', 'Классический роман о жизни и духовном пути великого казахского поэта Абая Кунанбаева, отражающий культуру и традиции казахского народа.', 'Роман', NOW(), NOW()),
(@book_id_1, 'kk', 'Абай жолы', 'Мұхтар Әуезов', 'Ұлы ақын Абай Құнанбайұлының өмірі мен рухани жолын бейнелейтін қазақ әдебиетінің классикалық романы.', 'Роман', NOW(), NOW());

-- 2. War and Peace
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'War and Peace',
    'Leo Tolstoy',
    'An epic novel that explores Russian society during the Napoleonic Wars, focusing on family, love, and destiny.',
    'Historical Novel',
    '978-0-19-923276-5',
    1869,
    'The Russian Messenger',
    'Russian',
    NOW(),
    NOW()
);

SET @book_id_2 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_2, 'en', 'War and Peace', 'Leo Tolstoy', 'An epic novel that explores Russian society during the Napoleonic Wars, focusing on family, love, and destiny.', 'Historical Novel', NOW(), NOW()),
(@book_id_2, 'ru', 'Война и мир', 'Лев Толстой', 'Эпический роман о судьбах людей на фоне войны 1812 года.', 'Исторический роман', NOW(), NOW()),
(@book_id_2, 'kk', 'Соғыс және бейбітшілік', 'Лев Толстой', 'Наполеон соғысы кезеңіндегі адамдар тағдырын суреттейтін тарихи роман.', 'Тарихи роман', NOW(), NOW());

-- 3. Crime and Punishment
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'Crime and Punishment',
    'Fyodor Dostoevsky',
    'A psychological novel about guilt, conscience, and redemption through the story of a young student who commits a crime.',
    'Psychological Novel',
    '978-0-14-044913-6',
    1866,
    'The Russian Herald',
    'Russian',
    NOW(),
    NOW()
);

SET @book_id_3 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_3, 'en', 'Crime and Punishment', 'Fyodor Dostoevsky', 'A psychological novel about guilt, conscience, and redemption through the story of a young student who commits a crime.', 'Psychological Novel', NOW(), NOW()),
(@book_id_3, 'ru', 'Преступление и наказание', 'Фёдор Достоевский', 'Роман о моральных терзаниях человека, совершившего преступление.', 'Психологический роман', NOW(), NOW()),
(@book_id_3, 'kk', 'Қылмыс пен жаза', 'Фёдор Достоевский', 'Адам ар-ұяты мен жауапкершілігі туралы психологиялық роман.', 'Психологиялық роман', NOW(), NOW());

-- 4. Hamlet
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'Hamlet',
    'William Shakespeare',
    'A tragedy about betrayal, revenge, and moral struggle in the royal family of Denmark.',
    'Drama',
    '978-0-7434-7712-3',
    1603,
    'Simon & Schuster',
    'English',
    NOW(),
    NOW()
);

SET @book_id_4 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_4, 'en', 'Hamlet', 'William Shakespeare', 'A tragedy about betrayal, revenge, and moral struggle in the royal family of Denmark.', 'Drama', NOW(), NOW()),
(@book_id_4, 'ru', 'Гамлет', 'Уильям Шекспир', 'Трагедия о мести, предательстве и нравственном выборе.', 'Трагедия', NOW(), NOW()),
(@book_id_4, 'kk', 'Гамлет', 'Уильям Шекспир', 'Адамдық, ар-ождан және кек туралы трагедия.', 'Трагедия', NOW(), NOW());

-- 5. The Adventures of Sherlock Holmes
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'The Adventures of Sherlock Holmes',
    'Arthur Conan Doyle',
    'A collection of detective stories featuring the brilliant detective Sherlock Holmes.',
    'Detective',
    '978-1-85326-033-9',
    1892,
    'George Newnes',
    'English',
    NOW(),
    NOW()
);

SET @book_id_5 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_5, 'en', 'The Adventures of Sherlock Holmes', 'Arthur Conan Doyle', 'A collection of detective stories featuring the brilliant detective Sherlock Holmes.', 'Detective', NOW(), NOW()),
(@book_id_5, 'ru', 'Приключения Шерлока Холмса', 'Артур Конан Дойл', 'Сборник детективных рассказов о знаменитом сыщике.', 'Детектив', NOW(), NOW()),
(@book_id_5, 'kk', 'Шерлок Холмстың шытырман оқиғалары', 'Артур Конан Дойл', 'Атақты детективтің қызықты оқиғалары туралы жинақ.', 'Детектив', NOW(), NOW());

-- 6. The Book of Words
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'The Book of Words',
    'Abai Kunanbayev',
    'A philosophical work consisting of moral reflections, thoughts on human nature, education, faith, and society by the great Kazakh thinker Abai Kunanbayev.',
    'Philosophy',
    '978-601-01-0101-1',
    1890,
    'Kazakh Classical Literature Press',
    'Kazakh',
    NOW(),
    NOW()
);

SET @book_id_6 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_6, 'en', 'The Book of Words', 'Abai Kunanbayev', 'A philosophical work consisting of moral reflections, thoughts on human nature, education, faith, and society by the great Kazakh thinker Abai Kunanbayev.', 'Philosophy', NOW(), NOW()),
(@book_id_6, 'ru', 'Книга слов', 'Абай Кунанбаев', 'Философское произведение, содержащее размышления о нравственности, воспитании, вере и человеческой природе.', 'Философия', NOW(), NOW()),
(@book_id_6, 'kk', 'Қара сөздер', 'Абай Құнанбайұлы', 'Абайдың адам, қоғам, білім мен мораль туралы терең философиялық ой-толғамдары жинақталған еңбегі.', 'Философия', NOW(), NOW());

-- 7. Collection of Poems
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'Collection of Poems',
    'Magzhan Zhumabayev',
    'A collection of poems reflecting love for the homeland, freedom, national identity, and deep emotional experiences.',
    'Poetry',
    '978-601-01-0102-2',
    1923,
    'Kazakh Poetry Publishing',
    'Kazakh',
    NOW(),
    NOW()
);

SET @book_id_7 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_7, 'en', 'Collection of Poems', 'Magzhan Zhumabayev', 'A collection of poems reflecting love for the homeland, freedom, national identity, and deep emotional experiences.', 'Poetry', NOW(), NOW()),
(@book_id_7, 'ru', 'Сборник стихотворений', 'Магжан Жумабаев', 'Поэтический сборник, наполненный лирикой, патриотизмом и философскими размышлениями.', 'Поэзия', NOW(), NOW()),
(@book_id_7, 'kk', 'Өлеңдер жинағы', 'Мағжан Жұмабаев', 'Ұлт рухы, махаббат пен еркіндік тақырыбындағы көркем өлеңдер жинағы.', 'Поэзия', NOW(), NOW());

-- 8. Selected Works
INSERT INTO books (title, author, description, genre, isbn, publication_year, publisher, language, created_at, updated_at)
VALUES (
    'Selected Works',
    'Ibrai Altynsarin',
    'A collection of selected literary and educational works aimed at promoting knowledge, morality, and enlightenment among young readers.',
    'Education / Literature',
    '978-601-01-0103-3',
    1889,
    'National Educational Publishing House',
    'Kazakh',
    NOW(),
    NOW()
);

SET @book_id_8 = LAST_INSERT_ID();

INSERT INTO book_translations (book_id, language, title, author, description, genre, created_at, updated_at) VALUES
(@book_id_8, 'en', 'Selected Works', 'Ibrai Altynsarin', 'A collection of selected literary and educational works aimed at promoting knowledge, morality, and enlightenment among young readers.', 'Education / Literature', NOW(), NOW()),
(@book_id_8, 'ru', 'Избранные произведения', 'Ибрай Алтынсарин', 'Сборник произведений, направленных на просвещение и воспитание молодёжи.', 'Образование / Литература', NOW(), NOW()),
(@book_id_8, 'kk', 'Таңдамалы шығармалар', 'Ыбырай Алтынсарин', 'Ағартушылық бағыттағы таңдаулы шығармалар жинағы.', 'Білім / Әдебиет', NOW(), NOW());

