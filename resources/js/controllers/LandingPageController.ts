// src/controllers/LandingPageController.ts
import { Ref, ref, nextTick } from 'vue';

export interface Slide {
  src: string;
  alt: string;
}

export interface Feature {
  title: string;
  description: string;
  imageUrl: string;
}

export class LandingPageController {
  // --- State ---
  isMobileMenuOpen: Ref<boolean> = ref(false);
  isLoginFormVisible: Ref<boolean> = ref(false);
  currentSlideIndex: Ref<number> = ref(0);
  activeSection: Ref<string> = ref('faq-section');
  loginMessage: Ref<string> = ref('');

  slides: Slide[];
  features: Feature[];

  private autoSlideTimer: number | undefined;
  private readonly sectionIds = [
    'faq-section',
    'help-section',
    'contacts-section',
    'about-section',
  ];

  constructor(slides: Slide[], features: Feature[]) {
    this.slides = slides;
    this.features = features;
  }

  // --- Auth / UI toggle logic ---

  toggleAuthView() {
    if (typeof window === 'undefined') return;

    if (window.innerWidth >= 640) {
      this.isLoginFormVisible.value = !this.isLoginFormVisible.value;

      if (this.isLoginFormVisible.value) {
        nextTick(() => {
          document.getElementById('email')?.focus();
        });
      }
    }
  }

  toggleMobileMenu() {
    this.isMobileMenuOpen.value = !this.isMobileMenuOpen.value;
    if (this.isMobileMenuOpen.value) {
      this.isLoginFormVisible.value = false;
    }
  }

  handleMobileLinkClick() {
    this.isMobileMenuOpen.value = false;
  }

  // --- Form Logic ---

  handleLogin(event: Event) {
    event.preventDefault();

    const form = event.target as HTMLFormElement;
    const emailInput = form.elements.namedItem('email') as HTMLInputElement;
    const passwordInput = form.elements.namedItem('password') as HTMLInputElement;

    const email = emailInput.value;
    const password = passwordInput.value;

    if (email === 'admin@example.com' && password === 'admin123') {
      this.loginMessage.value = 'Login successful! Redirecting...';
      setTimeout(() => (this.loginMessage.value = ''), 1500);
    } else {
      this.loginMessage.value = 'Invalid email or password!';
      setTimeout(() => (this.loginMessage.value = ''), 3000);
    }
  }

  handleContactSubmit(event: Event) {
    event.preventDefault();
    this.loginMessage.value = 'Inquiry submitted successfully!';

    setTimeout(() => {
      this.loginMessage.value = '';
      (event.target as HTMLFormElement).reset();
    }, 3000);
  }

  // --- Carousel Logic ---

  nextSlide() {
    this.currentSlideIndex.value =
      (this.currentSlideIndex.value + 1) % this.slides.length;
  }

  prevSlide() {
    this.currentSlideIndex.value =
      (this.currentSlideIndex.value - 1 + this.slides.length) %
      this.slides.length;
  }

  goToSlide(index: number) {
    this.currentSlideIndex.value = index;
  }

  private startAutoSlide() {
    if (typeof window === 'undefined') return;
    this.autoSlideTimer = window.setInterval(() => this.nextSlide(), 3000);
  }

  private stopAutoSlide() {
    if (this.autoSlideTimer !== undefined) {
      clearInterval(this.autoSlideTimer);
      this.autoSlideTimer = undefined;
    }
  }

  // --- Scroll / Active link logic ---

  updateActiveSection = () => {
    if (typeof window === 'undefined') return;

    let currentActive = 'faq-section';
    const windowHeight = window.innerHeight;

    for (const id of this.sectionIds) {
      const section = document.getElementById(id);
      if (!section) continue;

      const rect = section.getBoundingClientRect();
      if (rect.top <= windowHeight * 0.3 && rect.bottom >= windowHeight * 0.3) {
        currentActive = id;
      }
    }

    this.activeSection.value = currentActive;
  };

  isLinkActive(id: string) {
    return this.activeSection.value === id;
  }

  // --- Lifecycle-style methods (call from component) ---

  init() {
    if (typeof window === 'undefined') return;

    this.startAutoSlide();
    window.addEventListener('resize', this.updateActiveSection);
    window.addEventListener('scroll', this.updateActiveSection);
    this.updateActiveSection();
  }

  destroy() {
    if (typeof window === 'undefined') return;

    this.stopAutoSlide();
    window.removeEventListener('resize', this.updateActiveSection);
    window.removeEventListener('scroll', this.updateActiveSection);
  }
}
