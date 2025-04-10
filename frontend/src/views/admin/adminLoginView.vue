<script>
export default {
    name: "adminLoginView",
    data () {
        return {
            username: '',
            password: '',
        }
    },
    mounted () {
        document.body.classList.add("no-scroll");
    },
    methods: {
        login() {
            fetch (this.backend + 'admin/login', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    "login": this.username,
                    "password": this.password,
                }),
                credentials: 'include',
            }).then((response) => {
                if (response.status === 403) return alert ("Неправильный логин или пароль");
                else if (response.ok) this.$router.push("/admin");
                else alert ("Произошла непредвиденная ошибка. Обратитесь к разработчику");
            })
        }
    },
    computed: {
        backend () {
            return this.$store.state.backend;
        }
    }
}
</script>

<template>
    <div class="adminLogin">
        <div class="adminLogin_main">
            <div class="adminLogin_main_site">
                <img src="/telegram.svg" alt="">
            </div>
            <div class="adminLogin_main_title">
                <h1>С возвращением!</h1>
                <p>Пожалуйста, введите свои учетные данные, чтобы получить доступ к панели администратора и управления контентом веб-сайта.</p>
            </div>
            <form class="adminLogin_main_form" @submit.prevent="login">
                <input required v-model="username" type="text" placeholder="Username">
                <input required v-model="password" type="password" placeholder="Password">
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</template>

<style scoped>
</style>