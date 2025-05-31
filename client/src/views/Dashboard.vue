<template>
    <div class="p-6 bg-gray-100 min-h-screen">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Welcome, {{ auth.user?.name }}</h1>
            <button @click="logout" class="btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Logout</button>
        </div>

        <!-- Add New Task Form -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Add New Task</h2>
            <form @submit.prevent="addTask" class="space-y-4">
                <div v-if="addTaskError" class="text-red-500 text-sm">{{ addTaskError }}</div>
                <div>
                    <label for="newTaskTitle" class="block text-sm font-medium text-gray-700">Title</label>
                    <input v-model="newTaskTitle" type="text" id="newTaskTitle" placeholder="Task Title"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                </div>
                <div>
                    <label for="newTaskDescription" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea v-model="newTaskDescription" id="newTaskDescription" placeholder="Task Description"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    :disabled="addingTask">
                    {{ addingTask ? 'Adding...' : 'Add Task' }}
                </button>
            </form>
        </div>

        <div v-if="loadingTasks" class="text-center text-gray-600">Loading tasks...</div>
        <div v-else-if="fetchError" class="text-center text-red-500">Error fetching tasks: {{ fetchError }}</div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pending Tasks -->
            <div>
                <h2 class="text-xl font-semibold mb-3">Pending Tasks ({{ pendingTasks.length }})</h2>
                <draggable v-model="pendingTasks" group="tasks" item-key="id" class="space-y-3 min-h-[50px]">
                    <template #item="{ element, index }">
                        <div class="bg-white p-4 rounded shadow flex justify-between items-center cursor-move">
                            <span>{{ element.title }} ({{ element.owner }}) - {{ element.description }}</span>
                            <div class="flex items-center space-x-2">
                                <button @click="markComplete(element)"
                                    class="text-green-500 hover:text-green-700">✔</button>
                                <button @click="deleteTask(element.id)"
                                    class="text-red-500 hover:text-red-700">×</button>
                            </div>
                        </div>
                    </template>
                    <template #no-groud>No pending tasks.</template>
                </draggable>
            </div>

            <!-- Completed Tasks -->
            <div>
                <h2 class="text-xl font-semibold mb-3">Completed Tasks ({{ completedTasks.length }})</h2>
                <draggable v-model="completedTasks" group="tasks" item-key="id" class="space-y-3 min-h-[50px]">
                    <template #item="{ element, index }">
                        <div class="bg-white p-4 rounded shadow flex justify-between items-center cursor-move">
                            <span class="line-through text-gray-500">{{ element.title }} ({{ element.owner }}) - {{
                                element.description }}</span>
                            <div class="flex items-center space-x-2">
                                <button @click="undoTask(element)" class="text-blue-500 hover:text-blue-700">↩</button>
                                <button @click="deleteTask(element.id)"
                                    class="text-red-500 hover:text-red-700">×</button>
                            </div>
                        </div>
                    </template>
                    <template #no-groud>No completed tasks.</template>
                </draggable>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import draggable from 'vuedraggable'

const router = useRouter()
const auth = useAuthStore()

const pendingTasks = ref([])
const completedTasks = ref([])
const loadingTasks = ref(true)
const fetchError = ref(null)

const newTaskTitle = ref('');
const newTaskDescription = ref('');
const addingTask = ref(false);
const addTaskError = ref(null);

const fetchTasks = async () => {
    loadingTasks.value = true
    fetchError.value = null
    try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/tasks`, {
            headers: {
                'Authorization': `Bearer ${auth.token}`
            }
        })

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }

        const tasks = await response.json()

        // Filter tasks based on status
        pendingTasks.value = tasks.filter(task => task.status === 'pending');
        completedTasks.value = tasks.filter(task => task.status === 'completed');

    } catch (error) {
        console.error('Error fetching tasks:', error)
        fetchError.value = error.message
    } finally {
        loadingTasks.value = false
    }
}

// Function to add a new task via API
const addTask = async () => {
    if (!newTaskTitle.value) return; // Prevent adding empty tasks

    addingTask.value = true;
    addTaskError.value = null;

    try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/tasks`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${auth.token}`
            },
            body: JSON.stringify({
                title: newTaskTitle.value,
                description: newTaskDescription.value // Include description
            })
        });

        const newTask = await response.json();

        if (!response.ok) {
            throw new Error(newTask.message || `Failed to add task with status: ${response.status}`)
        }

        // Assuming API returns the created task including its ID and default status
        // Add the new task to the pending list
        pendingTasks.value.push(newTask);

        // Clear the form
        newTaskTitle.value = '';
        newTaskDescription.value = '';

    } catch (error) {
        console.error('Error adding task:', error);
        addTaskError.value = error.message;
    } finally {
        addingTask.value = false;
    }
};

onMounted(() => {
    fetchTasks()
})

// Note: These functions now ideally should call your API to update the backend

async function markComplete(task) {
    try {
        const taskIndex = pendingTasks.value.findIndex(_task => _task.id === task.id);
        if (taskIndex === -1) return console.error('Task not found');

        const response = await fetch(`${import.meta.env.VITE_API_URL}/tasks/${task.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${auth.token}`
            },
            body: JSON.stringify({ ...task, status: 'completed' })
        });

        const data = await response.json();
        console.log('Response data:', data);

        if (!response.ok) {
            throw new Error(data.message || 'Failed to update');
        }
        fetchTasks()
    } catch (error) {
        console.error('PUT failed:', error);
    }
}


async function undoTask(task) {
    try {
        const taskIndex = completedTasks.value.findIndex(_task => _task.id === task.id);
        if (taskIndex === -1) return console.error('Task not found');

        const response = await fetch(`${import.meta.env.VITE_API_URL}/tasks/${task.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${auth.token}`
            },
            body: JSON.stringify({ ...task, status: 'pending' })
        });

        const data = await response.json();
        console.log('Response data:', data);

        if (!response.ok) {
            throw new Error(data.message || 'Failed to update');
        }
        fetchTasks()
    } catch (error) {
        console.error('PUT failed:', error);
    }
}

async function deleteTask(taskId) {
    console.log(`Attempting to delete task with ID: ${taskId}`);
    try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${auth.token}`
            }
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Failed to delete task ${taskId} with status: ${response.status}`);
        }

        pendingTasks.value = pendingTasks.value.filter(task => task.id !== taskId);
        completedTasks.value = completedTasks.value.filter(task => task.id !== taskId);

        console.log(`Task ${taskId} deleted successfully.`);

    } catch (error) {
        console.error('Error deleting task:', error);
    }
}

function logout() {
    auth.logout()
    router.push('/login')
}
</script>
