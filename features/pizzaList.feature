Feature: A list of pizzas
   In order to recall prior pizzas
   As a builder
   I should be able to list existing Pizzas

   @javascript
   Scenario: Viewing pizza list
      Given I am signed in
      When I go to "/pizza/list"
      Then I should see a list of pizzas I made before.