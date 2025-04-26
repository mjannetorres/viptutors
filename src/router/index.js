import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import RegisterView from "../views/RegisterView.vue";
import LoginView from "../views/LoginView.vue";
import AdminDashboard from "../views/AdminDashboard.vue";

import { useUserStore } from "../stores/user";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
    },
    {
      path: "/register",
      name: "Register",
      component: RegisterView,
    },
    {
      path: "/login",
      name: "Login",
      component: LoginView,
    },
    {
      path: "/admin",
      name: "AdminDashboard",
      component: AdminDashboard,
    },
  ],
});

// 🛡️ Navigation guard to protect routes
router.beforeEach((to, from, next) => {
  const userStore = useUserStore();

  // Routes that are public and do not require authentication
  const publicPages = ["/login", "/register"];
  const authRequired = !publicPages.includes(to.path);
  const isLoggedIn = !!userStore.token;
  const isAdmin = userStore.user?.is_admin;

  // If trying to access a protected route and not logged in, redirect to login
  if (authRequired && !userStore.token) {
    return next("/login");
  }

  if (isLoggedIn && publicPages.includes(to.path)) {
    // 🚫 Logged in and trying to access login/register page
    return next("/");
  }

  // Otherwise, allow navigation
  next();
});

export default router;
