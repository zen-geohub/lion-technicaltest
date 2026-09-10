import api from "@/lib/axios";
import type { User } from "@/types";
import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: (): {
    user: User | null;
    token: string | null;
  } => ({
    user: null,
    token: localStorage.getItem("token") || null,
  }),

  getters: {
    isAuthenticated: (state): boolean => !!state.token,
    isAdmin: (state): boolean => state.user?.role === "admin",
  },

  actions: {
    async login(email: string, password: string): Promise<void> {
      const { data } = await api.post("/login", { email, password });
      this.token = data.token;
      this.user = data.user;

      localStorage.setItem("token", data.token);
    },

    async logout(): Promise<void> {
      await api.post("/logout");
      this.token = null;
      this.user = null;

      localStorage.removeItem("token");
    },

    async fetchUser(): Promise<void> {
      const { data } = await api.get("/me");
      this.user = data;
    },
  },
});
