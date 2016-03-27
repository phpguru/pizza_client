Feature: Create a new Topping
   In order to make pizzas more unique and personalized
   As a builder
   I should be able to create toppings that can be added to a Pizza

   @javascript
   Scenario: Creating a topping
      Given I am signed in
      When I go to "/topping/create"
      Then I should see a form to create a new topping

