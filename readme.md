# Eat Local ICT

This project is a locator for local food/drink places in Wichita, KS but it has been designed to allow expansion to all localities, as the database migrations were made with that concept in mind.


# Installation
Installation is pretty straight forward, as it is based on Laravel and Vue and uses Bulma for CSS (with some SASS customization).

Steps are as follows:

1.Clone the repository

2. Run the CLI command 'composer install' in the project root directory

3. Run the command 'npm install' or 'yarn install' just from the CLI in the project root.

4. In the root directory, create a file called '.env' with the same format as .env.example. An easy way to do this is to run the following command in the root directory 'cp .env .env.example'

5. Edit the .env with your database credentials.

6. Run 'php artisan migrate:fresh --seed' to build and populate your database.

6. Run the command 'php artisan key:generate' to populate the application key if it has not already been done.

7. Build the Vue assets. If you are building for production, run 'npm run production.' If you are building for development, you can run 'npm run watch' to simultaneously build the project and start the file watcher that rebuilds assets as you change them.

8. If you are running on a local machine, you can run 'php artisan serve' to start a local server, else you will need to configure your webserver to run this project.