<?php
require __DIR__ . '/../vendor/autoload.php'; // Load Composer autoloader 

use Psr\Http\Message\ResponseInterface as Response; 
use Psr\Http\Message\ServerRequestInterface as Request; 
use Slim\Factory\AppFactory; 

$config = file_exists(__DIR__ . '/../config.php')
    ? require __DIR__ . '/../config.php'
    : require __DIR__ . '/../config.example.php';

$apiToken = $config['api_token'] ?? 'YOUR_API_TOKEN';

// set ng constant na API_TOKEN na gagamitin para sa authentication ng API.
if (!defined('API_TOKEN')) {
    define('API_TOKEN', $apiToken);
}


// helper function para hindi ko na ulit-ulitin yung 
// paggawa ng JSON response. Siya na yung nagse-set ng response body, header, at status code. 
function jsonResponse(Response $response, int $status, array $payload): Response 
{
    $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($status);
}

// Connect to the Filipino cookbook MySQL database using PDO.
function getPdo(): PDO
{
    $config = file_exists(__DIR__ . '/../config.php')
        ? require __DIR__ . '/../config.php'
        : require __DIR__ . '/../config.example.php';

    $dsn = 'mysql:host=' . ($config['db_host'] ?? 'localhost') . ';dbname=' . ($config['db_name'] ?? 'filipino_cookbook_api') . ';charset=utf8mb4';
    $username = $config['db_user'] ?? 'root';
    $password = $config['db_pass'] ?? '';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    return new PDO($dsn, $username, $password, $options);
}

// Dito ko kinukuha yung data na galing sa client
// para sa mga POST request, kinukuha niya yung parsed body ng request gamit yung getParsedBody() method. 
function parseJsonBody(Request $request): array 
{
    $data = $request->getParsedBody(); //
    if (is_array($data)) {
        return $data;
    }

    $body = $request->getBody()->getContents();
    $decoded = json_decode($body, true);

    return is_array($decoded) ? $decoded : [];
}

function getRateLimitClientId(Request $request): string
{
    $forwardedFor = $request->getHeaderLine('X-Forwarded-For');
    if ($forwardedFor !== '') {
        return trim(explode(',', $forwardedFor)[0]);
    }

    $serverParams = $request->getServerParams();
    return (string) ($serverParams['REMOTE_ADDR'] ?? 'unknown');
}

function enforceRateLimit(Request $request): ?Response
{
    $path = $request->getUri()->getPath();
    if (strpos($path, '/api') !== 0) {
        return null;
    }

    static $requestCounts = [];
    $clientId = getRateLimitClientId($request);
    $now = time();
    $windowSeconds = 60;
    $maxRequests = 60;

    if (!isset($requestCounts[$clientId])) {
        $requestCounts[$clientId] = ['count' => 0, 'start' => $now];
    }

    if ($requestCounts[$clientId]['start'] < $now - $windowSeconds) {
        $requestCounts[$clientId] = ['count' => 0, 'start' => $now];
    }

    $requestCounts[$clientId]['count']++;

    if ($requestCounts[$clientId]['count'] > $maxRequests) {
        $response = new \Slim\Psr7\Response();
        $payload = [
            'status' => 'error',
            'message' => 'Too many requests. Please try again later.'
        ];
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(429);
    }

    return null;
}

// helper function na nagchecheck kung may existing record sa database based sa
function recordExists(PDO $pdo, string $table, string $field, int $value): bool 
{
    $stmt = $pdo->prepare("SELECT {$field} FROM {$table} WHERE {$field} = ?"); 
    $stmt->execute([$value]);

    return (bool) $stmt->fetch();
}

// Ito naman yung kumukuha ng lahat ng ingredients ng isang pagkain
function getIngredientsForFood(PDO $pdo, int $foodId): array // Ginagamit niya yung food_id para hanapin yung mga ingredients na related sa food na yun sa food_ingredients table.
{
    $ingredientsStmt = $pdo->prepare('SELECT i.ingredient_name FROM food_ingredients fi JOIN ingredients i ON i.ingredient_id = fi.ingredient_id WHERE fi.food_id = ? ORDER BY i.ingredient_name'); // eto
    $ingredientsStmt->execute([$foodId]); // a
    return array_column($ingredientsStmt->fetchAll(), 'ingredient_name'); // 
}

// This function gets the whole information ng isang food including yung category, origin, and ingredient list.
function fetchFoodWithIngredients(PDO $pdo, int $foodId): ?array
{
    $foodStmt = $pdo->prepare('SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions FROM foods f JOIN categories c ON c.category_id = f.category_id JOIN origins o ON o.origin_id = f.origin_id WHERE f.food_id = ?');
    $foodStmt->execute([$foodId]);
    $food = $foodStmt->fetch();

    if (!$food) {
        return null;
    }

    $food['ingredients'] = getIngredientsForFood($pdo, $foodId);

    return $food;
}

// Load all foods with their category, origin, and ingredient list.
function fetchAllFoods(PDO $pdo): array
{
    $foods = [];
    $stmt = $pdo->query('SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions FROM foods f JOIN categories c ON c.category_id = f.category_id JOIN origins o ON o.origin_id = f.origin_id ORDER BY f.food_id');
    foreach ($stmt->fetchAll() as $row) { // dito sa loop, kinukuha niya yung bawat food record at tinatawagan yung getIngredientsForFood function para makuha yung list ng ingredients para sa bawat food.
        $row['ingredients'] = getIngredientsForFood($pdo, $row['food_id']);
        $foods[] = $row;
    }

    return $foods;
}

function fetchFoodsByCategory(PDO $pdo, int $categoryId): array
{
    $foods = [];
    $stmt = $pdo->prepare('SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions FROM foods f JOIN categories c ON c.category_id = f.category_id JOIN origins o ON o.origin_id = f.origin_id WHERE f.category_id = ? ORDER BY f.food_name');
    $stmt->execute([$categoryId]);

    foreach ($stmt->fetchAll() as $row) {
        $row['ingredients'] = getIngredientsForFood($pdo, $row['food_id']);
        $foods[] = $row;
    }

    return $foods;
}

function fetchRandomFood(PDO $pdo): ?array
{
    $stmt = $pdo->query('SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions FROM foods f JOIN categories c ON c.category_id = f.category_id JOIN origins o ON o.origin_id = f.origin_id ORDER BY RAND() LIMIT 1');
    $food = $stmt->fetch();

    if (!$food) {
        return null;
    }

    $food['ingredients'] = getIngredientsForFood($pdo, $food['food_id']);

    return $food;
}
//Dito ko ginagawa o initialize yung Slim.
$app = AppFactory::create();

// security ng api, unang nagrurun bago yung route handlers. Kung hindi valid yung token, 
// hindi na siya makakapagaccess sa mga /api endpoints. tas magreereturn ng 401 Unauthorized response.
$app->add(function (Request $request, $handler) {
    $rateLimitResponse = enforceRateLimit($request);
    if ($rateLimitResponse instanceof Response) {
        return $rateLimitResponse;
    }

    $path = $request->getUri()->getPath();
    if (strpos($path, '/api') !== 0) { // dito, kung hindi nagstart sa /api yung path, hindi na niya ichecheck yung token.
        return $handler->handle($request);
    }

    $authHeader = $request->getHeaderLine('Authorization'); // dito kinukuha yung Authorization header na galing sa client request. Dapat may format
    if ($authHeader !== 'Bearer ' . API_TOKEN) { // dito, chinecheck niya kung valid yung token. Kung hindi valid, magre-return siya ng 401 Unauthorized response.
        $response = new \Slim\Psr7\Response(); // at dito niya sine-set yung response body, header, at status code para sa unauthorized access.
        $payload = [
            'status' => 'error',
            'message' => 'Unauthorized access. Valid API token is required.'
        ];
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    return $handler->handle($request);
});

// Public welcome route does not require token.
$app->get('/', function (Request $request, Response $response): Response {
    $payload = [
        'message' => 'Welcome to the Secured Filipino Cookbook API',
        'note' => 'Use a valid Bearer token to access /api endpoints.'
    ];
    return jsonResponse($response, 200, $payload);
});

// Kapag tinawag itong endpoint, coconnect sa database gamit getPdo(). 
$app->get('/api/foods', function (Request $request, Response $response): Response {
    $pdo = getPdo();
    return jsonResponse($response, 200, fetchAllFoods($pdo));
});

// Get a random Filipino food.
$app->get('/api/foods/random', function (Request $request, Response $response): Response {
    $pdo = getPdo();
    $food = fetchRandomFood($pdo);

    if ($food === null) {
        return jsonResponse($response, 404, [
            'status' => 'error',
            'message' => 'No food found.'
        ]);
    }

    return jsonResponse($response, 200, $food);
});

// dito sa endpoint na to kinukuha muna yung id na nasa URL na sinend ng client . Pagkatapos, kumokonekta siya sa database gamit ang getPdo() para hanapin yung food na may katugmang ID. 
// tas tatawagin niya yung helper function
$app->get('/api/foods/{id}', function (Request $request, Response $response, array $args): Response {
    $pdo = getPdo();
    $food = fetchFoodWithIngredients($pdo, (int) $args['id']);

    if ($food === null) {
        return jsonResponse($response, 404, [
            'status' => 'error',
            'message' => 'Food not found'
        ]);
    }

    return jsonResponse($response, 200, $food);
});

// search endpoint finish  query then loop to get yung mga ingredients for each food. tas ibabalik niya yung result as JSON response.
$app->get('/api/foods/search/{name}', function (Request $request, Response $response, array $args): Response {
    $pdo = getPdo();
    $searchTerm = '%' . trim($args['name']) . '%';
    $stmt = $pdo->prepare('SELECT f.food_id, f.food_name, c.category_name, o.origin_name, f.instructions 
    FROM foods f JOIN categories c ON c.category_id = f.category_id JOIN origins o ON o.origin_id = f.origin_id
    WHERE LOWER(f.food_name) LIKE LOWER(?) ORDER BY f.food_name'); // dito yung query na nagse-search sa foods table based sa food_name. Ginagamit niya yung LOWER function para maging case-insensitive yung search.
    $stmt->execute([$searchTerm]);

    $results = [];
    foreach ($stmt->fetchAll() as $row) {
        $row['ingredients'] = getIngredientsForFood($pdo, $row['food_id']);
        $results[] = $row;
    }

    return jsonResponse($response, 200, $results);
});

// Get all foods under a specific category.
$app->get('/api/categories/{id}/foods', function (Request $request, Response $response, array $args): Response {
    $categoryId = isset($args['id']) ? (int) $args['id'] : 0;
    if ($categoryId <= 0) {
        return jsonResponse($response, 400, [
            'status' => 'error',
            'message' => 'Invalid category_id.'
        ]);
    }

    $pdo = getPdo();
    $categoryStmt = $pdo->prepare('SELECT category_id FROM categories WHERE category_id = ?');
    $categoryStmt->execute([$categoryId]);

    if (!$categoryStmt->fetch()) {
        return jsonResponse($response, 404, [
            'status' => 'error',
            'message' => 'Category not found.'
        ]);
    }

    return jsonResponse($response, 200, fetchFoodsByCategory($pdo, $categoryId));
});

// List all categories.
$app->get('/api/categories', function (Request $request, Response $response): Response {
    $pdo = getPdo();
    $stmt = $pdo->query('SELECT category_id, category_name FROM categories ORDER BY category_name');
    return jsonResponse($response, 200, $stmt->fetchAll());
});

// List all ingredients.
$app->get('/api/ingredients', function (Request $request, Response $response): Response {
    $pdo = getPdo();
    $stmt = $pdo->query('SELECT ingredient_id, ingredient_name FROM ingredients ORDER BY ingredient_name');
    return jsonResponse($response, 200, $stmt->fetchAll());
});

// Dito ginagawa ang endpoint para magdagdag ng bagong food record
$app->post('/api/foods', function (Request $request, Response $response): Response {
    $data = parseJsonBody($request);

    //Dito kinukuha lahat ng info tungkol sa pagkain.    
    $foodName = trim((string) ($data['food_name'] ?? ''));
    $categoryId = isset($data['category_id']) ? (int) $data['category_id'] : 0;
    $originId = isset($data['origin_id']) ? (int) $data['origin_id'] : 0;
    $instructions = trim((string) ($data['instructions'] ?? ''));
    $ingredientIds = isset($data['ingredient_ids']) && is_array($data['ingredient_ids']) ? array_map('intval', $data['ingredient_ids']) : [];

        //before saving check
    if ($foodName === '' || $categoryId <= 0 || $originId <= 0 || $instructions === '' || count($ingredientIds) === 0) {
        return jsonResponse($response, 400, [
            'status' => 'error',
            'message' => 'Please provide food_name, category_id, origin_id, instructions, and ingredient_ids.'
        ]);
    }

    $pdo = getPdo();

     //Tinitingnan muna kung may existing na category ID and origin ID sa categories at origins table tas kung hindi valid, magre-return siya ng 400 Bad Request response.
    if (!recordExists($pdo, 'categories', 'category_id', $categoryId)) {
        return jsonResponse($response, 400, [
            'status' => 'error',
            'message' => 'Invalid category_id.'
        ]);
    }

    if (!recordExists($pdo, 'origins', 'origin_id', $originId)) { 
        return jsonResponse($response, 400, [
            'status' => 'error',
            'message' => 'Invalid origin_id.'
        ]);
    }

    //Dito kinukuha susunod na available na food_id. +1
    $nextIdQuery = $pdo->query('SELECT MAX(food_id) + 1 FROM foods');
    $nextFoodId = (int) $nextIdQuery->fetchColumn();
    if ($nextFoodId < 1) {
        $nextFoodId = 1;
    }

    // chinecheck lahat ng ingredient IDs kung valid ba sila sa ingredients table. Ginagamit niya yung array_unique para alisin yung duplicates tapos gagawa siya ng placeholders para sa prepared statement.
    $uniqueIngredientIds = array_values(array_unique($ingredientIds)); // Remove duplicates and reindex
    $placeholders = implode(',', array_fill(0, count($uniqueIngredientIds), '?')); // Create placeholders for prepared statement
    $ingredientStmt = $pdo->prepare("SELECT COUNT(DISTINCT ingredient_id) FROM ingredients WHERE ingredient_id IN ($placeholders)"); // Prepare the query
    $ingredientStmt->execute($uniqueIngredientIds); //  Execute the query with the unique ingredient IDs

    if ((int) $ingredientStmt->fetchColumn() !== count($uniqueIngredientIds)) { // Check if the count of valid ingredient IDs matches the count of unique ingredient IDs
        return jsonResponse($response, 400, [ // Return a 400 Bad Request response if any ingredient ID is invalid
            'status' => 'error',
            'message' => 'One or more ingredient_ids are invalid.'
        ]);
    }

    $pdo->beginTransaction(); // magstart ng transaction para masiguro na lahat ng operations ay successful bago mag-commit. Kung may error, magro-roll back siya sa previous state.
    try {
        $insertFood = $pdo->prepare('INSERT INTO foods (food_id, food_name, category_id, origin_id, instructions) VALUES (?, ?, ?, ?, ?)'); // dito
        $insertFood->execute([$nextFoodId, $foodName, $categoryId, $originId, $instructions]); // 

        $insertLink = $pdo->prepare('INSERT INTO food_ingredients (food_id, ingredient_id) VALUES (?, ?)'); // dito naman yung pag-iinsert ng link sa pagitan ng food at ingredients sa food_ingredients table.
        foreach ($ingredientIds as $ingredientId) { 
            $insertLink->execute([$nextFoodId, $ingredientId]);
        }

        $pdo->commit();
    } catch (Exception $exception) {
        $pdo->rollBack();
        return jsonResponse($response, 500, [ // 
            'status' => 'error',
            'message' => 'Unable to add food.'
        ]);
    }

    return jsonResponse($response, 201, [
        'status' => 'success',
        'message' => 'Food added successfully.'
    ]);
});

$app->run();