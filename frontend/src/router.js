import {createRouter, createWebHistory} from 'vue-router';
import AnalyticsView from "./views/AnalyticsView.vue";
import BalanceView from "./views/BalanceView.vue";
import GroupsView from "./views/GroupsView.vue";
import LoginView from "./views/LoginView.vue";
import PanelView from "./views/PanelView.vue";
import PostsView from "./views/PostsView.vue";
import SecurityView from "./views/SecurityView.vue";
import NotificationView from "./views/NotificationView.vue";
import AdminView from "./views/admin/adminView.vue";
import AdminLoginView from "./views/admin/adminLoginView.vue";

const routes = [
    {
        path: "/",
        component: LoginView,
    },
    {
        path: "/panel",
        component: PanelView,
    },
    {
        path: "/posts",
        component: PostsView,
    },
    {
        path: "/notifications",
        component: NotificationView,
    },
    {
        path: "/groups",
        component: GroupsView,
    },
    {
        path: "/analytics",
        component: AnalyticsView,
    },
    {
        path: "/security",
        component: SecurityView,
    },
    {
        path: "/balance",
        component: BalanceView,
    },
    {
        path: "/admin",
        component: AdminView,
    },
    {
        path: "/admin/login",
        component: AdminLoginView,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;