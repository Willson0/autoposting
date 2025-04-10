<script>
import PanelNavComponent from "../components/PanelNavComponent.vue";

export default {
    name: "ScheduleView.vue",
    components: {PanelNavComponent},
    data () {
        return {
        }
    },
    methods: {
        formatDate(input) {
            const months = ['янв', 'фев', 'мар', 'апр', 'май', 'июн',
                'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'];

            const date = new Date(input); // превращаем в ISO-совместимый формат
            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${day} ${month} ${year} года в ${hours}:${minutes}`;
        }
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
    <panel-nav-component>
        <div class="nav_main_title">Уведомления</div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Последние уведомления</div>
            <div class="notification_block">
                <div :class="note.status" v-for="note in user?.notifications">
                    <div class="notification_block_icon">
                        <i v-if="note.status === 'success'" style="color: #4caf50" class="fa-solid fa-circle-check"></i>
                        <i v-else-if="note.status === 'error'" style="color:#f44336" class="fa-solid fa-triangle-exclamation"></i>
                        <i v-else-if="note.status === 'note'" style="color: orange" class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <div class="notification_block_text_container">
                        <div class="notification_block_text">{{note.text}}</div>
                        <div class="notification_block_date">{{formatDate(note.created_at)}}</div>
                    </div>
                </div>
            </div>
        </div>
    </panel-nav-component>
</template>

<style scoped>

</style>