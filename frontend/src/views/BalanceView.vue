<script>
import PanelNavComponent from "../components/PanelNavComponent.vue";

export default {
    name: "BalanceView.vue",
    components: {PanelNavComponent},
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
        <div class="nav_main_title">Баланс</div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Премиум функции</div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>Доступ к выбору даты публикации</div>
                </div>
            </div>
            <button v-if="!this.user.subscription">Купить за 500₽ навсегда</button>
            <button style="background-color:gray !important; cursor: not-allowed" disabled v-else>Подписка уже активна</button>
        </div>
    </panel-nav-component>
</template>

<style scoped>

</style>