# Lost & Found Pets System

This is a PHP-based system built using Laravel, designed to help people who have lost their pets and those who have found a lost pet. The system allows users to post and search for lost and found animals, and features Google and Facebook login integration.

## Features

- **Lost Pets**: Users can post information about their lost pets, including photos and descriptions.
- **Found Pets**: Users can post about pets they have found, allowing owners to recover their animals.
- **User Authentication**: Secure login using Google and Facebook authentication.
- **Search & Filters**: Users can search for lost and found pets by various criteria (location, type of animal, etc.).
- **Pet Details**: Each listing includes detailed information, such as the pet’s breed, age, and any distinguishing features.
- **User Dashboard**: Each user has a personal dashboard where they can manage their posts and information.

## Installation

Follow these steps to set up the project locally:

1. Clone the repository:
   ```bash
   git clone https://github.com/Henriquuepedro/lost-and-found-pets.git
   ```

2. Navigate to the project directory:
   ```bash
   cd lost-and-found-pets
   ```

3. Install dependencies using Composer:
   ```bash
   composer install
   ```

4. Set up the environment variables. Copy the `.env.example` file to `.env` and update the necessary details (such as your database and API keys):
   ```bash
   cp .env.example .env
   ```

5. Generate the application key:
   ```bash
   php artisan key:generate
   ```

6. Run migrations to set up the database:
   ```bash
   php artisan migrate
   ```

7. Run the application:
   ```bash
   php artisan serve
   ```

The application should now be running at `http://localhost:8000`.

---

## Google and Facebook Authentication

To enable Google and Facebook login
- Set up OAuth credentials for Google and Facebook on their respective developer platforms.
- Add the credentials to your `.env` file:
   ```bash
    GOOGLE_CLIENT_ID=your-google-client-id
    GOOGLE_CLIENT_SECRET=your-google-client-secret
    FACEBOOK_CLIENT_ID=your-facebook-client-id
    FACEBOOK_CLIENT_SECRET=your-facebook-client-secret
   ```

## Contributing

Feel free to fork this project and submit pull requests. If you encounter any bugs or have suggestions for improvements, please open an issue on GitHub.

## License
This project is licensed under the MIT License - see the LICENSE file for details.
