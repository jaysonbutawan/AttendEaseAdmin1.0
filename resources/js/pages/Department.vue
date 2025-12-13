<script lang="ts">
import axios from 'axios';
import { computed, defineComponent, onMounted, ref } from 'vue';

interface Department {
    course_id?: number;
    course_name: string;
}

interface Header {
    key: string;
    label: string;
}

export default defineComponent({
    name: 'DepartmentManager',
    setup() {
        const headers: Header[] = [
            { key: 'departmentName', label: 'Department Name' },
            { key: 'actions', label: 'Actions' },
        ];

        const departments = ref<Department[]>([]);
        const searchTerm = ref('');
        const newDepartmentName = ref('');

        const fetchDepartments = async () => {
            try {
                const response = await axios.get('/courses');
                console.log('API Response:', response.data);
                departments.value = response.data.courses;
            } catch (error) {
                console.error('Failed to fetch departments:', error);
            }
        };

        const addDepartment = async (name: string) => {
            if (name.trim()) {
                try {
                    console.log('Adding new department with name:', name);
                    const newDepartment: Department = { course_name: name };
                    const response = await axios.post(
                        '/courses',
                        newDepartment,
                    );
                    console.log('Response from API:', response.data);
                    departments.value.push(response.data.course);
                    newDepartmentName.value = '';
                } catch (error) {
                    console.error('Failed to add department:', error);
                }
            }
        };

        const removeDepartment = async (id: number | undefined) => {
            if (!id) {
                console.error('Invalid department ID:', id); // If ID is undefined or invalid, log an error
                return;
            }

            console.log('Attempting to delete course with ID:', id); // Debugging log
            try {
                await axios.delete(`/courses/${id}`);
                console.log('Course deleted successfully'); // Log success
                departments.value = departments.value.filter(
                    (dept) => dept.course_id !== id,
                );
            } catch (error) {
                console.error('Failed to remove department:', error);
            }
        };

        const editDepartment = async (id: number, newName: string) => {
            const department = departments.value.find(
                (dept) => dept.course_id === id,
            );
            if (department) {
                try {
                    department.course_name = newName;
                    await axios.put(`/courses/${id}`, department);
                } catch (error) {
                    console.error('Failed to update department:', error);
                }
            }
        };

        const filteredDepartments = computed(() => {
            return departments.value.filter((dept) =>
                dept.course_name
                    .toLowerCase()
                    .includes(searchTerm.value.toLowerCase()),
            );
        });

        onMounted(fetchDepartments);

        return {
            headers,
            departments,
            searchTerm,
            newDepartmentName,
            addDepartment,
            removeDepartment,
            editDepartment,
            filteredDepartments,
        };
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="mx-auto max-w-6xl rounded-xl bg-white shadow-lg">
            <div class="flex flex-col gap-4 border-b p-6 sm:flex-row">
                <div class="relative flex-grow">
                    <input
                        type="text"
                        placeholder="Search departments..."
                        class="w-full rounded-lg border border-gray-300 py-2 pr-4 pl-10 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                    />
                    <svg
                        class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 transform text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        ></path>
                    </svg>
                </div>
                <input
                    type="text"
                    placeholder="Enter new department name..."
                    class="flex-grow rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none sm:w-64 sm:flex-none"
                    v-model="newDepartmentName"
                />
                <button
                    @click="addDepartment(newDepartmentName)"
                    class="flex items-center justify-center gap-1 rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white shadow-md transition duration-150 hover:bg-blue-700"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                        ></path>
                    </svg>
                    Save Department
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th
                                v-for="header in headers"
                                :key="header.key"
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase"
                                :class="
                                    header.key === 'actions' ? 'text-right' : ''
                                "
                            >
                                {{ header.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        <tr v-for="dept in departments" :key="dept.course_id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ dept.course_name }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td
                                class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap"
                            >
                                <div class="flex justify-end space-x-3">
                                    <button
                                        class="text-gray-400 transition duration-150 hover:text-blue-600"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                            ></path>
                                        </svg>
                                    </button>

                                    <button
                                        @click="
                                            () => {
                                                console.log(dept.course_id);
                                                removeDepartment(
                                                    dept.course_id,
                                                );
                                            }
                                        "
                                        class="text-gray-400 transition duration-150 hover:text-red-600"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex items-center justify-between border-t border-gray-100 p-6"
            >
                <div class="text-sm text-gray-500">
                    Showing <span class="font-semibold">4</span> of
                    <span class="font-semibold">12</span> departments
                </div>
                <div class="flex items-center space-x-1">
                    <button
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-500 hover:bg-gray-50"
                    >
                        Prev
                    </button>
                    <button
                        class="rounded-lg bg-blue-600 px-3 py-1 text-sm font-bold text-white shadow-md"
                    >
                        1
                    </button>
                    <button
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50"
                    >
                        2
                    </button>
                    <button
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50"
                    >
                        3
                    </button>
                    <button
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-500 hover:bg-gray-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
