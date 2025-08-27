# Project Management API

A RESTful API for a project management system built with Laravel. It provides endpoints for managing users, projects, and tasks, including features like authentication, searching, filtering, and soft-deletes.

## Table of Contents

-   [Features](#features)
-   [Prerequisites](#prerequisites)
-   [Installation & Setup](#installation--setup)
-   [Postman Collections](#collection)
-   [API Endpoints](#api-endpoints)
    -   [Authentication](#authentication)
    -   [Users](#users)
    -   [Projects](#projects)
    -   [Tasks](#tasks)

## Features

-   **Authentication**: Secure user registration and token-based authentication using Laravel Sanctum.
-   **Resource Management**: Full CRUD (Create, Read, Update, Delete) operations for Projects, Tasks, and Users.
-   **Soft Deletes**: Projects and Tasks can be soft-deleted and restored, preserving data integrity.
-   **Task Assignments**: Assign multiple users to tasks.
-   **Advanced Querying**:
    -   **Searching**: Full-text search on resource fields (e.g., project name, task title).
    -   **Filtering**: Filter resources by attributes like status, priority, or project ID.
    -   **Sorting**: Sort results by any attribute in ascending or descending order.
    -   **Pagination**: Configurable, paginated responses for large datasets.
-   **API Versioning**: All routes are prefixed with `/api/v1` for future-proofing.
-   **Standardized Responses**: Uses Eloquent API Resources to ensure consistent and well-structured JSON responses.
-   **Robust Validation**: Leverages Form Request validation to ensure data integrity and provide clear error messages.

## Prerequisites

-   PHP >= 8.1
-   Composer
-   A database server (e.g., MySQL, PostgreSQL, SQLite)
-   Git

## Installation & Setup

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/mostafa569/project-management-api
    cd project-management-api
    ```

2.  **Install PHP dependencies:**

    ```bash
    composer install
    ```

3.  **Create your environment file:**
    Copy the `.env.example` file to a new file named `.env`.

    ```bash
    # On Windows
    copy .env.example .env

    # On macOS/Linux
    cp .env.example .env
    ```

4.  **Configure your database:**
    Open the `.env` file and update the `DB_*` variables with your database credentials.

    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=project_management
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Generate an application key:**

    ```bash
    php artisan key:generate
    ```

6.  **Run database migrations:**
    This will create all the necessary tables in your database.

    ```bash
    php artisan migrate
    ```

7.  **(Optional) Seed the database:**
    If you have seeder classes, you can populate your database with dummy data.

    ```bash
    php artisan db:seed
    ```

8.  **Start the development server:**
    ```bash
    php artisan serve
    ```
    The API will be available at `http://127.0.0.1:8000`.

## Postman Collections 

    https://github.com/mostafa569/project-management-api/blob/main/Project%20Management%20API.postman_collection.json

## API Endpoints

All endpoints are prefixed with `/api/v1`. Authenticated routes require a `Bearer` token in the `Authorization` header.

### Authentication

| Method | Endpoint | Description         | Auth Required |
| :----- | :------- | :------------------ | :------------ |
| `POST` | `/users` | Register a new user | No            |
| `POST` | `/login` | Log in a user       | No            |

**Login Request Body:**

```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Login Success Response:**

```json
{
    "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ"
}
```

### Users

| Method   | Endpoint      | Description       | Auth Required |
| :------- | :------------ | :---------------- | :------------ |
| `GET`    | `/users`      | Get all users     | Yes           |
| `GET`    | `/users/{id}` | Get a single user | Yes           |
| `PUT`    | `/users/{id}` | Update a user     | Yes           |
| `DELETE` | `/users/{id}` | Delete a user     | Yes           |

**Query Parameters for `GET /users`:**

-   `search` (string): Search by user name or email.
-   `sort` (string): Field to sort by (default: `id`).
-   `order` (string): `asc` or `desc` (default: `asc`).
-   `per_page` (int): Items per page (default: `10`).

### Projects

| Method   | Endpoint                 | Description                    | Auth Required |
| :------- | :----------------------- | :----------------------------- | :------------ |
| `GET`    | `/projects`              | Get all projects               | Yes           |
| `POST`   | `/projects`              | Create a new project           | Yes           |
| `GET`    | `/projects/{id}`         | Get a single project           | Yes           |
| `PUT`    | `/projects/{id}`         | Update a project               | Yes           |
| `DELETE` | `/projects/{id}`         | Soft delete a project          | Yes           |
| `GET`    | `/projects/trashed`      | Get all trashed projects       | Yes           |
| `POST`   | `/projects/{id}/restore` | Restore a soft-deleted project | Yes           |

**Query Parameters for `GET /projects`:**

-   `search` (string): Search by project name or description.
-   `status` (string): Filter by status (e.g., 'pending', 'in_progress', 'completed').
-   `sort` (string): Field to sort by (default: `id`).
-   `order` (string): `asc` or `desc` (default: `asc`).
-   `per_page` (int): Items per page (default: `10`).
-   `withTrashed` (boolean): Set to `1` or `true` to include soft-deleted projects in the main list.

**Request Body for `POST /projects`:**

```json
{
    "name": "New API Project",
    "description": "Details about this new project.",
    "status": "pending",
    "deadline": "2024-12-31"
}
```

### Tasks

| Method   | Endpoint              | Description                 | Auth Required |
| :------- | :-------------------- | :-------------------------- | :------------ |
| `GET`    | `/tasks`              | Get all tasks               | Yes           |
| `POST`   | `/tasks`              | Create a new task           | Yes           |
| `GET`    | `/tasks/{id}`         | Get a single task           | Yes           |
| `PUT`    | `/tasks/{id}`         | Update a task               | Yes           |
| `DELETE` | `/tasks/{id}`         | Soft delete a task          | Yes           |
| `GET`    | `/tasks/trashed`      | Get all trashed tasks       | Yes           |
| `POST`   | `/tasks/{id}/restore` | Restore a soft-deleted task | Yes           |

**Query Parameters for `GET /tasks`:**

-   `search` (string): Search by task title or details.
-   `priority` (string): Filter by priority (e.g., 'low', 'medium', 'high').
-   `is_completed` (boolean): Filter by completion status (`1` for true, `0` for false).
-   `project_id` (int): Filter tasks belonging to a specific project.
-   `sort` (string): Field to sort by (default: `id`).
-   `order` (string): `asc` or `desc` (default: `asc`).
-   `per_page` (int): Items per page (default: `10`).
-   `withTrashed` (boolean): Set to `1` or `true` to include soft-deleted tasks in the main list.

**Request Body for `POST /tasks`:**

```json
{
    "title": "Design database schema",
    "details": "Plan all tables and relationships for the new feature.",
    "project_id": 1,
    "priority": "high",
    "is_completed": false,
    "assignee_ids": [1, 2]
}
```

---

