This is a boilerplate (on steroids) implementation of Symfony 4 REST API using JWT 
(JSON Web Token). It is created with best REST API practices in mind. 
REST API interaction more or less follows guidline/summary provided by this excellent 
article: https://blog.mwaysolutions.com/2014/06/05/10-best-practices-for-better-restful-api/

Regarding project itself. Several ideas were in mind, like thin-controller and TDD approach 
(this project was mainly created by first creating tests and then actual code using 
red-green-refactor technique). SOLID principles, speaking names and other good design 
practices were also kept in mind (thankfully Symfony itself is a good primer of this). 
Most business logic is moved from controllers to corresponding services, 
which in turn use other services and Doctrine repositories to execute various DB queries.
