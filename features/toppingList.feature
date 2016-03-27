Feature: A list of toppings
   In order to recall prior toppings
   As a builder
   I should be able to list the toppings I can to add to a Pizza

   Scenario: Viewing topping list
      Given I am signed in
      When I go to "/topping/list"
      Then I should see a list of toppings available

