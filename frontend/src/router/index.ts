import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";

const routes: readonly RouteRecordRaw[] = [
  {
    path: "/login",
    name: "login",
    component: () => import("@/views/LoginView.vue"),
    meta: { guestOnly: true },
  },
  {
    path: "/",
    component: AuthenticatedLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: "dashboard",
        name: "dashboard",
        component: () => import("@/views/DashboardView.vue"),
        meta: { admin: true },
      },
      {
        path: "folders/:id?",
        name: "folders",
        component: () => import("@/views/FolderView.vue"),
      },
      {
        path: "departments",
        name: "departments",
        component: () => import("@/views/DepartmentView.vue"),
        meta: { admin: true },
      },
    ],
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

router.beforeEach(async (to, _, next) => {
  const auth = useAuthStore();

  // Rehydrate user info
  if (auth.isAuthenticated && !auth.user) {
    try {
      await auth.fetchUser();
    } catch {
      //
    }
  }

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
