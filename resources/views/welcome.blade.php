<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Asset Management') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --pink-50: #fdf2f8;
            --pink-100: #fce7f3;
            --pink-200: #fbcfe8;
            --pink-300: #f9a8d4;
            --pink-400: #f472b6;
            --pink-500: #ec4899;
            --pink-600: #db2777;
            --pink-700: #be185d;
            --pink-800: #9d174d;
            --gray-50: #fafafa;
            --gray-100: #f4f4f5;
            --gray-500: #71717a;
            --gray-700: #3f3f46;
            --gray-900: #18181b;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: linear-gradient(135deg, var(--pink-50) 0%, #fff 40%, var(--pink-50) 100%);
            color: var(--gray-900);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 3rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            font-size: 1.125rem;
            color: var(--pink-700);
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--pink-500), var(--pink-700));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(236, 72, 153, 0.35);
        }

        .logo-icon svg { width: 22px; height: 22px; color: #fff; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--pink-100);
            color: var(--pink-700);
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            border: 1px solid var(--pink-200);
        }

        .badge-dot {
            width: 7px;
            height: 7px;
            background: var(--pink-500);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .hero {
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--pink-700), var(--pink-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.125rem;
            color: var(--gray-500);
            max-width: 560px;
            margin: 0 auto 2rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-size: 0.9375rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--pink-500), var(--pink-600));
            color: #fff;
            box-shadow: 0 4px 16px rgba(236, 72, 153, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.5);
        }

        .btn-outline {
            background: #fff;
            color: var(--pink-600);
            border: 1.5px solid var(--pink-300);
        }

        .btn-outline:hover {
            background: var(--pink-50);
            border-color: var(--pink-400);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-bottom: 3rem;
        }

        .feature-card {
            background: #fff;
            border: 1px solid var(--pink-100);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.25s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .feature-card:hover {
            border-color: var(--pink-300);
            box-shadow: 0 8px 24px rgba(236, 72, 153, 0.12);
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            background: var(--pink-50);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: var(--pink-600);
        }

        .feature-card h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.4rem;
            color: var(--gray-900);
        }

        .feature-card p {
            font-size: 0.875rem;
            color: var(--gray-500);
        }

        .api-section {
            background: linear-gradient(135deg, var(--pink-600), var(--pink-800));
            border-radius: 20px;
            padding: 2rem 2.5rem;
            color: #fff;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            box-shadow: 0 12px 40px rgba(190, 24, 93, 0.3);
        }

        .api-section h2 {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
        }

        .api-section p {
            font-size: 0.9375rem;
            opacity: 0.9;
        }

        .api-endpoint {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            font-family: ui-monospace, monospace;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        footer {
            text-align: center;
            margin-top: 3rem;
            font-size: 0.8125rem;
            color: var(--gray-500);
        }

        footer span { color: var(--pink-500); }

        @media (max-width: 640px) {
            .api-section { padding: 1.5rem; }
            .api-endpoint { font-size: 0.75rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                Asset Management
            </div>
            <div class="badge">
                <span class="badge-dot"></span>
                API Backend
            </div>
        </header>

        <section class="hero">
            <h1>Track, Assign & Maintain<br>Your Company Assets</h1>
            <p>
                A powerful backend for managing IT assets, employee assignments, maintenance requests,
                expenses, and detailed reports — all in one place.
            </p>
            <div class="hero-actions">
                <a href="#api" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Explore API
                </a>
                <a href="#features" class="btn btn-outline">View Features</a>
            </div>
        </section>

        <section class="features" id="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                    </svg>
                </div>
                <h3>Asset Inventory</h3>
                <p>Track asset codes, serial numbers, warranty periods, categories, status, and condition with image support.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3>Employee Assignments</h3>
                <p>Assign assets to employees, track assignment history, and handle returns seamlessly.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3>Maintenance Tracking</h3>
                <p>Submit and manage maintenance requests, reassign assets, and monitor repair status.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3>Expense Management</h3>
                <p>Handle expense requests with approval workflows and monthly or yearly expense reports.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3>Reports & Dashboard</h3>
                <p>Generate asset, user, and category reports with a real-time dashboard overview.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3>Roles & Activity Logs</h3>
                <p>Role-based access control with Sanctum authentication and full activity audit trails.</p>
            </div>
        </section>

        <section class="api-section" id="api">
            <div>
                <h2>RESTful API Ready</h2>
                <p>Connect your frontend or mobile app to this backend via authenticated API endpoints.</p>
            </div>
            <div class="api-endpoint">{{ url('/api') }}</div>
        </section>

        <footer>
            &copy; {{ date('Y') }} <span>Asset Management</span> &mdash; Built with Laravel
        </footer>
    </div>
</body>
</html>
