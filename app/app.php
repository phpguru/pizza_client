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


/****************************************************************\
 *      _____ _____ ____________          _____ 
 *     |  __ \_   _|___  /___  /   /\    / ____|
 *     | |__) || |    / /   / /   /  \  | (___  
 *     |  ___/ | |   / /   / /   / /\ \  \___ \ 
 *     | |    _| |_ / /__ / /__ / ____ \ ____) |
 *     |_|   |_____/_____/_____/_/    \_\_____/ 
 *                                              
\****************************************************************/

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
 * Edit an existing pizza
 */
$app->get('/pizza/edit/{id}', function($id) use ($app) {
    foreach($app['session']->get('pizzas.all') as $pizza) {
        if ((int)$id === (int)$pizza['id']) {
            break;
        }
    }
    
    try {
        $fetch_pizza_toppings = $app['guzzle']->get("/pizzas/{$id}/toppings");
        $pizza_toppings = json_decode($fetch_pizza_toppings->getBody()->getContents(), true);
        
        $fetch_all_toppings = $app['guzzle']->get('/toppings');
        $all_toppings = json_decode($fetch_all_toppings->getBody()->getContents(), true);
        
        $more_toppings = [];
        foreach ($all_toppings as $possible_topping) {
            foreach ($pizza_toppings as $existing_topping) {
                if ($possible_topping['name'] !== $existing_topping['name']) {
                    $more_toppings[] = $possible_topping;
                }
            }
        }
    } catch (Exception $ex) {
        throw new \Exception ($ex->getMessage());
    }
    
    return $app['twig']->render('pizza.edit.twig', array(
        'pizza' => $pizza, 
        'pizza_toppings' => $pizza_toppings,
        'more_toppings' => $more_toppings
    ));
});




/****************************************************************\
 *      _______ ____  _____  _____ _____ _   _  _____  _____ 
 *     |__   __/ __ \|  __ \|  __ \_   _| \ | |/ ____|/ ____|
 *        | | | |  | | |__) | |__) || | |  \| | |  __| (___  
 *        | | | |  | |  ___/|  ___/ | | | . ` | | |_ |\___ \ 
 *        | | | |__| | |    | |    _| |_| |\  | |__| |____) |
 *        |_|  \____/|_|    |_|   |_____|_| \_|\_____|_____/ 
 *                                                           
\****************************************************************/



/**
 * List existing toppings
 */
$app->get('topping/list', function() use ($app){
    try {
        $request = $app['guzzle']->get('/toppings');
        /* @var $request \GuzzleHttp\Psr7\Request */ // IDEHelper

        $toppings = json_decode($request->getBody()->getContents(), true);
        
        return $app['twig']->render('topping.list.twig', array(
            'toppings' => $toppings
        ));
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
});

/**
 * Empty form to create a new topping
 */
$app->get('topping/new', function() use ($app) {
    try {
        return $app['twig']->render('topping.create.twig', array());        
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
});

/**
 * Form post handler to create new toppings
 */
$app->post('topping/create', function(Request $request) use ($app) {
    
    // Try to create a new topping with the given name and description
    $topping_definition = $request->get('topping');
    
    // What we are going to post for remote storage
    $topping_package = ['topping' => $topping_definition];
    
    try {
        
        // Check and see if this topping already exists
        $request = $app['guzzle']->get('/toppings');
        /* @var $request \GuzzleHttp\Psr7\Request */ // IDEHelper

        $existing_toppings = json_decode($request->getBody()->getContents(), true);
        
        
        foreach($existing_toppings as $existing_topping) {
            if (trim(strtolower($existing_topping['name'])) === trim(strtolower($topping_definition['name'])))
            {
                $response = new Response();
                /* @var $response \Symfony\Component\HttpFoundation\Request */
                
                $response->setStatusCode(401, 'Topping Exists');
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent(json_encode([
                    'error' => 'Sorry, but that topping has already been created.'
                ]));
                return $response;
            }
        }
                
        // Store remotely
        $curl_response = $app['guzzle']->request('POST', '/toppings', [
            'json'    => $topping_package,
        ]);
        
        if ($curl_response) {
            return $app->json($curl_response);        
        } else {
            return $app->abort(500, 'Failed to create topping!');
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

