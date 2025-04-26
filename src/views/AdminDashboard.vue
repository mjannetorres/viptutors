<template>
  <div class="min-h-screen bg-gray-50 p-4">
    <div class="flex justify-end mt-4">
      <button
        @click="handleLogout"
        class="px-2 py-2 bg-slate-400 text-white text-sm rounded hover:bg-slate-600"
      >
        Log Out
      </button>
    </div>

    <h1 class="text-2xl font-bold text-center mt-10 mb-6">
      📝 {{ user }}'s Dashboard (Drag to reorder)
    </h1>

    <div class="max-w-5xl mx-auto space-y-4">
      <div class="flex items-center justify-between mb-4 gap-4 flex-wrap">
        <!-- Search Bar (left) -->
        <input
          @input="handleSearchTasks"
          v-model="searchQuery"
          type="text"
          placeholder="Search tasks..."
          class="w-full p-2 border border-gray-300 rounded sm:w-1/3"
        />

        <!-- Filters (right) -->
        <div class="flex items-center gap-2">
          <div class="flex items-center">
            <span class="mr-2 text-sm">Priority</span>
            <select
              @change="handleSearchTasks"
              v-model="priorityFilter"
              class="text-sm p-2 border border-gray-300 rounded"
            >
              <option value="all">All</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <div class="flex items-center">
            <span class="mr-2 text-sm">Status</span>
            <select
              @change="handleSearchTasks"
              v-model="statusFilter"
              class="text-sm p-2 border border-gray-300 rounded"
            >
              <option value="all">All</option>
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
            </select>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col border-dotted p-4 rounded-lg border border-1 border-gray-300"
      >
        <!-- Stats -->
        <div class="flex gap-2">
          <div
            class="w-1/5 items-center justify-between shadow-sm rounded-lg p-4 bg-yellow-100"
          >
            Pending : {{ pending }}
          </div>
          <div
            class="w-1/5 items-center justify-between shadow-sm rounded-lg p-4 bg-green-100"
          >
            Completed : {{ completed }}
          </div>
        </div>
        <!-- Task List with Drag-and-Drop -->
        <div class="mt-4">
          <draggable
            v-model="tasks"
            item-key="id"
            class="space-y-3"
            ghost-class="bg-blue-100"
            animation="200"
            @change="onDragChange"
          >
            <template #item="{ element: task }">
              <div
                :class="{
                  'bg-red-200 ': task.priority === 'high',
                  'bg-yellow-200 ': task.priority === 'medium',
                  'bg-green-200 ': task.priority === 'low',
                }"
                class="flex items-center justify-between shadow-sm rounded-lg p-4 hover:shadow-md transition-all cursor-move"
              >
                <div>
                  <p class="font-semibold">{{ task.user.name }}</p>
                  <p
                    :class="{
                      'line-through text-gray-400': task.status === 'completed',
                    }"
                  >
                    {{ task.title }}
                  </p>
                  <span
                    :class="{
                      'line-through text-gray-400': task.status === 'completed',
                    }"
                    class="text-xs"
                  >
                    {{ task.description }}
                  </span>
                </div>
                <button
                  @click="handleDelete(task.id)"
                  class="bg-red-500 text-sm px-3 py-1 rounded text-white"
                >
                  Delete
                </button>
              </div>
            </template>
          </draggable>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { debounce } from "lodash";
import { ref, onMounted, watchEffect, computed } from "vue";
import { useTasks } from "../api/tasks";
import { useRouter } from "vue-router";
import { useUserStore } from "@/stores/user";
import draggable from "vuedraggable";

export default {
  components: { draggable },
  setup() {
    const tasks = ref([]);
    const newTask = ref("");
    const newPriority = ref("medium");
    const newDescription = ref("");
    const searchQuery = ref("");
    const statusFilter = ref("all");
    const priorityFilter = ref("all");
    const useUser = useUserStore();
    const router = useRouter();
    const pending = ref("");
    const completed = ref("");

    const {
      getAllTasksApi,
      getFilteredTasksApi,
      addTaskApi,
      deleteTask,
      changeOrder,
      getStats,
    } = useTasks();

    const addNewTask = async () => {
      const res = await addTaskApi(
        newTask.value,
        newDescription.value,
        newPriority.value
      );
      tasks.value.push(res.data[0]);
      newTask.value = "";
      newDescription.value = "";
      newPriority.value = "medium";
    };

    const getTaskList = async () => {
      const res = await getAllTasksApi();
      tasks.value = res.data;
    };

    const getStatistics = async () => {
      const res = await getStats();
      pending.value = res.data.pending_tasks_count;
      completed.value = res.data.completed_tasks_count;
    };

    const fetchTasks = async () => {
      const res = await getFilteredTasksApi(
        searchQuery.value,
        priorityFilter.value === "all"
          ? null
          : priorityFilter.value.toLowerCase(),
        statusFilter.value === "all" ? null : statusFilter.value.toLowerCase()
      );
      tasks.value = res.data;
    };

    // Debounced version
    const handleSearchTasks = debounce(fetchTasks, 500);

    const handleDelete = async (id) => {
      await deleteTask(id);
      await handleSearchTasks();
    };

    const onDragChange = async (event) => {
      const { newIndex } = event.moved || {};

      if (newIndex !== undefined) {
        const task = tasks.value[newIndex]?.id;
        const newTaskOrder = newIndex + 1;

        await handleChangeOrder(task, newTaskOrder);
      } else {
        console.log("No movement detected");
      }
    };

    const handleChangeOrder = async (taskId, order) => {
      await changeOrder(taskId, order);
    };

    const handleLogout = async () => {
      await useUser.logout();
      if (!useUser.error) {
        router.push("/login");
      }
    };

    onMounted(() => {
      getTaskList();
      getStatistics();
    });

    const user = computed(() => useUser.user.name);

    // Watch for changes in searchQuery
    watchEffect(() => {
      handleSearchTasks();
    });

    return {
      tasks,
      newTask,
      newPriority,
      newDescription,
      priorityFilter,
      statusFilter,
      searchQuery,
      addNewTask,
      handleLogout,
      handleDelete,
      onDragChange,
      handleSearchTasks,
      user,
      pending,
      completed,
    };
  },
};
</script>
