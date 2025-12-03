<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { login, register } from '@/routes';
import type { LandingPageController } from '@/controllers/LandingPageController';

const props = defineProps<{
  controller: LandingPageController;
  canRegister: boolean;
}>();
</script>

<template>
    <div class="ml-30 grid flex-1 text-left text-xl">
      <p class="text-sm text-gray-500">
        <span class="text-2xl font-bold text-black">Attend</span>
        <span class="text-2xl font-bold text-indigo-600">Ease</span>.
      </p>
    </div>

    <div class="hidden flex-1 items-center justify-end space-x-3 lg:flex">
      <nav class="hidden items-center space-x-3 lg:flex">
        <div
          id="dynamic-content-container"
          class="desktop-flex-grow flex h-full items-center justify-end"
        >
          <nav
            id="nav-links"
            :class="{ hidden: props.controller.isLoginFormVisible.value }"
            class="flex h-full items-center lg:mr-4 lg:space-x-1"
          >
            <a
              href="#faq-section"
              :class="[
                'nav-item text-gray-500 hover:text-gray-900',
                { active: props.controller.isLinkActive('faq-section') },
              ]"
            >
              FAQ
            </a>
            <a
              href="#help-section"
              :class="[
                'nav-item text-gray-500 hover:text-gray-900',
                { active: props.controller.isLinkActive('help-section') },
              ]"
            >
              Help & Downloads
            </a>
            <a
              href="#contacts-section"
              :class="[
                'nav-item text-gray-500 hover:text-gray-900',
                { active: props.controller.isLinkActive('contacts-section') },
              ]"
            >
              Contact Us
            </a>
            <a
              href="#about-section"
              :class="[
                'nav-item text-gray-500 hover:text-gray-900',
                { active: props.controller.isLinkActive('about-section') },
              ]"
            >
              About Us
            </a>
          </nav>
        </div>

        <Link
          :href="login()"
          class="transform rounded-xl border border-gray-300 bg-white px-8 py-3 text-lg font-semibold text-gray-700 shadow-md transition duration-300 hover:scale-105 hover:bg-gray-50"
        >
          Log in
        </Link>
        <Link
          v-if="props.canRegister"
          :href="register()"
          class="transform rounded-xl bg-indigo-600 px-8 py-3 text-lg font-semibold text-white shadow-lg transition duration-300 hover:scale-105 hover:bg-indigo-700"
        >
          Register
        </Link>
      </nav>
    </div>

    <button
      type="button"
      class="ml-auto inline-flex items-center justify-center rounded-lg p-2 text-gray-600 hover:bg-gray-100 focus:ring-2 focus:ring-indigo-500 focus:outline-none lg:hidden"
      @click="props.controller.toggleMobileMenu()"
    >
      <svg
        v-if="!props.controller.isMobileMenuOpen"
        class="h-6 w-6"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16M4 18h16"
        />
      </svg>
      <svg
        v-else
        class="h-6 w-6"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>
  <div v-if="props.controller.isMobileMenuOpen" class="lg:hidden">
    <div
      class="mt-2 space-y-2 rounded-xl bg-white px-4 py-3 shadow-lg ring-1 ring-black/5 dark:bg-gray-800"
    >
      <nav class="flex flex-col space-y-2">
        <a
          href="#faq-section"
          @click="props.controller.handleMobileLinkClick()"
          :class="[
            'block rounded-lg px-2 py-2 text-base text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700',
            { 'font-semibold text-indigo-600': props.controller.isLinkActive('faq-section') },
          ]"
        >
          FAQ
        </a>
        <a
          href="#help-section"
          @click="props.controller.handleMobileLinkClick()"
          :class="[
            'block rounded-lg px-2 py-2 text-base text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700',
            { 'font-semibold text-indigo-600': props.controller.isLinkActive('help-section') },
          ]"
        >
          Help & Downloads
        </a>
        <a
          href="#contacts-section"
          @click="props.controller.handleMobileLinkClick()"
          :class="[
            'block rounded-lg px-2 py-2 text-base text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700',
            { 'font-semibold text-indigo-600': props.controller.isLinkActive('contacts-section') },
          ]"
        >
          Contact Us
        </a>
        <a
          href="#about-section"
          @click="props.controller.handleMobileLinkClick()"
          :class="[
            'block rounded-lg px-2 py-2 text-base text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700',
            { 'font-semibold text-indigo-600': props.controller.isLinkActive('about-section') },
          ]"
        >
          About Us
        </a>
      </nav>

      <div class="mt-3 flex flex-col space-y-2">
        <Link
          :href="login()"
          @click="props.controller.handleMobileLinkClick()"
          class="w-full rounded-lg border border-gray-200 px-4 py-2 text-center text-base font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
        >
          Log in
        </Link>
        <Link
          v-if="props.canRegister"
          :href="register()"
          @click="props.controller.handleMobileLinkClick()"
          class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-center text-base font-medium text-white shadow-md transition duration-150 hover:bg-indigo-700"
        >
          Register
        </Link>
      </div>
    </div>
  </div>
</template>
