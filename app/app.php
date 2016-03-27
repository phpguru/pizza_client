<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Application;

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


$app->get('pizza/list', function() use ($app){
    try {
        $request = $app['guzzle']->get('/pizzas');
        // return 'Pizzas : "Status Code "' . $request->getStatusCode() . '"  ' . $request->getBody()->getContents();
        $pizzas = json_decode($request->getBody()->getContents(), true);
        return $app['twig']->render('pizza.list.twig', array('pizzas' => $pizzas));
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

