<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

$app->register(new Silex\Provider\SessionServiceProvider());


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
        /* @var $request \GuzzleHttp\Psr7\Request */ // IDEHelper

        $pizzas = json_decode($request->getBody()->getContents(), true);
        
        $app['session']->set('pizzas.all', $pizzas);
        $app['session']->set('pizzas.my', [91,95]);
        
        
        return $app['twig']->render('pizza.list.twig', array(
            'pizzas' => $pizzas,
            'mypizzas' => $app['session']->get('pizzas.my')
        ));
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
});

/**
 * Create a pizza
 */
$app->get('pizza/new', function() use ($app) {
    try {
        return $app['twig']->render('pizza.create.twig', array(
            'pizzas' => $pizzas
        ));        
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
});

$app->post('pizza/create', function(Request $request) use ($app) {
    
    // Try to create a new pizza with the given name and description
    $pizza_definition = $request->get('pizza');
    
    // What we are going to post for remote storage
    $pizza_package = ['pizza' => $pizza_definition];
    
    try {
        $request = $app['guzzle']->get('/pizzas');
        /* @var $request \GuzzleHttp\Psr7\Request */ // IDEHelper
        $existing_pizzas = json_decode($request->getBody()->getContents(), true);
        
        
        foreach($existing_pizzas as $existing_pizza) {
            if (trim(strtolower($existing_pizza['name'])) === trim(strtolower($pizza_definition['name'])))
            {
                $response = new Response();
                /* @var $response \Symfony\Component\HttpFoundation\Request */
                
                $response->setStatusCode(401, 'Pizza Exists');
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode([
                    'error' => 'Sorry, but that pizza has already been created.'
                ]));
                return $response;
            }
        }
        
        // Store remotely
        $curl_response = $app['guzzle']->request('POST', '/pizzas', [
            'json'    => $pizza_package,
        ]);
        
        if ($curl_response) {
            return $app->json($curl_response);        
        } else {
            return $app->abort(500, 'Failed to create pizza!');
        }
        
    } catch (Exception $ex) {

        return $app->abort(500, $ex->getMessage());

    }
});

/**
 * Get help
 */
$app->get('/help', function () use ($app) {
    return $app['twig']->render('help.twig', array(
        'nav' => ''
    ));
});

/**
 * Example from the tutorial
 */
$app->get('/hello/{name}', function ($name) use ($app) {
    if (!$name) {
        $error = array('message' => 'The user was not found.');

        return $app->json($error, 404);
    }

    return $app->json(array('name'=>$name));
});

