<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <form
      @submit.prevent="handleLogin"
      class="bg-white p-6 rounded shadow-md w-full max-w-sm"
    >
      <h2 class="text-xl font-bold mb-4">Login</h2>

      <input
        v-model="email"
        type="email"
        placeholder="Email"
        class="input"
        required
      />
      <input
        v-model="password"
        type="password"
        placeholder="Password"
        class="input"
        required
      />
      <p v-show="message" class="text-red-500">{{ message }}</p>

      <button
        type="submit"
        class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 mt-4"
      >
        Login
      </button>
    </form>
  </div>
</template>

<script>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "../stores/user";

export default {
  setup() {
    const name = ref("");
    const email = ref("");
    const password = ref("");
    const router = useRouter();
    const userStore = useUserStore();
    const message = ref("");

    const handleLogin = async () => {
      await userStore.login(email.value, password.value);
      message.value = userStore.error;
      if (!userStore.error) {
        if (userStore.user.is_admin) {
          router.push("/admin");
        } else {
          router.push("/");
        }
      }
    };

    return {
      name,
      email,
      password,
      message,
      handleLogin,
      userStore,
    };
  },
};
</script>

<style scoped>
.input {
  @apply w-full border border-gray-300 rounded p-2 mb-3;
}
</style>
