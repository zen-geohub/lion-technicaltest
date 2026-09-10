import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const routes: readonly RouteRecordRaw[] = [
  {
    path: "/login",
    name: "login",
    component: () => import("@/views/LoginView.vue"),
    meta: { guestOnly: true },
  },
  {
    path: "/",
    name: "dashboard",
    component: () => import("@/views/DashboardView.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/folders/:id?",
    name: "folders",
    component: () => import("@/views/FolderView.vue"),
    meta: { requiresAuth: true },
  },
  {
    path: "/departments",
    name: "departments",
    component: () => {},
    meta: { requiresAuth: true, admin: true },
  },
  {
    path: "/:catchAll(.*)",
    name: "not-found",
    component: () => {},
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

router.beforeEach((to, _, next) => {
  const auth = useAuthStore();

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return next({ name: "folders" });
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: "login" });
  }

  if (to.meta.admin && !auth.isAdmin) {
    return next({ name: "folders" });
  }

  next();
});

export default router;
