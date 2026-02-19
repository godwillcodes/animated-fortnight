<?php
/**
 * Template Name: Home
 * Description: Campaign Homepage with Clean Typography
 */
get_header();
?>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center bg-white overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 hidden lg:block">
        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/95 to-transparent z-10"></div>
        <img class="w-full h-full object-contain object-right opacity-90"
             src="/wp-content/uploads/2025/08/signal-2025-08-26-223113_002.jpeg"
             alt="Campaign supporters representing Kenya's diverse communities" />
    </div>

    <div class="relative z-20 w-full px-6 py-32 lg:py-0 mx-auto container">
        <div class="max-w-2xl">
            <!-- Eyebrow -->
            <div class="inline-flex items-center gap-2 mb-6 px-4 py-2 bg-emerald-50 rounded-full">
                <span class="w-2 h-2 bg-emerald-600 rounded-full animate-pulse"></span>
                <span class="text-sm font-medium text-emerald-900 tracking-wide">2027 Presidential Campaign</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold text-gray-900 leading-[1.1] mb-8 tracking-tight">
                A New Kenya,<br>
                A Shared <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent">Future</span>
            </h1>

            <!-- Description -->
            <div class="space-y-6 mb-10">
                <p class="text-xl text-gray-700 leading-relaxed">
                    End inequality. Restore dignity. Create real opportunities. 
                    This isn't just politics, it's about your family's future.
                </p>

                <p class="text-lg text-gray-600 leading-relaxed">
                    From fighting corruption to guaranteeing healthcare, education, and jobs that pay living wages, we're building a Kenya where fairness isn't a promise, it's our foundation.
                </p>
            </div>

            <!-- CTA -->
            <a href="/volunteer"
               class="inline-flex items-center gap-3 bg-emerald-700 text-white font-semibold text-lg px-8 py-5 rounded-full hover:bg-red-600 hover:gap-5 transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105 group">
                <span>I want to help</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <!-- Mobile Image -->
        <div class="mt-16 lg:hidden">
            <img class="w-full rounded-2xl shadow-2xl"
                 src="/wp-content/uploads/2025/08/signal-2025-08-26-223113_002.jpeg"
                 alt="Campaign supporters" />
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 hidden lg:flex flex-col items-center gap-2 text-gray-400">
        <span class="text-xs uppercase tracking-widest">Scroll</span>
        <div class="w-[1px] h-12 bg-gradient-to-b from-gray-400 to-transparent"></div>
    </div>
</section>

<!-- Marquee Banner -->
<div class="relative w-full h-20 overflow-hidden bg-gray-50">
    <div class="absolute inset-0 w-[200%] h-full bg-repeat-x bg-contain animate-marquee" 
         style="background-image: url('/wp-content/uploads/2025/08/BM-TERRACE-BRANDING-Final-1_page-0001_11zon-1-scaled.jpg');"></div>
</div>

<!-- Vision Section -->
<section class="py-32 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-20">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-[2px] w-12 bg-gradient-to-r from-emerald-600 to-transparent"></div>
                    <span class="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">Our Vision</span>
                </div>
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                    Building unity, prosperity, and opportunity
                </h2>
            </div>

            <a href="/about"
                class="group inline-flex items-center gap-3 px-8 py-3 text-base font-semibold text-emerald-700 border-2 border-emerald-700 rounded-full hover:bg-emerald-700 hover:text-white transition-all duration-300">
                Get to Know Me
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24">
            <!-- Text Content -->
            <div class="space-y-16">
                <div class="group">
                    <div class="flex items-start gap-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 transition-colors duration-300">
                            <span class="text-2xl font-bold text-emerald-700 group-hover:text-white transition-colors duration-300">01</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                A Kenya That Works for Everyone
                            </h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Our campaign is about dignity, justice, and opportunity. We believe in a nation where leadership serves the people, resources are used fairly, and every community has the chance to thrive.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="group">
                    <div class="flex items-start gap-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 transition-colors duration-300">
                            <span class="text-2xl font-bold text-emerald-700 group-hover:text-white transition-colors duration-300">02</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                Ending Corruption, Restoring Trust
                            </h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                Corruption has stolen our future. We are building a movement that fights greed with integrity and ensures that public service is about service, not self-enrichment.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="group">
                    <div class="flex items-start gap-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600 transition-colors duration-300">
                            <span class="text-2xl font-bold text-emerald-700 group-hover:text-white transition-colors duration-300">03</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                                A Shared Future
                            </h3>
                            <p class="text-lg text-gray-600 leading-relaxed">
                                This is not one person's journey — it is ours together. From young people seeking jobs to families needing quality healthcare and education, our shared vision is a Kenya where fairness guides progress.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Section -->
            <div class="lg:sticky lg:top-24 lg:self-start">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl bg-gradient-to-br from-emerald-700 to-emerald-900 p-8">
                    <div class="relative w-full" style="padding-bottom: 56.25%;">
                        <iframe class="absolute top-0 left-0 w-full h-full rounded-2xl" 
                            src="https://www.youtube.com/embed/nUSAEGBaI_o" 
                            title="Who is Boniface Mwangi?" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- People's Manifesto Section -->
<section class="py-32 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Header -->
        <div class="text-center mb-24 max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-3 mb-6">
                <div class="h-[1px] w-12 bg-gradient-to-r from-transparent to-gray-300"></div>
                <span class="text-sm uppercase tracking-[0.3em] text-gray-400 font-medium">Built by the people</span>
                <div class="h-[1px] w-12 bg-gradient-to-l from-transparent to-gray-300"></div>
            </div>
            
            <h2 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-8 leading-[1.1] tracking-tight">
                The People's<br>
                <span class="bg-gradient-to-r from-emerald-600 to-emerald-800 bg-clip-text text-transparent">Manifesto</span>
            </h2>
            
            <p class="text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto mb-12">
                Boniface Mwangi isn't here to sell you promises. This movement starts where the people are — writing the future from the ground up.
            </p>
            
            <a href="/host-boniface-mwangi/" 
               class="inline-flex items-center gap-3 bg-gray-900 text-white px-10 py-5 rounded-full text-sm uppercase tracking-wider font-semibold hover:bg-emerald-700 hover:gap-5 transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105 group">
                <span>Host Boniface</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                </svg>
            </a>
        </div>

        <!-- Article 43 -->
        <div class="mb-16">
            <div class="flex items-center gap-4 mb-16 justify-center">
                <div class="h-[1px] w-16 bg-gradient-to-r from-transparent to-gray-300"></div>
                <h3 class="text-sm uppercase tracking-[0.3em] text-gray-400 font-medium">Article 43</h3>
                <div class="h-[1px] w-16 bg-gradient-to-l from-transparent to-gray-300"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Health -->
                <div class="bg-white p-8 rounded-3xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold mb-4 text-gray-900">Health</h4>
                    <p class="text-gray-600 leading-relaxed">Quality healthcare for every Kenyan — not privilege. Universal, accessible systems that protect lives, not profits.</p>
                </div>

                <!-- Education -->
                <div class="bg-white p-8 rounded-3xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold mb-4 text-gray-900">Education</h4>
                    <p class="text-gray-600 leading-relaxed">Education that empowers, never burdens. Equitable access, relevant curriculum, real investment in teachers and learners.</p>
                </div>

                <!-- Housing -->
                <div class="bg-white p-8 rounded-3xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold mb-4 text-gray-900">Housing</h4>
                    <p class="text-gray-600 leading-relaxed">Every family deserves a safe place to call home. Policies that make dignified housing a right, not a dream.</p>
                </div>

                <!-- Food -->
                <div class="bg-white p-8 rounded-3xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold mb-4 text-gray-900">Food</h4>
                    <p class="text-gray-600 leading-relaxed">No Kenyan should go hungry. Food systems that prioritize local farmers, fair markets, and sustainable production.</p>
                </div>

                <!-- Water -->
                <div class="bg-white p-8 rounded-3xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold mb-4 text-gray-900">Water</h4>
                    <p class="text-gray-600 leading-relaxed">Water is life. Every community should have reliable, clean access — free from corruption, privatization, or neglect.</p>
                </div>

                <!-- Social Security -->
                <div class="bg-white p-8 rounded-3xl hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-2xl font-bold mb-4 text-gray-900">Social Security</h4>
                    <p class="text-gray-600 leading-relaxed">A nation stands tall when it protects its most vulnerable. Fair labor, inclusive protection, dignity for every worker.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Get Involved Section -->
<section class="py-32 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-20">
            <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                What brings you here today?
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Choose how you want to make a difference in building the Kenya we deserve
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Take Action Card -->
            <div class="group relative bg-white border-2 border-gray-100 rounded-3xl p-10 hover:border-emerald-600 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                <div class="mb-8">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Take Action
                    </h3>
                    
                    <p class="text-gray-600 leading-relaxed mb-8">
                        Every volunteer strengthens the call for accountability, transparency, and real change. Make your mark—be part of the force that drives progress.
                    </p>
                </div>
                
                <a href="/volunteer-signup/" 
                   class="inline-flex items-center gap-2 text-emerald-700 font-semibold group-hover:gap-4 transition-all duration-300">
                    <span class="uppercase text-sm tracking-wider">Learn More</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>

            <!-- Make Your Voice Heard Card -->
            <div class="group relative bg-white border-2 border-gray-100 rounded-3xl p-10 hover:border-emerald-600 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                <div class="mb-8">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Make Your Voice Heard
                    </h3>
                    
                    <p class="text-gray-600 leading-relaxed mb-8">
                        Amplify the issues that impact your neighborhood, your city, and your future. Together, we turn conversations into action.
                    </p>
                </div>
                
                <a href="/community-voice/" 
                   class="inline-flex items-center gap-2 text-emerald-700 font-semibold group-hover:gap-4 transition-all duration-300">
                    <span class="uppercase text-sm tracking-wider">Learn More</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>

            <!-- Engage & Mobilize Card -->
            <div class="group relative bg-white border-2 border-gray-100 rounded-3xl p-10 hover:border-emerald-600 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500">
                <div class="mb-8">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:scale-110 transition-all duration-300">
                        <svg class="w-7 h-7 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">
                        Engage & Mobilize
                    </h3>
                    
                    <p class="text-gray-600 leading-relaxed mb-8">
                        From town halls and rallies to workshops and strategy sessions, our events connect citizens, leaders, and changemakers. Learn, network, and mobilize to make democracy tangible.
                    </p>
                </div>
                
                <a href="/events/" 
                   class="inline-flex items-center gap-2 text-emerald-700 font-semibold group-hover:gap-4 transition-all duration-300">
                    <span class="uppercase text-sm tracking-wider">Learn More</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Add CSS for marquee animation -->
<style>
    
    /* Gradient text utility */
    .gradient-text {
        background: linear-gradient(135deg, #0f6041 0%, #1a8f5f 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<?php
get_footer(); 
?>