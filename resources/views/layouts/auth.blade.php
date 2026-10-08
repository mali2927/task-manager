<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 font-sans antialiased selection:bg-indigo-500 selection:text-white relative overflow-x-hidden">
        
        <!-- Ambient Decorative Lighting Glows -->
        <div class="fixed -top-40 -left-40 size-[520px] rounded-full bg-indigo-600/15 blur-[140px] pointer-events-none"></div>
        <div class="fixed top-1/3 -right-40 size-[500px] rounded-full bg-sky-500/10 blur-[140px] pointer-events-none"></div>
        <div class="fixed -bottom-40 left-1/3 size-[560px] rounded-full bg-purple-600/10 blur-[150px] pointer-events-none"></div>

        <!-- Background Blueprint Grid Pattern -->
        <div class="fixed inset-0 bg-[linear-gradient(to_right,#1e293b12_1px,transparent_1px),linear-gradient(to_bottom,#1e293b12_1px,transparent_1px)] bg-[size:32px_32px] pointer-events-none"></div>

        <div class="relative min-h-screen grid lg:grid-cols-12">
            
            <!-- Left Showcase Panel (Visible on Desktop / Tablets) -->
            <div class="lg:col-span-5 xl:col-span-5 flex flex-col justify-between p-8 lg:p-12 xl:p-14 border-b lg:border-b-0 lg:border-r border-slate-800/80 bg-slate-900/50 backdrop-blur-2xl relative z-10">
                
                <!-- Institutional Brand Header -->
                <div>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3.5 group" wire:navigate>
                        <div class="relative p-2.5 rounded-2xl bg-white/10 dark:bg-white/5 border border-white/15 shadow-xl backdrop-blur-md group-hover:scale-105 transition-all duration-300">
                            <img src="/stmu-logo.png" alt="STMU MIS Logo" class="size-11 object-contain" />
                            <span class="absolute -top-1 -right-1 flex size-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full size-3 bg-emerald-500"></span>
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                                    Enterprise MIS
                                </span>
                                <span class="text-[10px] font-semibold text-emerald-400 flex items-center gap-1">
                                    <span class="size-1.5 rounded-full bg-emerald-400"></span> Live
                                </span>
                            </div>
                            <h1 class="text-xl font-black text-white tracking-tight mt-1">
                                STMU MIS Portal
                            </h1>
                            <p class="text-xs text-slate-400 font-medium">
                                Shifa Tameer-e-Millat University
                            </p>
                        </div>
                    </a>

                    <!-- Hero Headline -->
                    <div class="mt-10 lg:mt-14">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-950/60 border border-indigo-700/40 text-xs font-semibold text-indigo-300 mb-4">
                            <svg class="size-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Task & Resource Management Platform</span>
                        </div>
                        <h2 class="text-2xl xl:text-3xl font-black text-white tracking-tight leading-tight">
                            Unified Digital Governance & Project Operations
                        </h2>
                        <p class="text-sm text-slate-400 mt-3 leading-relaxed">
                            A centralized institutional workspace built for STMU departments, faculty task forces, research directorates, and engineering teams.
                        </p>
                    </div>

                    <!-- Key Integrated Systems -->
                    <div class="mt-8 space-y-3">
                        <!-- Admissions -->
                        <div class="p-3.5 rounded-xl border border-slate-800/80 bg-slate-950/40 hover:bg-slate-900/60 transition-colors flex items-start gap-3">
                            <div class="size-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0 mt-0.5">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-200">Admissions & Student Workflows</h3>
                                <p class="text-[11px] text-slate-400">Application evaluation pipelines, departmental merit processing, and faculty coordination.</p>
                            </div>
                        </div>

                        <!-- ORIC -->
                        <div class="p-3.5 rounded-xl border border-slate-800/80 bg-slate-950/40 hover:bg-slate-900/60 transition-colors flex items-start gap-3">
                            <div class="size-8 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-center text-sky-400 shrink-0 mt-0.5">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-200">ORIC Research & Grants Hub</h3>
                                <p class="text-[11px] text-slate-400">Research grants tracking, faculty publication milestones, and ethics committee reviews.</p>
                            </div>
                        </div>

                        <!-- LMS -->
                        <div class="p-3.5 rounded-xl border border-slate-800/80 bg-slate-950/40 hover:bg-slate-900/60 transition-colors flex items-start gap-3">
                            <div class="size-8 rounded-lg bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0 mt-0.5">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-200">LMS & Digital Campus Operations</h3>
                                <p class="text-[11px] text-slate-400">Course delivery synchronization, examination schedule audits, and portal release sprints.</p>
                            </div>
                        </div>

                        <!-- Gemini AI -->
                        <div class="p-3.5 rounded-xl border border-indigo-500/20 bg-indigo-950/20 hover:bg-indigo-900/30 transition-colors flex items-start gap-3">
                            <div class="size-8 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0 mt-0.5">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h3 class="text-xs font-bold text-indigo-200">Gemini AI Workspace Copilot</h3>
                                    <span class="text-[9px] font-black px-1.5 py-0.2 rounded bg-indigo-500/30 text-indigo-300">2.5 Pro</span>
                                </div>
                                <p class="text-[11px] text-slate-400">Automated executive summaries, daily standups, and role-hierarchical insight isolation.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Status Bar -->
                <div class="mt-10 pt-6 border-t border-slate-800/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-medium text-slate-300">STMU MIS Cloud: Operational</span>
                    </div>
                    <div class="flex items-center gap-3 text-[11px]">
                        <span>Islamabad, PK</span>
                        <span>•</span>
                        <span>v2.4 Enterprise</span>
                    </div>
                </div>
            </div>

            <!-- Right Interactive Form Column -->
            <div class="lg:col-span-7 xl:col-span-7 flex flex-col justify-center items-center p-5 sm:p-8 lg:p-12 relative z-10">
                
                <!-- Mobile Only Brand Header -->
                <div class="lg:hidden flex items-center gap-3 mb-6 w-full max-w-md">
                    <div class="p-2 rounded-xl bg-white/10 border border-white/15 shadow-md">
                        <img src="/stmu-logo.png" alt="STMU MIS" class="size-9 object-contain" />
                    </div>
                    <div>
                        <h2 class="text-base font-black text-white">STMU MIS Portal</h2>
                        <p class="text-xs text-slate-400">Shifa Tameer-e-Millat University</p>
                    </div>
                </div>

                <!-- Form Card Wrapper -->
                <div class="w-full max-w-md xl:max-w-lg">
                    <div class="rounded-2xl border border-slate-800/90 bg-slate-900/80 p-6 sm:p-9 backdrop-blur-2xl shadow-2xl shadow-black/50 relative overflow-hidden">
                        
                        <!-- Top Accent Line -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-indigo-500 via-sky-400 to-emerald-400"></div>

                        <!-- Content Slot -->
                        {{ $slot }}
                    </div>

                    <!-- Subtle Security & Copyright Footer -->
                    <div class="mt-6 text-center space-y-1 text-xs text-slate-400">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="size-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Institutional 256-bit TLS Encryption & WebAuthn Compliant</span>
                        </div>
                        <p>© {{ date('Y') }} Shifa Tameer-e-Millat University • Management Information Systems (MIS)</p>
                    </div>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
