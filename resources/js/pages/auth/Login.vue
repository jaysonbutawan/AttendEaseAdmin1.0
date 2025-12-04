<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const showPassword = ref(false);
const passwordType = computed(() => (showPassword.value ? 'text' : 'password'));
</script>

<template>
    <Head title="Log in" />

    <div
        class="flex min-h-screen flex-col items-center bg-white pt-12 dark:bg-neutral-800"
    >
        <div class="mb-8 text-center text-3xl font-bold text-indigo-700">
            <span class="text-4xl font-bold text-black">Attend</span>
            <span class="text-4xl font-bold text-indigo-600">Ease</span>.
        </div>

        <div class="flex w-full justify-center">
            <div
                class="w-full max-w-xl rounded-xl bg-white p-8 shadow-2xl md:p-10 dark:bg-neutral-900"
            >
                <div class="text-center">
                    <h1
                        class="text-2xl font-semibold text-neutral-900 dark:text-neutral-50"
                    >
                        Log in to your account
                    </h1>
                    <p
                        class="mt-2 text-sm text-neutral-500 dark:text-neutral-400"
                    >
                        Enter your email and password below to log in
                    </p>
                </div>

                <div
                    v-if="status"
                    class="mt-6 mb-4 text-center text-sm font-medium text-green-600"
                >
                    {{ status }}
                </div>

                <Form
                    v-bind="store.form()"
                    :reset-on-success="['password']"
                    v-slot="{ errors, processing }"
                    class="flex flex-col gap-6 pt-6"
                >
                    <div class="grid gap-4">
                        <!-- Email Input -->
                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                required
                                autofocus
                                :tabindex="1"
                                autocomplete="email"
                                placeholder="email@example.com"
                                class="h-10 border-neutral-300 dark:border-neutral-700"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <!-- Password Input with Toggle Eye -->
                        <!-- Password Input (No Toggle Eye) -->
                        <div class="grid gap-2">
                            <div class="flex items-center justify-between">
                                <Label for="password">Password</Label>
                            </div>

                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                tabindex="2"
                                autocomplete="current-password"
                                placeholder="Password"
                                class="h-10 border-neutral-300 dark:border-neutral-700"
                            />

                            <InputError :message="errors.password" />

                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="text-right text-sm font-medium text-indigo-600 hover:text-indigo-700"
                                :tabindex="5"
                            >
                                Forgot password?
                            </TextLink>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-start">
                            <Label
                                for="remember"
                                class="flex items-center space-x-2 text-sm font-normal"
                            >
                                <Checkbox
                                    id="remember"
                                    name="remember"
                                    :tabindex="3"
                                    class="h-4 w-4 rounded-md"
                                />
                                <span>Remember me</span>
                            </Label>
                        </div>
                    </div>

                    <!-- Log in Button -->
                    <Button
                        type="submit"
                        class="mt-2 h-10 w-full transform bg-indigo-600 text-base font-semibold text-white shadow-md transition duration-300 hover:scale-105 hover:bg-indigo-700"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        Log in
                    </Button>

                    <!-- Register Link -->
                </Form>
            </div>
        </div>
    </div>
</template>
<!-- Or continue with Divider -->
<!-- <div class="flex items-center justify-center space-x-2 py-2">
                    <div
                        class="h-px flex-grow bg-neutral-200 dark:bg-neutral-700"
                    ></div>
                    <span
                        class="text-xs font-medium text-neutral-400 uppercase dark:text-neutral-500"
                    >
                        Or continue with
                    </span>
                    <div
                        class="h-px flex-grow bg-neutral-200 dark:bg-neutral-700"
                    ></div>
                </div> -->

<!-- Log in with Google Button -->
<!-- Placeholder for actual Google sign-in component/logic -->
<!-- <Button
                    type="button"
                    variant="outline"
                    class="h-10 w-full border-neutral-300 text-neutral-700 hover:bg-neutral-50 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                > -->
<!-- Placeholder for Google Icon -->
<!-- <svg viewBox="0 0 48 48" class="mr-2 h-5 w-5">
                        <path
                            fill="#EA4335"
                            d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.63-6.42C34.46 3.24 29.46 1 24 1 14 1 5 7.8 5 18H15c0-4.68 3.63-8.5 9-8.5z"
                        />
                        <path
                            fill="#4285F4"
                            d="M47.9 24c0-1.3-.12-2.58-.33-3.83H24v7.35h13.62c-.58 3.03-2.3 5.4-4.8 7.02l6.32 5.06C43.5 35.5 47.9 30.3 47.9 24z"
                        />
                        <path
                            fill="#FBBC05"
                            d="M24 47c6.12 0 11.45-2.02 15.27-5.46l-6.32-5.06c-2.34 1.56-5.32 2.49-8.95 2.49-6.4 0-11.83-4.28-13.78-10H4.9c3 6.94 10.14 12 19.1 12z"
                        />
                        <path
                            fill="#34A853"
                            d="M5 18c0-3.04 1.11-5.75 2.9-7.96L4.9 5.05C2.65 7.6 1 11 1 14.8c0 4.1 1.7 7.8 4.65 10.45l5.96-4.63C7.57 19.8 5 16.14 5 18z"
                        />
                    </svg>
                    Log in with Google
                </Button> -->

<!-- Registration Link -->
<!-- <div
                    class="mt-4 text-center text-sm text-muted-foreground"
                    v-if="canRegister"
                >
                    Don't have an account?
                    <TextLink
                        :href="register()"
                        :tabindex="5"
                        class="font-medium text-indigo-600 hover:text-indigo-700"
                    >
                        Sign up
                    </TextLink>
                </div>-->
