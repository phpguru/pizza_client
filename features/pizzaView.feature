Feature: Show pizza toppings
   In order remember what I already added to the pizza
   As a builder
   I should be able to list the toppings on the selected Pizza

   Scenario: Viewing a selected pizza
      Given I am signed in
      And I have selected a pizza I created
      When I go to "http://pizza.dev:81/pizza/view/:id"
      Then I should see a list of toppings on the selected pizza

