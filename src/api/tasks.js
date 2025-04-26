import axios from "axios";
import { useUserStore } from "@/stores/user";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
});

export const useTasks = () => {
  const authStore = useUserStore();

  const addTaskApi = async (
    title,
    description,
    priority,
    status = "pending"
  ) => {
    if (title.trim() === "") return;
    const data = { title, description, priority, status };

    api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
    return await api.post("/tasks/store", data);
  };

  const getAllTasksApi = async () => {
    api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
    return await api.get("/tasks");
  };

  const getFilteredTasksApi = async (query, priority, status) => {
    api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
    return await api.get("/tasks/search", {
      params: { query, priority, status },
    });
  };

  const updateTask = async (id, status) => {
    api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
    const data = { status };
    return await api.put("/tasks/update/" + id, data);
  };

  const changeOrder = async (task_id, order) => {
    api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
    const data = { task_id, order };
    return await api.put("/tasks/reorder/", data);
  };

  const deleteTask = async (id) => {
    api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
    return await api.delete("/tasks/delete/" + id);
  };

  const getStats = async () => {
    api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
    return await api.get("/admin/dashboard/");
  };

  return {
    addTaskApi,
    getAllTasksApi,
    updateTask,
    changeOrder,
    getFilteredTasksApi,
    deleteTask,
    getStats,
  };
};
