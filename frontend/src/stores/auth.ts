import api from "@/lib/axios";
import { defineStore } from "pinia";

interface User {
  role: "admin" | "viewer";
}

export const useAuthStore = defineStore("auth", {
  state: (): {
    user: User | null;
    token: string | null;
  } => ({
    user: null,
    token: localStorage.getItem("token") || null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === "admin",
  },

  actions: {
    async login(email: string, password: string): Promise<boolean> {
      const { data } = await api.post("/login", { email, password });
      this.token = data.token;
      this.user = data.user;

      localStorage.setItem("token", data.token);
      return true;
    },

    async logout() {
      await api.post("/logout");
      this.token = null;
      this.user = null;

      localStorage.removeItem("token");
    },

    async fetchUser() {
      const { data } = await api.get("/me");
      this.user = data;
    },
  },
});
