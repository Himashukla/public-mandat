# Public Mandate — Real-Time Poll Application

A real-time polling application built with Laravel 12. Admins can create and manage polls, users can vote, and results update live across all browsers using WebSockets.

---

## Features

- Admin registration and authentication
- Create and manage polls with multiple options
- Public voting page with real-time vote count updates
- One vote per IP address enforcement
- Live results on admin panel via Livewire (auto-refreshes every 3 seconds)
- Real-time vote broadcasting via Laravel Reverb and Laravel Echo
- Shareable poll links
- Full test coverage for voting logic

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 |
| Frontend | Blade, jQuery, Bootstrap 4 |
| Real-time Vote Count Update (public) | Laravel Reverb + Laravel Echo |
| Real-time Vote Count Update (admin) | Livewire |
| Queue | Database |
| Testing | PHPUnit |

---

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+
- MySQL
- NPM

---

## Local Setup

### 1. Clone the repository
```bash
git clone https://github.com/your-username/public-mandate.git
cd public-mandate
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node dependencies
```bash
npm install
```

### 4. Copy the environment file
```bash
cp .env.example .env
```

### 5. Generate the application key
```bash
php artisan key:generate
```

### 6. Configure your `.env` file

Set your database credentials and Reverb keys:
```env
APP_NAME="Public Mandate"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=public_mandate
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 7. Run migrations
```bash
php artisan migrate
```

### 8. Seed sample polls (optional)
```bash
php artisan db:seed --class=PollSeeder
```

### 9. Build frontend assets
```bash
npm run build
```

---

## Running the Application

You need three terminals running at the same time.

**Terminal 1 — Laravel server**
```bash
php artisan serve
```

**Terminal 2 — Reverb WebSocket server**
```bash
php artisan reverb:start
```

**Terminal 3 — Queue worker**
```bash
php artisan queue:work
```

Then open: **http://127.0.0.1:8000/polls**

---

## Application URLs

| Page | URL |
|---|---|
| Public Polls | http://127.0.0.1:8000/polls |
| Admin Dashboard | http://127.0.0.1:8000/admin/dashboard |
| Register | http://127.0.0.1:8000/register |
| Login | http://127.0.0.1:8000/login |

---

## Admin Setup

1. Go to `/register` and create an account
2. All registered users are treated as admins
3. After registration you will land on the admin dashboard
4. From there you can create, edit, delete and manage your polls
5. Each admin can only see and manage their own polls

---

## How Real-Time Works

### Public page — Reverb + Echo

When someone votes on the public polls page, the vote is saved and a `VoteRecorded` event is fired immediately. Laravel Reverb picks this up and broadcasts it over WebSockets to everyone currently on the page. Laravel Echo running in the browser receives the event and jQuery updates the vote bars and counts on screen — no page refresh needed. All browsers watching the same page see the update at the same time.

### Admin results page — Livewire

The admin results page uses a Livewire component that automatically re-renders every 3 seconds using `wire:poll`. Each refresh pulls the latest vote counts from the database and updates the progress bars and percentages on screen. No JavaScript is needed on the admin side — Livewire handles everything.

---

## Testing

Run all tests:
```bash
php artisan test
```

Run vote tests only:
```bash
php artisan test --filter VoteTest
```

The following scenarios are covered:

| Test | What it checks |
|---|---|
| user can vote on active poll | Vote is saved correctly to the database |
| same ip cannot vote twice | A second vote from the same IP is rejected |
| different ips can vote on same poll | Multiple users from different IPs can all vote |
| cannot vote on closed poll | Voting on an inactive poll is blocked |
| cannot vote on expired poll | Voting after the end date is blocked |
| vote count updates after voting | Vote counts are accurate after multiple votes |

---

## Testing Real-Time Functionality

1. Make sure all three terminals are running
2. Open `http://127.0.0.1:8000/polls` in Browser 1
3. Open `http://127.0.0.1:8000/polls` in Browser 2
4. Cast a vote in Browser 1
5. Watch Browser 2 update instantly without any page refresh

To test admin live results:
1. Open any poll results page at `/admin/polls/{id}`
2. Cast a vote from the public page in another browser or tab
3. Watch the admin results page update automatically every 3 seconds

---

## Scalability

The app is built with growth in mind. Here is what is already in place and what can be expanded later.

**Database — votes table**

The votes table already has columns reserved for future use:
- `user_id` — currently null for guests, ready to link to authenticated users when login-based voting is added
- `ip_address` — used now for duplicate prevention, can also be used for geographic analytics
- `session_id` — stored for every vote, useful for fraud detection and audit trails
- `user_agent` — column is defined in the migration, ready to capture browser/device info for analytics
- `country` — column is defined, ready to populate via IP geolocation for regional vote breakdowns
- `is_flagged` — column is defined, ready to use for marking suspicious or duplicate votes for review

**Database — polls table**

The polls table has columns ready for future features:
- `is_multiple_choice` — column is defined, ready to allow users to select more than one option
- `visibility` — column is defined, ready to support public, private or password-protected polls
- `max_votes_per_user` — column is defined, ready to cap how many times a single user can vote
- `results_visibility` — column is defined, ready to control whether results are shown before or after voting

**Database — poll_options table**

The options table has columns ready for richer options:
- `image` — column is defined, ready to attach images to poll options

**Infrastructure**

- Queue connection can be switched from `database` to `redis` for much higher throughput under load
- Reverb can be scaled horizontally using Redis as the pub/sub backend
- Livewire polling interval can be adjusted or replaced with a Reverb listener on the admin side for true real-time instead of polling

---

## License

MIT