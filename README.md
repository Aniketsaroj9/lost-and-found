# Lost and Found

Lost and Found is a lightweight web application for reporting and searching lost and found items. Users can create listings with images and descriptions, search and filter by location/date/category, and claim items. The project is built with PHP for the backend and standard HTML/CSS for the frontend.

## Features
- Report lost or found items with title, description, location, date and image
- Browse and search listings
- Claim items and contact listers
- Simple PHP + HTML/CSS codebase intended for small deployments and learning

## Quick start
Prerequisites:
- PHP 7.4+ (or PHP 8.x)
- MySQL / MariaDB
- Composer (optional, if you add dependencies later)

1. Clone the repository

   git clone https://github.com/Aniketsaroj9/lost-and-found.git
   cd lost-and-found

2. Configure database

- The repository includes a `setup_database.php` script to create the database schema. Review the script before running it.
- Edit your database credentials in `includes/` (e.g. `includes/config.php`) or create a `config.php` file with your DB connection settings.

3. Run the database setup (from project root):

   php setup_database.php

4. Serve the app locally

You can use PHP's built-in web server for local testing:

   php -S localhost:8000

Then open http://localhost:8000 in your browser.

## Development notes
- Frontend files are in `index.html` and `css/` (styles).
- Server-side PHP files live at the project root and `includes/`.
- Images are stored in the `image/` folder.

## Security
- Review `setup_database.php` and other PHP files for safe credential handling, SQL injection protection, and input validation before deploying to production.
- Do not store production DB credentials directly in source control. Use environment variables or a non-committed config file.

## Contributing
Contributions are welcome. Please open issues for feature requests or bugs, and submit pull requests for code changes.

## License
This project is licensed under the MIT License - see the LICENSE file for details.
