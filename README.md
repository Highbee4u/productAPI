# Symfony RESTful API Project

## Overview

This project implements a RESTful API using Symfony 5.4+ for managing products. It includes JWT authentication for secure access and uses Doctrine ORM for database interactions.

## Table of Contents

- [Setup Instructions](#setup-instructions)
- [Architecture Overview](#architecture-overview)
- [API Documentation](#api-documentation)
- [Additional Notes](#additional-notes)

## Setup Instructions

### Prerequisites
- PHP >= 7.2.5
- Composer
- MySQL or SQLite (or any supported database)

### Installation Steps
1. Clone the repository:

   ```
   git clone https://github.com/Highbee4u/productAPI.git
   cd repository
   ```

2. Install dependencies:


3. Configure the environment:
- Copy `.env.example` to `.env` and configure database connection details:
  ```
  cp .env.example .env
  ```
  Edit `.env` to set `DATABASE_URL` with your database credentials.

4. Set up the database:
- Create the database (if not created):
  ```
  php bin/console doctrine:database:create
  ```
- Run migrations to create tables:
  ```
  php bin/console doctrine:migrations:migrate
  ```

5. Generate JWT keys (if using JWT authentication):

    ```
    php bin/console lexik:jwt
    ```


6. Start the Symfony server:


Access the API at `http://localhost:8000`.

### Default User Login
- **Username**: admin@test.com
- **Password**: password

## Architecture Overview

### Directory Structure
- `/config`: Configuration files (e.g., `services.yaml`, `security.yaml`).
- `/src`: Application source code.
- `/Controller`: API controllers.
- `/Entity`: Doctrine entities.
- `/Repository`: Doctrine repositories.
- `/EventListener`: Custom Error handling.
- `/tests`: PHPUnit test cases.

## API Documentation

### `GET /api/products`

- **Description**: Retrieves a list of all products.
- **Response**:

```

[
{
"id": 1,
"name": "Product A",
...
},
{
"id": 2,
"name": "Product B",
...
},
...
]


```

### `POST /api/products`

- **Description**: Creates a new product.
- **Request**:

```

{
"name": "New Product",
...
}

- **Response**:

```

{
"id": 3,
"name": "New Product",
...
}

```

### `GET /api/products/{id}`

- **Description**: Retrieves details of a specific product.
- **Response**:

```

{
"id": 3,
"name": "New Product",
...
}

```

### `PUT /api/products/{id}`

- **Description**: Updates an existing product.
- **Request**:

```

{
"name": "Updated Product",
...
}


```
- **Response**:

```

{
"id": 3,
"name": "Updated Product",
...
}

```

### `DELETE /api/products/{id}`

- **Description**: Deletes a product.
- **Response**: Status code `204 No Content`.

## Additional Notes

- **JWT Authentication**: This project uses JWT for authentication. Configure JWT keys in `.env` before deploying.
- **Testing**: PHPUnit tests are available in the `/tests` directory. Run tests using `php bin/phpunit`.
- **Future Development**: Plan to integrate user management and order processing features.


