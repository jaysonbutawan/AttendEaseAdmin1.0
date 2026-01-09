<script setup lang="ts">
import axios from 'axios';
import {
    BookOpen,
    Calendar,
    ChevronsUpDown,
    Clock,
    Home,
    PlusCircle,
    User,
    CheckCircle2,
    AlertCircle,
    X,
} from 'lucide-vue-next';
import { computed, defineComponent, h, onMounted, ref, VNode } from 'vue';

import { route } from 'ziggy-js';
import SubjectCombox from './CourseComboBox.vue';

type FormErrors = Partial<Record<keyof FormState, string>>;

const errors = ref<FormErrors>({});

interface FormState {
    subjectId: number | '';
    teacherId: string;
    dayOfWeek: string[];
    startTime: string;
    endTime: string;
    roomId: number | '';
}

const validateForm = (): boolean => {
    const f = form.value;
    const newErrors: FormErrors = {};

    if (!f.subjectId) newErrors.subjectId = 'Please select a subject.';
    if (!f.teacherId) newErrors.teacherId = 'Please select a teacher.';
    if (!f.roomId) newErrors.roomId = 'Please select a room.';
    if (!f.startTime) newErrors.startTime = 'Please select a start time.';
    if (!f.endTime) newErrors.endTime = 'Please select an end time.';
    if (f.dayOfWeek.length === 0)
        newErrors.dayOfWeek = 'Please select at least one day.';

    errors.value = newErrors;

    return Object.keys(newErrors).length === 0;
};

type TeacherApi = { name: string; daysAgo?: number; teacher_id: string };

const teachers = ref<TeacherApi[]>([]);
const teacherOptions = computed(() =>
    teachers.value.map((t) => ({
        label: t.name,
        value: t.teacher_id,
    })),
);

type RoomApi = {
    room_id: number;
    room_name: string;
    color?: string | null;
    polygon?: any[];
};

const rooms = ref<RoomApi[]>([]);
const roomOptions = computed(() =>
    rooms.value.map((r) => ({
        label: r.room_name,
        value: String(r.room_id),
    })),
);

const DAYS_OF_WEEK: { name: string; short: string }[] = [
    { name: 'Monday', short: 'Mon' },
    { name: 'Tuesday', short: 'Tue' },
    { name: 'Wednesday', short: 'Wed' },
    { name: 'Thursday', short: 'Thu' },
    { name: 'Friday', short: 'Fri' },
];

const Button = defineComponent({
    props: {
        variant: { type: String, default: 'primary' },
        size: { type: String, default: 'default' },
        className: { type: String, default: '' },
        disabled: { type: Boolean, default: false },
    },
    setup(props, { slots, emit }) {
        const handleClick = (e: MouseEvent) => {
            if (!props.disabled) {
                emit('click', e);
            }
        };

        const classes = computed(() => {
            let baseStyle =
                'inline-flex items-center justify-center rounded-xl font-semibold transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 disabled:pointer-events-none disabled:opacity-50';
            let variantStyle = '';
            let padding = 'px-6 py-3';
            let textStyle = 'text-sm';

            if (props.size === 'icon') {
                padding = 'p-2';
            }

            if (props.variant === 'primary') {
                variantStyle =
                    'bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-blue-500/30 hover:shadow-xl hover:shadow-blue-500/40 hover:scale-105';
            } else if (props.variant === 'outline') {
                variantStyle =
                    'border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-300';
            } else if (props.variant === 'ghost') {
                variantStyle =
                    'text-gray-600 hover:bg-gray-100 hover:text-gray-900';
            }

            return `${baseStyle} ${variantStyle} ${padding} ${textStyle} ${props.className} ${props.disabled ? 'opacity-50 cursor-not-allowed' : ''}`;
        });

        return () =>
            h(
                'button',
                {
                    type: 'button',
                    class: classes.value,
                    disabled: props.disabled,
                    onClick: handleClick,
                },
                slots.default ? slots.default() : undefined,
            );
    },
});

const Input = defineComponent({
    props: {
        modelValue: { type: [String, Number], default: '' },
        placeholder: { type: String, default: '' },
        className: { type: String, default: '' },
        type: { type: String, default: 'text' },
        name: { type: String, required: true },
    },
    setup(props, { emit }) {
        const handleInput = (e: Event) => {
            emit('update:modelValue', (e.target as HTMLInputElement).value);
        };

        const classes = computed(() => {
            const baseStyle =
                'flex h-12 w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-sm transition-all duration-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 hover:border-gray-300 disabled:cursor-not-allowed disabled:opacity-50';
            const typeStyle = props.type === 'time' ? 'appearance-none' : '';

            return `${baseStyle} ${typeStyle} ${props.className}`;
        });

        return () =>
            h('input', {
                class: classes.value,
                type: props.type,
                name: props.name,
                value: props.modelValue,
                placeholder: props.placeholder,
                onInput: handleInput,
            });
    },
});

type SelectOption = string | { label: string; value: string };

const Select = defineComponent({
    props: {
        modelValue: { type: String, default: '' },
        options: { type: Array as () => SelectOption[], required: true },
        placeholder: { type: String, default: 'Select option' },
        className: { type: String, default: '' },
        name: { type: String, required: true },
    },
    setup(props, { emit }) {
        const handleInput = (e: Event) => {
            emit('update:modelValue', (e.target as HTMLSelectElement).value);
        };

        const classes = computed(() => {
            const baseStyle =
                'flex h-12 w-full rounded-xl border-2 border-gray-200 bg-white px-4 py-3 text-sm transition-all duration-300 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 hover:border-gray-300 disabled:cursor-not-allowed disabled:opacity-50';

            return `${baseStyle} ${props.className} appearance-none pr-10`;
        });

        const optionsVNodes = computed(() => {
            const nodes: VNode[] = [
                h(
                    'option',
                    { value: '', disabled: true, selected: !props.modelValue },
                    props.placeholder,
                ),
                ...props.options.map((option) => {
                    const value =
                        typeof option === 'string' ? option : option.value;
                    const label =
                        typeof option === 'string' ? option : option.label;

                    return h('option', { value, key: value }, label);
                }),
            ];

            return nodes;
        });

        return () =>
            h('div', { class: 'relative' }, [
                h(
                    'select',
                    {
                        class: classes.value,
                        name: props.name,
                        value: props.modelValue,
                        onChange: handleInput,
                    },
                    optionsVNodes.value,
                ),
                h(ChevronsUpDown, {
                    class: 'h-5 w-5 absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-400',
                }),
            ]);
    },
});

type SubjectApi = { subject_id: number; subject_name: string };
const subjects = ref<SubjectApi[]>([]);

onMounted(async () => {
    try {
        const url = route ? route('subjects.index') : '/subjects';
        const { data } = await axios.get(url);

        if (Array.isArray(data.subjects)) {
            subjects.value = data.subjects
                .map((s: any) => {
                    if (typeof s === 'string') return null;
                    if (typeof s === 'object' && s !== null) {
                        return {
                            subject_id: Number(s.subject_id),
                            subject_name: String(s.subject_name),
                        };
                    }

                    return null;
                })
                .filter(Boolean) as SubjectApi[];
        } else {
            subjects.value = [];
        }
    } catch (error) {
        console.error('Error loading subjects, using fallback:', error);
        subjects.value = [];
    }

    try {
        const { data } = await axios.get('/room_polygon');
        rooms.value = Array.isArray(data.rooms) ? data.rooms : [];
    } catch (error) {
        console.error('Error loading rooms:', error);
        rooms.value = [];
    }

    try {
        const url = route ? route('teachers.index') : '/teachers_controller';
        const { data } = await axios.get(url);
        teachers.value = Array.isArray(data) ? data : [];
    } catch (error) {
        console.error('Error loading teachers:', error);
        teachers.value = [];
    }
});

const MAX_DAYS = 3;

const toggleDay = (day: string) => {
    const days = form.value.dayOfWeek;

    if (days.includes(day)) {
        form.value.dayOfWeek = days.filter((d) => d !== day);
        errors.value.dayOfWeek = undefined;
        return;
    }

    if (days.length >= MAX_DAYS) {
        statusMessage.value = `You can select up to ${MAX_DAYS} days only.`;
        showStatus.value = true;
        statusType.value = 'warning';
        return;
    }

    form.value.dayOfWeek = [...days, day];
    errors.value.dayOfWeek = undefined;
};

const form = ref<FormState>({
    subjectId: '',
    teacherId: '',
    dayOfWeek: [],
    startTime: '',
    endTime: '',
    roomId: '',
});
const statusMessage = ref<string>('');
const showStatus = ref<boolean>(false);
const statusType = ref<'success' | 'error' | 'warning'>('success');

const handleFormChange = <K extends keyof FormState>(
    name: K,
    value: FormState[K],
) => {
    form.value[name] = value;
};

const handleSubjectCreate = async (subjectName: string) => {
    try {
        const response = await axios.post(
            route ? route('subjects.store') : '/subjects',
            { subject_name: subjectName },
        );

        const created: SubjectApi = response.data.subject;
        const exists = subjects.value.some(
            (s) =>
                s.subject_id === created.subject_id ||
                s.subject_name.toLowerCase() ===
                    created.subject_name.toLowerCase(),
        );

        if (!exists) {
            subjects.value.push(created);
            subjects.value.sort((a, b) =>
                a.subject_name.localeCompare(b.subject_name),
            );
        }
        handleFormChange('subjectId', created.subject_id);

        statusMessage.value = `Subject "${created.subject_name}" saved successfully.`;
        showStatus.value = true;
        statusType.value = 'success';
    } catch (error) {
        console.error(error);
        statusMessage.value =
            'Error saving subject. Please try again or contact support.';
        showStatus.value = true;
        statusType.value = 'error';
    }
};
const isSubmitting = ref(false);

const handleAssignTeacher = async () => {
    if (isSubmitting.value) return;

    if (!validateForm()) {
        statusMessage.value = 'Please fill in all required fields.';
        showStatus.value = true;
        statusType.value = 'error';
        isSubmitting.value = false;
        return;
    }

    isSubmitting.value = true;
    const f = form.value;

    const payload = {
        subject_id: f.subjectId,
        teacher_id: f.teacherId,
        room_id: f.roomId,
        start_time: f.startTime,
        end_time: f.endTime,
        session_days: f.dayOfWeek.map((d) => d.toLowerCase()),
    };

    try {
        const res = await axios.post('/class_sessions', payload);
        console.log('SUCCESS RESPONSE:', res.data);

        statusMessage.value = 'Schedule saved successfully!';
        showStatus.value = true;
        statusType.value = 'success';
        form.value = {
            subjectId: '',
            teacherId: '',
            dayOfWeek: [],
            startTime: '',
            endTime: '',
            roomId: '',
        };
        errors.value = {};
    } catch (e: any) {
        console.error('FAILED STATUS:', e?.response?.status);
        console.error('FAILED DATA:', e?.response?.data);
        statusMessage.value =
            e?.response?.data?.message ?? 'Failed to save schedule.';
        showStatus.value = true;
        statusType.value = 'error';
    } finally {
        isSubmitting.value = false;
    }
};

const handleCancel = () => {
    form.value = {
        subjectId: '',
        teacherId: '',
        dayOfWeek: [],
        startTime: '',
        endTime: '',
        roomId: '',
    };
    errors.value = {};
    statusMessage.value = '';
    showStatus.value = false;
};

const formProgress = computed(() => {
    let filled = 0;
    const total = 6;

    if (form.value.subjectId) filled++;
    if (form.value.teacherId) filled++;
    if (form.value.roomId) filled++;
    if (form.value.startTime) filled++;
    if (form.value.endTime) filled++;
    if (form.value.dayOfWeek.length > 0) filled++;

    return Math.round((filled / total) * 100);
});
</script>

<template>
    <div class="w-full min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-indigo-50/30 p-6">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <Calendar class="h-7 w-7 text-white" />
                    </div>
                    Class Schedule
                </h1>
                <p class="mt-2 text-gray-600">Create and manage class schedules efficiently</p>
            </div>

            <!-- Progress Indicator -->
            <div class="hidden lg:block">
                <div class="text-right mb-2">
                    <span class="text-sm font-semibold text-gray-700">Form Progress</span>
                    <span class="ml-2 text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        {{ formProgress }}%
                    </span>
                </div>
                <div class="w-48 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                        class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 transition-all duration-500 ease-out"
                        :style="{ width: `${formProgress}%` }"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Status Toast -->
        <Transition name="slideDown">
            <div 
                v-if="showStatus"
                :class="[
                    'mb-6 rounded-2xl border-2 p-4 flex items-center justify-between shadow-lg backdrop-blur-sm',
                    statusType === 'success' ? 'bg-green-50 border-green-200' : '',
                    statusType === 'error' ? 'bg-red-50 border-red-200' : '',
                    statusType === 'warning' ? 'bg-amber-50 border-amber-200' : ''
                ]"
            >
                <div class="flex items-center gap-3">
                    <CheckCircle2 
                        v-if="statusType === 'success'" 
                        class="h-6 w-6 text-green-600"
                    />
                    <AlertCircle 
                        v-if="statusType === 'error' || statusType === 'warning'" 
                        :class="statusType === 'error' ? 'text-red-600' : 'text-amber-600'"
                        class="h-6 w-6"
                    />
                    <span 
                        :class="[
                            'font-semibold',
                            statusType === 'success' ? 'text-green-900' : '',
                            statusType === 'error' ? 'text-red-900' : '',
                            statusType === 'warning' ? 'text-amber-900' : ''
                        ]"
                    >
                        {{ statusMessage }}
                    </span>
                </div>
                <button 
                    @click="showStatus = false"
                    class="rounded-lg p-1 hover:bg-black/5 transition-colors"
                >
                    <X class="h-5 w-5 text-gray-500" />
                </button>
            </div>
        </Transition>

        <!-- Main Form Card -->
        <div class="rounded-3xl border-2 border-white/50 bg-white/80 backdrop-blur-sm p-8 shadow-2xl">
            <!-- Section 1: Basic Information -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                        <BookOpen class="h-5 w-5 text-blue-600" />
                    </div>
                    Basic Information
                </h2>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Subject -->
                    <div class="space-y-2 group">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <BookOpen class="mr-2 h-4 w-4 text-blue-500" />
                            Subject Name
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <SubjectCombox
                            :subjects="subjects"
                            :modelValue="form.subjectId"
                            @update:modelValue="
                                handleFormChange('subjectId', $event);
                                errors.subjectId = undefined;
                            "
                            :className="
                                errors.subjectId
                                    ? 'border-red-500 focus:ring-red-500'
                                    : ''
                            "
                            @subjectCreate="handleSubjectCreate"
                        />
                        <Transition name="fade">
                            <p v-if="errors.subjectId" class="text-sm text-red-600 flex items-center gap-1">
                                <AlertCircle class="h-3 w-3" />
                                {{ errors.subjectId }}
                            </p>
                        </Transition>
                    </div>

                    <!-- Teacher -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <User class="mr-2 h-4 w-4 text-blue-500" />
                            Assigned Teacher
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <Select
                            name="teacherId"
                            :modelValue="form.teacherId"
                            @update:modelValue="
                                handleFormChange('teacherId', $event);
                                errors.teacherId = undefined;
                            "
                            :className="
                                errors.teacherId
                                    ? 'border-red-500 focus:ring-red-500'
                                    : ''
                            "
                            :options="teacherOptions"
                            placeholder="Select a teacher"
                        />
                        <Transition name="fade">
                            <p v-if="errors.teacherId" class="text-sm text-red-600 flex items-center gap-1">
                                <AlertCircle class="h-3 w-3" />
                                {{ errors.teacherId }}
                            </p>
                        </Transition>
                    </div>

                    <!-- Room -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <Home class="mr-2 h-4 w-4 text-blue-500" />
                            Room Name
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <Select
                            name="roomId"
                            :modelValue="String(form.roomId)"
                            @update:modelValue="
                                handleFormChange('roomId', Number($event));
                                errors.roomId = undefined;
                            "
                            :className="
                                errors.roomId
                                    ? 'border-red-500 focus:ring-red-500'
                                    : ''
                            "
                            :options="roomOptions"
                            placeholder="Select Room"
                        />
                        <Transition name="fade">
                            <p v-if="errors.roomId" class="text-sm text-red-600 flex items-center gap-1">
                                <AlertCircle class="h-3 w-3" />
                                {{ errors.roomId }}
                            </p>
                        </Transition>
                    </div>
                </div>
            </div>

            <!-- Section 2: Schedule Details -->
            <div class="border-t-2 border-gray-100 pt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <Clock class="h-5 w-5 text-indigo-600" />
                    </div>
                    Schedule Details
                </h2>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Days of Week -->
                    <div class="space-y-3 lg:col-span-3">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <Calendar class="mr-2 h-4 w-4 text-blue-500" />
                            Days of the Week
                            <span class="ml-1 text-red-500">*</span>
                            <span class="ml-2 text-xs text-gray-500 font-normal">(Select up to 3 days)</span>
                        </label>

                        <div class="flex flex-wrap gap-3">
                            <button
                                v-for="day in DAYS_OF_WEEK"
                                :key="day.name"
                                type="button"
                                @click="toggleDay(day.name)"
                                :class="[
                                    'relative px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 overflow-hidden',
                                    form.dayOfWeek.includes(day.name)
                                        ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/30 scale-105'
                                        : 'bg-white border-2 border-gray-200 text-gray-700 hover:border-blue-300 hover:bg-blue-50'
                                ]"
                            >
                                <span class="relative z-10">{{ day.short }}</span>
                                <div 
                                    v-if="form.dayOfWeek.includes(day.name)"
                                    class="absolute inset-0 bg-gradient-to-r from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"
                                ></div>
                            </button>
                        </div>
                        <Transition name="fade">
                            <p v-if="errors.dayOfWeek" class="text-sm text-red-600 flex items-center gap-1">
                                <AlertCircle class="h-3 w-3" />
                                {{ errors.dayOfWeek }}
                            </p>
                        </Transition>
                    </div>

                    <!-- Start Time -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <Clock class="mr-2 h-4 w-4 text-blue-500" />
                            Start Time
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <Input
                            name="startTime"
                            type="time"
                            :modelValue="form.startTime"
                            @update:modelValue="
                                handleFormChange('startTime', $event);
                                errors.startTime = undefined;
                            "
                            :className="
                                errors.startTime
                                    ? 'border-red-500 focus:ring-red-500'
                                    : ''
                            "
                        />
                        <Transition name="fade">
                            <p v-if="errors.startTime" class="text-sm text-red-600 flex items-center gap-1">
                                <AlertCircle class="h-3 w-3" />
                                {{ errors.startTime }}
                            </p>
                        </Transition>
                    </div>

                    <!-- End Time -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <Clock class="mr-2 h-4 w-4 text-blue-500" />
                            End Time
                            <span class="ml-1 text-red-500">*</span>
                        </label>
                        <Input
                            name="endTime"
                            type="time"
                            :modelValue="form.endTime"
                            @update:modelValue="
                                handleFormChange('endTime', $event);
                                errors.endTime = undefined;
                            "
                            :className="
                                errors.endTime
                                    ? 'border-red-500 focus:ring-red-500'
                                    : ''
                            "
                        />
                        <Transition name="fade">
                            <p v-if="errors.endTime" class="text-sm text-red-600 flex items-center gap-1">
                                <AlertCircle class="h-3 w-3" />
                                {{ errors.endTime }}
                            </p>
                        </Transition>
                    </div>

                    <!-- Duration Display -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700">
                            <Clock class="mr-2 h-4 w-4 text-gray-400" />
                            Duration
                        </label>
                        <div class="h-12 flex items-center px-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                            <span class="text-sm font-semibold text-gray-600">
                                {{ form.startTime && form.endTime ? 'Configured' : 'Not set' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-10 flex items-center justify-end gap-4 border-t-2 border-gray-100 pt-6">
                <Button 
                    @click="handleCancel" 
                    variant="outline"
                    :disabled="isSubmitting"
                >
                    Cancel
                </Button>
                <Button
                    @click="handleAssignTeacher"
                    variant="primary"
                    :disabled="isSubmitting"
                >
                    <PlusCircle class="mr-2 h-5 w-5" />
                    {{ isSubmitting ? 'Saving...' : 'Assign Schedule' }}
                </Button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Fade Animation */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 200ms ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Slide Down Animation */
.slideDown-enter-active,
.slideDown-leave-active {
    transition: all 300ms ease;
}
.slideDown-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}
.slideDown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>