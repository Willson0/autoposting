<script>
import axios from 'axios';
export default {
    name: "PanelNavComponent.vue",
    data () {
        return {
            buttons: {
                "/panel": ["Панель управления", "fa-solid fa-table-columns"],
                "/posts": ["Мои посты", "fa-solid fa-book"],
                "/notifications": ["Уведомления", "fa-solid fa-bell"],
                "/balance": ["Баланс", "fa-solid fa-piggy-bank"]
            }
        }
    },
    async mounted () {
        axios.defaults.withCredentials = true;
        await this.fetchUser();
    },
    methods: {
        async fetchUser() {
            await axios.post(this.backend + "auth/profile")
                .then((response) => {
                    this.$store.dispatch("updateUser", response.data);
                }).catch((response) => {
                    this.$router.push("/");
                });
        },
    },
    computed: {
        user () {
            return this.$store.state.user;
        },
        backend () {
            return this.$store.state.backend;
        }
    }
}
</script>

<template>
    <div class="nav">
        <div class="nav_nav">
            <div class="nav_nav_title">Добро пожалователь, {{ user.username }}</div>
            <hr>
            <div class="nav_nav_buttons">
                <div :class="$route.path === link ? 'active' : ''" @click="$router.push(link)" v-for="(name, link) in buttons">
                    <i :class="name[1]"></i>
                    <div>{{ name[0] }}</div>
                </div>
            </div>
        </div>
        <div class="nav_main">
            <slot></slot>
        </div>
    </div>
</template>

<style scoped>

</style>