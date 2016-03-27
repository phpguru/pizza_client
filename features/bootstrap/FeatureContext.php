<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext implements Context, SnippetAcceptingContext 
{
    /**
     * Initializes context.
     * Every scenario gets its own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct()
    {
        // Initialize your context here
    }

    /**
     * @Given /^I am signed in$/
     */
    public function iAmSignedIn()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see a list of pizzas I made before\.$/
     */
    public function iShouldSeeAListOfPizzasIMadeBefore()
    {
        throw new PendingException();
    }
    
    /**
     * @Then /^I should see name and description field$/
     */
    public function iShouldSeeNameAndDescriptionField()
    {
        throw new PendingException();
    }


    /**
     * @Then /^I should see a form to create a new topping$/
     */
    public function iShouldSeeAFormToCreateANewTopping()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see a list of toppings available$/
     */
    public function iShouldSeeAListOfToppingsAvailable()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I have selected a pizza I created$/
     */
    public function iHaveSelectedAPizzaICreated()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see a form that lets me add toppings to the pizza$/
     */
    public function iShouldSeeAFormThatLetsMeAddToppingsToThePizza()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should be able to add toppings to the pizza$/
     */
    public function iShouldBeAbleToAddToppingsToThePizza()
    {
        throw new PendingException();
    }

    /**
     * @Then /^I should see a list of toppings on the selected pizza$/
     */
    public function iShouldSeeAListOfToppingsOnTheSelectedPizza()
    {
        throw new PendingException();
    }
}
