import { defineStore } from "pinia";
import { registerUser, loginUser, logoutUser } from "../api/auth";

export const useUserStore = defineStore("user", {
  state: () => ({
    token: localStorage.getItem("token") || null,
    user: null,
    error: null,
  }),
  actions: {
    async register(name, email, password, passwordConfirmation) {
      try {
        const response = await registerUser({
          name,
          email,
          password,
          password_confirmation: passwordConfirmation,
        });
        this.user = response.data.user || response.data;
        this.token = response.data.token;

        localStorage.setItem("user", JSON.stringify(this.user));
        localStorage.setItem("token", this.token);

        this.error = null;
        console.log("User registered:", this.user);
      } catch (error) {
        this.error = error.response?.data?.message || "Registration failed";
        console.error("Register error:", this.error);
      }
    },
    async login(email, password) {
      try {
        const response = await loginUser({
          email,
          password,
        });
        this.user = response.data.user || response.data;
        this.token = response.data.token;

        localStorage.setItem("user", JSON.stringify(this.user));
        localStorage.setItem("token", this.token);

        this.error = null;
        console.log("User logged in:", this.user);
      } catch (error) {
        console.log(error);
        this.error = error.response?.data?.message || "Login failed";
        console.error("Login error:", this.error);
      }
    },
    async logout() {
      try {
        await logoutUser();
      } catch (error) {
        console.log(error);
      } finally {
        this.user = null;
        this.token = null;
        this.error = null;
        localStorage.removeItem("user");
        localStorage.removeItem("token");
      }
    },
  },
  persist: true,
});
