CREATE DATABASE IF NOT EXISTS filipino_cookbook_api;
USE filipino_cookbook_api;

CREATE TABLE IF NOT EXISTS categories (
  category_id INT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS origins (
  origin_id INT PRIMARY KEY,
  origin_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS ingredients (
  ingredient_id INT PRIMARY KEY,
  ingredient_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS foods (
  food_id INT PRIMARY KEY,
  food_name VARCHAR(150) NOT NULL,
  category_id INT NOT NULL,
  origin_id INT NOT NULL,
  instructions TEXT NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(category_id),
  FOREIGN KEY (origin_id) REFERENCES origins(origin_id)
);

CREATE TABLE IF NOT EXISTS food_ingredients (
  food_id INT NOT NULL,
  ingredient_id INT NOT NULL,
  PRIMARY KEY (food_id, ingredient_id),
  FOREIGN KEY (food_id) REFERENCES foods(food_id),
  FOREIGN KEY (ingredient_id) REFERENCES ingredients(ingredient_id)
);

INSERT INTO categories (category_id, category_name) VALUES
(1, 'Appetizer'),
(2, 'Soup'),
(3, 'Main Dish'),
(4, 'Dessert');

INSERT INTO origins (origin_id, origin_name) VALUES
(1, 'Philippines'),
(2, 'Bacolod');

INSERT INTO ingredients (ingredient_id, ingredient_name) VALUES
(1, 'Bay leaves'),
(2, 'Carrots'),
(3, 'Egg'),
(4, 'Garlic'),
(5, 'Ground pork'),
(6, 'Onion'),
(7, 'Spring roll wrapper'),
(8, 'Beef shank'),
(9, 'Bone marrow'),
(10, 'Corn');

INSERT INTO foods (food_id, food_name, category_id, origin_id, instructions) VALUES
(11, 'Lumpiang Shanghai', 1, 1, 'Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.'),
(14, 'Bulalo', 2, 1, 'Boil beef shank and bone marrow until tender. Add corn and vegetables, then simmer before serving.'),
(19, 'Boiled Egg', 1, 2, 'Boil the egg until cooked.');

INSERT INTO food_ingredients (food_id, ingredient_id) VALUES
(11, 2),
(11, 3),
(11, 4),
(11, 5),
(11, 6),
(11, 7),
(14, 8),
(14, 9),
(14, 10),
(14, 6),
(19, 1);
