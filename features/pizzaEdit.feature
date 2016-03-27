Feature: Add/Edit pizza toppings
   In order customize pizzas
   As a builder
   I should be able to add a topping to a pizza

   Scenario: Improving a pizza
      Given I am signed in
      And I have selected a pizza I created
      When I go to "http://pizza.dev:81/pizza/edit/:id"
      Then I should be able to add toppings to the pizza

