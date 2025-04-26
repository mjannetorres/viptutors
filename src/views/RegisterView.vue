<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <form
      @submit.prevent="handleRegister"
      class="bg-white p-6 rounded shadow-md w-full max-w-sm"
    >
      <h2 class="text-xl font-bold mb-4">Register</h2>

      <input
        v-model="name"
        type="text"
        placeholder="Name"
        class="input"
        required
      />
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
      <input
        v-model="passwordConfirmation"
        type="password"
        placeholder="Password Confirmation"
        class="input"
        required
      />

      <p v-show="message" class="text-red-500">{{ message }}</p>

      <button
        type="submit"
        class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 mt-4"
      >
        Register
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
    const passwordConfirmation = ref("");
    const router = useRouter();
    const userStore = useUserStore();
    const message = ref("");

    const handleRegister = async () => {
      if (!checkPasswordMatched()) {
        message.value = "Password mismatched!";
        return;
      }

      await userStore.register(
        name.value,
        email.value,
        password.value,
        passwordConfirmation.value
      );
      message.value = userStore.error;
      if (!userStore.error) {
        router.push("/");
      }
    };

    const checkPasswordMatched = () => {
      return password.value === passwordConfirmation.value;
    };

    return {
      name,
      email,
      password,
      passwordConfirmation,
      message,
      handleRegister,
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
