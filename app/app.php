<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Middleware;

class PizzaClient extends Application
{
    use Application\TwigTrait;
    use Application\SecurityTrait;
    use Application\FormTrait;
    use Application\UrlGeneratorTrait;
    use Application\MonologTrait;
}

$app = new PizzaClient();

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new SilexGuzzle\GuzzleServiceProvider(),array(
    'guzzle.base_uri' => "https://pizzaserver.herokuapp.com/",
    'guzzle.timeout' => 5
));


// CONTROLLERS //////////////////////////////////////////////////////////////////////////////////////////////////////
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.twig', array(
        'nav' => ''
    ));
});

/**
 * List existing pizzas
 */
$app->get('pizza/list', function() use ($app){
    try {
        $request = $app['guzzle']->get('/pizzas');
        // return 'Pizzas : "Status Code "' . $request->getStatusCode() . '"  ' . $request->getBody()->getContents();
        $pizzas = json_decode($request->getBody()->getContents(), true);
        return $app['twig']->render('pizza.list.twig', array(
            'pizzas' => $pizzas
        ));
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
});

/**
 * Create a pizza
 */
$app->get('pizza/create', function() use ($app) {
    try {
        return $app['twig']->render('pizza.create.twig', array(
            'pizzas' => $pizzas
        ));        
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
});

$app->post('pizza/new', function(Request $request) use ($app) {
    $pizza_definition = $request->get('pizza');
    $pizza = ['pizza' => $pizza_definition];
    try {
        $curl_response = $app['guzzle']->request('POST', '/pizzas', [
            'json'    => $pizza,
        ]);
        return $app->json($curl_response);
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
});

$app->get('/help', function () use ($app) {
    return $app['twig']->render('help.twig', array(
        'nav' => ''
    ));
});

$app->get('/hello/{name}', function ($name) use ($app) {
    if (!$name) {
        $error = array('message' => 'The user was not found.');

        return $app->json($error, 404);
    }

    return $app->json(array('name'=>$name));
});

