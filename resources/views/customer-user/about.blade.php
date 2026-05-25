@extends('layouts.app')

@section('content')

<section class="py-12 sm:py-16 md:py-20 bg-white mt-4 mb-[90px]">
    <div class="container mx-auto px-4 sm:px-6 md:px-8 lg:px-20">
        <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12">
            
            <div class="relative flex-shrink-0 w-full md:w-1/2 flex flex-col md:flex-row items-center 
                        scroll-reveal-item" data-delay="0"> {{-- Added scroll-reveal --}}
                <img src="{{ asset('assets/images/about/about1.jpg') }}" alt="About 1"
                     class="w-[240px] sm:w-[280px] h-[320px] sm:h-[360px] object-cover rounded shadow-lg relative z-10 mb-8 mt-12 md:mb-0 
                            transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-xl"> {{-- Added hover --}}
                
                <img src="{{ asset('assets/images/about/about2.png') }}" alt="About 2"
                     class="w-[240px] sm:w-[280px] h-[320px] sm:h-[360px] object-cover rounded relative md:top-32 md:left-8 lg:top-32 lg:left-8 z-0 
                            transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-xl scroll-reveal-item" data-delay="100"> {{-- Added hover and scroll-reveal --}}
            </div>

            <div class="w-full md:w-1/2 text-left scroll-reveal-item" data-delay="150"> {{-- Added scroll-reveal --}}
                <p class="text-blue-700 text-sm font-semibold mb-2 ml-4">A BIT</p>
                <h2 class="text-2xl sm:text-3xl font-bold text-black mb-4 sm:mb-6 ml-4">ABOUT US</h2>
                <p class="text-black leading-relaxed text-justify text-sm sm:text-base ml-4">
                    Velora brings minimalist and fresh streetwear designs for those who embrace effortless style.
                    Our collection blends comfort, quality, and modern aesthetics to elevate your everyday look.
                    From casual essentials to statement pieces, we redefine street fashion with a unique identity.
                    Explore our latest drops and stay ahead of the trend. Shop now and be part of the movement.
                </p>
            </div>
        </div>
    </div>
</section>
{{-- Vanilla JavaScript for Scroll Reveal (Same as in shop.blade.php for consistency) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollRevealItems = document.querySelectorAll('.scroll-reveal-item');

    const observerOptions = {
        root: null, // viewport as the root
        rootMargin: '0px',
        threshold: 0.1 // Percentage of item visible to trigger
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = parseInt(entry.target.dataset.delay) || 0;
                
                entry.target.classList.add('opacity-0', 'translate-y-10');
                entry.target.classList.add('transition-all', 'duration-700', 'ease-out');
                
                setTimeout(() => {
                    entry.target.classList.remove('opacity-0', 'translate-y-10');
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                }, delay);

                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    scrollRevealItems.forEach(item => {
        item.classList.add('opacity-0', 'translate-y-10'); // Ensure initial state
        observer.observe(item);
    });

    // --- Page/Section Transition on Navigation (Client-side) ---
    const allInternalLinks = document.querySelectorAll('a[href^="{{ url('/') }}"]:not([href^="#"])');

    allInternalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); 
            const targetUrl = this.href;

            document.body.classList.add('page-exit-animation');
            
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 300); 
        });
    });

    document.body.classList.add('page-enter-animation');
    setTimeout(() => {
        document.body.classList.remove('page-enter-animation');
    }, 300);

});
</script>
@endsection