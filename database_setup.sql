-- NutriMind – Schema for Posts & Comments
-- Import this file in phpMyAdmin (XAMPP) after selecting the `nutrimind` database.
-- It assumes the existing `user` table already exists (with an `id` primary key).

-- Posts
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_posts_author (author_id),
    INDEX idx_posts_created_at (created_at)
);

-- Comments
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_comments_post (post_id),
    INDEX idx_comments_user (user_id)
);

-- Sample seed data (optional – safe to re-run)
INSERT INTO posts (title, content, author_id) VALUES
    ('Bienvenue sur le blog NutriMind',
     'Bienvenue sur le blog de NutriMind ! Retrouvez ici nos articles sur la nutrition, la planification des repas et le bien-être. Notre plateforme vous aide à gérer vos repas et vos ingrédients efficacement.',
     1),
    ('Les bases d''une alimentation équilibrée',
     'Manger équilibré repose sur quelques principes simples : variez les sources de protéines, privilégiez les glucides complexes, consommez au moins 5 portions de fruits et légumes par jour et hydratez-vous régulièrement.',
     1);

INSERT INTO comments (post_id, user_id, content) VALUES
    (1, 1, 'Super article pour démarrer, hâte de lire la suite !');
