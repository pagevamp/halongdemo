# Demo Project

This Project is created for demo purpose and is missing many dependencies. Small module from one of our project has been extracted. 

Project is setup using docker. We use docker for most of all our project to make sure all of the environments we work are same. There are many other benefits of docker.

We have followed Test Driven Development approach to make our system more robust and less error prone.
 
Php Cs Fixer is enabled to follow PHP coding standards as defined in PSR-2.
____
We believe in Simple, Clean, Readable, Understandable, Maintainable and Testable code. Whatever language we choose to work with, we make sure our standard practices are maintained and followed by every team member. Below, you can find few rules we follow:
* Follow a consistent coding standard (PHP: PSR-2)
* Name things properly, long variable and function names are allowed
* Be expressive, write code as you speak and be optimally verbose
* Max indent per method should be 2, in case of exceptions 3
* Avoid creating god object and long methods
* Keep the method in one place, inject the class and call it, DRY
* Avoid in-line comments (comment with code), put comments in the method doc


## Installation
* `git clone git@github.com:pagevamp/halongdemo.git`
* `cd halongdemo`
* `docker-compose up -d`
* `chmod -R 777 storage`
* `cp .env.example .env`
* `docker exec -i api.halongdemo.pv composer install`
* `docker exec -i api.halongdemo.pv php artisan migrate`
* `docker exec -i api.halongdemo.pv php artisan db:seed`


### Folder Structure

* `/app` is where application logic is
* folders inside `/app` are divided into modules(domains) and they have their own properties
  + app
    + Events
    + Listeners
    + Http
        + Controllers
        + Middleware
        + Request
        + Resources
    + Observers
    + Providers  
    + Repos
    + Services
    
  + tests  
    + Feature
  


#### 1) Events -: 
All app events are stored here like whenever many things has to be done after action has been taken out we use events.
For E.g whenever user has signed up signup verification email should be sent 
``` 
UserRegistered::class => [
               SendRegistrationConfirmationEmail::class
           ], 
```
`UserRegistered` is Event

#### 2) Listeners -: 
Whenever events are fired certain task has to be done whether synchronously or on background. Set of instructions are placed in listeners 
what to do when something happens.
``` 
UserRegistered::class => [
               SendRegistrationConfirmationEmail::class
           ], 
```    
`SendRegistrationConfirmationEmail` is Listener

#### 3) Controllers -:
Controllers generally act as a glue between things like domain, services, validation. It act as bridge only 
so controller doesnt have any idea on how business problems are solved but it has idea on which services, domain to call for solving problems.

#### 4) Middleware -:
When something has to be validated, authenticated before it reach to controller then middlewares are used. But in our case
default middleware worked well.

#### 5) Request -:
Whenever something validation with data has to be done like whether its data type / domain validation we do it upfront from 
request class and all you have to do is inject it on controller.

#### 6) Resources -:
Resources are generally transformers, which helps us in keeping consistency for database fields, introducing fields that are 
not in database. Resource are generally used in read side.

#### 7) Observers -:
When working with eloquent there are events like create, delete, update so we can catch post / pre CRUD events and process actions.
So every domain will have their own observer to listen these default eloquent events.

#### 8) Providers -:
We are using providers for extending laravel into our own flavour. All extends / modification on framework will passed from here.

#### 9) Repos -:
Repos are generally domain / eloquent / model / table which is responsible for all read and write through persistent disk.
Interface , Trait has been used in domain when some domains start sharing similar behaviours with each other.

#### 9) Services -:
Services like Query Filters, Api Response , Mailable etc things resides here which are / can be used my multiple components.  

  
#### 9) Feature -:
For every endpoint whether its a read or write there is a feature test for them which will confirm that they are working correctly.
For e.g For successful User Registration a record has to be persisted in database and confirmation email has to be sent so feature tests
asserts if these steps are carried out successfully or not. So software breaks are easily identified when someone touch existing code hence
less bugs in production.    

  
