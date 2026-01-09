<script setup lang="ts">
import { Clock, GraduationCap, MapPin, Pencil, ArrowRight, X, UserPlus, Trash2 } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import axios from 'axios'

type ClassSession = {
  session_id: number
  subject_name: string
  teacher_name: string
  room_name: string
  session_days: string[]
  start_time: string
  end_time: string
  session_status: string
}

type EnrolledStudent = {
  student_id: string
  firstname: string
  lastname: string
  email: string
  enrollment_status: string
  enrolled_at: string
}

type AvailableStudent = {
  student_id: string
  firstname: string
  lastname: string
  email: string
  full_name: string
}

const sessions = ref<ClassSession[]>([])
const selectedSession = ref<ClassSession | null>(null)
const enrolledStudents = ref<EnrolledStudent[]>([])
const availableStudents = ref<AvailableStudent[]>([])
const selectedStudents = ref<string[]>([])
const showModal = ref(false)
const showEnrollModal = ref(false)
const loadingStudents = ref(false)
const loadingAvailable = ref(false)
const enrolling = ref(false)

onMounted(async () => {
  const { data } = await axios.get('/class_sessions')
  sessions.value = data.sessions

   sessions.value.forEach(session => {
    if (session.teacher_name) {
      console.log(
        `Session ${session.session_id} → Teacher: ${session.teacher_name}`
      )
    }
  })
})

const openSessionDetails = async (session: ClassSession) => {
  selectedSession.value = session
  showModal.value = true
  loadingStudents.value = true
  
  try {
    const { data } = await axios.get(`/class_sessions/${session.session_id}/students`)
    enrolledStudents.value = data.students || []
  } catch (error) {
    console.error('Failed to fetch students:', error)
    enrolledStudents.value = []
  } finally {
    loadingStudents.value = false
  }
}

const closeModal = () => {
  showModal.value = false
  selectedSession.value = null
  enrolledStudents.value = []
}

const openEnrollModal = async () => {
  if (!selectedSession.value) return
  
  showEnrollModal.value = true
  loadingAvailable.value = true
  selectedStudents.value = []
  
  try {
    const { data } = await axios.get(`/class_sessions/${selectedSession.value.session_id}/available-students`)
    availableStudents.value = data.students || []
  } catch (error) {
    console.error('Failed to fetch available students:', error)
    availableStudents.value = []
  } finally {
    loadingAvailable.value = false
  }
}

const closeEnrollModal = () => {
  showEnrollModal.value = false
  selectedStudents.value = []
  availableStudents.value = []
}

const toggleStudentSelection = (studentId: string) => {
  const index = selectedStudents.value.indexOf(studentId)
  if (index > -1) {
    selectedStudents.value.splice(index, 1)
  } else {
    selectedStudents.value.push(studentId)
  }
}

const enrollSelectedStudents = async () => {
  if (!selectedSession.value || selectedStudents.value.length === 0) return
  
  enrolling.value = true
  
  try {
    await axios.post('/assign-students-to-sessions', {
      session_ids: [selectedSession.value.session_id],
      student_ids: selectedStudents.value
    })
    
    // Refresh enrolled students list
    const { data } = await axios.get(`/class_sessions/${selectedSession.value.session_id}/students`)
    enrolledStudents.value = data.students || []
    
    alert('Students enrolled successfully!')
    closeEnrollModal()
  } catch (error: any) {
    console.error('Failed to enroll students:', error)
    
    // Check if it's a schedule conflict error
    if (error?.response?.status === 422 && error?.response?.data?.conflicts) {
      const conflicts = error.response.data.conflicts
      let message = '⚠️ Schedule Conflicts Detected!\n\n'
      
      conflicts.forEach((conflict: any, index: number) => {
        message += `${index + 1}. ${conflict.student_name}:\n`
        message += `   Trying to assign: ${conflict.new_subject} (${conflict.new_days}, ${conflict.new_time})\n`
        message += `   Already enrolled: ${conflict.conflicting_subject} (${conflict.conflicting_days}, ${conflict.conflicting_time})\n\n`
      })
      
      message += 'Please resolve these conflicts before enrolling.'
      alert(message)
    } else {
      alert('Failed to enroll students. Please try again.')
    }
  } finally {
    enrolling.value = false
  }
}

const removeStudent = async (studentId: string) => {
  if (!selectedSession.value) return
  
  if (!confirm('Are you sure you want to remove this student from the session?')) return
  
  try {
    await axios.delete(`/class_sessions/${selectedSession.value.session_id}/students/${studentId}`)
    
    // Refresh enrolled students list
    enrolledStudents.value = enrolledStudents.value.filter(s => s.student_id !== studentId)
  } catch (error) {
    console.error('Failed to remove student:', error)
    alert('Failed to remove student. Please try again.')
  }
}

const formatDays = (days: string[] | string) => {
  let parsed: string[] = [];

  try {
    parsed = Array.isArray(days)
      ? days
      : JSON.parse(days);
  } catch {
    // fallback if parsing fails
    return String(days);
  }

  const DAY_ABBREVIATIONS: Record<string, string> = {
    monday: 'Mon',
    tuesday: 'Tue',
    wednesday: 'Wed',
    thursday: 'Thu',
    friday: 'Fri',
    saturday: 'Sat',
    sunday: 'Sun',
  };

  return parsed
    .map(d => DAY_ABBREVIATIONS[d.toLowerCase()] ?? d)
    .join(', ');
};
const formatStatus = (status: string) => {
  if (!status) return '';

  return status.toLowerCase() === 'pending'
    ? 'Not Active'
    : status;
};

const formatPHTime = (time: string) => {
  if (!time) return '';

  const [hours, minutes] = time.split(':').map(Number);

  const period = hours >= 12 ? 'PM' : 'AM';
  const hour12 = hours % 12 || 12;

  return `${hour12}:${minutes.toString().padStart(2, '0')} ${period}`;
};

const formatTime = (startTime: string, endTime: string) => {
  return `${formatPHTime(startTime)} - ${formatPHTime(endTime)}`;
};

const getEnrollmentStatusClass = (status: string) => {
  const baseClass = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium';
  switch (status?.toLowerCase()) {
    case 'enrolled':
      return `${baseClass} bg-green-100 text-green-800`;
    case 'pending':
      return `${baseClass} bg-yellow-100 text-yellow-800`;
    case 'dropped':
      return `${baseClass} bg-red-100 text-red-800`;
    default:
      return `${baseClass} bg-gray-100 text-gray-800`;
  }
};

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
};
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
    <div 
      v-for="session in sessions" 
      :key="session.session_id"
      class="bg-white rounded-[2rem] shadow-xl shadow-blue-100/50 w-full p-8 md:p-10 relative overflow-hidden transition duration-300 hover:scale-105"
    >
      
      <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-2 bg-indigo-50 px-4 py-1.5 rounded-full">
          <div class="w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
          <span class="text-indigo-600 text-xs font-bold tracking-wider uppercase">
            {{formatStatus(session.session_status) }}
          </span>
        </div>

        <span class="text-gray-400 text-sm font-medium">
          {{ formatDays(session.session_days) }}
        </span>
      </div>

      <div class="mb-8">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-4">
          {{ session.subject_name }}
        </h1>
        <div class="flex items-center gap-2 text-gray-400 font-semibold">
          <Clock class="w-5 h-5" />
          <span>{{ formatTime(session.start_time, session.end_time) }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-3xl">
          <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600">
            <GraduationCap class="w-6 h-6" />
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Teacher</p>
            <p class="text-slate-900 font-bold text-lg">{{ session.teacher_name ?? 'wawa' }}</p>
          </div>
        </div>

        <div class="flex items-center gap-4 p-4 border border-gray-100 rounded-3xl">
          <div class="bg-indigo-50 p-4 rounded-2xl text-indigo-600">
            <MapPin class="w-6 h-6" />
          </div>
          <div>
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Location</p>
            <p class="text-slate-900 font-bold text-lg">{{ session.room_name }}</p>
          </div>
        </div>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-6">
        <div class="flex items-center gap-3 w-full md:w-auto">
          <button class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-3.5 border border-gray-100 rounded-full font-bold text-slate-800 hover:bg-gray-50 transition duration-300 hover:scale-105">
            <Pencil class="w-4 h-4" />
            Edit
          </button>
          <button 
            @click="openSessionDetails(session)"
            class="flex-1 md:flex-none flex items-center justify-center gap-2 px-8 py-3.5 bg-indigo-600 text-white rounded-full font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition duration-300 hover:scale-105"
          >
            View Details
            <ArrowRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="sessions.length === 0" class="col-span-full text-gray-400 text-center py-8">
      No class sessions available
    </div>
  </div>

  <!-- Modal Overlay -->
  <transition name="fade">
    <div 
      v-if="showModal"
      class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="closeModal"
    >
      <transition name="slide-up">
        <div 
          v-if="showModal"
          class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col"
          @click.stop
        >
          <!-- Modal Header -->
          <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-8 py-6 flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-white">{{ selectedSession?.subject_name }}</h2>
              <p class="text-indigo-100 text-sm mt-1">Enrolled Students</p>
            </div>
            <button 
              @click="closeModal"
              class="p-2 hover:bg-indigo-500 rounded-lg transition"
            >
              <X class="w-6 h-6 text-white" />
            </button>
          </div>

          <!-- Modal Body -->
          <div class="overflow-y-auto flex-1 p-8">
            <!-- Session Info -->
            <div v-if="selectedSession" class="mb-8 p-6 bg-gray-50 rounded-xl">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <p class="text-xs text-gray-500 font-semibold uppercase">Teacher</p>
                  <p class="text-sm font-bold text-gray-900 mt-1">{{ selectedSession.teacher_name }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500 font-semibold uppercase">Room</p>
                  <p class="text-sm font-bold text-gray-900 mt-1">{{ selectedSession.room_name }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500 font-semibold uppercase">Time</p>
                  <p class="text-sm font-bold text-gray-900 mt-1">{{ formatTime(selectedSession.start_time, selectedSession.end_time) }}</p>
                </div>
                <div>
                  <p class="text-xs text-gray-500 font-semibold uppercase">Days</p>
                  <p class="text-sm font-bold text-gray-900 mt-1">{{ formatDays(selectedSession.session_days) }}</p>
                </div>
              </div>
            </div>

            <!-- Students List -->
            <div>
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Students</h3>
                <div class="flex items-center gap-3">
                  <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-semibold">
                    {{ enrolledStudents.length }}
                  </span>
                  <button
                    @click="openEnrollModal"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold"
                  >
                    <UserPlus class="w-4 h-4" />
                    Add Students
                  </button>
                </div>
              </div>

              <div v-if="loadingStudents" class="flex items-center justify-center py-8">
                <div class="animate-spin">
                  <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                </div>
              </div>

              <div v-else-if="enrolledStudents.length === 0" class="text-center py-8">
                <p class="text-gray-500">No students enrolled in this session</p>
              </div>

              <div v-else class="space-y-3">
                <div 
                  v-for="student in enrolledStudents"
                  :key="student.student_id"
                  class="p-4 border border-gray-200 rounded-lg hover:border-indigo-300 transition group"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                          {{ student.firstname.charAt(0) }}{{ student.lastname.charAt(0) }}
                        </div>
                        <div>
                          <p class="font-semibold text-gray-900">
                            {{ student.firstname }} {{ student.lastname }}
                          </p>
                          <p class="text-xs text-gray-500">{{ student.student_id }}</p>
                        </div>
                      </div>
                      <p class="text-sm text-gray-600 ml-13">{{ student.email }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                      <div class="flex flex-col items-end gap-2">
                        <span :class="getEnrollmentStatusClass(student.enrollment_status)">
                          {{ student.enrollment_status || 'Enrolled' }}
                        </span>
                        <p class="text-xs text-gray-500">
                          {{ formatDate(student.enrolled_at) }}
                        </p>
                      </div>
                      <button
                        @click="removeStudent(student.student_id)"
                        class="opacity-0 group-hover:opacity-100 p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                        title="Remove student"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="border-t border-gray-200 px-8 py-4 flex justify-end gap-3 bg-gray-50">
            <button 
              @click="closeModal"
              class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition"
            >
              Close
            </button>
          </div>
        </div>
      </transition>
    </div>
  </transition>

  <!-- Enroll Students Modal -->
  <transition name="fade">
    <div 
      v-if="showEnrollModal"
      class="fixed inset-0 backdrop-blur-sm flex items-center justify-center z-50 p-4"
      @click="closeEnrollModal"
    >
      <transition name="slide-up">
        <div 
          v-if="showEnrollModal"
          class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col"
          @click.stop
        >
          <!-- Enroll Modal Header -->
          <div class="bg-gradient-to-r from-green-600 to-green-700 px-8 py-6 flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-white">Add Students</h2>
              <p class="text-green-100 text-sm mt-1">{{ selectedSession?.subject_name }}</p>
            </div>
            <button 
              @click="closeEnrollModal"
              class="p-2 hover:bg-green-500 rounded-lg transition"
            >
              <X class="w-6 h-6 text-white" />
            </button>
          </div>

          <!-- Enroll Modal Body -->
          <div class="overflow-y-auto flex-1 p-8">
            <div v-if="loadingAvailable" class="flex items-center justify-center py-8">
              <div class="animate-spin">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
              </div>
            </div>

            <div v-else-if="availableStudents.length === 0" class="text-center py-8">
              <p class="text-gray-500">No available students to enroll</p>
            </div>

            <div v-else>
              <p class="text-sm text-gray-600 mb-4">
                Select students to add to this session ({{ selectedStudents.length }} selected)
              </p>
              
              <div class="space-y-2">
                <div 
                  v-for="student in availableStudents"
                  :key="student.student_id"
                  @click="toggleStudentSelection(student.student_id)"
                  :class="[
                    'p-4 border-2 rounded-lg cursor-pointer transition',
                    selectedStudents.includes(student.student_id)
                      ? 'border-green-500 bg-green-50'
                      : 'border-gray-200 hover:border-green-300'
                  ]"
                >
                  <div class="flex items-center gap-3">
                    <div 
                      :class="[
                        'w-5 h-5 rounded border-2 flex items-center justify-center transition',
                        selectedStudents.includes(student.student_id)
                          ? 'border-green-600 bg-green-600'
                          : 'border-gray-300'
                      ]"
                    >
                      <svg 
                        v-if="selectedStudents.includes(student.student_id)"
                        class="w-3 h-3 text-white" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                      {{ student.firstname.charAt(0) }}{{ student.lastname.charAt(0) }}
                    </div>
                    <div class="flex-1">
                      <p class="font-semibold text-gray-900">{{ student.full_name }}</p>
                      <p class="text-xs text-gray-500">{{ student.student_id }} • {{ student.email }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Enroll Modal Footer -->
          <div class="border-t border-gray-200 px-8 py-4 flex justify-end gap-3 bg-gray-50">
            <button 
              @click="closeEnrollModal"
              class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-100 transition"
            >
              Cancel
            </button>
            <button 
              @click="enrollSelectedStudents"
              :disabled="selectedStudents.length === 0 || enrolling"
              class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <UserPlus v-if="!enrolling" class="w-4 h-4" />
              <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              {{ enrolling ? 'Enrolling...' : `Enroll ${selectedStudents.length} Student${selectedStudents.length !== 1 ? 's' : ''}` }}
            </button>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<style scoped>
button {
  -webkit-tap-highlight-color: transparent;
}

/* Fade transition for overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide up transition for modal */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(30px);
  opacity: 0;
}
</style>