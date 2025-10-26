# The Assignment

Create a service which will be able to convert amount between currencies:
Service must leverage the exchange rates provided at https://swop.cx/ and leverage that to return a converted value.
So if you input 30, USD and GBP, you would need to return the calculated result.

- The Service has to be written in PHP. Feel free to use a framework of your choice.
- Develop the service as if it was for production - leverage validation, testing, caching
- We would like to see and test your code, so make the code available to us together with
  the link to the Git repository
- Format the resulting values using the Web i18n framework
- Extra points: Implement caching as a part of your solution to optimize performance. This
  will be considered for extra points.
- Extra Points: Implement CSRF and CSP for security
- Extra Points: Containerize your application with Docker for consistent deployment across
  different environments.
- Extra Points: Add instrumentation to your codebase. Use InfluxDB to log data from the
  backend and integrate it with Grafana for real-time monitoring and analytics.
- Extra Points: Develop a Vue.js user interface to facilitate smooth interactions with the
  currency conversion service.

___

# Setup

___

## Docker

The project relies
on [Docker](https://medium.com/@piyushkashyap045/comprehensive-guide-installing-docker-and-docker-compose-on-windows-linux-and-macos-a022cf82ac0b).
Install `docker` with `docker-compose` on the machine you want to run the app on.
___

## Environment

Create `.env` file in the root of the project. You can copy the `.env.example`, which is set to work with the
[development](#Services) service.

Once you have the `.env` file, make sure that the following values are set:

```dotenv
APP_KEY=
APP_ENV=local|development

DB_HOST=db
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

...

SWOP_CX_API_KEY=
```

___

## Services

There are 2 possible options for running the app:

- [production](#Production)
- [development](#Development)

To run the service in a detached mode add `-d` to the commands for starting them.

#### Production

To start the production service run:

```shell
  docker compose -f compose.prod.yaml up --build
```

To enter the production CLI container run:

```shell
  docker exec -it prod-cli bash
```

Once started you can access it by going to <localhost:80> in your browser.

#### Development

To start the development service run:

```shell
  docker compose -f compose.dev.yaml up --build
```

To enter the development CLI container run:

```shell
  docker exec -it prod-cli bash
```

Once started you can access it by going to <localhost:8080> in your browser.
___

### Initial DB Setup

Enter into the CLI container of the running [service](#Services).
Once inside the container, migrate the DB by running:

```shell
  php artisan migrate
```

You can exit the container by typing:

```shell
  exit
```

___

### Build Frontend

In the correct CLI [service](#services) you can run:

```shell
  npm run build
```

This will build the frontend so that the `VueJS devtool` will not work, and the resulting frontend build will be
minimized.

For debugging run:

```shell
    npm run build:dev
```

This will build the frontend for debugging and the `VueJS devtool` will work. This will only function correctly if you
are running the [dev](#development) docker service.
___

### Testing

In the CLI container run:

```shell
    php artisan test
```

To have a look at the code coverage run:

```shell
    php artisan test --coverage
```

To generate HTML code coverage run:

```shell
    vendor\bin\phpunit --coverage-html <directoryNameWhereItWillGenerateTheHtml>
```
