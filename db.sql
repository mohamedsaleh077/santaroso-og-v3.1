CREATE TABLE IF NOT EXISTS `category` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `name` VARCHAR(255),
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `ip` VARCHAR(255) NOT NULL,
    `is_banned` BOOLEAN NOT NULL DEFAULT false,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `username` VARCHAR(255) NOT NULL,
    `pwd_hash` VARCHAR(255) NOT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE IF NOT EXISTS `boards` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `c_id` INT NOT NULL,
    `name` VARCHAR(255),
    `description` TEXT(65535),
    `icon` VARCHAR(255),
    `bg` VARCHAR(255),
    `vid` VARCHAR(255),
    PRIMARY KEY(`id`),
    FOREIGN KEY(`c_id`) REFERENCES `category`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `userid` INT NOT NULL,
    `b_id` INT NOT NULL,
    `author` VARCHAR(255) NOT NULL DEFAULT 'Anonymous',
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT(65535),
    `media` VARCHAR(255),
    PRIMARY KEY(`id`),
    FOREIGN KEY(`b_id`) REFERENCES `boards`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY(`userid`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS `comments` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `p_id` INT NOT NULL,
    `userid` INT NOT NULL,
    `author` VARCHAR(255) NOT NULL DEFAULT 'Anonymous',
    `body` TEXT(65535),
    `media` VARCHAR(255),
    PRIMARY KEY(`id`),
    FOREIGN KEY(`p_id`) REFERENCES `posts`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY(`userid`) REFERENCES `users`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS `annoucments` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `adminid` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT(65535) NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`adminid`) REFERENCES `admins`(`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE IF NOT EXISTS `reports` (
    `id` INT NOT NULL AUTO_INCREMENT UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT(65535),
    `read` BOOLEAN NOT NULL DEFAULT false,
    PRIMARY KEY(`id`)
);

INSERT INTO category (`name`) VALUES ('general'), ('art'), ('tech'), ('japan'), ('memes');
INSERT INTO boards (`c_id`, `name`, `description`, `icon`, `bg`, `vid`)
VALUES
    (1, 'general', 'General Talking', 'icon-group-2511512280.png', '', ''),
    (2, 'photography', 'Post your Best Shots!', '251-2517898_creative-photography-icon-4199940194.jpg', '2558984-2139345370.jpg', ''),
    (3, 'technology', 'all Talk about Technology!', '9594898-405625452.png', 'footer-bg-2414456785.jpg', ''),
    (4, 'anime', 'The Space For All WEEBs', 'png-clipart-lain-iwakura-anime-television-show-manga-anime-face-black-hair-2330768280.png', 'serial-experiments-lain-thumb-2595120224.jpg', ''),
    (4, 'hatsune_miku', 'for all Vocaloid lovers! Based on Request from Abdulrahman Rashed', 'Anime-Miku-Hatsune-PNG-Transparent-Image.png', 'anime_girls_artwork_anime_simple_background_digital_art_Hatsune_Miku_Vocaloid_blue-1454921.jpg', 'https://github.com/mohamedsaleh077/santaroso-og-v3.1/raw/refs/heads/main/assets/videoplayback%20(1).mp4'),
    (5, 'memes', 'ShitPost Area!', 'meme_sad_frog-768x631-1048881241.png', '1-13023_doge-meme-wallpaper-meme-1-3658553829.jpg', '')