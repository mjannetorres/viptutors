import axios from "axios";
import { useUserStore } from "@/stores/user";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
});

export const registerUser = async (data) => {
  return await api.post("/register", data);
};

export const loginUser = async (data) => {
  return await api.post("/login", data);
};

export const logoutUser = async () => {
  const authStore = useUserStore();

  api.defaults.headers.common["Authorization"] = `Bearer ${authStore.token}`;
  return await api.post("/logout");
};
