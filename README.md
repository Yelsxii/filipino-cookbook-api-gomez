# Filipino Cookbook API

## API Title
Filipino Cookbook API

## API Description
This project is a Slim PHP REST API for managing Filipino foods, categories, origins, ingredients, and food-related records. It is designed for another student to install, configure, and consume from a driver or client application.

### Purpose of the API
- Provide Filipino food information through JSON endpoints
- Support future front-end or client applications
- Demonstrate PHP Slim REST API development with token authentication

### Type of Information Provided
- Filipino food items and instructions
- Food categories
- Ingredient lists
- Origins for foods

### Intended Users
- Students using the API for lab activities
- Front-end developers building a client application
- Anyone learning REST API integration with PHP and MySQL

### Main Functions of the API
- Retrieve all foods
- Retrieve a single food by ID
- Search foods by name
- Retrieve foods by category
- Retrieve a random food
- Retrieve all categories and ingredients
- Add a new food record
- Authenticate requests using a Bearer token

### Technologies Used
- PHP
- Slim Framework
- MySQL
- Composer
- JSON
- XAMPP or local web server
- Postman or Thunder Client
- Git and GitHub

## Features
- List all foods with ingredients and category/origin details
- Get a single food by ID with full ingredient list
- Search foods by name (case-insensitive)
- List all food categories
- List all ingredients
- Get foods by category
- Get a random food
- Add a new food record with validation
- Bearer token authentication for /api routes
- Basic rate limiting for /api requests

## Optional API Enhancements

### Enhancement Description
Two new endpoints were added to improve client workflows and make the API more useful for a future UI:
- `GET /api/foods/random`
- `GET /api/categories/{id}/foods`

A new security improvement was also added:
- Basic rate limiting for API requests

### Files Modified for Enhancements
- `public/index.php`
- `README.md`
- `config.example.php`
- `.gitignore`

### Endpoints Added
- `GET /api/foods/random`
- `GET /api/categories/{id}/foods`

### Security Features Implemented
- Bearer token authentication for `/api` routes
- Basic rate limiting for API requests

## Repository Contents
The repository contains:
- `public/` — API entry file and route definitions
- `composer.json` — PHP dependency definitions
- `config.example.php` — example configuration file
- `filipino_cookbook_api.sql` — SQL file for database setup
- `README.md` — full installation and API documentation
- `.gitignore` — files excluded from Git tracking
- `Screenshots/` — evidence of successful endpoint testing

## Installation Instructions
Follow these exact steps to install and run the API locally.

### 1. Clone the repository
```bash
git clone https://github.com/Yelsxii/filipino-cookbook-api-gomez.git
cd filipino-cookbook-api-gomez
```

### 2. Install dependencies
If Composer is installed:
```bash
composer install
```
If Composer is not installed, install it first from https://getcomposer.org.

### 3. Create local configuration
Copy the example config file and then open `config.php` in a text editor.

Windows:
```powershell
copy config.example.php config.php
```

Linux / macOS:
```bash
cp config.example.php config.php
```

Update `config.php` with your local database credentials. Example:
```php
return [
    'db_host' => 'localhost',
    'db_name' => 'filipino_cookbook_api',
    'db_user' => 'root',
    'db_pass' => '',
    'api_token' => 'dmmmsu-cookbook-token-2026',
];
```
Do not commit `config.php` to Git.

### 4. Create and import the database
Open phpMyAdmin or run the MySQL command line. Create the database:
```sql
CREATE DATABASE filipino_cookbook_api;
```
Then import the SQL file:
```bash
mysql -u YOUR_DB_USER -p filipino_cookbook_api < filipino_cookbook_api.sql
```

### 5. Start the API server
From the project root directory:
```bash
php -S localhost:8000 -t public
```

### 6. Confirm the API is running
Open in a browser or Postman:
- `http://localhost:8000/`
You should see a JSON welcome message.

## Database Setup
- Database name: `filipino_cookbook_api`
- SQL file: `filipino_cookbook_api.sql`
- Tables:
  - `categories`
  - `origins`
  - `foods`
  - `ingredients`
  - `food_ingredients`

### Table Relationships
- `categories -> foods <- origins`
- `foods -> food_ingredients <- ingredients`

## Base URL
For local use:
- `http://localhost:8000`

API endpoints begin with `/api`.

## Authentication Instructions
All `/api` requests require a Bearer token header.

Header:
```text
Authorization: Bearer dmmmsu-cookbook-token-2026
```

If the token is missing or invalid, the API returns:
- `401 Unauthorized`
- JSON body: `{"status":"error","message":"Unauthorized access. Valid API token is required."}`

## Full Endpoint Documentation
Each endpoint is documented below with exact request details.

---

### GET /api/foods
Description: Returns all foods in the database with category, origin, instructions, and ingredient list.

Required headers:
- `Authorization: Bearer dmmmsu-cookbook-token-2026`
- `Accept: application/json`

Example request:
```bash
curl -H "Authorization: Bearer dmmmsu-cookbook-token-2026" http://localhost:8000/api/foods
```

Example successful response:
```json
[
  {
    "food_id": 11,
    "food_name": "Lumpiang Shanghai",
    "category_name": "Appetizer",
    "origin_name": "Philippines",
    "instructions": "Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.",
    "ingredients": ["Carrots","Egg","Garlic","Ground pork","Onion","Spring roll wrapper"]
  }
]
```

Possible errors:
- `401` if token is missing or invalid
- `429` if rate limit is exceeded

---

### GET /api/foods/{id}
Description: Returns the full details for one food item, including ingredient names.

Path parameter:
- `id` — numeric food ID

Example request:
```bash
curl -H "Authorization: Bearer dmmmsu-cookbook-token-2026" http://localhost:8000/api/foods/11
```

Example successful response:
```json
{
  "food_id": 11,
  "food_name": "Lumpiang Shanghai",
  "category_name": "Appetizer",
  "origin_name": "Philippines",
  "instructions": "Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.",
  "ingredients": ["Carrots","Egg","Garlic","Ground pork","Onion","Spring roll wrapper"]
}
```

Error responses:
- `404` if the food ID does not exist
- `401` for missing/invalid token

---

### GET /api/foods/search/{name}
Description: Finds foods whose name contains the search term.

Path parameter:
- `name` — food search term

Example request:
```bash
curl -H "Authorization: Bearer dmmmsu-cookbook-token-2026" http://localhost:8000/api/foods/search/adobo
```

Example response:
```json
[
  {
    "food_id": 12,
    "food_name": "Adobo",
    "category_name": "Main Dish",
    "origin_name": "Philippines",
    "instructions": "Cook pork with soy sauce, vinegar, garlic, and bay leaves.",
    "ingredients": ["Garlic","Bay leaves","Pork","Vinegar","Soy sauce"]
  }
]
```

---

### GET /api/categories
Description: Returns all food categories.

Example request:
```bash
curl -H "Authorization: Bearer dmmmsu-cookbook-token-2026" http://localhost:8000/api/categories
```

Example response:
```json
[
  {"category_id": 1, "category_name": "Appetizer"},
  {"category_id": 2, "category_name": "Soup"}
]
```

---

### GET /api/categories/{id}/foods
Description: Returns foods that belong to the selected category.

Path parameter:
- `id` — numeric category ID

Example request:
```bash
curl -H "Authorization: Bearer dmmmsu-cookbook-token-2026" http://localhost:8000/api/categories/1/foods
```

Example response:
```json
[
  {
    "food_id": 11,
    "food_name": "Lumpiang Shanghai",
    "category_name": "Appetizer",
    "origin_name": "Philippines",
    "instructions": "Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.",
    "ingredients": ["Carrots","Egg","Garlic","Ground pork","Onion","Spring roll wrapper"]
  }
]
```

Possible errors:
- `400` for invalid category ID
- `404` if the category does not exist

---

### GET /api/foods/random
Description: Returns one randomly selected food item.

Example request:
```bash
curl -H "Authorization: Bearer dmmmsu-cookbook-token-2026" http://localhost:8000/api/foods/random
```

Example response:
```json
{
  "food_id": 14,
  "food_name": "Bulalo",
  "category_name": "Soup",
  "origin_name": "Philippines",
  "instructions": "Boil beef shank and bone marrow until tender. Add corn and vegetables, then simmer before serving.",
  "ingredients": ["Beef shank","Bone marrow","Cabbage","Corn","Onion","Pechay","Peppercorn"]
}
```

---

### GET /api/ingredients
Description: Returns all ingredients in the database.

Example request:
```bash
curl -H "Authorization: Bearer dmmmsu-cookbook-token-2026" http://localhost:8000/api/ingredients
```

Example response:
```json
[
  {"ingredient_id": 1, "ingredient_name": "Bay leaves"},
  {"ingredient_id": 2, "ingredient_name": "Carrots"}
]
```

---

### POST /api/foods
Description: Adds a new food record with category, origin, instructions, and ingredients.

Required headers:
- `Authorization: Bearer dmmmsu-cookbook-token-2026`
- `Content-Type: application/json`

Example request:
```bash
curl -X POST http://localhost:8000/api/foods \
  -H "Authorization: Bearer dmmmsu-cookbook-token-2026" \
  -H "Content-Type: application/json" \
  -d '{
    "food_name": "New Dish",
    "category_id": 1,
    "origin_id": 1,
    "instructions": "Prepare and cook.",
    "ingredient_ids": [1, 2]
  }'
```

Success response:
```json
{
  "status": "success",
  "message": "Food added successfully."
}
```

Possible errors:
- `400` if required fields are missing or invalid
- `400` if category or origin does not exist
- `400` if any ingredient IDs are invalid
- `500` on database failure

## HTTP Status Codes
- `200` Request completed successfully
- `201` Resource created successfully
- `400` Invalid request or parameter
- `401` Missing or invalid authentication
- `404` Resource not found
- `429` Too many requests
- `500` Internal server error

## Testing Instructions
These are the exact steps another student should follow to verify the API.

1. Start the local server:
   ```bash
   php -S localhost:8000 -t public
   ```

2. Open Postman or Thunder Client.
3. Create a request and set the URL to one of the endpoints.
4. Add the Authorization header:
   - `Authorization: Bearer dmmmsu-cookbook-token-2026`
5. Send the request and confirm you receive JSON.

### Test cases to run
- `GET http://localhost:8000/api/foods`
- `GET http://localhost:8000/api/foods/11`
- `GET http://localhost:8000/api/foods/random`
- `GET http://localhost:8000/api/foods/search/lumpia`
- `GET http://localhost:8000/api/categories`
- `GET http://localhost:8000/api/categories/1/foods`
- `GET http://localhost:8000/api/ingredients`
- `POST http://localhost:8000/api/foods` with a valid JSON body
- `GET http://localhost:8000/api/foods` without the Authorization header to confirm `401`

### What to change in the local setup
Open `config.php` and update:
- `db_host`
- `db_name`
- `db_user`
- `db_pass`
- `api_token`

Use your own MySQL credentials and the same token value in requests.

## Testing Evidence

### Random Food Endpoint Test
![Random Food Endpoint Test](Screenshots/Random.png)

### Category Foods Endpoint Test
![Category Foods Endpoint Test](Screenshots/Category.png)

## Developer Information
- Student Name: Lizhary Ylexis Gomez
 - Course and Section: Information Technology - 4B
- GitHub Username: Yelsxii
- Repository Link: https://github.com/Yelsxii/filipino-cookbook-api-gomez.git
- Date Completed: 2026-07-22
