# Filipino Cookbook API

This project is a Slim PHP REST API for managing Filipino foods, categories, origins, ingredients, and food-related records.

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

### Endpoints Added
- GET /api/foods/random
- GET /api/categories/{id}/foods

### Security Features Implemented
- Basic rate limiting for API requests

### Testing Instructions
1. Start the local server:
   ```bash
   php -S localhost:8000 -t public
   ```

2. Use the following Bearer token in the Authorization header:
   ```text
   Bearer dmmmsu-cookbook-token-2026
   ```

3. Test the new endpoints:
   - http://localhost:8080/api/foods/random
   - http://localhost:8080/api/categories/1/foods

4. Test unauthorized access by sending a request without the Authorization header to confirm that the API returns a 401 response.

### Example Successful Responses

#### Random Food
```json
{
  "food_id": 14,
  "food_name": "Bulalo",
  "category_name": "Soup",
  "origin_name": "Philippines",
  "instructions": "Boil beef shank and bone marrow until tender.",
  "ingredients": [
    "Beef shank",
    "Bone marrow",
    "Corn"
  ]
}
```

#### Foods by Category
```json
[
  {
    "food_id": 19,
    "food_name": "Boiled Egg",
    "category_name": "Appetizer",
    "origin_name": "Bacolod",
    "instructions": "Boil the egg until cooked.",
    "ingredients": [
      "Bay leaves"
    ]
  }
]
```

### Screenshots

#### Random Food Endpoint Test
![Random Food Endpoint Test](Screenshots/Random.png)

#### Category Foods Endpoint Test
![Category Foods Endpoint Test](Screenshots/Category.png)
