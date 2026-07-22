# Filipino Cookbook API

## API Title
Filipino Cookbook API

## API Description
This project is a Slim PHP REST API for managing Filipino foods, categories, origins, ingredients, and food-related records. It is intended for students and developers who want to learn how a simple token-protected REST API can be built and consumed by another application.

### Purpose of the API
- Provide access to Filipino food data through JSON endpoints
- Support future UI or client applications
- Demonstrate API development using PHP and Slim Framework

### Type of Information Provided
- Filipino food items
- Food categories
- Origins
- Ingredients
- Food details and instructions

### Intended Users
- Students
- Developers
- Front-end clients consuming the API

### Main Functions of the API
- Retrieve foods
- Retrieve a specific food by ID
- Search foods by name
- Retrieve foods by category
- Retrieve a random food
- Authenticate requests with a Bearer token

### Technologies Used
- PHP
- Slim Framework
- MySQL
- Composer
- JSON
- XAMPP
- Thunder Client or Postman
- Git and GitHub

## Features
- List all foods
- Get a single food by ID
- Search foods by name
- List categories and ingredients
- Add a new food with validation
- Protect API routes with Bearer token authentication
- Apply basic rate limiting to API requests

## Optional API Enhancements

### Description
This project includes two new API endpoints and one basic security enhancement to improve the API for future front-end or UI use.

### Purpose
The enhancements make the API more useful for clients by allowing them to:
- retrieve a random food
- view foods under a specific category

The security enhancement also helps protect the API from excessive requests.

### Files Modified
- public/index.php
- README.md
- config.example.php
- .gitignore

### Endpoints Added
- GET /api/foods/random
- GET /api/categories/{id}/foods

### Security Features Implemented
- Bearer token authentication for /api routes
- Basic rate limiting for API requests

## Installation Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/USERNAME/filipino-cookbook-api-surname.git
   cd filipino-cookbook-api-surname
   ```

2. Install Composer dependencies:
   ```bash
   composer install
   ```

3. Create a local configuration file:
   ```bash
   copy config.example.php config.php
   ```
   Then update the values in config.php with your local MySQL credentials and token.

4. Create and import the database:
   - Create a MySQL database named `filipino_cookbook_api`
   - Import the SQL file `filipino_cookbook_api.sql`

5. Start the local server:
   ```bash
   php -S localhost:8000 -t public
   ```

## Database Setup
- Database name: `filipino_cookbook_api`
- SQL file: `filipino_cookbook_api.sql`
- Main tables:
  - `categories`
  - `origins`
  - `foods`
  - `ingredients`
  - `food_ingredients`

### Table Relationships
- `categories -> foods <- origins`
- `foods -> food_ingredients <- ingredients`

## Base URL
- http://localhost:8000

## Authentication Instructions
Send the token in the Authorization header:

```text
Authorization: Bearer YOUR_ACCESS_TOKEN
```

If the token is missing or invalid, the API returns a 401 response.

## Endpoint Documentation

### GET /api/foods
Returns all foods stored in the database.

### GET /api/foods/{id}
Returns the details of a specific food by ID.

### GET /api/foods/search/{name}
Searches foods by name.

### GET /api/categories
Returns all categories.

### GET /api/categories/{id}/foods
Returns all foods under a specific category.

### GET /api/foods/random
Returns one randomly selected food.

### GET /api/ingredients
Returns all ingredients.

### POST /api/foods
Adds a new food record.

## Example Requests

### Random Food
```bash
curl -H "Authorization: Bearer YOUR_ACCESS_TOKEN" http://localhost:8000/api/foods/random
```

### Foods by Category
```bash
curl -H "Authorization: Bearer YOUR_ACCESS_TOKEN" http://localhost:8000/api/categories/1/foods
```

## HTTP Status Codes
- 200: Request completed successfully
- 400: Invalid request or parameter
- 401: Missing or invalid authentication
- 404: Requested resource was not found
- 429: Too many requests
- 500: Internal server error

## Testing Instructions
1. Start the local server:
   ```bash
   php -S localhost:8000 -t public
   ```

2. Use the Bearer token in the Authorization header.

3. Test the endpoints:
   - http://localhost:8000/api/foods/random
   - http://localhost:8000/api/categories/1/foods

4. Test unauthorized access without the token to confirm a 401 response.

## Testing Evidence

### Random Food Endpoint Test
![Random Food Endpoint Test](Screenshots/Random.png)

### Category Foods Endpoint Test
![Category Foods Endpoint Test](Screenshots/Category.png)

## Developer Information
- Student Name: Your Name
- Course and Section: Your Course/Section
- GitHub Username: Your GitHub Username
- Repository Link: https://github.com/Yelsxii/filipino-cookbook-api-gomez.git
- Date Completed: 2026-07-22
