Feature: Create a pizza
   In order to stop my hunger
   As a builder
   I should be able to create a new pizza

   Scenario: Creating a pizza
      Given I am signed in
      When I go to "http://pizza.dev:81/pizza/create"
      Then I should see name and description field