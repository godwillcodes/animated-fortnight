<?php
/**
 * Template Name: About Us
 * Description: Campaign About Us Page with Clean Typography
 */
get_header();
?>
<section class="shadow-sm">
    <div class="h-[500px] md:h-[400px] lg:h-[404px] relative"
        style="background-color: #0f6041;">
        <div class="absolute inset-0 flex items-end">
            <div class="container mx-auto w-full px-10 lg:px-0 pb-4 md:pb-12 lg:pb-12 text-white">
                <h1 class="text-2xl md:text-4xl lg:text-4xl font-bold" data-aos="fade-up" data-aos-delay="200">
                    Boniface Mwangi
                </h1>
                <p class="text-base lg:text-lg my-4 max-w-4xl" data-aos="fade-up" data-aos-delay="400">
                    Relentless Voice for Justice, Freedom, and People’s Power
                </p>
                <a href="/donate" 
  class="inline-flex items-center gap-2 bg-white text-[#0f6041] px-6 py-2.5 mt-4 text-base font-semibold rounded-full shadow-sm">
  Donate Now
  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
  </svg>
</a>

            </div>
        </div>
    </div>
</section>
<div class="relative w-full h-20 overflow-hidden bg-gray-50">
    <div class="absolute inset-0 w-[200%] h-full bg-repeat-x bg-contain animate-marquee" 
         style="background-image: url('/wp-content/uploads/2025/08/BM-TERRACE-BRANDING-Final-1_page-0001_11zon-1-scaled.jpg');"></div>
</div>




<section class="bg-white py-16 px-6">
    <div class="container mx-auto space-y-24">

        <!-- Section One -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            <!-- Left: Biography + Personal Background -->
            <div class="space-y-8" data-aos="fade-up" data-aos-once="true" data-aos-duration="400">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-wide">Biography</h2>
                <p class="text-lg text-gray-700 leading-relaxed">
                    Once in a generation, a man or a woman with unfathomable courage rises and dedicates his life to
                    bettering humanity, in total disregard of his safety or comfort.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    From the most unlikely places one such man with a capacity to make the country stand still and
                    listen is Boniface Mwangi. This man, curated from a boy who was subjected to unspeakable human
                    rights violations but never succumbed or lost focus, instead projected his voice and deployed all
                    his skills to highlight the ills bedeviling society as he saw them.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    The world, which had initially ignored him as a boy, can no longer afford to do so today, for he has
                    joined the ranks of those shaping the world through his camera’s lenses.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    Born in 1983, the self-made Mwangi—mistakenly christened “Softie” by peers who underrated him—had to
                    wait tables in kiosks in exchange for leftovers, charm snake charmers to earn rent as a teenager,
                    but ultimately earned diplomas before he graduated from secondary school. Today, he routinely gives
                    talks in the most prestigious schools in the world.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    Mwangi’s immutable voice has brought down walls of injustice built by authoritarian kleptomanias. So
                    forceful and persuasive is Mwangi that President Barrack Obama paused to read his story and was so
                    moved that he even agreed to write a foreword for one of his books, <em>UnBounded</em>. Former
                    Secretary of State and US First Lady, Hillary Clinton, was equally moved by Mwangi’s stunning
                    photography and talent.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    This man, who redefined how campaigns are done in Kenya by soliciting funding from the common man in
                    the street through crowdsourcing rather than offering handouts to voters, has figuratively seen it
                    all. From winning continental journalism awards to being hauled to police cells for speaking truth
                    to power by shouting down former President Mwai Kibaki, Mwangi has seen it all.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    His courage, just like his creativity, knows no bounds, just as his passion for freedom knows not
                    national boundaries. It is on account of his empathy for freedom and loathing for tyranny that he
                    was abducted and tortured by a combined effort of Kenyan and Tanzanian authorities when he went to
                    monitor Tundu Lissu's treason case. To Kenyan authorities, Mwangi is a “terrorist” who must be
                    confined and contained at whatever cost. But to compatriots like former Chief Justice Willy Mutunga,
                    he is a youthful rebel and revolutionary determined to overthrow the global status quo. His
                    selflessness and sacrifice, according to acclaimed South African songstress Yvonne Chaka Chaka,
                    place him among a rare breed of journalists who disregard their safety and comfort so that the world
                    can be a better place.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    At times, Mwangi’s bravery is confused for “careless” patriotism, as Principal Magistrate Samson
                    Temu cautioned even as he warned the activist not to risk his life fighting for Kenya because the
                    country would fight for itself.
                </p>

                <p class="text-lg text-gray-700 leading-relaxed">
                    This is the man who now wants to be Kenya’s next president. It will be interesting to see how he
                    will navigate this space as he soldiers on with his cause for defending freedom and good governance
                    for all, guided by <em>Ukweli</em> (the truth). Is Kenya ready for this man, his truth, and his
                    courage?
                </p>

            </div>

            <!-- Right: Video (Static on scroll) -->
            <div class="rounded-3xl overflow-hidden shadow-2xl sticky top-32" data-aos="fade-up" data-aos-once="true"
                data-aos-duration="400">
                
				
				<iframe class="w-full h-72 md:h-96 rounded-3xl" src="https://www.youtube.com/embed/nUSAEGBaI_o" title="Who is Boniface Mwangi?" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
        </div>

      

    </div>
</section>




<style>
.fade-in-scale {
    animation: fadeInScale 0.6s ease-out forwards;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(1.05);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
</style>

<script>
class SimpleTimeline {
    constructor() {
        this.activeItem = 0;
        this.timelineItems = document.querySelectorAll('.timeline-item');
        this.totalItems = this.timelineItems.length;
        this.autoAdvanceTimer = null;

        this.init();
    }

    init() {
        this.createImages();
        this.createNavigationDots();
        this.bindEvents();
        this.setActiveItem(0);
        this.startAutoAdvance();
    }

    createImages() {
        const container = document.getElementById('imageContainer');
        container.innerHTML = '';

        this.timelineItems.forEach((item, index) => {
            const imageDiv = document.createElement('div');
            imageDiv.className =
                'absolute inset-0 transition-all duration-500 opacity-0 transform scale-105';
            imageDiv.innerHTML = `
                <img src="${item.dataset.image}" alt="${item.dataset.title}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <div class="glass-effect rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-1">${item.dataset.title}</h4>
                        <p class="text-sm opacity-90">${item.dataset.caption}</p>
                    </div>
                </div>
            `;
            container.appendChild(imageDiv);
        });
    }

    createNavigationDots() {
        const container = document.getElementById('navigationDots');
        container.innerHTML = '';

        this.timelineItems.forEach((item, index) => {
            const dot = document.createElement('button');
            dot.className =
            'w-3 h-3 rounded-full transition-all duration-300 bg-white/50 hover:bg-white/75';
            dot.addEventListener('click', () => this.setActiveItem(index));
            container.appendChild(dot);
        });
    }

    setActiveItem(index) {
        this.activeItem = index;

        // Update timeline items
        this.timelineItems.forEach((item, i) => {
            const dot = item.querySelector('.timeline-dot');
            const dotInner = dot.querySelector('div');
            const arrow = item.querySelector('svg');
            const card = item.querySelector('.bg-white');

            if (i === index) {
                // Activate current item
                item.classList.remove('opacity-60');
                item.classList.add('opacity-100');
                card.classList.add('ring-2', 'ring-green-400', 'ring-opacity-20', 'shadow-lg');

                // Update dot with data attributes
                dot.className =
                    `timeline-dot relative z-10 flex-shrink-0 w-12 h-12 rounded-full border-4 border-white shadow-lg flex items-center justify-center mr-6 ${item.dataset.dotColor}`;
                dotInner.className = 'w-4 h-4 rounded-full bg-white';

                // Scale arrow
                arrow.classList.add('transform', 'scale-110');
            } else {
                // Deactivate other items
                item.classList.remove('opacity-100');
                item.classList.add('opacity-60');
                card.classList.remove('ring-2', 'ring-green-400', 'ring-opacity-20', 'shadow-lg');

                // Reset dot
                dot.className =
                    'timeline-dot relative z-10 flex-shrink-0 w-12 h-12 rounded-full border-4 border-white shadow-lg flex items-center justify-center mr-6 bg-gray-300';
                dotInner.className = 'w-4 h-4 rounded-full bg-gray-500';

                // Reset arrow
                arrow.classList.remove('transform', 'scale-110');
            }
        });

        // Update images
        const images = document.querySelectorAll('#imageContainer > div');
        images.forEach((img, i) => {
            if (i === index) {
                img.classList.remove('opacity-0', 'scale-105');
                img.classList.add('opacity-100', 'scale-100', 'fade-in-scale');
            } else {
                img.classList.remove('opacity-100', 'scale-100', 'fade-in-scale');
                img.classList.add('opacity-0', 'scale-95');
            }
        });

        // Update navigation dots
        const navDots = document.querySelectorAll('#navigationDots button');
        navDots.forEach((dot, i) => {
            if (i === index) {
                dot.className = 'w-3 h-3 rounded-full transition-all duration-300 bg-white scale-125';
            } else {
                dot.className =
                    'w-3 h-3 rounded-full transition-all duration-300 bg-white/50 hover:bg-white/75';
            }
        });

        // Update progress bar
        const progressBar = document.getElementById('progressBar');
        progressBar.style.width = `${((index + 1) / this.totalItems) * 100}%`;

        // Update counter
        document.getElementById('counter').textContent = `${index + 1} of ${this.totalItems}`;

        // Update navigation buttons
        this.updateNavigationButtons();
    }

    updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        // Previous button
        if (this.activeItem === 0) {
            prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
            prevBtn.disabled = true;
        } else {
            prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            prevBtn.disabled = false;
        }

        // Next button
        if (this.activeItem === this.totalItems - 1) {
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
            nextBtn.disabled = true;
        } else {
            nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            nextBtn.disabled = false;
        }
    }

    nextItem() {
        if (this.activeItem < this.totalItems - 1) {
            this.setActiveItem(this.activeItem + 1);
        }
    }

    previousItem() {
        if (this.activeItem > 0) {
            this.setActiveItem(this.activeItem - 1);
        }
    }

    bindEvents() {
        // Navigation buttons
        document.getElementById('nextBtn').addEventListener('click', () => {
            this.nextItem();
            this.resetAutoAdvance();
        });

        document.getElementById('prevBtn').addEventListener('click', () => {
            this.previousItem();
            this.resetAutoAdvance();
        });

        // Timeline item clicks
        this.timelineItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                this.setActiveItem(index);
                this.resetAutoAdvance();
            });
        });

        // Pause auto-advance on hover
        const section = document.querySelector('section');
        section.addEventListener('mouseenter', () => this.stopAutoAdvance());
        section.addEventListener('mouseleave', () => this.startAutoAdvance());
    }

    startAutoAdvance() {
        this.autoAdvanceTimer = setInterval(() => {
            if (this.activeItem < this.totalItems - 1) {
                this.nextItem();
            } else {
                this.setActiveItem(0); // Loop back to first item
            }
        }, 5000); // Changed to 5 seconds for better UX with 3 items
    }

    stopAutoAdvance() {
        if (this.autoAdvanceTimer) {
            clearInterval(this.autoAdvanceTimer);
            this.autoAdvanceTimer = null;
        }
    }

    resetAutoAdvance() {
        this.stopAutoAdvance();
        this.startAutoAdvance();
    }
}

// Initialize timeline when page loads
document.addEventListener('DOMContentLoaded', () => {
    new SimpleTimeline();
});
</script>


<?php
get_footer(); 
?>