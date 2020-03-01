**ApartmentRentals**

Apartment rental applciation made with laravel and vue js.

**How to Install**

All vue js files are in the frontend folder, move that folder to another directory,
cd into that directory.

The remainig files in this folder are for laravel application, to run open cmd and "php -S localhost:port"
e.g. "php -S localhost:8001".

Go to frontend/src/store.js and update the state.api to whatever api url you are using, adding "/api" behind it. In this case it will be store.js state.api will be "localhost:8001/api".

After setting up the api url in store.js, go to your laravel .env file and change the "FRONTEND_ROUTE" option to your frontend route then you "npm run serve". 

That is all.
