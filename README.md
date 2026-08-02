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
If you run the PHP built-in server from the project root, the base URL is:
- `http://127.0.0.1:8080/api`

Welcome endpoint:
- `GET http://127.0.0.1:8080/api`

A sample endpoint URL is:
- `http://127.0.0.1:8080/api/foods/random`

If you run the project under a local web server like XAMPP and the repository is placed inside `htdocs`, the base URL can be:
- `http://localhost/filipino-cookbook-api/public/api`

API endpoints begin with `/api`.

## Authentication Instructions
All `/api` requests require a Bearer token header.

Header:
```text
Authorization: Bearer YOUR_API_TOKEN
```

If the token is missing or invalid, the API returns:
- `401 Unauthorized`
- JSON body: `{"status":"error","message":"Unauthorized access. Valid API token is required."}`

## Full Endpoint Documentation
Each endpoint is documented below with exact request details.

---

### GET /api
**Endpoint:**
GET /api

**Description:**
Returns the public welcome message for the API.

**Required headers:**
- No authentication required for this endpoint
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api`
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
```json
{
  "message": "Welcome to the Secured Filipino Cookbook API",
  "note": "Use a valid Bearer token to access /api endpoints."
}
```

---

### GET /api/foods
**Endpoint:**
GET /api/foods

**Description:**
Returns all foods in the database with category, origin, instructions, and ingredient list.

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api/foods

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api/foods`
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
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

**Example error response:**
```json
{
  "status": "error",
  "message": "Unauthorized access. Valid API token is required."
}
```

Possible errors:
- `401` if token is missing or invalid
- `429` if rate limit is exceeded

---

### GET /api/foods/{id}
**Endpoint:**
GET /api/foods/{id}

**Description:**
Returns the full details for one food item, including ingredient names.

**Path parameter:**
- `id` — numeric food ID

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api/foods/11 (replace `11` with the desired id)

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api/foods/11` (replace `11` with the desired id)
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
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

**Example error response:**
```json
{
  "status": "error",
  "message": "Food not found"
}
```

Error responses:
- `404` if the food ID does not exist
- `401` for missing/invalid token

---

### GET /api/foods/search/{name}
**Endpoint:**
GET /api/foods/search/{name}
**Description:**
Finds foods whose name contains the search term.

**Path parameter:**
- `name` — food search term

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api/foods/search/adobo (replace `adobo` with your search term)

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api/foods/search/adobo` (replace `adobo` with your search term)
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
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

**Example error response:**
```json
{
  "status": "error",
  "message": "Unauthorized access. Valid API token is required."
}
```

---

### GET /api/categories
**Endpoint:**
GET /api/categories
**Description:**
Returns all food categories.

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api/categories

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api/categories`
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
```json
[
  {"category_id": 1, "category_name": "Appetizer"},
  {"category_id": 2, "category_name": "Soup"}
]
```

**Example error response:**
```json
{
  "status": "error",
  "message": "Unauthorized access. Valid API token is required."
}
```

---

### GET /api/categories/{id}/foods
**Endpoint:**
GET /api/categories/{id}/foods
**Description:**
Returns foods that belong to the selected category.

**Path parameter:**
- `id` — numeric category ID

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api/categories/1/foods (replace `1` with a valid category id)

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api/categories/1/foods` (replace `1` with a valid category id)
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
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

**Example error response:**
```json
{
  "status": "error",
  "message": "Invalid category_id. Please provide a valid category id."
}
```

Possible errors:
- `400` for invalid category ID
- `404` if the category does not exist

---

### GET /api/foods/random
**Endpoint:**
GET /api/foods/random
**Description:**
Returns one randomly selected food item.

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api/foods/random

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api/foods/random`
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
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

**Example error response:**
```json
{
  "status": "error",
  "message": "Unauthorized access. Valid API token is required."
}
```

---

### GET /api/ingredients
**Endpoint:**
GET /api/ingredients
**Description:**
Returns all ingredients in the database.

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Accept: application/json`

**Example request:**
http://127.0.0.1:8080/api/ingredients

Thunder Client / Postman steps:
- Create a new request → Method: `GET` → URL: `http://127.0.0.1:8080/api/ingredients`
- Click **Send** and inspect the JSON response shown below.

**Example successful response:**
```json
[
  {"ingredient_id": 1, "ingredient_name": "Bay leaves"},
  {"ingredient_id": 2, "ingredient_name": "Carrots"}
]
```

**Example error response:**
```json
{
  "status": "error",
  "message": "Unauthorized access. Valid API token is required."
}
```

---

### POST /api/foods
**Endpoint:**
POST /api/foods
**Description:**
Adds a new food record with category, origin, instructions, and ingredients.

**Required headers:**
- `Authorization: Bearer YOUR_API_TOKEN`
- `Content-Type: application/json`

**Example request:**
http://127.0.0.1:8080/api/foods

Thunder Client / Postman steps:
- Create a new request → Method: `POST` → URL: `http://127.0.0.1:8080/api/foods`
- Body: select raw JSON and paste the payload below, then click **Send**.
  ```json
  {
    "food_name": "New Dish",
    "category_id": 1,
    "origin_id": 1,
    "instructions": "Prepare and cook.",
    "ingredient_ids": [1, 2]
  }
  ```

**Success response:**
```json
{
  "status": "success",
  "message": "Food added successfully."
}
```

**Example error response:**
```json
{
  "status": "error",
  "message": "Invalid or missing input data. Please check all required fields."
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
