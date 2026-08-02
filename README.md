# Filipino Cookbook API

## API Title
Filipino Cookbook API

## API Description

> A secure and reusable REST API that transforms structured Filipino cookbook data into clear JSON resources for web, mobile, and other client applications.

The **Filipino Cookbook API** is built with PHP, the Slim Framework, and MySQL. It provides organized access to Filipino food records, categories, origins, cooking instructions, and ingredients through protected REST endpoints. The project is designed to demonstrate practical API development, database integration, request validation, authentication, and client-side consumption in an educational setting.

### API at a Glance

| Area | Description |
|---|---|
| **Primary purpose** | Provide structured Filipino cookbook data through RESTful JSON endpoints |
| **Data source** | A relational MySQL database containing foods, categories, origins, ingredients, and food-ingredient relationships |
| **Client access** | Web interfaces, mobile applications, desktop clients, and API testing tools |
| **Security** | Bearer token authentication for protected `/api` routes and basic request rate limiting |
| **Response format** | JSON responses with appropriate HTTP status codes and understandable messages |

### Purpose of the API

The API was developed to:

- make Filipino cookbook information accessible through consistent and reusable endpoints;
- support the development of separate driver or client applications;
- demonstrate how a PHP application can expose relational database records as JSON resources;
- apply authentication, validation, error handling, and rate limiting in a working REST API; and
- provide another student with a documented API that can be installed, tested, and integrated independently.

### Information Available

The API provides access to:

- Filipino food names and preparation instructions;
- food categories used to organize dishes;
- origins associated with food records;
- ingredient names and food-ingredient relationships; and
- complete food details that combine category, origin, instructions, and ingredients.

### Intended Users

This API is suitable for:

- students completing API integration and client-development activities;
- front-end developers building a Filipino cookbook interface;
- developers learning REST API consumption with PHP, JavaScript, or other client technologies; and
- users who need a structured and searchable source of Filipino cookbook data.

### Core Capabilities

Through its available endpoints, the API can:

- retrieve the complete food collection;
- return the full details of a specific food record;
- search foods by name;
- retrieve foods under a selected category;
- return a randomly selected Filipino food;
- list all categories and ingredients;
- create a new food record with related ingredients;
- validate incoming request data; and
- protect API resources using Bearer token authentication and basic rate limiting.

### Technology Stack

| Technology | Role in the Project |
|---|---|
| **PHP** | Implements the API logic and request handling |
| **Slim Framework** | Defines routes, middleware, and HTTP responses |
| **MySQL** | Stores foods, categories, origins, ingredients, and relationships |
| **Composer** | Manages PHP dependencies and autoloading |
| **JSON** | Provides the standard request and response data format |
| **XAMPP or PHP Built-in Server** | Runs the API in a local development environment |
| **Thunder Client or Postman** | Tests endpoints, headers, bodies, and responses |
| **Git and GitHub** | Supports version control, documentation, and repository sharing |

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

Two additional GET endpoints were added to improve client workflows and provide more flexible access to the cookbook data:

- `GET /api/foods/random`
- `GET /api/categories/{id}/foods`

A basic rate-limiting security feature was also implemented for protected `/api` routes.

### Files Modified for Enhancements

- `public/index.php`

### Endpoints Added

- `GET /api/foods/random` — returns one randomly selected Filipino food
- `GET /api/categories/{id}/foods` — returns the foods assigned to a selected category

### Security Feature Implemented

- Basic rate limiting per client IP address
- Maximum of 60 requests within a 60-second window
- Requests beyond the limit return `429 Too Many Requests`

### Testing the Enhancements

Start the API from the project root before testing:

```bash
php -S 127.0.0.1:8080 -t public
```

For protected endpoints, include these headers:

```text
Authorization: Bearer YOUR_API_TOKEN
Accept: application/json
```

#### Test the Random Food Endpoint

- **Method:** `GET`
- **URL:** `http://127.0.0.1:8080/api/foods/random`
- **Expected status:** `200 OK`
- **Expected result:** One complete food record is returned. Repeating the request may return a different food.

Example response:

```json
{
  "food_id": 14,
  "food_name": "Bulalo",
  "category_name": "Soup",
  "origin_name": "Philippines",
  "instructions": "Boil beef shank and bone marrow until tender. Add corn and vegetables, then simmer before serving.",
  "ingredients": [
    "Beef shank",
    "Bone marrow",
    "Cabbage",
    "Corn",
    "Onion",
    "Pechay",
    "Peppercorn"
  ]
}
```

#### Test the Foods-by-Category Endpoint

- **Method:** `GET`
- **URL:** `http://127.0.0.1:8080/api/categories/1/foods`
- **Path parameter:** Replace `1` with an existing category ID.
- **Expected status:** `200 OK`
- **Expected result:** Only foods assigned to the selected category are returned.

Example response:

```json
[
  {
    "food_id": 11,
    "food_name": "Lumpiang Shanghai",
    "category_name": "Appetizer",
    "origin_name": "Philippines",
    "instructions": "Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.",
    "ingredients": [
      "Carrots",
      "Egg",
      "Garlic",
      "Ground pork",
      "Onion",
      "Spring roll wrapper"
    ]
  }
]
```

#### Test the Rate Limiter

The following procedure verifies that the first 60 requests from the same client are accepted within the active window and that the next request is rejected with HTTP `429`.

1. Keep the API server running in one terminal.
2. Open a second PowerShell terminal in the project root.
3. Run the command below. It resets the previous local rate-limit counter, reads the token from `config.php`, and sends exactly 60 authenticated requests to `GET /api/categories`.

```powershell
Remove-Item "$env:TEMP\filipino_cookbook_rate_limit.json" -ErrorAction SilentlyContinue; $token = (php -r '$c = require "config.php"; echo $c["api_token"] ?? "";').Trim(); 1..60 | ForEach-Object { $code = curl.exe -s -o NUL -w "%{http_code}" -H "Authorization: Bearer $token" -H "Accept: application/json" "http://127.0.0.1:8080/api/categories"; Write-Host "Request $_ - Status $code" }
```

The terminal should end with results similar to:

```text
Request 58 - Status 200
Request 59 - Status 200
Request 60 - Status 200
```

4. Immediately create or open this request in Thunder Client:

```text
GET http://127.0.0.1:8080/api/categories
```

5. Add the same valid Bearer token and click **Send** once. This request becomes the 61st request in the current window.

Expected status:

```text
429 Too Many Requests
```

Expected response:

```json
{
  "status": "error",
  "message": "Too many requests. Please try again later."
}
```

After testing, either wait for the 60-second window to expire or reset the local counter:

```powershell
Remove-Item "$env:TEMP\filipino_cookbook_rate_limit.json" -ErrorAction SilentlyContinue
```

### Enhancement Testing Screenshots

Random food endpoint:

![Random Food Endpoint Test](Screenshots/Random.png)

Foods-by-category endpoint:

![Category Foods Endpoint Test](Screenshots/Category.png)

Rate-limit security test:

![Rate Limit Exceeded](Screenshots/RateLimit429.png)

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
    'db_name' => 'filipino_cookbook_api_surname',
    'db_user' => 'root',
    'db_pass' => '',
    'api_token' => 'YOUR_API_TOKEN',
];
```


### 4. Create and import the database
Create the database and import the SQL file using either phpMyAdmin or the MySQL command-line client.

#### Option 1: Use phpMyAdmin
- Open phpMyAdmin.
- Create a new database named `filipino_cookbook_api_surname`.
- Select the new database.
- Open the **Import** tab.
- Upload the `filipino_cookbook_api.sql` file from the project folder.
- Run the import.

#### Option 2: Use the MySQL command line
Create the database:
```sql
CREATE DATABASE filipino_cookbook_api_surname;
```
Then run this command from the project folder:
```bash
mysql -u YOUR_DB_USER -p filipino_cookbook_api_surname < filipino_cookbook_api.sql
```

### 5. Start the API server
From the project root directory:
```bash
php -S 127.0.0.1:8080 -t public
```

### 6. Confirm the API is running
Open in a browser or Postman:
- `http://127.0.0.1:8080/` (this will redirect to the welcome endpoint)
- `http://127.0.0.1:8080/api`
You should see a JSON welcome message.

## Database Setup
- Database name: `filipino_cookbook_api_surname`
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

The API can be run through the PHP built-in server or through a local web server such as XAMPP. Use the base URL that matches your local setup.

### Recommended local setup

From the project root, start the PHP development server:

```bash
php -S 127.0.0.1:8080 -t public
```

Use this base URL for all API requests:

```text
http://127.0.0.1:8080/api
```

### Alternative XAMPP setup

When the project is placed inside the XAMPP `htdocs` directory, the base URL may be:

```text
http://localhost/filipino-cookbook-api/public/api
```

Replace `filipino-cookbook-api` with the actual folder name when necessary.

### URL structure

| Request | Example |
|---|---|
| Welcome endpoint | `http://127.0.0.1:8080/api` |
| All foods | `http://127.0.0.1:8080/api/foods` |
| One food by ID | `http://127.0.0.1:8080/api/foods/11` |
| Search by food name | `http://127.0.0.1:8080/api/foods/search/adobo` |
| Foods under one category | `http://127.0.0.1:8080/api/categories/1/foods` |

All documented routes begin with `/api`. Keep the server running while testing the endpoints or connecting a client application.

## Authentication Instructions

The welcome endpoint is public. All other `/api` routes are protected by Bearer token authentication.

### 1. Configure the token

Open the local `config.php` file and assign a token value:

```php
return [
    // Database settings...
    'api_token' => 'YOUR_API_TOKEN',
];
```

The actual `config.php` file should remain private and must not be uploaded when it contains real credentials. Use `config.example.php` to show the required configuration structure.

### 2. Send the Authorization header

Add the following header to every protected request:

```text
Authorization: Bearer YOUR_API_TOKEN
```

For JSON responses, also include:

```text
Accept: application/json
```

For `POST /api/foods`, include:

```text
Content-Type: application/json
```

### 3. Thunder Client or Postman setup

| Field | Value |
|---|---|
| Header name | `Authorization` |
| Header value | `Bearer YOUR_API_TOKEN` |
| Response format | `Accept: application/json` |
| POST body format | `Content-Type: application/json` |

The word `Bearer` and the token must be separated by one space.

### Authentication failure

A missing or incorrect token returns:

```text
401 Unauthorized
```

```json
{
  "status": "error",
  "message": "Unauthorized access. Valid API token is required."
}
```

## Full Endpoint Documentation

### Endpoint overview

| Method | Endpoint | Authentication | Purpose |
|---|---|---|---|
| `GET` | `/api` | Public | Confirm that the API is running |
| `GET` | `/api/foods` | Bearer token | Retrieve all foods |
| `GET` | `/api/foods/{id}` | Bearer token | Retrieve one food by ID |
| `GET` | `/api/foods/search/{name}` | Bearer token | Search foods by name |
| `GET` | `/api/categories` | Bearer token | Retrieve all categories |
| `GET` | `/api/categories/{id}/foods` | Bearer token | Retrieve foods in one category |
| `GET` | `/api/foods/random` | Bearer token | Retrieve one random food |
| `GET` | `/api/ingredients` | Bearer token | Retrieve all ingredients |
| `POST` | `/api/foods` | Bearer token | Create a new food record |

The examples below use the recommended local base URL:

```text
http://127.0.0.1:8080/api
```

---

### `GET /api`

Returns the public welcome message and confirms that the API server is available.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api` |
| Authentication | Not required |
| Required header | `Accept: application/json` |
| Success status | `200 OK` |

**Example response:**

```json
{
  "message": "Welcome to the Secured Filipino Cookbook API",
  "note": "Use a valid Bearer token to access /api endpoints."
}
```


---

### `GET /api/foods`

Returns the complete food collection with category, origin, preparation instructions, and ingredient names.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/foods` |
| Authentication | Bearer token required |
| Success status | `200 OK` |
| Possible errors | `401 Unauthorized`, `429 Too Many Requests` |

**Example response:**

```json
[
  {
    "food_id": 11,
    "food_name": "Lumpiang Shanghai",
    "category_name": "Appetizer",
    "origin_name": "Philippines",
    "instructions": "Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.",
    "ingredients": [
      "Carrots",
      "Egg",
      "Garlic",
      "Ground pork",
      "Onion",
      "Spring roll wrapper"
    ]
  }
]
```


---

### `GET /api/foods/{id}`

Returns the complete information for one food record.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/foods/11` |
| Authentication | Bearer token required |
| Path parameter | `id` — numeric food ID |
| Success status | `200 OK` |
| Possible errors | `401 Unauthorized`, `404 Not Found`, `429 Too Many Requests` |

**Example successful response:**

```json
{
  "food_id": 11,
  "food_name": "Lumpiang Shanghai",
  "category_name": "Appetizer",
  "origin_name": "Philippines",
  "instructions": "Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.",
  "ingredients": [
    "Carrots",
    "Egg",
    "Garlic",
    "Ground pork",
    "Onion",
    "Spring roll wrapper"
  ]
}
```


**Example not-found response:**

```json
{
  "status": "error",
  "message": "Food not found"
}
```


---

### `GET /api/foods/search/{name}`

Performs a case-insensitive search and returns foods whose names contain the supplied term.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/foods/search/adobo` |
| Authentication | Bearer token required |
| Path parameter | `name` — food name or partial search term |
| Success status | `200 OK` |
| Possible errors | `401 Unauthorized`, `429 Too Many Requests` |

**Example response:**

```json
[
  {
    "food_id": 12,
    "food_name": "Adobo",
    "category_name": "Main Dish",
    "origin_name": "Philippines",
    "instructions": "Cook pork with soy sauce, vinegar, garlic, and bay leaves.",
    "ingredients": [
      "Garlic",
      "Bay leaves",
      "Pork",
      "Vinegar",
      "Soy sauce"
    ]
  }
]
```


---

### `GET /api/categories`

Returns all available food categories.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/categories` |
| Authentication | Bearer token required |
| Success status | `200 OK` |
| Possible errors | `401 Unauthorized`, `429 Too Many Requests` |

**Example response:**

```json
[
  {
    "category_id": 1,
    "category_name": "Appetizer"
  },
  {
    "category_id": 2,
    "category_name": "Dessert"
  }
]
```


---

### `GET /api/categories/{id}/foods`

Returns only the foods assigned to the selected category.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/categories/1/foods` |
| Authentication | Bearer token required |
| Path parameter | `id` — numeric category ID |
| Success status | `200 OK` |
| Possible errors | `400 Bad Request`, `401 Unauthorized`, `404 Not Found`, `429 Too Many Requests` |

**Example response:**

```json
[
  {
    "food_id": 11,
    "food_name": "Lumpiang Shanghai",
    "category_name": "Appetizer",
    "origin_name": "Philippines",
    "instructions": "Mix ground pork, vegetables, and egg. Wrap in spring roll wrappers and deep-fry until golden brown.",
    "ingredients": [
      "Carrots",
      "Egg",
      "Garlic",
      "Ground pork",
      "Onion",
      "Spring roll wrapper"
    ]
  }
]
```


**Example invalid-parameter response:**

```json
{
  "status": "error",
  "message": "Invalid category_id. Please provide a valid category id."
}
```


---

### `GET /api/foods/random`

Returns one complete food record selected randomly from the database.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/foods/random` |
| Authentication | Bearer token required |
| Success status | `200 OK` |
| Possible errors | `401 Unauthorized`, `404 Not Found`, `429 Too Many Requests` |

**Example response:**

```json
{
  "food_id": 14,
  "food_name": "Bulalo",
  "category_name": "Soup",
  "origin_name": "Philippines",
  "instructions": "Boil beef shank and bone marrow until tender. Add corn and vegetables, then simmer before serving.",
  "ingredients": [
    "Beef shank",
    "Bone marrow",
    "Cabbage",
    "Corn",
    "Onion",
    "Pechay",
    "Peppercorn"
  ]
}
```


---

### `GET /api/ingredients`

Returns all ingredients available in the database.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/ingredients` |
| Authentication | Bearer token required |
| Success status | `200 OK` |
| Possible errors | `401 Unauthorized`, `429 Too Many Requests` |

**Example response:**

```json
[
  {
    "ingredient_id": 1,
    "ingredient_name": "Bay leaves"
  },
  {
    "ingredient_id": 2,
    "ingredient_name": "Carrots"
  }
]
```


---

### `POST /api/foods`

Creates a new food record and stores its selected ingredient relationships.

| Item | Details |
|---|---|
| Full URL | `http://127.0.0.1:8080/api/foods` |
| Authentication | Bearer token required |
| Content type | `application/json` |
| Success status | `201 Created` |
| Possible errors | `400 Bad Request`, `401 Unauthorized`, `429 Too Many Requests`, `500 Internal Server Error` |

#### Request body

| Field | Type | Description |
|---|---|---|
| `food_name` | String | Name of the food |
| `category_id` | Integer | Existing category ID |
| `origin_id` | Integer | Existing origin ID |
| `instructions` | String | Food preparation instructions |
| `ingredient_ids` | Integer array | One or more existing ingredient IDs |

```json
{
  "food_name": "New Dish",
  "category_id": 1,
  "origin_id": 1,
  "instructions": "Prepare the ingredients and cook until done.",
  "ingredient_ids": [1, 2]
}
```

**Example successful response:**

```json
{
  "status": "success",
  "message": "Food added successfully."
}
```


**Example validation error:**

```json
{
  "status": "error",
  "message": "Invalid or missing input data. Please check all required fields."
}
```



## HTTP Status Codes
- `200` Request completed successfully
- `201` Resource created successfully
- `400` Invalid request or parameter
- `401` Missing or invalid authentication
- `404` Resource not found
- `429` Too many requests
- `500` Internal server error

## Testing Instructions

Follow these steps to verify the API after installation.

### 1. Start the API

From the project root:

```bash
php -S 127.0.0.1:8080 -t public
```

### 2. Choose a Testing Tool

The endpoints may be tested using:

- Thunder Client
- Postman
- `curl` or PowerShell

### 3. Configure Protected Requests

All protected `/api` requests require:

```text
Authorization: Bearer YOUR_API_TOKEN
Accept: application/json
```

For `POST /api/foods`, also include:

```text
Content-Type: application/json
```

The token must match the `api_token` value in the local `config.php` file.

### 4. Run the Endpoint Tests

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | `/api` | Confirm that the API is running |
| `GET` | `/api/foods` | Retrieve all foods |
| `GET` | `/api/foods/{id}` | Retrieve one food by ID |
| `GET` | `/api/foods/search/{name}` | Search foods by name |
| `GET` | `/api/categories` | Retrieve all categories |
| `GET` | `/api/categories/{id}/foods` | Retrieve foods by category |
| `GET` | `/api/foods/random` | Retrieve one random food |
| `GET` | `/api/ingredients` | Retrieve all ingredients |
| `POST` | `/api/foods` | Add a new food record |

For each request, verify that:

- the expected HTTP status code is returned;
- the response has `Content-Type: application/json`;
- the response body follows the documented JSON structure;
- protected endpoints reject missing or invalid tokens;
- invalid parameters and missing records return clear error messages.

### 5. Verify the Rate Limiter

Use the procedure under **Optional API Enhancements → Test the Rate Limiter**. The first 60 rapid requests should return `200`, and the next request within the same 60-second window should return `429 Too Many Requests`.

### 6. Local Configuration

Create `config.php` before running the API:

Windows:

```powershell
copy config.example.php config.php
```

Linux or macOS:

```bash
cp config.example.php config.php
```

Update these values in `config.php`:

- `db_host`
- `db_name`
- `db_user`
- `db_pass`
- `api_token`

Use the same configured token when sending authenticated requests.

## Testing Evidence

The screenshots below demonstrate successful endpoint responses, optional enhancements, authentication, validation, error handling, and rate limiting. Each screenshot should show the request method, URL, HTTP status, and JSON response while keeping private credentials out of view.

### Successful Endpoint Tests

#### 1. API Welcome Endpoint

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api` |
| Authentication | Not required |
| Expected status | `200 OK` |
| Expected result | The API returns its welcome message and confirms that protected endpoints require a valid token. |

![API Welcome Endpoint](Screenshots/Welcome.png)

*Evidence: The public welcome endpoint responded successfully and confirmed that the API server was running.*

#### 2. Retrieve All Foods

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api/foods` |
| Expected status | `200 OK` |
| Expected result | The response contains the food collection with category, origin, instructions, and ingredient data. |

![Retrieve All Foods](Screenshots/Foods.png)

*Evidence: The API returned the complete food collection in JSON format.*

#### 3. Retrieve One Food by ID

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api/foods/1` |
| Expected status | `200 OK` |
| Expected result | The response contains the complete record for the selected food ID. |

![Retrieve Food by ID](Screenshots/FoodDetails.png)

*Evidence: A valid food ID returned one food record with its full ingredient list.*

#### 4. Search Foods by Name

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api/foods/search/adobo` |
| Expected status | `200 OK` |
| Expected result | The response contains foods whose names match the supplied search term. |

![Search Foods by Name](Screenshots/FoodSearch.png)

*Evidence: The search endpoint returned matching food records using a case-insensitive name search.*

#### 5. Retrieve All Categories

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api/categories` |
| Expected status | `200 OK` |
| Expected result | The response contains all available food categories. |

![Retrieve All Categories](Screenshots/Categories.png)

*Evidence: The API returned the complete category list in JSON format.*

#### 6. Retrieve All Ingredients

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api/ingredients` |
| Expected status | `200 OK` |
| Expected result | The response contains all ingredient records. |

![Retrieve All Ingredients](Screenshots/Ingredients.png)

*Evidence: The API returned the complete ingredient list in JSON format.*

#### 7. Add a New Food

| Test detail | Value |
|---|---|
| Request | `POST http://127.0.0.1:8080/api/foods` |
| Expected status | `201 Created` |
| Expected result | A new food record and its ingredient relationships are inserted successfully. |

Use a raw JSON body containing valid category, origin, and ingredient IDs:

```json
{
  "food_name": "API Test Dish",
  "category_id": 4,
  "origin_id": 4,
  "instructions": "Prepare the ingredients and cook until done.",
  "ingredient_ids": [18, 26, 40]
}
```

Use a unique `food_name` when repeating the test.

![Add a New Food](Screenshots/AddFood.png)

*Evidence: The API accepted valid JSON input and returned a successful resource-creation response.*

### Optional Enhancement Tests

#### 8. Retrieve a Random Food

| Test detail | Value |
|---|---|
| Enhancement | Additional GET endpoint |
| Request | `GET http://127.0.0.1:8080/api/foods/random` |
| Expected status | `200 OK` |
| Expected result | One complete food record is selected and returned. |

![Random Food Endpoint Test](Screenshots/Random.png)

*Evidence: The optional endpoint returned one randomly selected food with its complete details.*

#### 9. Retrieve Foods by Category

| Test detail | Value |
|---|---|
| Enhancement | Additional GET endpoint |
| Request | `GET http://127.0.0.1:8080/api/categories/1/foods` |
| Expected status | `200 OK` |
| Expected result | Only foods assigned to category ID `1` are returned. |

![Category Foods Endpoint Test](Screenshots/Category.png)

*Evidence: The optional endpoint filtered the food collection using the selected category ID.*

#### 10. Rate-Limit Security Test

| Test detail | Value |
|---|---|
| Enhancement | Security improvement |
| Endpoint used | `GET http://127.0.0.1:8080/api/categories` |
| Configured limit | 60 requests per client IP within 60 seconds |
| Expected result | Requests 1–60 return `200`; the next request returns `429 Too Many Requests`. |

Run this command from a second Windows PowerShell terminal while the API server remains active:

```powershell
Remove-Item "$env:TEMP\filipino_cookbook_rate_limit.json" -ErrorAction SilentlyContinue; $token = (php -r '$c = require "config.php"; echo $c["api_token"] ?? "";').Trim(); 1..60 | ForEach-Object { $code = curl.exe -s -o NUL -w "%{http_code}" -H "Authorization: Bearer $token" -H "Accept: application/json" "http://127.0.0.1:8080/api/categories"; Write-Host "Request $_ - Status $code" }
```

Immediately send one additional authenticated `GET /api/categories` request in Thunder Client.

Expected response:

```json
{
  "status": "error",
  "message": "Too many requests. Please try again later."
}
```

![Rate Limit Exceeded](Screenshots/RateLimit429.png)

*Evidence: The rate limiter blocked the request that exceeded the configured 60-request window and returned HTTP 429.*

### Common Error Response Tests

#### 11. Missing or Invalid Bearer Token

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api/foods` |
| Test condition | Remove the `Authorization` header or provide an incorrect token. |
| Expected status | `401 Unauthorized` |

Expected response:

```json
{
  "status": "error",
  "message": "Unauthorized access. Valid API token is required."
}
```

![Unauthorized Request](Screenshots/Error401.png)

*Evidence: The API rejected a protected request that did not contain the correct Bearer token.*

#### 12. Food Record Not Found

| Test detail | Value |
|---|---|
| Request | `GET http://127.0.0.1:8080/api/foods/99999` |
| Test condition | Use a food ID that does not exist while keeping a valid Bearer token. |
| Expected status | `404 Not Found` |

Expected response:

```json
{
  "status": "error",
  "message": "Food not found"
}
```

![Food Not Found](Screenshots/Error404.png)

*Evidence: The API returned a clear not-found response for a missing food record.*

#### 13. Invalid or Incomplete Food Data

| Test detail | Value |
|---|---|
| Request | `POST http://127.0.0.1:8080/api/foods` |
| Test condition | Omit a required field such as `ingredient_ids`. |
| Expected status | `400 Bad Request` |

Invalid example body:

```json
{
  "food_name": "Incomplete Test Dish",
  "category_id": 4,
  "origin_id": 4,
  "instructions": "This request intentionally has no ingredient IDs."
}
```

Expected response:

```json
{
  "status": "error",
  "message": "Please provide food_name, category_id, origin_id, instructions, and ingredient_ids."
}
```

![Invalid Food Data](Screenshots/Error400.png)

*Evidence: The API validated the required fields and rejected incomplete input before database insertion.*

### Evidence Summary

The screenshots verify:

- successful responses from every documented endpoint;
- successful testing of both optional GET endpoints;
- correct Bearer token authentication;
- valid JSON response formatting;
- successful resource creation;
- input validation and not-found handling;
- enforcement of the 60-request rate limit through an HTTP `429` response.

## Developer Information
- Student Name: Lizhary Ylexis Gomez
 - Course and Section: Information Technology - 4B
- GitHub Username: Yelsxii
- Repository Link: https://github.com/Yelsxii/filipino-cookbook-api-gomez.git
- Date Completed: 2026-08-02
