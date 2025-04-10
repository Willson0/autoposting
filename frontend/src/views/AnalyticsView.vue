<script>
import PanelNavComponent from "../components/PanelNavComponent.vue";

export default {
    name: "AnalyticsView.vue",
    components: {PanelNavComponent}
}
</script>

<template>
    <panel-nav-component>
        <div class="nav_main_title">Аналитика</div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Настройки бота</div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>Токен бота</div>
                    <input type="text" placeholder="Введите токен бота">
                </div>
            </div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>API ID</div>
                    <input type="text" placeholder="Введите API ID">
                </div>
                <div>
                    <div>API Hash</div>
                    <input type="text" placeholder="Введите API Hash">
                </div>
            </div>
        </div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Подтверждение номера</div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>Номер телефона</div>
                    <input type="text" placeholder="+7XXXXXXXXXX">
                </div>
            </div>
            <button>Отправить код подтверждения</button>
        </div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Управление группами</div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>ID группы</div>
                    <input type="text" placeholder="Введите ID группы">
                </div>
            </div>
            <button>Добавить группу</button>
        </div>
    </panel-nav-component>
</template>

<style scoped>

</style>