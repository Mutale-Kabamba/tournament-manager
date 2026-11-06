<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Play it Forward — Tiyende Pamodzi Tournament</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Outfit (Premier League style headline) & Chakra Petch (Numbers) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        display: ['Chakra Petch', 'sans-serif'],
                    },
                    colors: {
                        pl: {
                            purple: '#38003c',       // Iconic Premier League deep plum
                            dark: '#240027',         // Deeper plum shadow
                            night: '#150017',        // Midnight base
                            neon: '#00ff87',         // Premier League electric mint
                            cyan: '#04f5ff',         // Electric cyan
                            pink: '#ff2882',         // Premier League neon pink
                            card: '#ffffff',
                            surface: '#f5f3f7',
                            border: '#e4dbe7',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f6f4f8;
            color: #1a001f;
            -webkit-tap-highlight-color: transparent;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .pl-gradient {
            background: linear-gradient(135deg, #38003c 0%, #200024 60%, #150017 100%);
        }
        .pl-neon-gradient {
            background: linear-gradient(135deg, #00ff87 0%, #04f5ff 100%);
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
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }
        .live-dot {
            animation: pulse-live 1.2s infinite;
        }
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col selection:bg-pl-neon selection:text-pl-purple">

    <!-- Top Broadcast Header -->
    <header class="pl-gradient text-white shadow-2xl sticky top-0 z-30 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-2.5 sm:py-3.5">
            <div class="flex items-center justify-between gap-2">
                <!-- Branding -->
                <div class="flex items-center gap-2.5 sm:gap-3.5 min-w-0">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shadow-inner shrink-0 relative overflow-hidden group">
                        <div class="absolute inset-0 pl-neon-gradient opacity-20"></div>
                        <i class="fa-solid fa-trophy text-amber-300 text-lg sm:text-xl drop-shadow-md"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[9px] sm:text-[10px] font-black tracking-widest uppercase px-2 py-0.5 rounded bg-pl-pink text-white shadow-xs">
                                Matchday
                            </span>
                            <span id="mode-status-badge" class="text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-full bg-black/40 text-slate-200 border border-white/15 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-pl-neon live-dot"></span> Public Live
                            </span>
                            <span id="sync-status-badge" class="hidden sm:flex text-[9px] font-bold px-1.5 py-0.5 rounded bg-white/10 text-emerald-300 border border-emerald-400/30 items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-pl-neon"></span> Live Sync
                            </span>
                        </div>
                        <h1 class="text-base sm:text-xl md:text-2xl font-black tracking-tight mt-0.5 truncate leading-tight text-white flex items-center gap-1.5">
                            Play it Forward
                            <span class="text-xs sm:text-sm font-semibold text-pl-neon hidden md:inline">• Tiyende Pamodzi</span>
                        </h1>
                        <p class="text-[10px] sm:text-xs text-purple-200/80 font-medium truncate md:hidden">
                            Tiyende Pamodzi Tournament
                        </p>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center gap-1.5 sm:gap-2 no-print shrink-0">
                    <!-- Progress Pill (hidden on tiny screens) -->
                    <div class="hidden lg:flex bg-black/30 border border-white/10 rounded-xl px-2.5 py-1 text-xs items-center gap-2">
                        <span id="header-progress-text" class="font-display font-bold text-pl-neon text-xs">0 / 22</span>
                        <div class="w-12 h-1.5 bg-white/20 rounded-full overflow-hidden">
                            <div id="header-progress-bar" class="bg-pl-neon h-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>

                    <button onclick="window.print()" class="p-2 sm:px-3 sm:py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition border border-white/15 flex items-center gap-1.5" title="Print Tournament">
                        <i class="fa-solid fa-print text-pl-neon text-sm sm:text-xs"></i>
                        <span class="hidden sm:inline">Print</span>
                    </button>

                    <!-- Admin Unlocked Buttons -->
                    <button onclick="openChangePinModal()" class="admin-only p-2 sm:px-3 sm:py-1.5 rounded-xl bg-amber-400/20 hover:bg-amber-400/30 text-amber-200 text-xs font-bold transition border border-amber-400/30 flex items-center gap-1.5" title="Change PIN">
                        <i class="fa-solid fa-key"></i>
                        <span class="hidden sm:inline">PIN</span>
                    </button>

                    <button onclick="openConfirmModal('reset')" class="admin-only p-2 sm:px-3 sm:py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-200 text-xs font-bold transition border border-rose-400/30 flex items-center gap-1.5" title="Reset All">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </button>

                    <!-- Lock / Login Pill -->
                    <button onclick="openPinModal()" class="public-only px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl pl-neon-gradient text-pl-purple font-extrabold text-xs sm:text-sm transition flex items-center gap-1.5 shadow-md shadow-pl-neon/20 hover:brightness-110 active:scale-95">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span>Admin</span>
                    </button>

                    <button onclick="exitAdminMode()" class="admin-only px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-pl-pink text-white font-extrabold text-xs sm:text-sm transition flex items-center gap-1.5 shadow-md shadow-pl-pink/20 hover:brightness-110 active:scale-95">
                        <i class="fa-solid fa-lock-open text-xs"></i>
                        <span>Lock</span>
                    </button>
                </div>
            </div>

            <!-- Premier League Tabs Navigation -->
            <nav class="flex space-x-1.5 sm:space-x-2 mt-2.5 sm:mt-3 overflow-x-auto pb-1 no-print scrollbar-none" aria-label="Tabs">
                <button onclick="switchTab('boys')" id="tab-btn-boys" class="tab-btn flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl font-extrabold text-xs sm:text-sm transition whitespace-nowrap bg-pl-neon text-pl-purple shadow-md shadow-pl-neon/20 shrink-0">
                    <i class="fa-solid fa-futbol text-xs"></i>
                    <span>Boys</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-pl-purple/20 font-black">10 Teams</span>
                </button>
                <button onclick="switchTab('girls')" id="tab-btn-girls" class="tab-btn flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl font-bold text-xs sm:text-sm transition whitespace-nowrap text-purple-200 hover:bg-white/10 shrink-0">
                    <i class="fa-solid fa-crown text-xs"></i>
                    <span>Girls</span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-white/10 font-black">6 Teams</span>
                </button>
                <button onclick="switchTab('schedule')" id="tab-btn-schedule" class="tab-btn flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl font-bold text-xs sm:text-sm transition whitespace-nowrap text-purple-200 hover:bg-white/10 shrink-0">
                    <i class="fa-solid fa-calendar-day text-xs"></i>
                    <span>Pitch Schedule</span>
                </button>
                <button onclick="switchTab('teams')" id="tab-btn-teams" class="admin-only tab-btn flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl font-bold text-xs sm:text-sm transition whitespace-nowrap text-purple-200 hover:bg-white/10 shrink-0">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    <span>Draw & Teams</span>
                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-400 text-slate-950 font-black">ADMIN</span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-2.5 sm:px-6 lg:px-8 py-3.5 sm:py-6 space-y-4 sm:space-y-6">

        <!-- Mobile Quick Info Strips (Clean & Minimal) -->
        <div class="public-only bg-white border border-pl-border rounded-xl px-3 py-2 flex items-center justify-between text-xs shadow-2xs no-print">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-2 h-2 rounded-full bg-pl-neon live-dot shrink-0"></span>
                <span class="text-slate-700 font-semibold truncate text-[11px] sm:text-xs">Live Tournament Standings & Results</span>
            </div>
            <button onclick="openPinModal()" class="text-pl-purple hover:text-pl-pink font-extrabold flex items-center gap-1 text-[11px] sm:text-xs shrink-0 ml-2">
                <i class="fa-solid fa-key text-[10px]"></i> Admin PIN
            </button>
        </div>

        <div class="admin-only bg-amber-500/10 border border-amber-300 text-amber-950 rounded-xl px-3 py-2 flex items-center justify-between text-xs shadow-2xs no-print">
            <div class="flex items-center gap-2 min-w-0">
                <span class="px-1.5 py-0.5 rounded bg-amber-400 text-slate-950 font-black text-[9px] uppercase tracking-wider">Admin</span>
                <span class="font-bold text-[11px] sm:text-xs truncate">Score entry active. Auto-syncing to cloud.</span>
            </div>
            <button onclick="exitAdminMode()" class="text-amber-900 hover:text-slate-900 font-black text-[11px] shrink-0 ml-2">
                Lock <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i>
            </button>
        </div>

        <!-- Champions Banner -->
        <div id="champions-banner" class="hidden"></div>

        <!-- ==================== TAB 1: BOYS DIVISION ==================== -->
        <section id="tab-boys" class="space-y-4 sm:space-y-6">
            <!-- Compact Format Strip -->
            <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-2xs border border-pl-border flex flex-wrap items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-pl-purple text-pl-neon flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">Boys Tournament</h2>
                        <div class="flex items-center gap-1.5 flex-wrap text-[10px] sm:text-xs font-semibold text-slate-500 mt-0.5">
                            <span class="px-2 py-0.2 rounded bg-purple-100 text-pl-purple font-bold">10 Teams</span>
                            <span>•</span>
                            <span class="px-2 py-0.2 rounded bg-blue-100 text-blue-800 font-bold">2 Groups of 5</span>
                            <span>•</span>
                            <span class="px-2 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold">2 Games / Team</span>
                            <span>•</span>
                            <span class="text-pl-purple font-extrabold">Top 2 to SF</span>
                        </div>
                    </div>
                </div>
                <div class="admin-only flex items-center gap-1.5 no-print shrink-0">
                    <button onclick="openAddMatchModal('boys')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1">
                        <i class="fa-solid fa-plus text-pl-purple text-[10px]"></i> Match
                    </button>
                    <button onclick="shuffleAndRedraw('boys')" class="px-2.5 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-pl-purple text-xs font-bold transition border border-purple-200 flex items-center gap-1">
                        <i class="fa-solid fa-shuffle text-[10px]"></i> Redraw
                    </button>
                </div>
            </div>

            <!-- Group 1 & 2 Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <!-- Group 1 Card -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xs border border-pl-border overflow-hidden flex flex-col print-break">
                    <div class="pl-gradient text-white px-3.5 py-2.5 sm:px-4 sm:py-3 flex items-center justify-between border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-pl-neon"></span>
                            <h3 class="font-black text-xs sm:text-sm tracking-wider uppercase">Boys Group 1</h3>
                        </div>
                        <span class="text-[10px] bg-white/10 text-pl-neon border border-pl-neon/30 px-2 py-0.5 rounded font-extrabold uppercase">
                            Top 2 Qualify
                        </span>
                    </div>

                    <!-- Standings Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                    <th class="py-2 pl-3 pr-1 w-6">#</th>
                                    <th class="py-2 px-2">Team</th>
                                    <th class="py-2 px-1 text-center w-7">P</th>
                                    <th class="py-2 px-1 text-center w-7">W</th>
                                    <th class="py-2 px-1 text-center w-7">D</th>
                                    <th class="py-2 px-1 text-center w-7">L</th>
                                    <th class="py-2 px-1 text-center w-10 hidden sm:table-cell">GF:GA</th>
                                    <th class="py-2 px-1 text-center w-8">GD</th>
                                    <th class="py-2 pr-3 pl-1 text-center w-9 font-black text-pl-purple">PTS</th>
                                </tr>
                            </thead>
                            <tbody id="boys-g1-standings" class="divide-y divide-slate-100 font-medium"></tbody>
                        </table>
                    </div>

                    <!-- Fixtures -->
                    <div class="p-2.5 sm:p-3.5 bg-slate-50/80 border-t border-slate-200 flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Fixtures</span>
                            <span id="boys-g1-games-badge" class="text-[10px] font-bold text-slate-400"></span>
                        </div>
                        <div id="boys-g1-fixtures" class="space-y-1.5"></div>
                    </div>
                </div>

                <!-- Group 2 Card -->
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xs border border-pl-border overflow-hidden flex flex-col print-break">
                    <div class="pl-gradient text-white px-3.5 py-2.5 sm:px-4 sm:py-3 flex items-center justify-between border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-pl-cyan"></span>
                            <h3 class="font-black text-xs sm:text-sm tracking-wider uppercase">Boys Group 2</h3>
                        </div>
                        <span class="text-[10px] bg-white/10 text-pl-cyan border border-pl-cyan/30 px-2 py-0.5 rounded font-extrabold uppercase">
                            Top 2 Qualify
                        </span>
                    </div>

                    <!-- Standings Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                    <th class="py-2 pl-3 pr-1 w-6">#</th>
                                    <th class="py-2 px-2">Team</th>
                                    <th class="py-2 px-1 text-center w-7">P</th>
                                    <th class="py-2 px-1 text-center w-7">W</th>
                                    <th class="py-2 px-1 text-center w-7">D</th>
                                    <th class="py-2 px-1 text-center w-7">L</th>
                                    <th class="py-2 px-1 text-center w-10 hidden sm:table-cell">GF:GA</th>
                                    <th class="py-2 px-1 text-center w-8">GD</th>
                                    <th class="py-2 pr-3 pl-1 text-center w-9 font-black text-pl-purple">PTS</th>
                                </tr>
                            </thead>
                            <tbody id="boys-g2-standings" class="divide-y divide-slate-100 font-medium"></tbody>
                        </table>
                    </div>

                    <!-- Fixtures -->
                    <div class="p-2.5 sm:p-3.5 bg-slate-50/80 border-t border-slate-200 flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-500">Fixtures</span>
                            <span id="boys-g2-games-badge" class="text-[10px] font-bold text-slate-400"></span>
                        </div>
                        <div id="boys-g2-fixtures" class="space-y-1.5"></div>
                    </div>
                </div>
            </div>

            <!-- Boys Knockout Bracket -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xs border border-pl-border overflow-hidden print-break">
                <div class="pl-gradient text-white px-3.5 py-3 sm:px-5 sm:py-3.5 flex flex-wrap items-center justify-between gap-2 border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-300 text-sm"></i>
                        <h3 class="font-black text-xs sm:text-sm tracking-wider uppercase">Boys Knockout Stage</h3>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button onclick="seedKnockoutFromStandings('boys')" class="admin-only text-[11px] font-extrabold text-pl-purple bg-pl-neon hover:brightness-110 px-2.5 py-1 rounded-lg transition shadow-xs flex items-center gap-1">
                            <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i> Seed Top 2
                        </button>
                        <button onclick="clearKnockoutSeeds('boys')" class="admin-only text-[11px] font-semibold text-slate-200 bg-white/10 hover:bg-white/20 px-2 py-1 rounded-lg transition">
                            Clear
                        </button>
                    </div>
                </div>
                <div id="boys-knockout-container" class="p-2.5 sm:p-4 grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 bg-slate-50/60"></div>
            </div>
        </section>

        <!-- ==================== TAB 2: GIRLS DIVISION ==================== -->
        <section id="tab-girls" class="hidden space-y-4 sm:space-y-6">
            <!-- Compact Format Strip -->
            <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-2xs border border-pl-border flex flex-wrap items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-purple-100 text-pl-purple flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-crown"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">Girls Tournament</h2>
                        <div class="flex items-center gap-1.5 flex-wrap text-[10px] sm:text-xs font-semibold text-slate-500 mt-0.5">
                            <span class="px-2 py-0.2 rounded bg-purple-100 text-pl-purple font-bold">6 Teams</span>
                            <span>•</span>
                            <span class="px-2 py-0.2 rounded bg-purple-50 text-purple-700 font-bold">Single League</span>
                            <span>•</span>
                            <span class="px-2 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold">2 Games / Team</span>
                            <span>•</span>
                            <span class="text-pl-purple font-extrabold">Top 4 to SF</span>
                        </div>
                    </div>
                </div>
                <div class="admin-only flex items-center gap-1.5 no-print shrink-0">
                    <button onclick="openAddMatchModal('girls')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1">
                        <i class="fa-solid fa-plus text-pl-purple text-[10px]"></i> Match
                    </button>
                    <button onclick="shuffleAndRedraw('girls')" class="px-2.5 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-pl-purple text-xs font-bold transition border border-purple-200 flex items-center gap-1">
                        <i class="fa-solid fa-shuffle text-[10px]"></i> Redraw
                    </button>
                </div>
            </div>

            <!-- Standings & Fixtures Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
                <!-- Standings Table -->
                <div class="lg:col-span-7 bg-white rounded-xl sm:rounded-2xl shadow-2xs border border-pl-border overflow-hidden flex flex-col print-break">
                    <div class="pl-gradient text-white px-3.5 py-2.5 sm:px-4 sm:py-3 flex items-center justify-between border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-list-ol text-pl-neon text-xs"></i>
                            <h3 class="font-black text-xs sm:text-sm tracking-wider uppercase">Girls Standings</h3>
                        </div>
                        <span class="text-[10px] bg-white/10 text-pl-neon border border-pl-neon/30 px-2 py-0.5 rounded font-extrabold uppercase">
                            Top 4 to Semi-Finals
                        </span>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                    <th class="py-2 pl-3 pr-1 w-6">#</th>
                                    <th class="py-2 px-2">Team</th>
                                    <th class="py-2 px-1 text-center w-7">P</th>
                                    <th class="py-2 px-1 text-center w-7">W</th>
                                    <th class="py-2 px-1 text-center w-7">D</th>
                                    <th class="py-2 px-1 text-center w-7">L</th>
                                    <th class="py-2 px-1 text-center w-10 hidden sm:table-cell">GF:GA</th>
                                    <th class="py-2 px-1 text-center w-8">GD</th>
                                    <th class="py-2 pr-3 pl-1 text-center w-9 font-black text-pl-purple">PTS</th>
                                </tr>
                            </thead>
                            <tbody id="girls-standings" class="divide-y divide-slate-100 font-medium"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Fixtures -->
                <div class="lg:col-span-5 bg-white rounded-xl sm:rounded-2xl shadow-2xs border border-pl-border overflow-hidden flex flex-col print-break">
                    <div class="pl-gradient text-white px-3.5 py-2.5 sm:px-4 sm:py-3 flex items-center justify-between border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-futbol text-pl-cyan text-xs"></i>
                            <h3 class="font-black text-xs sm:text-sm tracking-wider uppercase">Girls Fixtures</h3>
                        </div>
                        <span id="girls-games-badge" class="text-[10px] text-purple-200 font-bold"></span>
                    </div>
                    <div class="p-2.5 sm:p-3.5 bg-slate-50/80 flex-1 space-y-1.5" id="girls-fixtures"></div>
                </div>
            </div>

            <!-- Girls Knockout Bracket -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xs border border-pl-border overflow-hidden print-break">
                <div class="pl-gradient text-white px-3.5 py-3 sm:px-5 sm:py-3.5 flex flex-wrap items-center justify-between gap-2 border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-300 text-sm"></i>
                        <h3 class="font-black text-xs sm:text-sm tracking-wider uppercase">Girls Knockout (Top 4)</h3>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button onclick="seedKnockoutFromStandings('girls')" class="admin-only text-[11px] font-extrabold text-pl-purple bg-pl-neon hover:brightness-110 px-2.5 py-1 rounded-lg transition shadow-xs flex items-center gap-1">
                            <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i> Seed Top 4
                        </button>
                        <button onclick="clearKnockoutSeeds('girls')" class="admin-only text-[11px] font-semibold text-slate-200 bg-white/10 hover:bg-white/20 px-2 py-1 rounded-lg transition">
                            Clear
                        </button>
                    </div>
                </div>
                <div id="girls-knockout-container" class="p-2.5 sm:p-4 grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 bg-slate-50/60"></div>
            </div>
        </section>

        <!-- ==================== TAB 3: MASTER PITCH SCHEDULE ==================== -->
        <section id="tab-schedule" class="hidden space-y-4">
            <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-2xs border border-pl-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-calendar-check text-pl-purple"></i>
                        Pitch Match Schedule
                    </h2>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                        Chronological sequence for all matches
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2 no-print">
                    <select id="schedule-filter-division" onchange="renderMasterSchedule()" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-bold bg-slate-50 text-slate-700">
                        <option value="all">All Divisions</option>
                        <option value="boys">Boys</option>
                        <option value="girls">Girls</option>
                    </select>
                    <select id="schedule-filter-status" onchange="renderMasterSchedule()" class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-bold bg-slate-50 text-slate-700">
                        <option value="all">All Statuses</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="live">Live</option>
                        <option value="completed">Completed</option>
                    </select>
                    <button onclick="downloadPdfSchedule()" class="px-3 py-1.5 rounded-lg pl-neon-gradient text-pl-purple text-xs font-extrabold transition shadow-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </button>
                </div>
            </div>

            <div id="master-schedule-pdf-area" class="space-y-3">
                <div class="hidden print:block mb-3 text-center">
                    <h1 class="text-xl font-black">Play it Forward — Tiyende Pamodzi Tournament</h1>
                    <p class="text-xs text-slate-500">Official Master Pitch Schedule</p>
                </div>
                <div id="master-schedule-list" class="grid grid-cols-1 md:grid-cols-2 gap-3"></div>
            </div>
        </section>

        <!-- ==================== TAB 4: TEAMS & DRAW MANAGER ==================== -->
        <section id="tab-teams" class="hidden space-y-4 no-print">
            <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-2xs border border-pl-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-users text-pl-purple"></i>
                        Team Rosters & Group Draw
                    </h2>
                    <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">
                        Rename teams, swap groups, or redraw fixtures
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="clearAllScoresOnly()" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold transition border border-rose-200">
                        <i class="fa-solid fa-eraser mr-1"></i> Clear Scores
                    </button>
                    <button onclick="randomizeBoysGroupsEntirely()" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-800 hover:bg-blue-100 text-xs font-bold transition border border-blue-200">
                        <i class="fa-solid fa-dice mr-1"></i> Reshuffle Boys
                    </button>
                    <button onclick="shuffleAndRedraw('girls')" class="px-3 py-1.5 rounded-lg bg-purple-50 text-pl-purple hover:bg-purple-100 text-xs font-bold transition border border-purple-200">
                        <i class="fa-solid fa-dice mr-1"></i> Reshuffle Girls
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Boys G1 -->
                <div class="bg-white rounded-xl shadow-2xs border border-pl-border overflow-hidden">
                    <div class="pl-gradient text-white px-3.5 py-2.5 flex items-center justify-between">
                        <h3 class="font-extrabold text-xs flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-pl-neon"></span> Boys G1
                        </h3>
                        <span class="text-[10px] text-purple-200">5 Teams</span>
                    </div>
                    <div id="editor-boys-g1" class="p-3 space-y-2"></div>
                </div>

                <!-- Boys G2 -->
                <div class="bg-white rounded-xl shadow-2xs border border-pl-border overflow-hidden">
                    <div class="pl-gradient text-white px-3.5 py-2.5 flex items-center justify-between">
                        <h3 class="font-extrabold text-xs flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-pl-cyan"></span> Boys G2
                        </h3>
                        <span class="text-[10px] text-purple-200">5 Teams</span>
                    </div>
                    <div id="editor-boys-g2" class="p-3 space-y-2"></div>
                </div>

                <!-- Girls -->
                <div class="bg-white rounded-xl shadow-2xs border border-pl-border overflow-hidden">
                    <div class="pl-gradient text-white px-3.5 py-2.5 flex items-center justify-between">
                        <h3 class="font-extrabold text-xs flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-pl-pink"></span> Girls
                        </h3>
                        <span class="text-[10px] text-purple-200">6 Teams</span>
                    </div>
                    <div id="editor-girls" class="p-3 space-y-2"></div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-pl-border mt-8 py-4 text-center text-xs text-slate-500 no-print">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-1.5">
            <p class="font-bold text-slate-800 flex items-center gap-1">
                <i class="fa-solid fa-futbol text-pl-purple"></i>
                Play it Forward • Tiyende Pamodzi Tournament
            </p>
            <p class="text-[11px] text-slate-400">Mobile Matchday Live Center</p>
        </div>
    </footer>

    <!-- ==================== MODAL: ADMIN PIN LOGIN ==================== -->
    <div id="pin-modal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-pl-border overflow-hidden animate-scale-in">
            <div class="pl-gradient text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-black text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-pl-neon"></i> Admin Unlock
                </h3>
                <button onclick="closePinModal()" class="text-white/70 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form onsubmit="handlePinSubmit(event)" class="p-5 space-y-4">
                <p class="text-xs text-slate-600 font-medium">
                    Enter organizer PIN to unlock score entry and match management.
                </p>
                <div>
                    <label class="block text-[11px] font-black uppercase text-slate-400 tracking-wider mb-1.5">Admin PIN</label>
                    <input type="password" id="admin-pin-input" inputmode="numeric" maxlength="8" placeholder="••••" autocomplete="off"
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-center font-display font-bold text-2xl tracking-[0.4em] text-slate-900 focus:outline-none focus:ring-2 focus:ring-pl-purple focus:bg-white" />
                    <p id="pin-error-msg" class="text-xs text-rose-600 font-bold mt-1.5 hidden">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> Incorrect PIN code.
                    </p>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="closePinModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">Cancel</button>
                    <button type="submit" id="pin-submit-btn" class="px-5 py-2.5 rounded-xl bg-pl-purple hover:bg-pl-dark text-white text-xs font-extrabold shadow-md flex items-center gap-1.5">
                        <i class="fa-solid fa-unlock text-pl-neon"></i> Unlock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: CHANGE ADMIN PIN ==================== -->
    <div id="change-pin-modal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-pl-border overflow-hidden">
            <div class="pl-gradient text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-black text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-key text-amber-300"></i> Change PIN
                </h3>
                <button onclick="closeChangePinModal()" class="text-white/70 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form onsubmit="handleChangePinSubmit(event)" class="p-5 space-y-4">
                <div>
                    <label class="block text-[11px] font-black uppercase text-slate-400 tracking-wider mb-1.5">New PIN (4-8 digits)</label>
                    <input type="text" id="new-admin-pin-input" inputmode="numeric" minlength="4" maxlength="8" placeholder="e.g. 2026" required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 text-center font-display font-bold text-xl tracking-widest text-slate-900 focus:outline-none focus:ring-2 focus:ring-pl-purple focus:bg-white" />
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" onclick="closeChangePinModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-pl-purple text-white text-xs font-extrabold">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== MODAL: ADD CUSTOM MATCH ==================== -->
    <div id="custom-match-modal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-pl-border overflow-hidden">
            <div class="pl-gradient text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-black text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-pl-neon"></i> Add Match
                </h3>
                <button onclick="closeCustomMatchModal()" class="text-white/70 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-5 space-y-3.5">
                <div id="custom-match-group-wrapper">
                    <label class="block text-[11px] font-black uppercase text-slate-400 tracking-wider mb-1">Group</label>
                    <select id="custom-match-group" onchange="populateCustomMatchTeams()" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold">
                        <option value="G1">Boys Group 1</option>
                        <option value="G2">Boys Group 2</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-400 tracking-wider mb-1">Home Team</label>
                        <select id="custom-match-home" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold"></select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-black uppercase text-slate-400 tracking-wider mb-1">Away Team</label>
                        <select id="custom-match-away" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs font-bold"></select>
                    </div>
                </div>
                <p id="custom-match-error" class="text-xs text-rose-600 font-bold hidden">Home and Away teams must be different.</p>
                <div class="flex justify-end gap-2 pt-2">
                    <button onclick="closeCustomMatchModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Cancel</button>
                    <button onclick="saveCustomMatch()" class="px-4 py-2 rounded-xl bg-pl-purple text-white text-xs font-extrabold">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL: CONFIRMATION ==================== -->
    <div id="confirm-modal" class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full shadow-2xl border border-pl-border overflow-hidden p-5">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-3 text-lg font-bold">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="text-base font-black text-slate-900">Reset Tournament?</h3>
            <p class="text-xs text-slate-600 mt-1">
                Restores original team rosters and clears all entered scores.
            </p>
            <div class="flex justify-end gap-2 mt-5">
                <button onclick="closeConfirmModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold">Cancel</button>
                <button id="confirm-modal-action-btn" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-container" class="fixed bottom-4 right-4 left-4 sm:left-auto z-50 space-y-2 pointer-events-none max-w-xs sm:max-w-sm ml-auto"></div>

    <!-- ==================== APPLICATION JAVASCRIPT ==================== -->
    <script>
        const STORAGE_KEY = 'tournament_manager_v2_state';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
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
            } catch (e) {}
            return null;
        }

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
                        setSyncBadge('synced');
                    } else if (response.status === 401 || response.status === 403) {
                        setSyncBadge('error');
                        exitAdminMode();
                        showToast('Admin session expired. Please re-enter PIN.', 'error');
                    } else {
                        setSyncBadge('warning');
                    }
                } catch (err) {
                    setSyncBadge('warning');
                }
            }, 300);
        }

        async function fetchServerState() {
            if (isSyncing) return;
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
                badge.className = 'hidden sm:flex text-[9px] font-bold px-1.5 py-0.5 rounded bg-white/10 text-emerald-300 border border-emerald-400/30 items-center gap-1';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-pl-neon"></span> Live Sync';
            } else if (status === 'syncing') {
                badge.className = 'hidden sm:flex text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-400/30 items-center gap-1';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-400 live-dot"></span> Saving';
            } else {
                badge.className = 'hidden sm:flex text-[9px] font-bold px-1.5 py-0.5 rounded bg-white/10 text-slate-300 border border-white/20 items-center gap-1';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Cached';
            }
        }

        function updateAdminModeUI() {
            const badge = document.getElementById('mode-status-badge');
            if (isAdmin) {
                document.body.classList.add('is-admin');
                if (badge) {
                    badge.className = 'text-[10px] sm:text-xs font-black px-2.5 py-0.5 rounded-full bg-amber-400 text-slate-950 flex items-center gap-1 shadow-xs';
                    badge.innerHTML = '<i class="fa-solid fa-unlock-keyhole text-[10px]"></i> Admin';
                }
            } else {
                document.body.classList.remove('is-admin');
                if (badge) {
                    badge.className = 'text-[10px] sm:text-xs font-bold px-2.5 py-0.5 rounded-full bg-black/40 text-slate-200 border border-white/15 flex items-center gap-1';
                    badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-pl-neon live-dot"></span> Public Live';
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
            setTimeout(() => input.focus(), 60);
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
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

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
                    showToast('Admin Mode unlocked!');
                } else {
                    document.getElementById('pin-error-msg').classList.remove('hidden');
                    document.getElementById('admin-pin-input').select();
                }
            } catch (err) {
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
                submitBtn.innerHTML = '<i class="fa-solid fa-unlock text-pl-neon"></i> Unlock';
            }
        }

        function exitAdminMode() {
            isAdmin = false;
            sessionStorage.removeItem('tournament_admin_unlocked');
            updateAdminModeUI();
            showToast('Switched to Public View');
        }

        function openChangePinModal() {
            if (!isAdmin) return;
            const modal = document.getElementById('change-pin-modal');
            const input = document.getElementById('new-admin-pin-input');
            input.value = adminPinCached || '1234';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => input.select(), 60);
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
                    showToast(`Admin PIN updated`);
                } else {
                    showToast('Failed to update PIN', 'error');
                }
            } catch (err) {
                state.adminPin = newPin;
                adminPinCached = newPin;
                closeChangePinModal();
                showToast(`Admin PIN updated`);
            }
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bg = type === 'success' ? 'bg-pl-purple border-pl-neon/40 text-white' : 'bg-rose-950 border-rose-500/40 text-white';
            toast.className = `${bg} border px-3.5 py-2.5 rounded-xl shadow-2xl text-xs font-bold flex items-center gap-2.5 transition-all duration-300`;
            toast.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check text-pl-neon' : 'fa-circle-info text-amber-400'} text-sm shrink-0"></i>
                <span class="leading-snug">${message}</span>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 250);
            }, 2500);
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
                const rowBorder = qualifies ? 'border-l-4 border-pl-neon bg-emerald-50/40' : 'border-l-4 border-transparent hover:bg-slate-50/80';
                const posBadge = qualifies ? 'bg-pl-purple text-pl-neon font-black' : 'bg-slate-100 text-slate-500 font-bold';
                const gdFormatted = team.GD > 0 ? `+${team.GD}` : `${team.GD}`;
                const gdColor = team.GD > 0 ? 'text-emerald-600 font-bold' : (team.GD < 0 ? 'text-rose-500' : 'text-slate-400');

                return `
                    <tr class="${rowBorder} transition">
                        <td class="py-2 pl-2.5 sm:pl-3 pr-1">
                            <span class="w-5 h-5 rounded-md text-[10px] font-display inline-flex items-center justify-center ${posBadge}">
                                ${pos}
                            </span>
                        </td>
                        <td class="py-2 px-2 font-bold text-slate-900 min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="truncate text-xs sm:text-sm">${escapeHtml(team.name)}</span>
                                ${qualifies ? `<span class="text-[8px] font-black uppercase px-1 py-0.2 rounded bg-pl-neon text-pl-purple">Q</span>` : ''}
                            </div>
                        </td>
                        <td class="py-2 px-1 text-center font-semibold text-slate-600 text-xs">${team.P}</td>
                        <td class="py-2 px-1 text-center text-slate-600 text-xs">${team.W}</td>
                        <td class="py-2 px-1 text-center text-slate-500 text-xs">${team.D}</td>
                        <td class="py-2 px-1 text-center text-slate-500 text-xs">${team.L}</td>
                        <td class="py-2 px-1 text-center text-slate-400 text-[10px] hidden sm:table-cell">${team.GF}:${team.GA}</td>
                        <td class="py-2 px-1 text-center text-xs ${gdColor}">${gdFormatted}</td>
                        <td class="py-2 pr-3 pl-1 text-center font-display font-extrabold text-xs sm:text-sm text-pl-purple">${team.PTS}</td>
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
                        statusBadge = `<button onclick="cycleMatchStatus('${division}', '${m.id}')" class="text-[9px] font-black px-1.5 py-0.5 rounded bg-pl-purple text-pl-neon" title="Click status">FT</button>`;
                    } else if (isLive) {
                        statusBadge = `<button onclick="cycleMatchStatus('${division}', '${m.id}')" class="text-[9px] font-black px-1.5 py-0.5 rounded bg-pl-pink text-white" title="Click status">LIVE</button>`;
                    } else {
                        statusBadge = `<button onclick="cycleMatchStatus('${division}', '${m.id}')" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500" title="Click status">M${idx + 1}</button>`;
                    }
                } else {
                    if (isDone) statusBadge = `<span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-pl-purple text-pl-neon">FT</span>`;
                    else if (isLive) statusBadge = `<span class="text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-pl-pink text-white flex items-center gap-1"><span class="w-1 h-1 rounded-full bg-white live-dot"></span>LIVE</span>`;
                    else statusBadge = `<span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-400">M${idx + 1}</span>`;
                }

                let centerScoreHtml = '';
                if (isAdmin) {
                    centerScoreHtml = `
                        <div class="flex items-center gap-1 shrink-0">
                            <input type="number" min="0" max="99" placeholder="-" value="${hs}" inputmode="numeric"
                                oninput="updateGroupMatchScore('${division}', '${m.id}', 'home', this.value)"
                                class="score-input w-8 sm:w-9 h-7 sm:h-8 rounded-lg border border-slate-300 bg-slate-50 text-center font-display font-bold text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-pl-purple" />
                            <span class="text-[10px] font-black text-slate-300">:</span>
                            <input type="number" min="0" max="99" placeholder="-" value="${as}" inputmode="numeric"
                                oninput="updateGroupMatchScore('${division}', '${m.id}', 'away', this.value)"
                                class="score-input w-8 sm:w-9 h-7 sm:h-8 rounded-lg border border-slate-300 bg-slate-50 text-center font-display font-bold text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-pl-purple" />
                        </div>
                    `;
                } else {
                    if (hasBothScores && (isDone || isLive)) {
                        centerScoreHtml = `
                            <div class="px-2 py-0.5 rounded-md ${isLive ? 'bg-pl-pink text-white' : 'bg-pl-purple text-white'} font-display font-black text-xs tracking-wider flex items-center gap-1 shrink-0 shadow-2xs">
                                <span>${hs}</span><span class="text-white/40">-</span><span>${as}</span>
                            </div>
                        `;
                    } else {
                        centerScoreHtml = `<div class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-400 font-display font-black text-[9px] uppercase tracking-wider shrink-0">VS</div>`;
                    }
                }

                return `
                    <div class="bg-white rounded-lg p-2 border ${isDone ? 'border-purple-200' : (isLive ? 'border-pl-pink shadow-xs' : 'border-slate-200')} flex items-center justify-between gap-1.5 sm:gap-2">
                        <div class="flex items-center gap-1 min-w-0 flex-1 justify-end text-right">
                            ${homeWon ? '<i class="fa-solid fa-caret-right text-pl-neon text-[10px] shrink-0"></i>' : ''}
                            <span class="text-xs truncate ${homeWon ? 'font-black text-pl-purple' : 'font-bold text-slate-800'}">${escapeHtml(home.name)}</span>
                        </div>

                        ${centerScoreHtml}

                        <div class="flex items-center justify-between gap-1 min-w-0 flex-1">
                            <span class="text-xs truncate ${awayWon ? 'font-black text-pl-purple' : 'font-bold text-slate-800'}">${escapeHtml(away.name)}</span>
                            <div class="flex items-center gap-1 shrink-0 no-print">
                                ${statusBadge}
                                ${(isAdmin && matches.length > 3) ? `<button onclick="deleteGroupMatch('${division}', '${m.id}')" class="text-slate-300 hover:text-rose-500 text-xs px-0.5"><i class="fa-solid fa-trash-can"></i></button>` : ''}
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
                sf1HomePlaceholder = { id: null, name: '1st G1', tag: '1st G1' };
                sf1AwayPlaceholder = { id: null, name: '2nd G2', tag: '2nd G2' };
                sf2HomePlaceholder = { id: null, name: '1st G2', tag: '1st G2' };
                sf2AwayPlaceholder = { id: null, name: '2nd G1', tag: '2nd G1' };
            } else {
                sf1HomePlaceholder = { id: null, name: '1st Place', tag: '1st' };
                sf1AwayPlaceholder = { id: null, name: '4th Place', tag: '4th' };
                sf2HomePlaceholder = { id: null, name: '2nd Place', tag: '2nd' };
                sf2AwayPlaceholder = { id: null, name: '3rd Place', tag: '3rd' };
            }

            const sf1Home = ko.sf1.customHomeId ? { ...getTeamById(division, ko.sf1.customHomeId), tag: sf1HomePlaceholder.tag } : sf1HomePlaceholder;
            const sf1Away = ko.sf1.customAwayId ? { ...getTeamById(division, ko.sf1.customAwayId), tag: sf1AwayPlaceholder.tag } : sf1AwayPlaceholder;
            const sf2Home = ko.sf2.customHomeId ? { ...getTeamById(division, ko.sf2.customHomeId), tag: sf2HomePlaceholder.tag } : sf2HomePlaceholder;
            const sf2Away = ko.sf2.customAwayId ? { ...getTeamById(division, ko.sf2.customAwayId), tag: sf2AwayPlaceholder.tag } : sf2AwayPlaceholder;

            const sf1Outcome = getKnockoutResult(ko.sf1, sf1Home, sf1Away);
            const sf2Outcome = getKnockoutResult(ko.sf2, sf2Home, sf2Away);

            const thirdHome = ko.third.customHomeId ? { ...getTeamById(division, ko.third.customHomeId), tag: 'SF1' } : (sf1Outcome.loser ? { ...sf1Outcome.loser, tag: 'Loser SF1' } : { id: null, name: 'Loser SF1', tag: 'SF1' });
            const thirdAway = ko.third.customAwayId ? { ...getTeamById(division, ko.third.customAwayId), tag: 'SF2' } : (sf2Outcome.loser ? { ...sf2Outcome.loser, tag: 'Loser SF2' } : { id: null, name: 'Loser SF2', tag: 'SF2' });

            const finalHome = ko.final.customHomeId ? { ...getTeamById(division, ko.final.customHomeId), tag: 'SF1' } : (sf1Outcome.winner ? { ...sf1Outcome.winner, tag: 'Winner SF1' } : { id: null, name: 'Winner SF1', tag: 'SF1' });
            const finalAway = ko.final.customAwayId ? { ...getTeamById(division, ko.final.customAwayId), tag: 'SF2' } : (sf2Outcome.winner ? { ...sf2Outcome.winner, tag: 'Winner SF2' } : { id: null, name: 'Winner SF2', tag: 'SF2' });

            container.innerHTML = `
                <div class="space-y-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Semi-Finals</span>
                    ${renderKnockoutCard(division, 'sf1', ko.sf1, sf1Home, sf1Away, allTeams, false)}
                    ${renderKnockoutCard(division, 'sf2', ko.sf2, sf2Home, sf2Away, allTeams, false)}
                </div>
                <div class="space-y-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">3rd Place</span>
                    ${renderKnockoutCard(division, 'third', ko.third, thirdHome, thirdAway, allTeams, false)}
                </div>
                <div class="space-y-2">
                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 flex items-center gap-1">
                        <i class="fa-solid fa-trophy"></i> Championship Final
                    </span>
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

            const homeOptions = `<option value="">-- ${escapeHtml(homeTeam.id ? 'Reset' : homeTeam.name)} --</option>` +
                allTeams.map(t => `<option value="${t.id}" ${koMatch.customHomeId === t.id ? 'selected' : ''}>${escapeHtml(t.name)} (${t.group})</option>`).join('');

            const awayOptions = `<option value="">-- ${escapeHtml(awayTeam.id ? 'Reset' : awayTeam.name)} --</option>` +
                allTeams.map(t => `<option value="${t.id}" ${koMatch.customAwayId === t.id ? 'selected' : ''}>${escapeHtml(t.name)} (${t.group})</option>`).join('');

            const homeTeamNameDisplay = isAdmin
                ? `<select onchange="updateKnockoutTeam('${division}', '${stageKey}', 'customHomeId', this.value)" class="w-full rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-xs font-bold text-slate-800">${homeOptions}</select>`
                : `<span class="text-xs truncate ${homeIsWinner ? 'font-black text-pl-purple' : (homeTeam.id ? 'font-bold text-slate-800' : 'font-medium italic text-slate-400')}">${escapeHtml(homeTeam.name)}</span>`;

            const awayTeamNameDisplay = isAdmin
                ? `<select onchange="updateKnockoutTeam('${division}', '${stageKey}', 'customAwayId', this.value)" class="w-full rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-xs font-bold text-slate-800">${awayOptions}</select>`
                : `<span class="text-xs truncate ${awayIsWinner ? 'font-black text-pl-purple' : (awayTeam.id ? 'font-bold text-slate-800' : 'font-medium italic text-slate-400')}">${escapeHtml(awayTeam.name)}</span>`;

            const homeScoreControl = isAdmin
                ? `<input type="number" min="0" max="99" placeholder="-" value="${hs}" inputmode="numeric" oninput="updateKnockoutScore('${division}', '${stageKey}', 'homeScore', this.value)" class="score-input w-8 h-7 rounded border border-slate-300 bg-slate-50 text-center font-display font-bold text-xs shrink-0" />`
                : `<span class="w-7 h-7 rounded ${homeIsWinner ? 'bg-pl-purple text-pl-neon font-black' : 'bg-slate-100 text-slate-700 font-bold'} font-display text-xs inline-flex items-center justify-center shrink-0">${hs !== '' ? hs : '-'}</span>`;

            const awayScoreControl = isAdmin
                ? `<input type="number" min="0" max="99" placeholder="-" value="${as}" inputmode="numeric" oninput="updateKnockoutScore('${division}', '${stageKey}', 'awayScore', this.value)" class="score-input w-8 h-7 rounded border border-slate-300 bg-slate-50 text-center font-display font-bold text-xs shrink-0" />`
                : `<span class="w-7 h-7 rounded ${awayIsWinner ? 'bg-pl-purple text-pl-neon font-black' : 'bg-slate-100 text-slate-700 font-bold'} font-display text-xs inline-flex items-center justify-center shrink-0">${as !== '' ? as : '-'}</span>`;

            return `
                <div class="rounded-xl p-2.5 border ${isFinal ? 'bg-gradient-to-b from-amber-50 to-white border-amber-300 shadow-xs' : 'bg-white border-slate-200 shadow-2xs'} space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-1">
                        <span class="text-[10px] font-black uppercase ${isFinal ? 'text-amber-800' : 'text-slate-700'}">${koMatch.label}</span>
                        ${outcome.winner ? `<span class="text-[9px] font-black px-1.5 py-0.2 rounded ${isFinal ? 'bg-amber-400 text-slate-950' : 'bg-pl-purple text-pl-neon'}">${escapeHtml(outcome.winner.name)}</span>` : ''}
                    </div>
                    <div class="flex items-center justify-between gap-1.5">
                        <div class="min-w-0 flex-1">${homeTeamNameDisplay}</div>
                        ${homeScoreControl}
                    </div>
                    <div class="flex items-center justify-between gap-1.5">
                        <div class="min-w-0 flex-1">${awayTeamNameDisplay}</div>
                        ${awayScoreControl}
                    </div>
                    ${outcome.isDraw ? (
                        isAdmin ? `
                        <div class="pt-1 border-t border-amber-200 bg-amber-50/70 -mx-2.5 -mb-2.5 p-2 rounded-b-xl flex items-center justify-between text-xs">
                            <span class="font-bold text-amber-900 text-[10px]">Penalties:</span>
                            <div class="flex items-center gap-1">
                                <input type="number" min="0" max="99" placeholder="H" value="${hp}" inputmode="numeric" oninput="updateKnockoutScore('${division}', '${stageKey}', 'homePen', this.value)" class="score-input w-7 h-6 rounded border border-amber-300 bg-white text-center font-bold text-xs" />
                                <span class="text-amber-700">-</span>
                                <input type="number" min="0" max="99" placeholder="A" value="${ap}" inputmode="numeric" oninput="updateKnockoutScore('${division}', '${stageKey}', 'awayPen', this.value)" class="score-input w-7 h-6 rounded border border-amber-300 bg-white text-center font-bold text-xs" />
                            </div>
                        </div>` : `
                        <div class="pt-1 border-t border-amber-200 bg-amber-50/70 -mx-2.5 -mb-2.5 px-2 py-1 rounded-b-xl flex items-center justify-between text-[10px] text-amber-900 font-bold">
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
                showToast('Boys Semi-Finals seeded');
            } else {
                const table = calculateStandings('girls', 'G1');
                state.girlsKnockout.sf1.customHomeId = table[0]?.id || null;
                state.girlsKnockout.sf1.customAwayId = table[3]?.id || null;
                state.girlsKnockout.sf2.customHomeId = table[1]?.id || null;
                state.girlsKnockout.sf2.customAwayId = table[2]?.id || null;
                showToast('Girls Semi-Finals seeded');
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
            showToast('Knockouts reset');
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
                    <div class="pl-neon-gradient text-pl-purple rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-lg flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-pl-purple text-pl-neon flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-pl-purple/80">Champions</span>
                                <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                    ${boysChamp ? `<span class="text-xs sm:text-sm font-black">🏆 Boys: ${escapeHtml(boysChamp.name)}</span>` : ''}
                                    ${girlsChamp ? `<span class="text-xs sm:text-sm font-black">👑 Girls: ${escapeHtml(girlsChamp.name)}</span>` : ''}
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

            const g1 = state.boysMatches.filter(m => m.group === 'G1').map((m, i) => ({ ...m, label: `Boys G1 (#${i + 1})`, badgeColor: 'bg-emerald-50 text-emerald-800 border-emerald-200' }));
            const g2 = state.boysMatches.filter(m => m.group === 'G2').map((m, i) => ({ ...m, label: `Boys G2 (#${i + 1})`, badgeColor: 'bg-blue-50 text-blue-800 border-blue-200' }));
            const girls = state.girlsMatches.map((m, i) => ({ ...m, label: `Girls (#${i + 1})`, badgeColor: 'bg-purple-50 text-purple-800 border-purple-200' }));

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
                container.innerHTML = `<div class="col-span-2 bg-white rounded-xl p-6 text-center text-slate-400 text-xs border border-slate-200">No matches found.</div>`;
                return;
            }

            container.innerHTML = filtered.map((m, idx) => {
                const home = getTeamById(m.division, m.homeId);
                const away = getTeamById(m.division, m.awayId);
                const hs = m.homeScore ?? '';
                const as = m.awayScore ?? '';
                const hasScores = hs !== '' && as !== '';

                const statusBadge = isAdmin
                    ? `<button onclick="cycleMatchStatus('${m.division}', '${m.id}')" class="text-[10px] font-black px-2 py-0.5 rounded ${m.status === 'completed' ? 'bg-pl-purple text-pl-neon' : (m.status === 'live' ? 'bg-pl-pink text-white' : 'bg-slate-100 text-slate-600')}">
                        ${m.status === 'completed' ? 'FT' : (m.status === 'live' ? 'LIVE' : 'Upcoming')}
                       </button>`
                    : `<span class="text-[10px] font-black px-2 py-0.5 rounded ${m.status === 'completed' ? 'bg-pl-purple text-pl-neon' : (m.status === 'live' ? 'bg-pl-pink text-white' : 'bg-slate-100 text-slate-500')}">
                        ${m.status === 'completed' ? 'FT' : (m.status === 'live' ? 'LIVE' : 'Upcoming')}
                       </span>`;

                const centerScoreControl = isAdmin
                    ? `<div class="flex items-center gap-1">
                        <input type="number" min="0" max="99" placeholder="-" value="${hs}" inputmode="numeric" oninput="updateGroupMatchScore('${m.division}', '${m.id}', 'home', this.value)" class="score-input w-8 h-8 rounded-lg border border-slate-300 bg-slate-50 text-center font-display font-bold text-xs" />
                        <span class="text-slate-300 font-bold">:</span>
                        <input type="number" min="0" max="99" placeholder="-" value="${as}" inputmode="numeric" oninput="updateGroupMatchScore('${m.division}', '${m.id}', 'away', this.value)" class="score-input w-8 h-8 rounded-lg border border-slate-300 bg-slate-50 text-center font-display font-bold text-xs" />
                       </div>`
                    : (hasScores && (m.status === 'completed' || m.status === 'live')
                        ? `<div class="px-2.5 py-0.5 rounded-md bg-pl-purple text-white font-display font-black text-xs">${hs} - ${as}</div>`
                        : `<div class="px-2 py-0.5 rounded bg-slate-100 text-slate-400 font-display font-black text-[10px]">VS</div>`);

                return `
                    <div class="bg-white rounded-xl p-3 shadow-2xs border border-pl-border flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <div class="w-7 h-7 rounded-lg bg-pl-purple text-pl-neon font-display font-black text-xs flex items-center justify-center shrink-0">
                                ${idx + 1}
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[9px] font-black uppercase px-1.5 py-0.2 rounded border ${m.badgeColor} inline-block">
                                    ${m.label}
                                </span>
                                <div class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-900 truncate mt-0.5">
                                    <span class="truncate">${escapeHtml(home.name)}</span>
                                    <span class="text-[10px] text-slate-300">vs</span>
                                    <span class="truncate">${escapeHtml(away.name)}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
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
                <div style="font-family: Outfit, sans-serif; padding: 20px; color: #1a001f;">
                    <h1 style="font-size: 20px; font-weight: 900; margin-bottom: 2px;">Play it Forward — Tiyende Pamodzi Tournament</h1>
                    <p style="font-size: 11px; color: #666; margin-bottom: 16px;">Official Master Pitch Schedule</p>
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
                <div class="flex items-center gap-1.5">
                    <span class="w-4 text-xs font-bold text-slate-400">${i + 1}.</span>
                    <input type="text" value="${escapeHtml(t.name)}" onchange="renameTeam('boys', '${t.id}', this.value)" class="flex-1 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-bold text-slate-800" />
                    <button onclick="moveBoysTeamGroup('${t.id}', 'G2')" class="px-2 py-1 rounded bg-slate-100 hover:bg-blue-100 text-slate-600 text-[10px] font-bold">G2</button>
                </div>
            `).join('');

            g2.innerHTML = state.boysTeams.filter(t => t.group === 'G2').map((t, i) => `
                <div class="flex items-center gap-1.5">
                    <span class="w-4 text-xs font-bold text-slate-400">${i + 6}.</span>
                    <input type="text" value="${escapeHtml(t.name)}" onchange="renameTeam('boys', '${t.id}', this.value)" class="flex-1 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-bold text-slate-800" />
                    <button onclick="moveBoysTeamGroup('${t.id}', 'G1')" class="px-2 py-1 rounded bg-slate-100 hover:bg-purple-100 text-slate-600 text-[10px] font-bold">G1</button>
                </div>
            `).join('');

            girls.innerHTML = state.girlsTeams.map((t, i) => `
                <div class="flex items-center gap-1.5">
                    <span class="w-4 text-xs font-bold text-slate-400">${i + 1}.</span>
                    <input type="text" value="${escapeHtml(t.name)}" onchange="renameTeam('girls', '${t.id}', this.value)" class="flex-1 rounded-lg border border-slate-200 px-2.5 py-1 text-xs font-bold text-slate-800" />
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
            showToast(`Moved to ${targetGroup}`);
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
                showToast('Boys draw redrawn');
            } else {
                state.girlsMatches = generateTwoGamesPerTeamFixtures(shuffleArray(state.girlsTeams.map(t => t.id)), 'girls', 'G1');
                showToast('Girls draw redrawn');
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
            showToast('Boys regrouped');
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
                    showToast('Tournament reset');
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
                    btn.className = `${t === 'teams' ? 'admin-only ' : ''}tab-btn flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl font-extrabold text-xs sm:text-sm transition whitespace-nowrap bg-pl-neon text-pl-purple shadow-md shadow-pl-neon/20 shrink-0`;
                } else {
                    sec.classList.add('hidden');
                    btn.className = `${t === 'teams' ? 'admin-only ' : ''}tab-btn flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl font-bold text-xs sm:text-sm transition whitespace-nowrap text-purple-200 hover:bg-white/10 shrink-0`;
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
            setInterval(fetchServerState, 3500);
        };
    </script>
</body>
</html>
