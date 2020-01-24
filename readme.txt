
Check city by criteria
city: city to find

http://localhost/testAdolfo/public/check/city=malaga

Criteria list (criteria.json)
http://localhost/testAdolfo/public/criteria

Enable/disable criterias
n: criteria name
s: status

http://localhost/testAdolfo/public/criteria/n=oddLetter&s=false

Add new criteria by a pool of existing field (weather_api_fields.json)
n: criteria name
o: option
f: field
p:process
v: value to check

http://localhost/testAdolfo/public/criteria/new/n=test&o=weather&f=main&p=compare&v=clouds


.\vendor\bin\phpunit