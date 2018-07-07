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


Usage/testing:

You can simply look at and run PHPUnit tests to execute all possible REST API endpoints, but if you want, you can also use tools like POSTMAN to manually access REST API endpoints. Here is how to test all currently available API endpoints:

We can use POSTMAN to access all endpoints:

##### 1) Create API user to work with:

method: POST
url: http://localhost:8000/users/create
Body (select raw) and add this line: {"email": "rest@jwtrestapi.com", "password": "test123"}

you should get the following response:

{
    "data": {
        "email": "rest@jwtrestapi.com"
    }
}

or 

{
    "error": {
        "code": 400,
        "message": "User with given email already exists"
    }
}

if user with given email already exists.

##### 2) Authenticate (acquire JWT token) for just created user to be able to make REST API calls: 

method: POST
url: http://localhost:8000/api/authenticate
Authorization type: Basic Auth
	username: rest@jwtrestapi.com
	password: test123

{
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJyZXN0QGp3dHJlc3RhcGkuY29tIiwiZW1haWwiOiJyZXN0QGp3dHJlc3RhcGkuY29tIiwiaWF0IjoxNTMwOTg1NTc2LCJleHAiOjE1MzA5ODkxNzZ9.CtGhP3YCs6Wz6o724ElU5-4GudcWpqYrBEDRRHrvjio"
    }
}

copy JWT token you got (without quotes)	to clipboard

##### 3) Use REST API using your JWT token

- Create a football league:
	method: POST
	url: http://localhost:8000/api/leagues
	Body (select raw) and add this line: 

	{"name": "League 1"}

	Header:
	- Add header Key called "Authorization"
	- Add value: Bearer <your_jwt_token_value> (note there is space between "Bearer" and your JWT)

	Response should look similar to this:

	{
		"data": {
			"id": 181,
			"name": "League 1"
		}
	}

- Create a football team request (use league id value you got from /api/leagues response):

	method: POST
	url: http://localhost:8000/api/teams
	Body (select raw) and add this line: 

	{"name": "Test team 1","strip": "Test strip 1","league_id": 181}

	Header:
	- Add header Key called "Authorization"
	- Add value: Bearer <your_jwt_token_value> (note there is space between "Bearer" and your JWT)	
	
	Response should look similar to this:

	{
		"data": {
			"id": 121,
			"name": "Test team 1",
			"strip": "Test strip 1",
			"league_id": 181
		}
	}

- Update attributes of a football team. Let's say we want to change "strip" value of some particular football team:

	method: PUT
	url: http://localhost:8000/api/teams/{id} (where {id} is id of existing football team you want to modify, for example http://localhost:8000/api/teams/121)
	Body (select raw) and add this line: 
	{"strip": "New strip 1"}

	Header:
	- Add header Key called "Authorization"
	- Add value: Bearer <your_jwt_token_value> (note there is space between "Bearer" and your JWT)	
	
	Response should look similar to this:

	{
		"data": {
			"id": 121,
			"name": "Test team 1",
			"strip": "New strip 1",
			"league_id": 181
		}
	}

- Delete football team:

	method: DELETE
	url: http://localhost:8000/api/teams/{id} (where {id} is id of existing football team you want to delete, for example http://localhost:8000/api/teams/121)	

	Header:
	- Add header Key called "Authorization"
	- Add value: Bearer <your_jwt_token_value> (note there is space between "Bearer" and your JWT)	
	
	Response HTTP status should be 204 (endpoint is successfully executed, but there is nothing to return)
	
- Delete league:
	* Make sure all assigned football teams to this league are deleted (use "Delete football team" scenario to delete league's football teams)
	
	method: DELETE
	url: http://localhost:8000/api/leagues/{id} (where {id} is id of existing football team you want to delete, for example http://localhost:8000/api/leagues/181)	

	Header:
	- Add header Key called "Authorization"
	- Add value: Bearer <your_jwt_token_value> (note there is space between "Bearer" and your JWT)	
	
	Response HTTP status should be 204 (endpoint is successfully executed, but there is nothing to return)
	
	
	