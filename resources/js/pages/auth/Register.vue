<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import { Eye, EyeOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';

defineProps<{
    status?: string;
}>();

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const passwordType = computed(() => (showPassword.value ? 'text' : 'password'));
const confirmPasswordType = computed(() =>
    showConfirmPassword.value ? 'text' : 'password',
);
</script>

<template>
    <Head title="Register" />

    <div
        class="flex min-h-screen flex-col items-center bg-white pt-12 dark:bg-neutral-800"
    >
        <div class="mb-8 text-center text-3xl font-bold text-indigo-700">
            <span class="text-4xl font-bold text-black">Attend</span>
            <span class="text-4xl font-bold text-indigo-600">Ease</span>.
        </div>

        <!-- Card below, centered horizontally -->
        <div
            class="w-full max-w-xl rounded-xl bg-white p-8 shadow-2xl md:p-10 dark:bg-neutral-900"
        >
            <div class="text-center">
                <h1
                    class="text-2xl font-semibold text-neutral-900 dark:text-neutral-50"
                >
                    Create your account
                </h1>
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                    Enter your details below to create a new account
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
                :reset-on-success="['password', 'password_confirmation']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6 pt-6"
            >
                <div class="grid gap-4">
                    <!-- Name Input -->
                    <div class="grid gap-2">
                        <Label for="name">Full name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            name="name"
                            placeholder="Ricky Paran"
                            class="h-10 border-neutral-300 dark:border-neutral-700"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <!-- Email Input -->
                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="paran@example.com"
                            class="h-10 border-neutral-300 dark:border-neutral-700"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <!-- Password Input with Toggle Eye -->
                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <div class="relative">
                            <Input
                                id="password"
                                :type="passwordType"
                                name="password"
                                required
                                :tabindex="3"
                                autocomplete="new-password"
                                placeholder="********"
                                class="h-10 border-neutral-300 pr-10 dark:border-neutral-700"
                            />
                            <!-- Password Visibility Toggle Button -->
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                @click="showPassword = !showPassword"
                                class="absolute top-1/2 right-1 -translate-y-1/2 rounded-full text-neutral-500 hover:bg-transparent hover:text-neutral-700 dark:hover:text-neutral-300"
                                aria-label="Toggle password visibility"
                            >
                                <Eye v-if="showPassword" class="h-5 w-5" />
                                <EyeOff v-else class="h-5 w-5" />
                            </Button>
                        </div>
                        <InputError :message="errors.password" />
                    </div>

                    <!-- Confirm Password Input with Toggle Eye -->
                    <div class="grid gap-2">
                        <Label for="password_confirmation"
                            >Confirm password</Label
                        >
                        <div class="relative">
                            <Input
                                id="password_confirmation"
                                :type="confirmPasswordType"
                                name="password_confirmation"
                                required
                                :tabindex="4"
                                autocomplete="new-password"
                                placeholder="********"
                                class="h-10 border-neutral-300 pr-10 dark:border-neutral-700"
                            />
                            <!-- Confirm Password Visibility Toggle Button -->
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                @click="
                                    showConfirmPassword = !showConfirmPassword
                                "
                                class="absolute top-1/2 right-1 -translate-y-1/2 rounded-full text-neutral-500 hover:bg-transparent hover:text-neutral-700 dark:hover:text-neutral-300"
                                aria-label="Toggle confirm password visibility"
                            >
                                <Eye
                                    v-if="showConfirmPassword"
                                    class="h-5 w-5"
                                />
                                <EyeOff v-else class="h-5 w-5" />
                            </Button>
                        </div>
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <!-- Sign Up Button -->
                    <Button
                        type="submit"
                        class="transform mt-2 h-10 w-full bg-indigo-600 text-base font-semibold text-white shadow-md transition duration-300 hover:scale-105 hover:bg-indigo-700"
                        :tabindex="5"
                        :disabled="processing"
                        data-test="register-user-button"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        Sign Up
                    </Button>
                </div>

                <!-- Login Link -->
                <div class="mt-4 text-center text-sm text-muted-foreground">
                    Already have an account?
                    <TextLink
                        :href="login()"
                        :tabindex="6"
                        class="font-medium text-indigo-600 hover:text-indigo-700"
                        >Log in</TextLink
                    >
                </div>
            </Form>
        </div>
    </div>
</template>
