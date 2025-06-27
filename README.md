


# 🚀 Project Setup Guide

This project includes a **Vue.js frontend** and a **Laravel backend** with database queues.

---

## 📦 Vue.js Frontend

1. Install dependencies:

   ```bash
   npm install
   ```

3. Compile and hot-reload for development:

   ```bash
   npm run dev
   ```

4. Compile and minify for production:

   ```bash
   npm run build
   ```

---

## ⚙️ Laravel Backend

1. Install dependencies using Composer:

   ```bash
   composer install
   ```

3. Copy `.env.example` to `.env`:

   ```bash
   cp .env.example .env
   ```

4. Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. Configure your database credentials in `.env`, e.g.:

   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

6. Run database migrations:

   ```bash
   php artisan migrate
   ```

7. Seed the database:

   ```bash
   php artisan db:seed
   ```

---

## 🛠️ Queue Setup

1. Configure the queue driver in your `.env` file:

   ```
   QUEUE_CONNECTION=database
   ```

3. Start the queue worker to process jobs:

   ```bash
   php artisan queue:work
   ```

---

## ✅ Done!

You can now:

* Run the **Vue frontend** with `npm run dev` .
* Run the **Laravel backend**, process database queues, and start serving your app.

---

### 📌 Notes

* Make sure your database is up and running before migrating or seeding.
* For production, use a process manager (e.g., Supervisor) to keep `queue:work` running continuously.
* Customize the `.env` file with your local or production environment settings.

---
