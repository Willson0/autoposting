<script>
import PanelNavComponent from "../components/PanelNavComponent.vue";
import notify from "../utils.js";
import axios from 'axios'

export default {
    name: "PanelView.vue",
    components: {PanelNavComponent},
    data () {
        return {
            newGroupID: "",
            code: ["", "", "", "", ""],
            password: "",
        }
    },
    methods: {
        notify,
        showpopup (cl) {
            document.body.style.overflow = "hidden";
            document.querySelector('.popup.' + cl).classList.add('active');
        },
        hidepopup(cl) {
            document.querySelectorAll('.popup').forEach(el => el.classList.remove('active'));
            document.body.style.overflow = "";
        },
        removeGroup (id) {
            axios.delete(this.backend + "group/" + id)
                .then((response) => {
                    if (response.status) {
                        this.user.groups = this.user.groups.filter((el) => el.id !== id);
                        this.notify("Группа успешно удалена!");
                    }
                });
        },
        async addGroup () {
            document.querySelector("#newGroup").style.border = "";

            let id = parseFloat(this.newGroupID);
            if (isNaN(id)) {
                document.querySelector("#newGroup").style.border = "1px solid #f44336";
                return this.notify("Введите корректный айди группы!", 1);
            }
            id = Math.abs(id);
            if (this.user.groups.filter(el => el.group_id === id).length > 0) {
                document.querySelector("#newGroup").style.border = "1px solid #f44336";
                return this.notify("Данная группа уже добавлена!", 1);
            }

            await axios.post(this.backend + "group/", {
                "group": id,
            }).then((response) => {
                this.user.groups.push(response.data);
                this.notify("Новая группа успешно добавлена!");
            }).catch((error) => {
                this.notify(`Непредвиденная ошибка! ${error}`, 1);
            })
        },
        async saveToken () {
            let input = document.querySelector("#botToken");
            input.style.border = "";

            if (!/^\d{9,}:[\w-]{35}$/.test(this.user.bot_token) && this.user.bot_token.length !== 0) {
                input.style.border = "1px solid #f44336";
                return this.notify("Токен не соответствует формату!", 1);
            } else if (this.user.bot_token.length === 0) this.user.bot_token = null;

            await axios.post(this.backend + "auth/settings", {
                "bot_token": this.user.bot_token,
            }).then((response) => {
                this.notify("Токен бота успешно сохранен!");
            }).catch((error) => {
                this.notify(`Непредвиденная ошибка! ${error}`, 1);
            })
        },
        async saveID () {
            let input = document.querySelector("#apiID");
            input.style.border = "";

            if (this.user.api_id.length !== 0) {
                let id = parseFloat(this.user.api_id);
                if (isNaN(id) || id < 0) {
                    input.style.border = "1px solid #f44336";
                    return this.notify("API ID не соответствует формату!", 1);
                }
            } else this.user.api_id = null;

            await axios.post(this.backend + "auth/settings", {
                "api_id": this.user.api_id,
            }).then((response) => {
                this.notify("API ID успешно сохранено!");
            }).catch((error) => {
                this.notify(`Непредвиденная ошибка! ${error}`, 1);
            })
        },
        async saveHash () {
            let input = document.querySelector("#apiHash");
            input.style.border = "";

            if (this.user.api_hash.length === 0) this.user.api_hash = null
            else if (!/^[a-f0-9]{32}$/.test(this.user.api_hash)) {
                input.style.border = "1px solid #f44336";
                return this.notify("API HASH не соответствует формату!", 1);
            }

            await axios.post(this.backend + "auth/settings", {
                "api_hash": this.user.api_hash,
            }).then((response) => {
                this.notify("API HASH успешно сохранено!");
            }).catch((error) => {
                this.notify(`Непредвиденная ошибка! ${error}`, 1);
            })
        },
        async sendCode () {
            document.querySelector("#apiID").style.border = "";
            document.querySelector("#apiHash").style.border = "";

            let error = 0;
            if (!this.user.api_id) {
                document.querySelector("#apiID").style.border = "1px solid #f44336";
                error = 1;
            }
            if (!this.user.api_hash) {
                document.querySelector("#apiHash").style.border = "1px solid #f44336";
                error = 1;
            }
            if (error) return notify ("API ID и API HASH должны быть заполнены!", 1);

            await axios.post(this.backend + "auth/phone", {
                "phone": this.user.phone
            }).then((response) => {
                this.showpopup("auth");
            }).catch((response) => {
                notify(`Неправильные данные API ID/HASH, либо Вы уже авторизованы!`, 1);
            });
        },
        onKeyDown(index, event) {
            const key = event.key;

            // Разрешаем только цифры
            if (/^\d$/.test(key)) {
                // Записываем цифру
                this.code[index] = key;

                // Переход к следующему input
                this.$nextTick(() => {
                    if (index < this.code.length - 1) {
                        this.$refs.otpRefs[index + 1].focus();
                    } else {
                        event.target.blur();
                    }
                });

                // Отменяем дефолтный ввод (чтобы не дублировалась цифра)
                event.preventDefault();
            }

            // Обработка Backspace
            if (key === "Backspace") {
                if (this.code[index] === "") {
                    if (index > 0) {
                        this.$refs.otpRefs[index - 1].focus();
                    }
                } else {
                    this.code[index] = ""; // просто удаляем текущую цифру
                }

                event.preventDefault();
            }
        },
        async checkCode () {
            if (this.code.join("").length < 5) return notify("Введите полностью код!", 1);
            this.hidepopup();

            let code = parseInt(this.code.join(""), 10);

            await axios.post(this.backend + "auth/code", {
                "code": code
            }).then((response) => {
                console.log(response);
                if (response["data"]["next"] === "end") return notify("Аккаунт телеграмма успешно авторизован!");
                else this.showpopup("password");
            }).catch((response) => {
                this.notify(`Непредвиденная ошибка! ${response}`, 1);
            })
        },
        async checkPassword () {
            if (!this.password) return notify("Пароль не может быть пустым", 1);

            await axios.post(this.backend + "auth/password", {
                "password": this.password
            }).then((response) => {
                this.hidepopup();
                return notify("Аккаунт телеграмма успешно авторизован!");
            }).catch((response) => {
                this.notify(`Непредвиденная ошибка! ${response}`, 1);
            })
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
    <div class="notification_container"></div>
    <div class="popup password">
        <div>
            <img src="/lock.webp" alt="LOCK EMOJI">
            <div class="popup_auth_title">Введите резервный пароль</div>
            <div class="popup_auth_description">Этот пароль используется для двухэтапной аутентификации</div>
            <input v-model="password" type="text">
            <hr>
            <div class="popup_auth_buttons">
                <button @click="hidepopup" class="popup_auth_buttons_cancel">Назад</button>
                <button class="popup_auth_buttons_send" @click="checkPassword">Отправить</button>
            </div>
        </div>
    </div>
    <div class="popup auth">
        <div>
            <img src="/telegram.svg" alt="TG LOGO">
            <div class="popup_auth_title">Введите верификационный код</div>
            <div class="popup_auth_description">Мы отправили код Вам в Telegram</div>
            <div class="popup_auth_fields">
                <input
                    v-for="(digit, index) in code"
                    :key="index"
                    ref="otpRefs"
                    v-model="code[index]"
                    type="text"
                    maxlength="1"
                    inputmode="numeric"
                    @keydown="onKeyDown(index, $event)"
                >
            </div>
            <hr>
            <div class="popup_auth_buttons">
                <button @click="hidepopup" class="popup_auth_buttons_cancel">Назад</button>
                <button class="popup_auth_buttons_send" @click="checkCode">Отправить</button>
            </div>
        </div>
    </div>
    <panel-nav-component>
        <div class="nav_main_title">Панель управления</div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Настройки бота</div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>Токен бота</div>
                    <input @blur="saveToken" id="botToken" v-model="user.bot_token" type="text" placeholder="Введите токен бота">
                </div>
            </div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>API ID</div>
                    <input @blur="saveID" id="apiID" v-model="user.api_id" type="text" placeholder="Введите API ID">
                </div>
                <div>
                    <div>API Hash</div>
                    <input @blur="saveHash" id="apiHash" v-model="user.api_hash" type="text" placeholder="Введите API Hash">
                </div>
            </div>
        </div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Подтверждение номера</div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>Номер телефона</div>
                    <input v-model="user.phone" type="text" placeholder="+7XXXXXXXXXX">
                </div>
            </div>
            <button @click="sendCode">Отправить код подтверждения</button>
        </div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Управление группами</div>
            <div class="nav_main_block_input_container">
                <div>
                    <div>ID группы</div>
                    <input v-model="newGroupID" type="text" id="newGroup" placeholder="Введите ID группы">
                </div>
            </div>
            <button @click="addGroup">Добавить группу</button>
            <div class="nav_main_block_list">
                <div @click="removeGroup(group.id)" v-for="group in user?.groups">
                    <div>{{group.group_id}}</div>
                    <i class="fa-regular fa-circle-xmark"></i>
                </div>
            </div>
        </div>
    </panel-nav-component>
</template>

<style scoped>

</style>