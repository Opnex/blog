# Opnex Blog Platform

A full-stack blog platform where users can register, log in, create, edit, and delete blog posts, and view a responsive dashboard with post analytics.

## Features
- User registration and login (with secure password hashing)
- User authentication and session management
- Dashboard with post and comment analytics
- Create, view, edit, and delete blog posts (CRUD)
- Comment system for posts
- Responsive design using Bootstrap
- Clean, modern UI

## Technologies Used
- PHP (backend)
- MySQL (database)
- HTML, CSS, Bootstrap (frontend)
- JavaScript (minor interactivity)

## Database Schema
You should have a MySQL database named `opnex_blog` with at least these tables:

### `users`
| Field      | Type         | Description         |
|------------|--------------|---------------------|
| id         | INT, PK, AI  | User ID             |
| username   | VARCHAR(50)  | Unique username     |
| email      | VARCHAR(100) | Unique email        |
| password   | VARCHAR(255) | Hashed password     |
| created_at | DATETIME     | Registration date   |

### `posts`
| Field      | Type         | Description         |
|------------|--------------|---------------------|
| id         | INT, PK, AI  | Post ID             |
| user_id    | INT, FK      | Author (users.id)   |
| title      | VARCHAR(255) | Post title          |
| content    | TEXT         | Post content        |
| status     | ENUM         | 'published','draft' |
| created_at | DATETIME     | Creation date       |

### `comments`
| Field      | Type         | Description         |
|------------|--------------|---------------------|
| id         | INT, PK, AI  | Comment ID          |
| post_id    | INT, FK      | Post (posts.id)     |
| user_id    | INT, FK      | Author (users.id)   |
| content    | TEXT         | Comment text        |
| created_at | DATETIME     | Comment date        |

**Foreign keys:**
- `posts.user_id` → `users.id`
- `comments.post_id` → `posts.id`
- `comments.user_id` → `users.id`

## Setup Instructions
1. **Clone the repository:**
   ```
   git clone https://github.com/yourusername/opnex_blog.git
   cd opnex_blog
   ```
2. **Import the database:**
   - Create a MySQL database named `opnex_blog`.
   - Use the schema above to create the tables, or import your own SQL file if available.
3. **Configure database connection:**
   - Edit `includes/db.php` if your MySQL username/password differ from `root`/`''`.
4. **Run the application:**
   - Place the project in your web server's root (e.g., `htdocs` for XAMPP).
   - Access `http://localhost/opnex_blog/` in your browser.

## Usage Notes
- Register a new account or log in with existing credentials.
- Create, edit, and delete your own posts from the dashboard.
- Comment on posts when logged in.
- Only post owners can edit or delete their posts.

## License
MIT
