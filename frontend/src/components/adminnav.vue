<script>
export default {
    data() {
        return {
            admin: {},
        }
    },
    async mounted() {
        // document.body.style.backgroundColor = "#14141e"
        // document.querySelectorAll(".adminnav_main_nav_main>div>p").forEach((el)=>{
        //     el.addEventListener("mouseenter", (ev) => ev.stopPropagation());
        // })

        let nav = document.querySelector(".adminnav_main_nav");
        nav.addEventListener("mouseenter", (ev) => {
            nav.classList.add("active");
        });

        nav.addEventListener("mouseleave", () => {
            nav.classList.remove("active");
        });

        nav.style.width = nav.clientWidth + 'px';

        let accountmenu = document.querySelector(".adminnav_buttons_account_menu");
        document.addEventListener('click', (event) => {
            if (!accountmenu.parentElement.contains(event.target) && accountmenu.classList.contains("active")) {
                accountmenu.classList.remove("active");
            }
        });
        document.body.style.backgroundColor = "#12121c";

        await fetch (this.backend + "admin/profile", {
            method: "GET",
            credentials: "include",
        }).then((response) => {
            if (response.status === 401) return this.$router.push("/admin/login");
            return response.json();
        }).then((response) => {
            this.admin = response;
            console.log(response);
        });
    },
    methods: {
        showaccount () {
            document.querySelector(".adminnav_buttons_account_menu").classList.toggle("active");
        },
        showmenu () {
            document.querySelector(".adminnav_main_nav").classList.toggle("active");
        },
        async logout () {
            await fetch (this.backend + "admin/logout", {
                method: "POST",
                credentials: "include",
            }).then((response) => {
                if (!response.ok) return alert ("Error");
                this.$router.push("/");
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
    <div class="notifyContainer"></div>
    <div class="loadPopup">
        <p>Loading...</p>
    </div>
<div class="adminnav">
    <div class="adminnav_main">
        <div class="adminnav_main_main">
            <slot></slot>
        </div>
    </div>
</div>
</template>

<style scoped>

</style>