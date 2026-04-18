## Frontend 'Tear Down and Rebuild' — Modern Cyberpunk Aesthetic

### 🚀 Process Summary
This PR executes a complete 'Tear Down and Rebuild' strategy for the frontend of the Portfolio-CMS. The legacy, outdated frontend code has been archived in `.backup_views/`, and a fresh, modern, and highly responsive UI has been built from scratch using strictly Laravel Blade, Tailwind CSS v4, and vanilla JavaScript (via Vite).

### ✨ Key Highlights
- **Tailwind CSS v4 & Vite:** Leverages the latest Tailwind 4 features with a "Subtle Cyberpunk" (Zinc/Emerald) aesthetic.
- **Modular Blade Components:** Introduced a robust component system (`x-button`, `x-card`, `x-input`, `x-modal`) to keep the codebase DRY and consistent.
- **Glassmorphism Theme:** Implemented a sleek, professional UI with glassmorphism effects, neon accents, and smooth micro-interactions.
- **Mobile-First Approach:** Every view (Public Portfolio & Admin Dashboard) is fully responsive and optimized for mobile devices.
- **Clean Admin Suite:** Rebuilt the entire admin panel with a functional dashboard, stat overview, and full CRUD interfaces for all resources.
- **Data Integrity:** Preserved all existing Laravel controller logic, CSRF protection, and form state retention (`old()`).

### 🛠️ Testing Locally
1. **Clone the repository:**
   ```bash
   git clone -b feature/frontend-rebuild https://github.com/mohammedzom/Portfolio-CMS.git
   cd Portfolio-CMS
   ```
2. **Install dependencies:**
   ```bash
   composer install
   pnpm install  # or npm install
   ```
3. **Build assets:**
   ```bash
   pnpm run build
   ```
4. **Environment Setup:**
   Ensure your `.env` file is configured and run migrations/seeders if necessary.
5. **Serve the application:**
   ```bash
   php artisan serve
   ```
6. **Verification:**
   - Visit `/` to see the new public portfolio.
   - Visit `/admin` to access the rebuilt dashboard.

---
*Built with ❤️ by Manus — Autonomous Senior Full-Stack Engineer.*
