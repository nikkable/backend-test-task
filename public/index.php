<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Raketa\BackendTestTask\Controller\GetProductsController;
use Raketa\BackendTestTask\Infrastructure\ProductRepository;
use Raketa\BackendTestTask\View\CartView;
use Raketa\BackendTestTask\View\ProductsView;

$connectionParams = [
    'driver'   => 'pdo_mysql',
    'host'     => getenv('DB_HOST') ?: 'db',
    'dbname'   => getenv('MYSQL_DATABASE') ?: 'test_task',
    'user'     => getenv('MYSQL_USER') ?: 'test_user',
    'password' => getenv('MYSQL_PASSWORD') ?: 'test_password',
    'port'     => getenv('DB_PORT') ?: 3306,
];

try {
    $conn = DriverManager::getConnection($connectionParams);
} catch (\Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Could not connect to the database: ' . $e->getMessage()]);
    exit;
}

$productRepository = new ProductRepository($conn);
$cartView = new CartView($productRepository);
$productsView = new ProductsView($productRepository);
//$cartManager = new CartManager('Экземпляр коннектора', 'Экземпляр логгера');

// Создание контроллеров
//$getCartController = new GetCartController($cartView, $cartManager);
//$addToCartController = new AddToCartController($productRepository, $cartView, $cartManager);
$getProductsController = new GetProductsController($productsView);

// Получение запроса
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
//$request = new RequestAdapter($method, $uri, $_GET, $_POST);

// Маршрутизация
$controller = null;

switch ($uri) {
    case '/cart':
//        $controller = $getCartController;
        break;
    case '/cart/add':
//        $controller = $addToCartController;
        break;
    case '/products':
        $controller = $getProductsController;
        break;
    default:
        throw new \RuntimeException('Route not found');
}

//$response = $controller->get($request);

//http_response_code($response->getStatusCode());
//header('Content-Type: application/json');
//echo $response->getBody();