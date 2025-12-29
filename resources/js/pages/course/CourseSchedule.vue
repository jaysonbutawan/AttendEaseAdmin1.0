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

const DAYS_OF_WEEK: string[] = [
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
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
                'inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-blue-500 disabled:pointer-events-none disabled:opacity-50';
            let variantStyle = '';
            let padding = 'px-4 py-2';
            let textStyle = 'text-sm';

            if (props.size === 'icon') {
                padding = 'p-2';
            }

            if (props.variant === 'primary') {
                variantStyle =
                    'bg-blue-600 text-white hover:bg-blue-700 shadow-md hover:shadow-lg';
            } else if (props.variant === 'outline') {
                variantStyle =
                    'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700';
            } else if (props.variant === 'ghost') {
                variantStyle =
                    'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700';
            }

            return `${baseStyle} ${variantStyle} ${padding} ${textStyle} ${props.className} ${props.disabled ? 'opacity-50 cursor-not-allowed' : ''}`;
        });

        return () =>
            h(
                'button',
                {
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
                'flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50';
            const darkStyle =
                'dark:border-gray-700 dark:bg-gray-900 dark:text-gray-50 dark:placeholder:text-gray-500';
            const typeStyle = props.type === 'time' ? 'appearance-none' : '';

            return `${baseStyle} ${darkStyle} ${typeStyle} ${props.className}`;
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
                'flex h-10 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50';
            const darkStyle =
                'dark:border-gray-700 dark:bg-gray-900 dark:text-gray-50 dark:placeholder:text-gray-500';

            return `${baseStyle} ${darkStyle} ${props.className} appearance-none pr-8`;
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
                    class: 'h-4 w-4 absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none text-gray-500 dark:text-gray-400',
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
        return;
    }

    if (days.length >= MAX_DAYS) {
        statusMessage.value = `You can select up to ${MAX_DAYS} days only.`;
        return;
    }

    form.value.dayOfWeek = [...days, day];
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
    } catch (error) {
        console.error(error);
        statusMessage.value =
            'Error saving subject. Please try again or contact support.';
    }
};

const handleAssignTeacher = async () => {
    if (!validateForm()) {
        return;
    }
    const f = form.value;

    console.log('FORM BEFORE CHECK:', JSON.stringify(f, null, 2));

    if (
        !f.subjectId ||
        !f.teacherId ||
        f.dayOfWeek.length === 0 ||
        !f.startTime ||
        !f.endTime ||
        !f.roomId
    ) {
        console.log('BLOCKED: missing fields', {
            subjectId: f.subjectId,
            teacherId: f.teacherId,
            roomId: f.roomId,
            dayOfWeek: f.dayOfWeek,
            startTime: f.startTime,
            endTime: f.endTime,
        });
        statusMessage.value = 'Please fill all fields before assigning.';
        return;
    }

    const payload = {
        subject_id: f.subjectId,
        teacher_id: f.teacherId,
        room_id: f.roomId,
        start_time: f.startTime,
        end_time: f.endTime,
        session_days: f.dayOfWeek.map((d) => d.toLowerCase()),
    };

    console.log('PAYLOAD TO SERVER:', payload);

    try {
        const res = await axios.post('/class_sessions', payload);
        console.log('SUCCESS RESPONSE:', res.data);

        statusMessage.value = 'Schedule saved successfully.';
        form.value = {
            subjectId: '',
            teacherId: '',
            dayOfWeek: [],
            startTime: '',
            endTime: '',
            roomId: '',
        };
    } catch (e: any) {
        console.error('FAILED STATUS:', e?.response?.status);
        console.error('FAILED DATA:', e?.response?.data);
        statusMessage.value =
            e?.response?.data?.message ?? 'Failed to save schedule.';
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
    statusMessage.value = 'Form reset.';
};
</script>

<template>
    <div class="w-full">
        <h1
            class="mb-8 border-b border-gray-200 pb-2 text-3xl font-extrabold text-gray-900 dark:text-white"
        >
            <Calendar class="mr-2 inline-block h-6 w-6 text-blue-600" />
            Class Schedule Assignment
        </h1>

        <div
            class="rounded-xl border border-gray-100 bg-white p-6 shadow-2xl md:p-8 dark:border-gray-700/50 dark:bg-gray-800"
        >
            <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        <BookOpen class="mr-2 h-4 w-4 text-blue-500" />
                        Subject Name
                    </label>
                    <SubjectCombox
                        :subjects="subjects"
                        :modelValue="form.subjectId"
                        @update:modelValue="
                            handleFormChange('subjectId', $event);
                            errors.subjectId = undefined;
                        "
                        @subjectCreate="handleSubjectCreate"
                    />
                    <p v-if="errors.subjectId" class="text-sm text-red-600">
                        {{ errors.subjectId }}
                    </p>
                </div>
                <!-- Assigned Teacher -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        <User class="mr-2 h-4 w-4 text-blue-500" />
                        Assigned Teacher
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
                    <p v-if="errors.teacherId" class="text-sm text-red-600">
                        {{ errors.teacherId }}
                    </p>
                </div>

                <!-- Room Number -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        <Home class="mr-2 h-4 w-4 text-blue-500" />
                        Room Name
                    </label>
                    <Select
                        name="roomId"
                        :modelValue="String(form.roomId)"
                        @update:modelValue="
                            handleFormChange('roomId', Number($event));
                            errors.subjectId = undefined;
                        "
                        :className="
                            errors.roomId
                                ? 'border-red-500 focus:ring-red-500'
                                : ''
                        "
                        :options="roomOptions"
                        placeholder="Select Room"
                    />
                    <p v-if="errors.subjectId" class="text-sm text-red-600">
                        {{ errors.subjectId }}
                    </p>
                </div>
            </div>

            <!-- Row 2: 3 columns -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Day of Week -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        <Calendar class="mr-2 h-4 w-4 text-blue-500" />
                        Day of the Week (select up to 3)
                    </label>

                    <div class="grid grid-cols-2 gap-2">
                        <label
                            v-for="day in DAYS_OF_WEEK"
                            :key="day"
                            class="flex items-center gap-2 rounded border bg-white px-3 py-2 text-sm hover:bg-gray-50"
                        >
                            <input
                                type="checkbox"
                                :checked="form.dayOfWeek.includes(day)"
                                @change="toggleDay(day)"
                            />

                            <span>{{ day }}</span>
                        </label>
                    </div>
                    <p
                        v-if="errors.dayOfWeek"
                        class="mt-2 text-sm text-red-600"
                    >
                        {{ errors.dayOfWeek }}
                    </p>
                </div>

                <!-- Start Time -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        <Clock class="mr-2 h-4 w-4 text-blue-500" />
                        Start Time
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
                    <p v-if="errors.startTime" class="text-sm text-red-600">
                        {{ errors.startTime }}
                    </p>
                </div>

                <!-- End Time -->
                <div class="space-y-2">
                    <label
                        class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300"
                    >
                        <Clock class="mr-2 h-4 w-4 text-blue-500" />
                        End Time
                    </label>
                    <Input
                        name="endTime"
                        type="time"
                        :modelValue="form.endTime"
                        @update:modelValue="
                            handleFormChange('endTime', $event);
                            errors.startTime = undefined;
                        "
                        :className="
                            errors.startTime
                                ? 'border-red-500 focus:ring-red-500'
                                : ''
                        "
                    />
                    <p v-if="errors.startTime" class="text-sm text-red-600">
                        {{ errors.startTime }}
                    </p>
                </div>
            </div>

            <div
                class="mt-8 flex items-center justify-end border-t border-gray-100 pt-6 dark:border-gray-700/50"
            >
                <Button @click="handleCancel" variant="ghost" class="mr-3">
                    Cancel
                </Button>
                <Button
                    @click="handleAssignTeacher"
                    variant="primary"
                    className="transition duration-300 hover:scale-105"
                >
                    <PlusCircle class="mr-2 h-4 w-4" />
                    Assign Schedule
                </Button>
            </div>
        </div>
    </div>
</template>
