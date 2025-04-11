<script>
import axios from "axios";
export default {
    name: "LoginView.vue",
    async mounted() {
        document.body.style.overflow = "hidden";
        axios.defaults.withCredentials = true;

        document.body.style.backgroundColor = "#F9F6F3";
        let backend = this.backend;
        window.onTelegramAuth = async function (user) {
            await fetch (backend + "auth/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(user),
                credentials: 'include'
            })
                .then((response) => {
                    if (!response.ok) return alert ("Error");
                    location.href = "/panel";
                })
        }
        const script = document.createElement('script');
        script.async = true;
        script.src = 'https://telegram.org/js/telegram-widget.js?22';
        script.setAttribute('data-telegram-login', 'AutoPostingOfficialBot');
        script.setAttribute('data-size', 'large');
        script.setAttribute('data-onauth', 'onTelegramAuth(user)');
        script.setAttribute('data-request-access', 'write');
        document.getElementById('telegram-container').appendChild(script);
    },
    methods: {
    },
    computed: {
        backend () {
            return this.$store.state.backend;
        }
    }
}
</script>

<template>
    <div class="telegram-container_wrapper">
        <div id="telegram-container"></div>
    </div>
</template>

<style scoped>

</style>