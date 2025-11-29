<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import SubjectAssignment from './SubjectAssignment.vue';
import StudentsTable from './StudentsTable.vue';
// ... other imports
</script>

<template>
    <AppLayout> 
        <SubjectAssignment />
        <hr class="my-8 border-gray-200" /> 
        <StudentsTable />
    </AppLayout>
</template>