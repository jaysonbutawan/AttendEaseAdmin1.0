<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import StudentsTable from './StudentsTable.vue';
import SelectSubject from './SelectSubject.vue';
</script>

<template>
    <AppLayout> 
        <SelectSubject />
        <StudentsTable />
    </AppLayout>
</template>