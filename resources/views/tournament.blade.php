<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tournament->title ?? 'Tournament Manager — Boys & Girls Divisions' }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Inter & Chakra Petch for sports numbers -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Chakra Petch', 'sans-serif'],
                    },
                    colors: {
                        pitch: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f1f5f9;
            color: #0f172a;
            -webkit-tap-highlight-color: transparent;
        }
        .pitch-gradient {
            background: linear-gradient(135deg, #022c22 0%, #064e3b 55%, #0f172a 100%);
        }
        .score-input::-webkit-inner-spin-button,
        .score-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .score-input {
            -moz-appearance: textfield;
        }
        body:not(.is-admin) .admin-only {
            display: none !important;
        }
        body.is-admin .public-only {
            display: none !important;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #fff;
            }
            .print-break {
                page-break-inside: avoid;
            }
            #master-schedule-pdf-area {
                width: 100% !important;
                padding: 0 !important;
            }
        }
        @keyframes pulse-live {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .live-dot {
            animation: pulse-live 1.5s infinite;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-emerald-500 selection:text-white">

    <!-- Top Header -->
    <header class="pitch-gradient text-white shadow-xl sticky top-0 z-30 border-b border-emerald-800/60">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-3.5 sm:py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center shadow-inner shrink-0">
                        <i class="fa-solid fa-trophy text-amber-400 text-xl sm:text-2xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-400/20">Official Cup</span>
                            <!-- Public vs Admin Mode Status Pill -->
                            <span id="mode-status-badge" class="text-[11px] sm:text-xs font-bold px-2.5 py-0.5 rounded-full bg-slate-900/70 text-slate-200 border border-white/15 flex items-center gap-1.5">
                                <i class="fa-solid fa-eye text-emerald-400"></i> Public Live View
                            </span>
                            <!-- Server Sync Status Badge -->
                            <span id="sync-status-badge" class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Server Live
                            </span>
                        </div>
                        <h1 class="text-lg sm:text-2xl font-extrabold tracking-tight mt-0.5 truncate" id="tournament-title-display">
                            {{ $tournament->title ?? 'Boys & Girls Knockout Cup' }}
                        </h1>
                    </div>
                </div>

                <!-- Quick Stats & Action Buttons -->
                <div class="flex flex-wrap items-center gap-2 no-print">
                    <div class="hidden md:flex bg-slate-900/60 border border-white/10 rounded-xl px-3 py-1.5 text-xs items-center gap-3">
                        <div>
                            <span class="text-slate-400 block">Progress</span>
                            <span id="header-progress-text" class="font-display font-bold text-sm text-emerald-400">0 / 22</span>
                        </div>
                        <div class="w-16 h-2 bg-slate-700 rounded-full overflow-hidden">
                            <div id="header-progress-bar" class="bg-emerald-400 h-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    <button onclick="window.print()" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs sm:text-sm font-medium transition flex items-center gap-1.5 border border-white/10">
                        <i class="fa-solid fa-print text-emerald-300"></i>
                        <span class="hidden sm:inline">Print</span>
                    </button>

                    <!-- Admin Controls (Only visible when Admin is unlocked) -->
                    <button onclick="openChangePinModal()" class="admin-only px-3 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-200 text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 border border-amber-400/30" title="Change Admin PIN">
                        <i class="fa-solid fa-key"></i>
                        <span class="hidden sm:inline">PIN</span>
                    </button>

                    <button onclick="openConfirmModal('reset')" class="admin-only px-3 py-2 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-200 text-xs sm:text-sm font-semibold transition flex items-center gap-1.5 border border-rose-400/20">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </button>

                    <!-- Public Button: Unlock Admin -->
                    <button onclick="openPinModal()" class="public-only px-3.5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs sm:text-sm font-extrabold transition flex items-center gap-1.5 shadow-lg shadow-amber-500/10">
                        <i class="fa-solid fa-lock"></i>
                        <span>Admin Login</span>
                    </button>

                    <!-- Admin Button: Lock / Exit to Public View -->
                    <button onclick="exitAdminMode()" class="admin-only px-3.5 py-2 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs sm:text-sm font-extrabold transition flex items-center gap-1.5 shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-lock-open"></i>
                        <span>Lock</span>
                    </button>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <nav class="flex space-x-1 sm:space-x-2 mt-4 overflow-x-auto pb-1 no-print scrollbar-none" aria-label="Tabs">
                <button onclick="switchTab('boys')" id="tab-btn-boys" class="tab-btn flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition whitespace-nowrap bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20 shrink-0">
                    <i class="fa-solid fa-futbol"></i>
                    <span>Boys Division</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-slate-950/20 font-bold">2 Grp</span>
                </button>
                <button onclick="switchTab('girls')" id="tab-btn-girls" class="tab-btn flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition whitespace-nowrap text-emerald-100 hover:bg-white/10 shrink-0">
                    <i class="fa-solid fa-medal"></i>
                    <span>Girls Division</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-white/10 font-bold">Top 4</span>
                </button>
                <button onclick="switchTab('schedule')" id="tab-btn-schedule" class="tab-btn flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition whitespace-nowrap text-emerald-100 hover:bg-white/10 shrink-0">
                    <i class="fa-solid fa-calendar-day"></i>
                    <span>Pitch Schedule</span>
                </button>
                <button onclick="switchTab('teams')" id="tab-btn-teams" class="admin-only tab-btn flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition whitespace-nowrap text-emerald-100 hover:bg-white/10 shrink-0">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Teams & Draw</span>
                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-400/20 text-amber-300 font-bold">ADMIN</span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-6">

        <!-- Public vs Admin Mode Info Bar -->
        <div class="public-only bg-slate-900 text-slate-200 rounded-2xl px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border border-slate-800 shadow-xs no-print">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 live-dot shrink-0"></span>
                <span><strong>Public Live View:</strong> Real-time read-only standings, fixtures, and bracket updates powered by Laravel.</span>
            </div>
            <button onclick="openPinModal()" class="text-amber-400 hover:text-amber-300 font-bold flex items-center gap-1 self-start sm:self-auto">
                <i class="fa-solid fa-key"></i> Enter Admin PIN
            </button>
        </div>

        <div class="admin-only bg-amber-50 border border-amber-300 text-amber-950 rounded-2xl px-4 py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs shadow-xs no-print">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded bg-amber-400 text-slate-950 font-black uppercase tracking-wider text-[10px]">Admin Active</span>
                <span><strong>Score Entry Unlocked:</strong> Scores save automatically to server database and reflect live on all viewers' screens.</span>
            </div>
            <div class="flex items-center gap-3 self-start sm:self-auto">
                <button onclick="openChangePinModal()" class="text-amber-800 hover:text-amber-950 font-bold underline">Change PIN</button>
                <button onclick="exitAdminMode()" class="px-2.5 py-1 rounded-lg bg-amber-900 text-white hover:bg-slate-900 font-bold transition">Lock View</button>
            </div>
        </div>

        <!-- Champions Banner (Dynamically shown when a Final is completed) -->
        <div id="champions-banner" class="hidden"></div>

        <!-- ==================== TAB 1: BOYS DIVISION ==================== -->
        <section id="tab-boys" class="space-y-6 sm:space-y-8">
            <!-- Boys Format Summary Banner -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 font-bold text-lg border border-blue-200">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Boys Tournament — 10 Teams</h2>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">2 Groups of 5</span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">2 Games Each</span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                            Each team plays <strong>2 group games</strong>. The <strong>Top 2 from Group 1</strong> and <strong>Top 2 from Group 2</strong> advance to Semi-Finals.
                        </p>
                    </div>
                </div>
                <div class="admin-only flex flex-wrap items-center gap-2 no-print shrink-0">
                    <button onclick="openAddMatchModal('boys')" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus text-blue-600"></i> Add Match
                    </button>
                    <button onclick="shuffleAndRedraw('boys')" class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold transition flex items-center gap-1.5 border border-blue-200">
                        <i class="fa-solid fa-shuffle"></i> Redraw Draw
                    </button>
                </div>
            </div>

            <!-- Boys Group 1 & Group 2 Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Group 1 Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col print-break">
                    <div class="bg-slate-900 text-white px-4 sm:px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <h3 class="font-bold tracking-wide text-sm sm:text-base">BOYS GROUP 1</h3>
                        </div>
                        <span class="text-[11px] bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-2.5 py-0.5 rounded-full font-medium">
                            Top 2 Qualify
                        </span>
                    </div>

                    <!-- Standings Table Group 1 -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-2.5 pl-3 sm:pl-4 pr-1 w-7">#</th>
                                    <th class="py-2.5 px-2">Team</th>
                                    <th class="py-2.5 px-1.5 text-center">P</th>
                                    <th class="py-2.5 px-1.5 text-center">W</th>
                                    <th class="py-2.5 px-1.5 text-center">D</th>
                                    <th class="py-2.5 px-1.5 text-center">L</th>
                                    <th class="py-2.5 px-1.5 text-center hidden sm:table-cell">GF:GA</th>
                                    <th class="py-2.5 px-1.5 text-center">GD</th>
                                    <th class="py-2.5 pl-1.5 pr-3 sm:pr-4 text-center font-extrabold text-slate-800">PTS</th>
                                </tr>
                            </thead>
                            <tbody id="boys-g1-standings" class="divide-y divide-slate-100 text-xs sm:text-sm"></tbody>
                        </table>
                    </div>

                    <!-- Group 1 Fixtures -->
                    <div class="p-3 sm:p-4 bg-slate-50/70 border-t border-slate-200 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar-check text-emerald-600"></i> Group 1 Fixtures
                                </h4>
                                <span id="boys-g1-games-badge" class="text-[10px] sm:text-[11px] font-medium text-slate-500"></span>
                            </div>
                            <div id="boys-g1-fixtures" class="space-y-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Group 2 Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col print-break">
                    <div class="bg-slate-900 text-white px-4 sm:px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-blue-400"></span>
                            <h3 class="font-bold tracking-wide text-sm sm:text-base">BOYS GROUP 2</h3>
                        </div>
                        <span class="text-[11px] bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-2.5 py-0.5 rounded-full font-medium">
                            Top 2 Qualify
                        </span>
                    </div>

                    <!-- Standings Table Group 2 -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-2.5 pl-3 sm:pl-4 pr-1 w-7">#</th>
                                    <th class="py-2.5 px-2">Team</th>
                                    <th class="py-2.5 px-1.5 text-center">P</th>
                                    <th class="py-2.5 px-1.5 text-center">W</th>
                                    <th class="py-2.5 px-1.5 text-center">D</th>
                                    <th class="py-2.5 px-1.5 text-center">L</th>
                                    <th class="py-2.5 px-1.5 text-center hidden sm:table-cell">GF:GA</th>
                                    <th class="py-2.5 px-1.5 text-center">GD</th>
                                    <th class="py-2.5 pl-1.5 pr-3 sm:pr-4 text-center font-extrabold text-slate-800">PTS</th>
                                </tr>
                            </thead>
                            <tbody id="boys-g2-standings" class="divide-y divide-slate-100 text-xs sm:text-sm"></tbody>
                        </table>
                    </div>

                    <!-- Group 2 Fixtures -->
                    <div class="p-3 sm:p-4 bg-slate-50/70 border-t border-slate-200 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar-check text-blue-600"></i> Group 2 Fixtures
                                </h4>
                                <span id="boys-g2-games-badge" class="text-[10px] sm:text-[11px] font-medium text-slate-500"></span>
                            </div>
                            <div id="boys-g2-fixtures" class="space-y-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boys Knockout Stage -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden print-break">
                <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 text-white px-4 sm:px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-amber-300 shrink-0">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base tracking-wide">BOYS KNOCKOUT STAGE</h3>
                            <p class="text-[11px] sm:text-xs text-slate-300">Semi-Finals & Championship Final</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 no-print">
                        <button onclick="seedKnockoutFromStandings('boys')" class="admin-only text-xs font-bold text-slate-950 bg-emerald-400 hover:bg-emerald-300 px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Seed Top 2
                        </button>
                        <button onclick="clearKnockoutSeeds('boys')" class="admin-only text-xs font-semibold text-slate-200 bg-white/10 hover:bg-white/20 border border-white/15 px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-left"></i> Clear
                        </button>
                        <span class="public-only text-[11px] text-slate-300 bg-white/10 border border-white/15 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-lock mr-1 text-amber-400"></i> Admin bracket
                        </span>
                    </div>
                </div>

                <div id="boys-knockout-container" class="p-3 sm:p-5 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 bg-slate-50/50"></div>
            </div>
        </section>

        <!-- ==================== TAB 2: GIRLS DIVISION ==================== -->
        <section id="tab-girls" class="hidden space-y-6 sm:space-y-8">
            <!-- Girls Format Summary Banner -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 font-bold text-lg border border-purple-200">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-base sm:text-lg font-bold text-slate-900">Girls Tournament — 6 Teams</h2>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800">Single League</span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">2 Games • Top 4 to KO</span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                            Each team plays <strong>2 league games</strong>. The <strong>Top 4 teams</strong> advance to Semi-Finals (1st vs 4th, 2nd vs 3rd).
                        </p>
                    </div>
                </div>
                <div class="admin-only flex flex-wrap items-center gap-2 no-print shrink-0">
                    <button onclick="openAddMatchModal('girls')" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus text-purple-600"></i> Add Match
                    </button>
                    <button onclick="shuffleAndRedraw('girls')" class="px-3 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold transition flex items-center gap-1.5 border border-purple-200">
                        <i class="fa-solid fa-shuffle"></i> Redraw Draw
                    </button>
                </div>
            </div>

            <!-- Girls Standings & Fixtures Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Girls League Table (7 cols) -->
                <div class="lg:col-span-7 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col print-break">
                    <div class="bg-slate-900 text-white px-4 sm:px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 rounded-full bg-purple-400"></span>
                            <h3 class="font-bold tracking-wide text-sm sm:text-base">GIRLS STANDINGS</h3>
                        </div>
                        <span class="text-[11px] bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-2.5 py-0.5 rounded-full font-medium">
                            Top 4 Qualify
                        </span>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] sm:text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3 pl-3 sm:pl-4 pr-1 w-7">#</th>
                                    <th class="py-3 px-2">Team</th>
                                    <th class="py-3 px-1.5 text-center">P</th>
                                    <th class="py-3 px-1.5 text-center">W</th>
                                    <th class="py-3 px-1.5 text-center">D</th>
                                    <th class="py-3 px-1.5 text-center">L</th>
                                    <th class="py-3 px-1.5 text-center hidden sm:table-cell">GF:GA</th>
                                    <th class="py-3 px-1.5 text-center">GD</th>
                                    <th class="py-3 pl-1.5 pr-3 sm:pr-4 text-center font-extrabold text-slate-800">PTS</th>
                                </tr>
                            </thead>
                            <tbody id="girls-standings" class="divide-y divide-slate-100 text-xs sm:text-sm"></tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 bg-emerald-50/70 border-t border-emerald-100 flex items-center justify-between text-[11px] sm:text-xs text-emerald-900">
                        <span class="flex items-center gap-2 font-medium">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block shrink-0"></span>
                            Positions 1–4 advance to Knockouts (SF1: 1v4 • SF2: 2v3)
                        </span>
                    </div>
                </div>

                <!-- Girls 6 League Fixtures (5 cols) -->
                <div class="lg:col-span-5 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col print-break">
                    <div class="bg-slate-900 text-white px-4 sm:px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-futbol text-purple-400"></i>
                            <h3 class="font-bold tracking-wide text-sm sm:text-base">GIRLS FIXTURES</h3>
                        </div>
                        <span id="girls-games-badge" class="text-[11px] text-slate-300 font-medium">2 Games / Team</span>
                    </div>
                    <div class="p-3 sm:p-4 bg-slate-50/70 flex-1 space-y-2" id="girls-fixtures"></div>
                </div>
            </div>

            <!-- Girls Knockout Stage -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden print-break">
                <div class="bg-gradient-to-r from-slate-900 via-purple-950 to-slate-900 text-white px-4 sm:px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-amber-300 shrink-0">
                            <i class="fa-solid fa-trophy"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm sm:text-base tracking-wide">GIRLS KNOCKOUT STAGE (TOP 4)</h3>
                            <p class="text-[11px] sm:text-xs text-slate-300">Semi-Finals & Grand Final</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 no-print">
                        <button onclick="seedKnockoutFromStandings('girls')" class="admin-only text-xs font-bold text-slate-950 bg-purple-300 hover:bg-purple-200 px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Seed Top 4
                        </button>
                        <button onclick="clearKnockoutSeeds('girls')" class="admin-only text-xs font-semibold text-slate-200 bg-white/10 hover:bg-white/20 border border-white/15 px-3 py-1.5 rounded-xl transition flex items-center gap-1.5">
                            <i class="fa-solid fa-rotate-left"></i> Clear
                        </button>
                        <span class="public-only text-[11px] text-purple-200 bg-purple-500/20 border border-purple-400/30 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-lock mr-1 text-amber-400"></i> Admin bracket
                        </span>
                    </div>
                </div>

                <div id="girls-knockout-container" class="p-3 sm:p-5 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 bg-slate-50/50"></div>
            </div>
        </section>

        <!-- ==================== TAB 3: MASTER PITCH SCHEDULE ==================== -->
        <section id="tab-schedule" class="hidden space-y-6">
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-emerald-600"></i>
                        Master Pitch Schedule & Timeline
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                        Chronological pitch sequence for all Boys & Girls matches. Download as PDF or print.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5 no-print">
                    <select id="schedule-filter-division" onchange="renderMasterSchedule()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 text-slate-700">
                        <option value="all">All Divisions</option>
                        <option value="boys">Boys Division</option>
                        <option value="girls">Girls Division</option>
                    </select>
                    <select id="schedule-filter-status" onchange="renderMasterSchedule()" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold bg-slate-50 text-slate-700">
                        <option value="all">All Statuses</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="live">Live Now</option>
                        <option value="completed">Completed</option>
                    </select>
                    <button onclick="downloadPdfSchedule()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF
                    </button>
                </div>
            </div>

            <!-- PDF printable container -->
            <div id="master-schedule-pdf-area" class="space-y-4">
                <div class="hidden print:block mb-4 text-center">
                    <h1 class="text-2xl font-extrabold">Official Tournament Master Pitch Schedule</h1>
                    <p class="text-xs text-slate-500">Boys & Girls Knockout Cup • Generated from Tournament Manager</p>
                </div>
                <div id="master-schedule-list" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
            </div>
        </section>

        <!-- ==================== TAB 4: TEAMS & DRAW MANAGER ==================== -->
        <section id="tab-teams" class="hidden space-y-6 no-print">
            <div class="bg-emerald-950 text-white rounded-2xl p-4 sm:p-5 shadow-md border border-emerald-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-bold bg-emerald-400 text-slate-950">Team Roster</span>
                        <h2 class="text-base sm:text-lg font-bold">Group Draw & Team Names</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-emerald-200/90">
                        Rename teams, switch Boys between Group 1 & Group 2, or reshuffle fixtures instantly.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="clearAllScoresOnly()" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition border border-white/15">
                        <i class="fa-solid fa-eraser mr-1.5"></i> Clear All Scores
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Boys Group 1 Editor -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-900 text-white px-4 py-3 flex items-center justify-between">
                        <h3 class="font-bold text-xs sm:text-sm flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            Boys Group 1 (5 Teams)
                        </h3>
                        <span class="text-[11px] text-slate-400">Swap to G2</span>
                    </div>
                    <div id="editor-boys-g1" class="p-3 sm:p-4 space-y-2.5"></div>
                </div>

                <!-- Boys Group 2 Editor -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-900 text-white px-4 py-3 flex items-center justify-between">
                        <h3 class="font-bold text-xs sm:text-sm flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                            Boys Group 2 (5 Teams)
                        </h3>
                        <span class="text-[11px] text-slate-400">Swap to G1</span>
                    </div>
                    <div id="editor-boys-g2" class="p-3 sm:p-4 space-y-2.5"></div>
                </div>

                <!-- Girls Division Editor -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-900 text-white px-4 py-3 flex items-center justify-between">
                        <h3 class="font-bold text-xs sm:text-sm flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                            Girls Division (6 Teams)
                        </h3>
                        <span class="text-[11px] text-slate-400">Single Group</span>
                    </div>
                    <div id="editor-girls" class="p-3 sm:p-4 space-y-2.5"></div>
                </div>
            </div>

            <!-- Draw Actions Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Randomize Groups & 2-Games-Per-Team Draw</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Automatically pairs teams using a balanced cycle graph so every team plays exactly 2 games (1 Home, 1 Away).
                    </p>
                </div>
                <div class="flex flex-wrap gap-2.5 shrink-0">
                    <button onclick="randomizeBoysGroupsEntirely()" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-dice"></i> Randomize Boys Groups
                    </button>
                    <button onclick="shuffleAndRedraw('girls')" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-dice"></i> Randomize Girls Draw
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-5 text-center text-xs text-slate-500 no-print">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="font-medium text-slate-700">
                <i class="fa-solid fa-futbol text-emerald-600 mr-1"></i>
                Tournament Manager • Hosted with Laravel & SQLite / MySQL
            </p>
            <p>Public Live View & Admin PIN</p>
        </div>
    </footer>

    <!-- ==================== MODAL: ADMIN PIN LOGIN ==================== -->
    <div id="pin-modal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-bold text-base flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-amber-400"></i> Admin PIN Required
                </h3>
                <button onclick="closePinModal()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form onsubmit="handlePinSubmit(event)" class="p-6 space-y-4">
                <p class="text-xs text-slate-600">
                    Enter the organizer PIN to unlock score entry and match controls.
                </p>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">Enter Admin PIN</label>
                    <input type="password" id="admin-pin-input" inputmode="numeric" maxlength="8" placeholder="••••" autocomplete="off"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-center font-display font-bold text-2xl tracking-[0.5em] text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white" />
                    <p id="pin-error-msg" class="text-xs text-rose-600 font-semibold mt-1.5 hidden">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Incorrect PIN code.
                    </p>
                    <p id="default-pin-hint" class="text-[11px] text-slate-400 mt-1.5 text-center">
                        Default PIN: <strong class="text-slate-600 font-mono">1234</strong>
                    </p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closePinModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button type="submit" id="pin-submit-btn" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md flex items-center gap-1.5">
                        <i class="fa-solid fa-unlock"></i> Unlock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: CHANGE ADMIN PIN ==================== -->
    <div id="change-pin-modal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-bold text-base flex items-center gap-2">
                    <i class="fa-solid fa-key text-amber-400"></i> Change Admin PIN
                </h3>
                <button onclick="closeChangePinModal()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form onsubmit="handleChangePinSubmit(event)" class="p-6 space-y-4">
                <p class="text-xs text-slate-600">Set a new 4 to 8 character PIN code.</p>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1.5">New PIN</label>
                    <input type="text" id="new-admin-pin-input" inputmode="numeric" minlength="4" maxlength="8" placeholder="e.g. 2025" required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-center font-display font-bold text-xl tracking-widest text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeChangePinModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-extrabold">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: ADD CUSTOM MATCH ==================== -->
    <div id="custom-match-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-bold text-base flex items-center gap-2" id="custom-match-modal-title">
                    <i class="fa-solid fa-plus-circle text-emerald-400"></i> Add Custom Match
                </h3>
                <button onclick="closeCustomMatchModal()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-5 space-y-4">
                <div id="custom-match-group-wrapper">
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Select Group</label>
                    <select id="custom-match-group" onchange="populateCustomMatchTeams()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium">
                        <option value="G1">Boys Group 1</option>
                        <option value="G2">Boys Group 2</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Home Team</label>
                        <select id="custom-match-home" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium"></select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Away Team</label>
                        <select id="custom-match-away" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium"></select>
                    </div>
                </div>
                <p id="custom-match-error" class="text-xs text-rose-600 font-medium hidden">Home and Away teams must be different.</p>
                <div class="flex justify-end gap-2 pt-2">
                    <button onclick="closeCustomMatchModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button onclick="saveCustomMatch()" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL: CONFIRMATION ==================== -->
    <div id="confirm-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
            <div class="p-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-4 text-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 id="confirm-modal-title" class="text-lg font-bold text-slate-900">Reset Tournament Data?</h3>
                <p id="confirm-modal-desc" class="text-sm text-slate-600 mt-1">
                    This will restore default teams (Anthony, Mumba, etc.) and clear all entered scores in the server database.
                </p>
                <div class="flex justify-end gap-2.5 mt-6">
                    <button onclick="closeConfirmModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button id="confirm-modal-action-btn" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">Confirm Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2 pointer-events-none max-w-xs"></div>

    <!-- ==================== APPLICATION JAVASCRIPT ==================== -->
    <script>
        const STORAGE_KEY = 'tournament_manager_v2_state';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Initial state pre-hydrated directly from Laravel Blade
        const INITIAL_SERVER_STATE = @json($initialState);
        let currentTab = 'boys';
        let currentCustomMatchDivision = 'boys';
        let isAdmin = sessionStorage.getItem('tournament_admin_unlocked') === 'true';
        let adminPinCached = sessionStorage.getItem('tournament_admin_pin') || '1234';
        let isSyncing = false;
        let saveTimeout = null;

        let state = INITIAL_SERVER_STATE || loadLocalState();

        function loadLocalState() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) return JSON.parse(saved);
            } catch (e) {
                console.error('Failed reading localStorage', e);
            }
            return null;
        }

        // Save state to Laravel server database and local cache
        function saveState() {
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
            } catch (e) {}

            if (!isAdmin) return;

            setSyncBadge('syncing');

            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(async () => {
                try {
                    const response = await fetch('/api/tournament/state', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Admin-PIN': adminPinCached,
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                        },
                        body: JSON.stringify({
                            pin: adminPinCached,
                            state: state
                        })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        setSyncBadge('synced');
                    } else if (response.status === 401 || response.status === 403) {
                        setSyncBadge('error');
                        exitAdminMode();
                        showToast('Admin session expired. Please re-enter PIN.', 'error');
                    } else {
                        setSyncBadge('warning');
                    }
                } catch (err) {
                    console.warn('Network sync error:', err);
                    setSyncBadge('warning');
                }
            }, 300);
        }

        // Background polling to keep public viewer screens automatically up to date
        async function fetchServerState() {
            if (isSyncing) return;
            // Avoid interrupting an admin while typing in an input
            const activeEl = document.activeElement;
            const isTyping = activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'SELECT');
            if (isTyping && isAdmin) return;

            try {
                isSyncing = true;
                const response = await fetch('/api/tournament/state', {
                    headers: { 'Accept': 'application/json' }
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.state) {
                        const newSerialized = JSON.stringify(data.state);
                        const currentSerialized = JSON.stringify(state);
                        if (newSerialized !== currentSerialized) {
                            state = data.state;
                            try { localStorage.setItem(STORAGE_KEY, newSerialized); } catch (e) {}
                            renderAll();
                        }
                        setSyncBadge('synced');
                    }
                }
            } catch (e) {
                setSyncBadge('warning');
            } finally {
                isSyncing = false;
            }
        }

        function setSyncBadge(status) {
            const badge = document.getElementById('sync-status-badge');
            if (!badge) return;
            if (status === 'synced') {
                badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-950/80 text-emerald-300 border border-emerald-500/30 flex items-center gap-1';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Server Live';
            } else if (status === 'syncing') {
                badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-950/80 text-amber-300 border border-amber-500/30 flex items-center gap-1';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400 live-dot"></span> Saving...';
            } else if (status === 'warning') {
                badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-600 flex items-center gap-1';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Offline Cache';
            } else {
                badge.className = 'text-[10px] font-semibold px-2 py-0.5 rounded-full bg-rose-950/80 text-rose-300 border border-rose-500/30 flex items-center gap-1';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Auth Error';
            }
        }

        function updateAdminModeUI() {
            const badge = document.getElementById('mode-status-badge');
            if (isAdmin) {
                document.body.classList.add('is-admin');
                if (badge) {
                    badge.className = 'text-[11px] sm:text-xs font-bold px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-950 flex items-center gap-1.5 shadow-xs';
                    badge.innerHTML = '<i class="fa-solid fa-unlock-keyhole"></i> Admin Unlocked';
                }
            } else {
                document.body.classList.remove('is-admin');
                if (badge) {
                    badge.className = 'text-[11px] sm:text-xs font-bold px-2.5 py-0.5 rounded-full bg-slate-900/70 text-slate-200 border border-white/15 flex items-center gap-1.5';
                    badge.innerHTML = '<i class="fa-solid fa-eye text-emerald-400"></i> Public Live View';
                }
                if (currentTab === 'teams') {
                    switchTab('boys');
                    return;
                }
            }
            renderAll();
        }

        function openPinModal() {
            const modal = document.getElementById('pin-modal');
            const input = document.getElementById('admin-pin-input');
            document.getElementById('pin-error-msg').classList.add('hidden');
            input.value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => input.focus(), 50);
        }

        function closePinModal() {
            document.getElementById('pin-modal').classList.add('hidden');
            document.getElementById('pin-modal').classList.remove('flex');
        }

        async function handlePinSubmit(e) {
            e.preventDefault();
            const entered = document.getElementById('admin-pin-input').value.trim();
            const submitBtn = document.getElementById('pin-submit-btn');

            if (!entered) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';

            try {
                const response = await fetch('/api/tournament/verify-pin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ pin: entered })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    isAdmin = true;
                    adminPinCached = entered;
                    sessionStorage.setItem('tournament_admin_unlocked', 'true');
                    sessionStorage.setItem('tournament_admin_pin', entered);
                    closePinModal();
                    updateAdminModeUI();
                    showToast('Admin Mode unlocked! Scores will sync to server.');
                } else {
                    document.getElementById('pin-error-msg').classList.remove('hidden');
                    document.getElementById('admin-pin-input').select();
                }
            } catch (err) {
                // Fallback to client-side verification if offline
                if (entered === (state.adminPin || '1234')) {
                    isAdmin = true;
                    adminPinCached = entered;
                    sessionStorage.setItem('tournament_admin_unlocked', 'true');
                    sessionStorage.setItem('tournament_admin_pin', entered);
                    closePinModal();
                    updateAdminModeUI();
                    showToast('Admin Mode unlocked (Offline)');
                } else {
                    document.getElementById('pin-error-msg').classList.remove('hidden');
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-unlock"></i> Unlock';
            }
        }

        function exitAdminMode() {
            isAdmin = false;
            sessionStorage.removeItem('tournament_admin_unlocked');
            updateAdminModeUI();
            showToast('Switched to Public Read-Only View.');
        }

        function openChangePinModal() {
            if (!isAdmin) return;
            const modal = document.getElementById('change-pin-modal');
            const input = document.getElementById('new-admin-pin-input');
            input.value = adminPinCached || '1234';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => input.select(), 50);
        }

        function closeChangePinModal() {
            document.getElementById('change-pin-modal').classList.add('hidden');
            document.getElementById('change-pin-modal').classList.remove('flex');
        }

        async function handleChangePinSubmit(e) {
            e.preventDefault();
            if (!isAdmin) return;
            const newPin = document.getElementById('new-admin-pin-input').value.trim();
            if (newPin.length < 4) return;

            try {
                const response = await fetch('/api/tournament/change-pin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({
                        current_pin: adminPinCached,
                        new_pin: newPin
                    })
                });

                if (response.ok) {
                    state.adminPin = newPin;
                    adminPinCached = newPin;
                    sessionStorage.setItem('tournament_admin_pin', newPin);
                    closeChangePinModal();
                    showToast(`Admin PIN updated to "${newPin}" on server`);
                } else {
                    showToast('Failed to update PIN on server', 'error');
                }
            } catch (err) {
                state.adminPin = newPin;
                adminPinCached = newPin;
                closeChangePinModal();
                showToast(`Admin PIN updated to "${newPin}"`);
            }
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bg = type === 'success' ? 'bg-slate-900 border-emerald-500/40 text-white' : 'bg-rose-950 border-rose-500/40 text-white';
            toast.className = `${bg} border px-4 py-3 rounded-xl shadow-xl text-xs font-semibold flex items-center gap-2.5 transition-all duration-300`;
            toast.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-info text-amber-400'} text-sm shrink-0"></i>
                <span class="leading-snug">${message}</span>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 2600);
        }

        function getTeamById(division, teamId) {
            const list = division === 'boys' ? state.boysTeams : state.girlsTeams;
            return list.find(t => t.id === teamId) || { id: teamId, name: 'TBD' };
        }

        function calculateStandings(division, groupCode) {
            const teams = (division === 'boys' ? state.boysTeams : state.girlsTeams)
                .filter(t => t.group === groupCode);
            const matches = (division === 'boys' ? state.boysMatches : state.girlsMatches)
                .filter(m => m.group === groupCode);

            const statsMap = {};
            teams.forEach(t => {
                statsMap[t.id] = { id: t.id, name: t.name, seed: t.seed, P: 0, W: 0, D: 0, L: 0, GF: 0, GA: 0, GD: 0, PTS: 0 };
            });

            matches.forEach(m => {
                const hasScores = m.homeScore !== null && m.homeScore !== '' && m.awayScore !== null && m.awayScore !== '';
                if ((m.status === 'completed' || m.status === 'live') && hasScores) {
                    const hs = parseInt(m.homeScore, 10);
                    const as = parseInt(m.awayScore, 10);
                    if (isNaN(hs) || isNaN(as)) return;

                    const home = statsMap[m.homeId];
                    const away = statsMap[m.awayId];
                    if (!home || !away) return;

                    home.P += 1; away.P += 1;
                    home.GF += hs; home.GA += as;
                    away.GF += as; away.GA += hs;
                    home.GD = home.GF - home.GA;
                    away.GD = away.GF - away.GA;

                    if (hs > as) {
                        home.W += 1; home.PTS += 3; away.L += 1;
                    } else if (hs < as) {
                        away.W += 1; away.PTS += 3; home.L += 1;
                    } else {
                        home.D += 1; away.D += 1; home.PTS += 1; away.PTS += 1;
                    }
                }
            });

            return Object.values(statsMap).sort((a, b) => {
                if (b.PTS !== a.PTS) return b.PTS - a.PTS;
                if (b.GD !== a.GD) return b.GD - a.GD;
                if (b.GF !== a.GF) return b.GF - a.GF;
                return a.seed - b.seed;
            });
        }

        function renderStandingsTable(tbodyId, standings, qualifyCount) {
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            tbody.innerHTML = standings.map((team, index) => {
                const pos = index + 1;
                const qualifies = pos <= qualifyCount;
                const rowBg = qualifies ? 'bg-emerald-50/60 hover:bg-emerald-50/90' : 'hover:bg-slate-50';
                const badgeClass = qualifies ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-500';
                const gdFormatted = team.GD > 0 ? `+${team.GD}` : `${team.GD}`;
                const gdColor = team.GD > 0 ? 'text-emerald-600 font-bold' : (team.GD < 0 ? 'text-rose-500' : 'text-slate-500');

                return `
                    <tr class="${rowBg} transition">
                        <td class="py-2.5 pl-3 sm:pl-4 pr-1">
                            <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg text-[11px] sm:text-xs font-display font-bold inline-flex items-center justify-center ${badgeClass}">
                                ${pos}
                            </span>
                        </td>
                        <td class="py-2.5 px-2 font-semibold text-slate-900">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="truncate">${escapeHtml(team.name)}</span>
                                ${qualifies ? `<span class="text-[9px] font-bold uppercase px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 border border-emerald-200">Q</span>` : ''}
                            </div>
                        </td>
                        <td class="py-2.5 px-1.5 text-center font-medium text-slate-600">${team.P}</td>
                        <td class="py-2.5 px-1.5 text-center text-slate-600">${team.W}</td>
                        <td class="py-2.5 px-1.5 text-center text-slate-600">${team.D}</td>
                        <td class="py-2.5 px-1.5 text-center text-slate-600">${team.L}</td>
                        <td class="py-2.5 px-1.5 text-center text-slate-500 text-[11px] hidden sm:table-cell">${team.GF}:${team.GA}</td>
                        <td class="py-2.5 px-1.5 text-center text-[11px] ${gdColor}">${gdFormatted}</td>
                        <td class="py-2.5 pl-1.5 pr-3 sm:pr-4 text-center font-display font-bold text-sm sm:text-base text-slate-900">${team.PTS}</td>
                    </tr>
                `;
            }).join('');
        }

        function renderGroupFixtures(containerId, badgeId, division, groupCode) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const matches = (division === 'boys' ? state.boysMatches : state.girlsMatches)
                .filter(m => m.group === groupCode);

            const completedCount = matches.filter(m => m.status === 'completed').length;
            const badge = document.getElementById(badgeId);
            if (badge) badge.textContent = `${completedCount}/${matches.length} Done`;

            container.innerHTML = matches.map((m, idx) => {
                const home = getTeamById(division, m.homeId);
                const away = getTeamById(division, m.awayId);
                const hs = m.homeScore !== null && m.homeScore !== '' ? m.homeScore : '';
                const as = m.awayScore !== null && m.awayScore !== '' ? m.awayScore : '';
                const hasBothScores = hs !== '' && as !== '';
                const isDone = m.status === 'completed';
                const isLive = m.status === 'live';

                const homeWon = isDone && hasBothScores && parseInt(hs) > parseInt(as);
                const awayWon = isDone && hasBothScores && parseInt(as) > parseInt(hs);

                let statusBadge = '';
                if (isAdmin) {
                    if (isDone) {
                        statusBadge = `<button onclick="cycleMatchStatus('${division}', '${m.id}')" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800" title="Click status">FT</button>`;
                    } else if (isLive) {
                        statusBadge = `<button onclick="cycleMatchStatus('${division}', '${m.id}')" class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800" title="Click status">LIVE</button>`;
                    } else {
                        statusBadge = `<button onclick="cycleMatchStatus('${division}', '${m.id}')" class="text-[10px] font-medium px-1.5 py-0.5 rounded bg-slate-100 text-slate-600" title="Click status">M${idx + 1}</button>`;
                    }
                } else {
                    if (isDone) statusBadge = `<span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">FT</span>`;
                    else if (isLive) statusBadge = `<span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 flex items-center gap-1"><span class="w-1 h-1 rounded-full bg-amber-500 live-dot"></span>LIVE</span>`;
                    else statusBadge = `<span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">M${idx + 1}</span>`;
                }

                let centerScoreHtml = '';
                if (isAdmin) {
                    centerScoreHtml = `
                        <div class="flex items-center gap-1 shrink-0">
                            <input type="number" min="0" max="99" placeholder="-" value="${hs}"
                                oninput="updateGroupMatchScore('${division}', '${m.id}', 'home', this.value)"
                                class="score-input w-9 h-8 rounded border ${isDone ? 'bg-slate-50 border-slate-300' : 'bg-slate-100 border-slate-200'} text-center font-display font-bold text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                            <span class="text-[10px] font-bold text-slate-400">:</span>
                            <input type="number" min="0" max="99" placeholder="-" value="${as}"
                                oninput="updateGroupMatchScore('${division}', '${m.id}', 'away', this.value)"
                                class="score-input w-9 h-8 rounded border ${isDone ? 'bg-slate-50 border-slate-300' : 'bg-slate-100 border-slate-200'} text-center font-display font-bold text-sm text-slate-900 focus:outline-none focus:ring-1 focus:ring-emerald-500" />
                        </div>
                    `;
                } else {
                    if (hasBothScores && (isDone || isLive)) {
                        centerScoreHtml = `
                            <div class="px-2.5 py-0.5 rounded-lg ${isLive ? 'bg-amber-500 text-slate-950' : 'bg-slate-900 text-white'} font-display font-bold text-xs tracking-wider flex items-center gap-1.5 shrink-0">
                                <span>${hs}</span><span class="opacity-40">-</span><span>${as}</span>
                            </div>
                        `;
                    } else {
                        centerScoreHtml = `<div class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-500 font-display font-bold text-[10px] uppercase tracking-wider shrink-0">VS</div>`;
                    }
                }

                return `
                    <div class="bg-white rounded-xl p-2 border ${isDone ? 'border-emerald-200' : (isLive ? 'border-amber-300 shadow-xs' : 'border-slate-200')} flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1 min-w-0 flex-1 justify-end text-right">
                            ${homeWon ? '<i class="fa-solid fa-caret-right text-emerald-600 text-[10px] shrink-0"></i>' : ''}
                            <span class="text-xs truncate ${homeWon ? 'font-extrabold text-emerald-900' : 'font-semibold text-slate-800'}">${escapeHtml(home.name)}</span>
                        </div>

                        ${centerScoreHtml}

                        <div class="flex items-center justify-between gap-1 min-w-0 flex-1">
                            <span class="text-xs truncate ${awayWon ? 'font-extrabold text-emerald-900' : 'font-semibold text-slate-800'}">${escapeHtml(away.name)}</span>
                            <div class="flex items-center gap-1 shrink-0 no-print">
                                ${statusBadge}
                                ${(isAdmin && matches.length > 3) ? `<button onclick="deleteGroupMatch('${division}', '${m.id}')" class="text-slate-300 hover:text-rose-500 text-xs px-1"><i class="fa-solid fa-trash-can"></i></button>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function updateGroupMatchScore(division, matchId, side, value) {
            if (!isAdmin) return;
            const list = division === 'boys' ? state.boysMatches : state.girlsMatches;
            const match = list.find(m => m.id === matchId);
            if (!match) return;

            if (side === 'home') match.homeScore = value === '' ? null : Math.max(0, parseInt(value, 10));
            else match.awayScore = value === '' ? null : Math.max(0, parseInt(value, 10));

            if (match.homeScore !== null && !isNaN(match.homeScore) && match.awayScore !== null && !isNaN(match.awayScore)) {
                match.status = 'completed';
            } else if (match.homeScore === null && match.awayScore === null) {
                match.status = 'upcoming';
            }

            saveState();
            refreshStandingsAndKnockouts();
        }

        function cycleMatchStatus(division, matchId) {
            if (!isAdmin) return;
            const list = division === 'boys' ? state.boysMatches : state.girlsMatches;
            const match = list.find(m => m.id === matchId);
            if (!match) return;

            const order = ['upcoming', 'live', 'completed'];
            match.status = order[(order.indexOf(match.status) + 1) % order.length];
            if (match.status === 'completed' && (match.homeScore === null || match.awayScore === null)) {
                match.homeScore = match.homeScore ?? 0;
                match.awayScore = match.awayScore ?? 0;
            }

            saveState();
            renderAll();
        }

        function deleteGroupMatch(division, matchId) {
            if (!isAdmin) return;
            if (division === 'boys') state.boysMatches = state.boysMatches.filter(m => m.id !== matchId);
            else state.girlsMatches = state.girlsMatches.filter(m => m.id !== matchId);
            saveState();
            renderAll();
            showToast('Match removed');
        }

        function getKnockoutResult(koMatch, resolvedHome, resolvedAway) {
            if (!resolvedHome || !resolvedHome.id || !resolvedAway || !resolvedAway.id) {
                return { winner: null, loser: null, isDraw: false };
            }
            const hs = koMatch.homeScore !== null && koMatch.homeScore !== '' ? parseInt(koMatch.homeScore, 10) : null;
            const as = koMatch.awayScore !== null && koMatch.awayScore !== '' ? parseInt(koMatch.awayScore, 10) : null;

            if (hs === null || as === null || isNaN(hs) || isNaN(as)) return { winner: null, loser: null, isDraw: false };

            if (hs > as) return { winner: resolvedHome, loser: resolvedAway, isDraw: false };
            if (as > hs) return { winner: resolvedAway, loser: resolvedHome, isDraw: false };

            const hp = koMatch.homePen !== null && koMatch.homePen !== '' ? parseInt(koMatch.homePen, 10) : null;
            const ap = koMatch.awayPen !== null && koMatch.awayPen !== '' ? parseInt(koMatch.awayPen, 10) : null;

            if (hp !== null && ap !== null && !isNaN(hp) && !isNaN(ap) && hp !== ap) {
                return hp > ap ? { winner: resolvedHome, loser: resolvedAway, isDraw: true } : { winner: resolvedAway, loser: resolvedHome, isDraw: true };
            }

            return { winner: null, loser: null, isDraw: true };
        }

        function renderKnockoutStage(division) {
            const container = document.getElementById(`${division}-knockout-container`);
            if (!container) return;

            const ko = division === 'boys' ? state.boysKnockout : state.girlsKnockout;
            const allTeams = division === 'boys' ? state.boysTeams : state.girlsTeams;

            let sf1HomePlaceholder, sf1AwayPlaceholder, sf2HomePlaceholder, sf2AwayPlaceholder;
            if (division === 'boys') {
                sf1HomePlaceholder = { id: null, name: 'TBD (1st G1)', tag: '1st G1' };
                sf1AwayPlaceholder = { id: null, name: 'TBD (2nd G2)', tag: '2nd G2' };
                sf2HomePlaceholder = { id: null, name: 'TBD (1st G2)', tag: '1st G2' };
                sf2AwayPlaceholder = { id: null, name: 'TBD (2nd G1)', tag: '2nd G1' };
            } else {
                sf1HomePlaceholder = { id: null, name: 'TBD (1st)', tag: '1st' };
                sf1AwayPlaceholder = { id: null, name: 'TBD (4th)', tag: '4th' };
                sf2HomePlaceholder = { id: null, name: 'TBD (2nd)', tag: '2nd' };
                sf2AwayPlaceholder = { id: null, name: 'TBD (3rd)', tag: '3rd' };
            }

            const sf1Home = ko.sf1.customHomeId ? { ...getTeamById(division, ko.sf1.customHomeId), tag: sf1HomePlaceholder.tag } : sf1HomePlaceholder;
            const sf1Away = ko.sf1.customAwayId ? { ...getTeamById(division, ko.sf1.customAwayId), tag: sf1AwayPlaceholder.tag } : sf1AwayPlaceholder;
            const sf2Home = ko.sf2.customHomeId ? { ...getTeamById(division, ko.sf2.customHomeId), tag: sf2HomePlaceholder.tag } : sf2HomePlaceholder;
            const sf2Away = ko.sf2.customAwayId ? { ...getTeamById(division, ko.sf2.customAwayId), tag: sf2AwayPlaceholder.tag } : sf2AwayPlaceholder;

            const sf1Outcome = getKnockoutResult(ko.sf1, sf1Home, sf1Away);
            const sf2Outcome = getKnockoutResult(ko.sf2, sf2Home, sf2Away);

            const thirdHome = ko.third.customHomeId ? { ...getTeamById(division, ko.third.customHomeId), tag: 'SF1' } : (sf1Outcome.loser ? { ...sf1Outcome.loser, tag: 'Loser SF1' } : { id: null, name: 'TBD (Loser SF1)', tag: 'SF1' });
            const thirdAway = ko.third.customAwayId ? { ...getTeamById(division, ko.third.customAwayId), tag: 'SF2' } : (sf2Outcome.loser ? { ...sf2Outcome.loser, tag: 'Loser SF2' } : { id: null, name: 'TBD (Loser SF2)', tag: 'SF2' });

            const finalHome = ko.final.customHomeId ? { ...getTeamById(division, ko.final.customHomeId), tag: 'SF1' } : (sf1Outcome.winner ? { ...sf1Outcome.winner, tag: 'Winner SF1' } : { id: null, name: 'TBD (Winner SF1)', tag: 'SF1' });
            const finalAway = ko.final.customAwayId ? { ...getTeamById(division, ko.final.customAwayId), tag: 'SF2' } : (sf2Outcome.winner ? { ...sf2Outcome.winner, tag: 'Winner SF2' } : { id: null, name: 'TBD (Winner SF2)', tag: 'SF2' });

            container.innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Semi-Finals</span>
                    </div>
                    ${renderKnockoutCard(division, 'sf1', ko.sf1, sf1Home, sf1Away, allTeams, false)}
                    ${renderKnockoutCard(division, 'sf2', ko.sf2, sf2Home, sf2Away, allTeams, false)}
                </div>
                <div class="space-y-3 flex flex-col justify-center">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">3rd Place Playoff</span>
                    </div>
                    ${renderKnockoutCard(division, 'third', ko.third, thirdHome, thirdAway, allTeams, false)}
                </div>
                <div class="space-y-3 flex flex-col justify-center">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-600 flex items-center gap-1">
                            <i class="fa-solid fa-crown"></i> Championship Final
                        </span>
                    </div>
                    ${renderKnockoutCard(division, 'final', ko.final, finalHome, finalAway, allTeams, true)}
                </div>
            `;
        }

        function renderKnockoutCard(division, stageKey, koMatch, homeTeam, awayTeam, allTeams, isFinal) {
            const outcome = getKnockoutResult(koMatch, homeTeam, awayTeam);
            const hs = koMatch.homeScore ?? '';
            const as = koMatch.awayScore ?? '';
            const hp = koMatch.homePen ?? '';
            const ap = koMatch.awayPen ?? '';

            const homeIsWinner = outcome.winner && outcome.winner.id === homeTeam.id && homeTeam.id !== null;
            const awayIsWinner = outcome.winner && outcome.winner.id === awayTeam.id && awayTeam.id !== null;

            const homeOptions = `<option value="">-- ${escapeHtml(homeTeam.id ? 'Reset TBD' : homeTeam.name)} --</option>` +
                allTeams.map(t => `<option value="${t.id}" ${koMatch.customHomeId === t.id ? 'selected' : ''}>${escapeHtml(t.name)} (${t.group})</option>`).join('');

            const awayOptions = `<option value="">-- ${escapeHtml(awayTeam.id ? 'Reset TBD' : awayTeam.name)} --</option>` +
                allTeams.map(t => `<option value="${t.id}" ${koMatch.customAwayId === t.id ? 'selected' : ''}>${escapeHtml(t.name)} (${t.group})</option>`).join('');

            const homeTeamNameDisplay = isAdmin
                ? `<select onchange="updateKnockoutTeam('${division}', '${stageKey}', 'customHomeId', this.value)" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800">${homeOptions}</select>`
                : `<span class="text-xs sm:text-sm truncate ${homeIsWinner ? 'font-extrabold text-emerald-700' : (homeTeam.id ? 'font-semibold text-slate-800' : 'font-medium italic text-slate-400')}">${escapeHtml(homeTeam.name)}</span>`;

            const awayTeamNameDisplay = isAdmin
                ? `<select onchange="updateKnockoutTeam('${division}', '${stageKey}', 'customAwayId', this.value)" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800">${awayOptions}</select>`
                : `<span class="text-xs sm:text-sm truncate ${awayIsWinner ? 'font-extrabold text-emerald-700' : (awayTeam.id ? 'font-semibold text-slate-800' : 'font-medium italic text-slate-400')}">${escapeHtml(awayTeam.name)}</span>`;

            const homeScoreControl = isAdmin
                ? `<input type="number" min="0" max="99" placeholder="-" value="${hs}" oninput="updateKnockoutScore('${division}', '${stageKey}', 'homeScore', this.value)" class="score-input w-10 h-8 rounded border border-slate-300 bg-slate-50 text-center font-display font-bold text-sm shrink-0" />`
                : `<span class="w-8 h-8 rounded ${homeIsWinner ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800'} font-display font-bold text-xs inline-flex items-center justify-center shrink-0">${hs !== '' ? hs : '-'}</span>`;

            const awayScoreControl = isAdmin
                ? `<input type="number" min="0" max="99" placeholder="-" value="${as}" oninput="updateKnockoutScore('${division}', '${stageKey}', 'awayScore', this.value)" class="score-input w-10 h-8 rounded border border-slate-300 bg-slate-50 text-center font-display font-bold text-sm shrink-0" />`
                : `<span class="w-8 h-8 rounded ${awayIsWinner ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800'} font-display font-bold text-xs inline-flex items-center justify-center shrink-0">${as !== '' ? as : '-'}</span>`;

            return `
                <div class="rounded-xl p-3 border ${isFinal ? 'bg-gradient-to-b from-amber-50/80 to-white border-amber-300 shadow-sm' : 'bg-white border-slate-200 shadow-2xs'} space-y-2.5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                        <div>
                            <span class="text-[11px] font-extrabold ${isFinal ? 'text-amber-800' : 'text-slate-800'}">${koMatch.label}</span>
                            <span class="block text-[10px] text-slate-400">${koMatch.sublabel}</span>
                        </div>
                        ${outcome.winner ? `<span class="text-[10px] font-bold px-2 py-0.5 rounded-full ${isFinal ? 'bg-amber-400 text-slate-950' : 'bg-emerald-100 text-emerald-800'}">${escapeHtml(outcome.winner.name)}</span>` : ''}
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 min-w-0 flex-1">
                            <span class="text-[9px] font-bold px-1 py-0.2 rounded bg-slate-100 text-slate-600 shrink-0">${homeTeam.tag || 'Seed'}</span>
                            <div class="min-w-0 flex-1">${homeTeamNameDisplay}</div>
                        </div>
                        ${homeScoreControl}
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 min-w-0 flex-1">
                            <span class="text-[9px] font-bold px-1 py-0.2 rounded bg-slate-100 text-slate-600 shrink-0">${awayTeam.tag || 'Seed'}</span>
                            <div class="min-w-0 flex-1">${awayTeamNameDisplay}</div>
                        </div>
                        ${awayScoreControl}
                    </div>
                    ${outcome.isDraw ? (
                        isAdmin ? `
                        <div class="pt-1.5 border-t border-amber-200 bg-amber-50 -mx-3 -mb-3 p-2.5 rounded-b-xl flex items-center justify-between gap-2 text-xs">
                            <span class="font-bold text-amber-900 text-[11px]">Penalties (PSO):</span>
                            <div class="flex items-center gap-1">
                                <input type="number" min="0" max="99" placeholder="H" value="${hp}" oninput="updateKnockoutScore('${division}', '${stageKey}', 'homePen', this.value)" class="score-input w-9 h-7 rounded border border-amber-300 bg-white text-center font-bold text-xs" />
                                <span class="text-amber-700">-</span>
                                <input type="number" min="0" max="99" placeholder="A" value="${ap}" oninput="updateKnockoutScore('${division}', '${stageKey}', 'awayPen', this.value)" class="score-input w-9 h-7 rounded border border-amber-300 bg-white text-center font-bold text-xs" />
                            </div>
                        </div>` : `
                        <div class="pt-1.5 border-t border-amber-200 bg-amber-50 -mx-3 -mb-3 px-3 py-1.5 rounded-b-xl flex items-center justify-between text-[11px] text-amber-900 font-bold">
                            <span>Penalties (PSO)</span>
                            <span>${hp !== '' && ap !== '' ? `${hp} - ${ap}` : 'Tied'}</span>
                        </div>`
                    ) : ''}
                </div>
            `;
        }

        function updateKnockoutTeam(division, stageKey, sideField, teamId) {
            if (!isAdmin) return;
            const ko = division === 'boys' ? state.boysKnockout : state.girlsKnockout;
            ko[stageKey][sideField] = teamId === '' ? null : teamId;
            saveState();
            renderKnockoutStage(division);
            updateHeaderProgressAndChampions();
        }

        function seedKnockoutFromStandings(division) {
            if (!isAdmin) return;
            if (division === 'boys') {
                const g1 = calculateStandings('boys', 'G1');
                const g2 = calculateStandings('boys', 'G2');
                state.boysKnockout.sf1.customHomeId = g1[0]?.id || null;
                state.boysKnockout.sf1.customAwayId = g2[1]?.id || null;
                state.boysKnockout.sf2.customHomeId = g2[0]?.id || null;
                state.boysKnockout.sf2.customAwayId = g1[1]?.id || null;
                showToast('Boys Semi-Finals seeded from standings!');
            } else {
                const table = calculateStandings('girls', 'G1');
                state.girlsKnockout.sf1.customHomeId = table[0]?.id || null;
                state.girlsKnockout.sf1.customAwayId = table[3]?.id || null;
                state.girlsKnockout.sf2.customHomeId = table[1]?.id || null;
                state.girlsKnockout.sf2.customAwayId = table[2]?.id || null;
                showToast('Girls Semi-Finals seeded with Top 4!');
            }
            saveState();
            renderKnockoutStage(division);
            updateHeaderProgressAndChampions();
        }

        function clearKnockoutSeeds(division) {
            if (!isAdmin) return;
            const ko = division === 'boys' ? state.boysKnockout : state.girlsKnockout;
            Object.values(ko).forEach(m => {
                m.customHomeId = null; m.customAwayId = null; m.homeScore = null; m.awayScore = null; m.homePen = null; m.awayPen = null; m.status = 'upcoming';
            });
            saveState();
            renderKnockoutStage(division);
            updateHeaderProgressAndChampions();
            showToast('Knockout bracket reset');
        }

        function updateKnockoutScore(division, stageKey, field, value) {
            if (!isAdmin) return;
            const ko = division === 'boys' ? state.boysKnockout : state.girlsKnockout;
            ko[stageKey][field] = value === '' ? null : Math.max(0, parseInt(value, 10));
            ko[stageKey].status = (ko[stageKey].homeScore !== null && ko[stageKey].awayScore !== null) ? 'completed' : 'upcoming';
            saveState();
            refreshStandingsAndKnockouts();
        }

        function refreshStandingsAndKnockouts() {
            renderStandingsTable('boys-g1-standings', calculateStandings('boys', 'G1'), 2);
            renderStandingsTable('boys-g2-standings', calculateStandings('boys', 'G2'), 2);
            renderStandingsTable('girls-standings', calculateStandings('girls', 'G1'), 4);

            const activeEl = document.activeElement;
            const isTyping = activeEl && (activeEl.closest('#boys-knockout-container') || activeEl.closest('#girls-knockout-container'));
            if (!isTyping) {
                renderKnockoutStage('boys');
                renderKnockoutStage('girls');
            }
            updateHeaderProgressAndChampions();
        }

        function updateHeaderProgressAndChampions() {
            const groupTotal = state.boysMatches.length + state.girlsMatches.length;
            const groupDone = state.boysMatches.filter(m => m.status === 'completed').length + state.girlsMatches.filter(m => m.status === 'completed').length;
            const koList = [state.boysKnockout.sf1, state.boysKnockout.sf2, state.boysKnockout.final, state.girlsKnockout.sf1, state.girlsKnockout.sf2, state.girlsKnockout.final];
            const koDone = koList.filter(k => k.homeScore !== null && k.awayScore !== null).length;

            const total = groupTotal + koList.length;
            const done = groupDone + koDone;
            const pct = total > 0 ? Math.round((done / total) * 100) : 0;

            const progText = document.getElementById('header-progress-text');
            const progBar = document.getElementById('header-progress-bar');
            if (progText) progText.textContent = `${done} / ${total}`;
            if (progBar) progBar.style.width = `${pct}%`;

            const banner = document.getElementById('champions-banner');
            const boysChamp = getDivisionChampion('boys');
            const girlsChamp = getDivisionChampion('girls');

            if (boysChamp || girlsChamp) {
                banner.classList.remove('hidden');
                banner.innerHTML = `
                    <div class="bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-600 text-slate-950 rounded-2xl p-4 sm:p-5 shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-slate-950 text-amber-400 flex items-center justify-center text-xl shrink-0">
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-900/80">Champions Crowned</span>
                                <div class="flex flex-wrap items-center gap-3 mt-0.5">
                                    ${boysChamp ? `<span class="text-sm sm:text-base font-extrabold">🏆 Boys: ${escapeHtml(boysChamp.name)}</span>` : ''}
                                    ${girlsChamp ? `<span class="text-sm sm:text-base font-extrabold">👑 Girls: ${escapeHtml(girlsChamp.name)}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                banner.classList.add('hidden');
            }
        }

        function getDivisionChampion(division) {
            const ko = division === 'boys' ? state.boysKnockout : state.girlsKnockout;
            const sf1Res = getKnockoutResult(ko.sf1, ko.sf1.customHomeId ? getTeamById(division, ko.sf1.customHomeId) : { id: null }, ko.sf1.customAwayId ? getTeamById(division, ko.sf1.customAwayId) : { id: null });
            const sf2Res = getKnockoutResult(ko.sf2, ko.sf2.customHomeId ? getTeamById(division, ko.sf2.customHomeId) : { id: null }, ko.sf2.customAwayId ? getTeamById(division, ko.sf2.customAwayId) : { id: null });
            const finHome = ko.final.customHomeId ? getTeamById(division, ko.final.customHomeId) : (sf1Res.winner || { id: null });
            const finAway = ko.final.customAwayId ? getTeamById(division, ko.final.customAwayId) : (sf2Res.winner || { id: null });
            return getKnockoutResult(ko.final, finHome, finAway).winner;
        }

        function renderMasterSchedule() {
            const container = document.getElementById('master-schedule-list');
            if (!container) return;

            const divFilter = document.getElementById('schedule-filter-division').value;
            const statusFilter = document.getElementById('schedule-filter-status').value;

            const g1 = state.boysMatches.filter(m => m.group === 'G1').map((m, i) => ({ ...m, label: `Boys Group 1 (Match #${i + 1})`, badgeColor: 'bg-emerald-500/10 text-emerald-800 border-emerald-200' }));
            const g2 = state.boysMatches.filter(m => m.group === 'G2').map((m, i) => ({ ...m, label: `Boys Group 2 (Match #${i + 1})`, badgeColor: 'bg-blue-500/10 text-blue-800 border-blue-200' }));
            const girls = state.girlsMatches.map((m, i) => ({ ...m, label: `Girls League (Match #${i + 1})`, badgeColor: 'bg-purple-500/10 text-purple-800 border-purple-200' }));

            const combined = [];
            const maxLen = Math.max(g1.length, g2.length, girls.length);
            for (let i = 0; i < maxLen; i++) {
                if (g1[i]) combined.push(g1[i]);
                if (girls[i]) combined.push(girls[i]);
                if (g2[i]) combined.push(g2[i]);
            }

            const filtered = combined.filter(m => {
                if (divFilter !== 'all' && m.division !== divFilter) return false;
                if (statusFilter !== 'all' && m.status !== statusFilter) return false;
                return true;
            });

            if (filtered.length === 0) {
                container.innerHTML = `<div class="col-span-2 bg-white rounded-2xl p-8 text-center text-slate-500 text-sm border border-slate-200">No matches found for this filter.</div>`;
                return;
            }

            container.innerHTML = filtered.map((m, idx) => {
                const home = getTeamById(m.division, m.homeId);
                const away = getTeamById(m.division, m.awayId);
                const hs = m.homeScore ?? '';
                const as = m.awayScore ?? '';
                const hasScores = hs !== '' && as !== '';

                const statusBadge = isAdmin
                    ? `<button onclick="cycleMatchStatus('${m.division}', '${m.id}')" class="text-xs font-bold px-2.5 py-1 rounded-lg ${m.status === 'completed' ? 'bg-emerald-600 text-white' : (m.status === 'live' ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-700')}">
                        ${m.status === 'completed' ? 'FT' : (m.status === 'live' ? 'LIVE' : 'Upcoming')}
                       </button>`
                    : `<span class="text-xs font-bold px-2.5 py-1 rounded-lg ${m.status === 'completed' ? 'bg-emerald-100 text-emerald-800' : (m.status === 'live' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600')}">
                        ${m.status === 'completed' ? 'Full Time' : (m.status === 'live' ? 'LIVE' : 'Upcoming')}
                       </span>`;

                const centerScoreControl = isAdmin
                    ? `<div class="flex items-center gap-1">
                        <input type="number" min="0" max="99" placeholder="-" value="${hs}" oninput="updateGroupMatchScore('${m.division}', '${m.id}', 'home', this.value)" class="score-input w-10 h-9 rounded-lg border border-slate-300 bg-slate-50 text-center font-display font-bold text-sm" />
                        <span class="text-slate-400 font-bold">:</span>
                        <input type="number" min="0" max="99" placeholder="-" value="${as}" oninput="updateGroupMatchScore('${m.division}', '${m.id}', 'away', this.value)" class="score-input w-10 h-9 rounded-lg border border-slate-300 bg-slate-50 text-center font-display font-bold text-sm" />
                       </div>`
                    : (hasScores && (m.status === 'completed' || m.status === 'live')
                        ? `<div class="px-3 py-1 rounded-xl bg-slate-900 text-white font-display font-bold text-sm tracking-wider">${hs} - ${as}</div>`
                        : `<div class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 font-display font-bold text-xs">VS</div>`);

                return `
                    <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/90 hover:shadow-md transition flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-emerald-400 font-display font-bold text-sm flex items-center justify-center shrink-0 shadow-xs">
                                #${idx + 1}
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-md border ${m.badgeColor} inline-block mb-1">
                                    ${m.label}
                                </span>
                                <div class="flex items-center gap-2 text-sm sm:text-base font-bold text-slate-900 truncate">
                                    <span class="truncate">${escapeHtml(home.name)}</span>
                                    <span class="text-xs text-slate-400 font-normal">vs</span>
                                    <span class="truncate">${escapeHtml(away.name)}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            ${centerScoreControl}
                            ${statusBadge}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function downloadPdfSchedule() {
            const printArea = document.getElementById('master-schedule-pdf-area').innerHTML;
            const originalBody = document.body.innerHTML;

            document.body.innerHTML = `
                <div style="font-family: Inter, sans-serif; padding: 20px; color: #0f172a;">
                    <h1 style="font-size: 22px; font-weight: 800; margin-bottom: 4px;">Official Master Pitch Schedule</h1>
                    <p style="font-size: 12px; color: #64748b; margin-bottom: 20px;">Boys & Girls Tournament Manager</p>
                    ${printArea}
                </div>
            `;
            window.print();
            document.body.innerHTML = originalBody;
            window.location.reload();
        }

        function renderTeamEditors() {
            const g1 = document.getElementById('editor-boys-g1');
            const g2 = document.getElementById('editor-boys-g2');
            const girls = document.getElementById('editor-girls');
            if (!g1 || !g2 || !girls) return;

            g1.innerHTML = state.boysTeams.filter(t => t.group === 'G1').map((t, i) => `
                <div class="flex items-center gap-2">
                    <span class="w-5 text-xs font-bold text-slate-400">${i + 1}.</span>
                    <input type="text" value="${escapeHtml(t.name)}" onchange="renameTeam('boys', '${t.id}', this.value)" class="flex-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-800" />
                    <button onclick="moveBoysTeamGroup('${t.id}', 'G2')" class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-600 text-xs font-semibold" title="Move to G2">G2</button>
                </div>
            `).join('');

            g2.innerHTML = state.boysTeams.filter(t => t.group === 'G2').map((t, i) => `
                <div class="flex items-center gap-2">
                    <span class="w-5 text-xs font-bold text-slate-400">${i + 6}.</span>
                    <input type="text" value="${escapeHtml(t.name)}" onchange="renameTeam('boys', '${t.id}', this.value)" class="flex-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-800" />
                    <button onclick="moveBoysTeamGroup('${t.id}', 'G1')" class="px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-600 text-xs font-semibold" title="Move to G1">G1</button>
                </div>
            `).join('');

            girls.innerHTML = state.girlsTeams.map((t, i) => `
                <div class="flex items-center gap-2">
                    <span class="w-5 text-xs font-bold text-slate-400">${i + 1}.</span>
                    <input type="text" value="${escapeHtml(t.name)}" onchange="renameTeam('girls', '${t.id}', this.value)" class="flex-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs sm:text-sm font-semibold text-slate-800" />
                </div>
            `).join('');
        }

        function renameTeam(division, teamId, newName) {
            const clean = newName.trim();
            if (!clean) return;
            const list = division === 'boys' ? state.boysTeams : state.girlsTeams;
            const team = list.find(t => t.id === teamId);
            if (team) {
                team.name = clean;
                saveState();
                renderAll();
                showToast(`Renamed to "${clean}"`);
            }
        }

        function moveBoysTeamGroup(teamId, targetGroup) {
            const team = state.boysTeams.find(t => t.id === teamId);
            if (!team) return;
            const other = state.boysTeams.filter(t => t.group === targetGroup);
            if (other.length >= 5) other[other.length - 1].group = team.group;
            team.group = targetGroup;

            state.boysMatches = [
                ...generateTwoGamesPerTeamFixtures(state.boysTeams.filter(t => t.group === 'G1').map(t => t.id), 'boys', 'G1'),
                ...generateTwoGamesPerTeamFixtures(state.boysTeams.filter(t => t.group === 'G2').map(t => t.id), 'boys', 'G2')
            ];
            saveState();
            renderAll();
            showToast(`Moved to ${targetGroup === 'G1' ? 'Group 1' : 'Group 2'}`);
        }

        function generateTwoGamesPerTeamFixtures(teamIds, division, group) {
            const n = teamIds.length;
            if (n < 3) return [];

            const rawPairs = [];
            for (let i = 0; i < n; i++) {
                rawPairs.push({
                    homeId: teamIds[i],
                    awayId: teamIds[(i + 1) % n]
                });
            }

            const orderedPairs = [];
            for (let i = 0; i < n; i += 2) orderedPairs.push(rawPairs[i]);
            for (let i = 1; i < n; i += 2) orderedPairs.push(rawPairs[i]);

            return orderedPairs.map((pair, idx) => ({
                id: `${division}_${group}_m${idx + 1}_${Date.now()}_${Math.random().toString(36).substring(2, 6)}`,
                division,
                group,
                matchNumber: idx + 1,
                homeId: pair.homeId,
                awayId: pair.awayId,
                homeScore: null,
                awayScore: null,
                status: 'upcoming'
            }));
        }

        function shuffleAndRedraw(division) {
            if (division === 'boys') {
                state.boysMatches = [
                    ...generateTwoGamesPerTeamFixtures(shuffleArray(state.boysTeams.filter(t => t.group === 'G1').map(t => t.id)), 'boys', 'G1'),
                    ...generateTwoGamesPerTeamFixtures(shuffleArray(state.boysTeams.filter(t => t.group === 'G2').map(t => t.id)), 'boys', 'G2')
                ];
                showToast('Boys fixtures redrawn');
            } else {
                state.girlsMatches = generateTwoGamesPerTeamFixtures(shuffleArray(state.girlsTeams.map(t => t.id)), 'girls', 'G1');
                showToast('Girls fixtures redrawn');
            }
            saveState();
            renderAll();
        }

        function randomizeBoysGroupsEntirely() {
            const shuffled = shuffleArray([...state.boysTeams]);
            shuffled.forEach((t, idx) => t.group = idx < 5 ? 'G1' : 'G2');
            state.boysMatches = [
                ...generateTwoGamesPerTeamFixtures(state.boysTeams.filter(t => t.group === 'G1').map(t => t.id), 'boys', 'G1'),
                ...generateTwoGamesPerTeamFixtures(state.boysTeams.filter(t => t.group === 'G2').map(t => t.id), 'boys', 'G2')
            ];
            saveState();
            renderAll();
            showToast('Boys teams randomly regrouped');
        }

        function clearAllScoresOnly() {
            state.boysMatches.forEach(m => { m.homeScore = null; m.awayScore = null; m.status = 'upcoming'; });
            state.girlsMatches.forEach(m => { m.homeScore = null; m.awayScore = null; m.status = 'upcoming'; });
            Object.values(state.boysKnockout).forEach(k => { k.homeScore = null; k.awayScore = null; k.homePen = null; k.awayPen = null; k.status = 'upcoming'; });
            Object.values(state.girlsKnockout).forEach(k => { k.homeScore = null; k.awayScore = null; k.homePen = null; k.awayPen = null; k.status = 'upcoming'; });
            saveState();
            renderAll();
            showToast('Scores cleared');
        }

        function openAddMatchModal(division) {
            currentCustomMatchDivision = division;
            const modal = document.getElementById('custom-match-modal');
            const groupWrapper = document.getElementById('custom-match-group-wrapper');
            document.getElementById('custom-match-error').classList.add('hidden');
            if (division === 'boys') groupWrapper.classList.remove('hidden');
            else groupWrapper.classList.add('hidden');

            populateCustomMatchTeams();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCustomMatchModal() {
            document.getElementById('custom-match-modal').classList.add('hidden');
            document.getElementById('custom-match-modal').classList.remove('flex');
        }

        function populateCustomMatchTeams() {
            const homeSel = document.getElementById('custom-match-home');
            const awaySel = document.getElementById('custom-match-away');
            const groupCode = currentCustomMatchDivision === 'boys' ? document.getElementById('custom-match-group').value : 'G1';
            const teams = (currentCustomMatchDivision === 'boys' ? state.boysTeams : state.girlsTeams).filter(t => t.group === groupCode);
            const html = teams.map(t => `<option value="${t.id}">${escapeHtml(t.name)}</option>`).join('');
            homeSel.innerHTML = html;
            awaySel.innerHTML = html;
            if (teams.length > 1) awaySel.selectedIndex = 1;
        }

        function saveCustomMatch() {
            const homeId = document.getElementById('custom-match-home').value;
            const awayId = document.getElementById('custom-match-away').value;
            if (homeId === awayId) {
                document.getElementById('custom-match-error').classList.remove('hidden');
                return;
            }
            const groupCode = currentCustomMatchDivision === 'boys' ? document.getElementById('custom-match-group').value : 'G1';
            const newMatch = { id: `${currentCustomMatchDivision}_${groupCode}_cust_${Date.now()}`, division: currentCustomMatchDivision, group: groupCode, matchNumber: 99, homeId, awayId, homeScore: null, awayScore: null, status: 'upcoming' };
            if (currentCustomMatchDivision === 'boys') state.boysMatches.push(newMatch);
            else state.girlsMatches.push(newMatch);

            saveState();
            closeCustomMatchModal();
            renderAll();
            showToast('Match added');
        }

        function openConfirmModal(type) {
            const modal = document.getElementById('confirm-modal');
            const btn = document.getElementById('confirm-modal-action-btn');
            if (type === 'reset') {
                btn.onclick = async () => {
                    try {
                        const response = await fetch('/api/tournament/reset', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Admin-PIN': adminPinCached,
                                'X-CSRF-TOKEN': CSRF_TOKEN,
                            },
                            body: JSON.stringify({ pin: adminPinCached })
                        });
                        if (response.ok) {
                            const data = await response.json();
                            if (data.state) state = data.state;
                        }
                    } catch (e) {}
                    saveState();
                    renderAll();
                    closeConfirmModal();
                    showToast('Tournament reset to defaults');
                };
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeConfirmModal() {
            document.getElementById('confirm-modal').classList.add('hidden');
            document.getElementById('confirm-modal').classList.remove('flex');
        }

        function switchTab(tabName) {
            if (tabName === 'teams' && !isAdmin) {
                openPinModal();
                return;
            }
            currentTab = tabName;
            ['boys', 'girls', 'schedule', 'teams'].forEach(t => {
                const sec = document.getElementById(`tab-${t}`);
                const btn = document.getElementById(`tab-btn-${t}`);
                if (!sec || !btn) return;
                if (t === tabName) {
                    sec.classList.remove('hidden');
                    btn.className = `${t === 'teams' ? 'admin-only ' : ''}tab-btn flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition whitespace-nowrap bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20 shrink-0`;
                } else {
                    sec.classList.add('hidden');
                    btn.className = `${t === 'teams' ? 'admin-only ' : ''}tab-btn flex items-center gap-2 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-semibold text-xs sm:text-sm transition whitespace-nowrap text-emerald-100 hover:bg-white/10 shrink-0`;
                }
            });
            renderAll();
        }

        function shuffleArray(arr) {
            const copy = [...arr];
            for (let i = copy.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [copy[i], copy[j]] = [copy[j], copy[i]];
            }
            return copy;
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function renderAll() {
            renderGroupFixtures('boys-g1-fixtures', 'boys-g1-games-badge', 'boys', 'G1');
            renderGroupFixtures('boys-g2-fixtures', 'boys-g2-games-badge', 'boys', 'G2');
            renderGroupFixtures('girls-fixtures', 'girls-games-badge', 'girls', 'G1');
            refreshStandingsAndKnockouts();
            renderMasterSchedule();
            renderTeamEditors();
        }

        window.onload = function () {
            updateAdminModeUI();
            // Start periodic polling every 4 seconds to sync live scores across all devices
            setInterval(fetchServerState, 4000);
        };
    </script>
</body>
</html>
