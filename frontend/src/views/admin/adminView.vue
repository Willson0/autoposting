<script>

import notify, {formatDate, removeLoading, togglePopup} from "../../utils.js";
import adminnav from "../../components/adminnav.vue";
import axios from "axios";
export default {
    name: "adminView",
    data () {
        return {
            taskselected: [],
            data: {},
            perf: "accounts",
            tasks: [],
            task: -1,
            updtask: {},
            time_repeat: 0,
            cooldown: 0,
            price: 0,
        };
    },
    components: {
        adminnav,
    },
    computed: {
        user () {
            return this.$store.state.user;
        },
        backend () {
            return this.$store.state.backend;
        }
    },
    methods: {
        notify,
        formatDate,
        togglePopup,
        canvinit() {
            let canv = document.querySelector(".admin_main_subscriptions canvas");

            canv.height = canv.parentElement.clientHeight;
            canv.width = canv.parentElement.clientWidth;

            let ctx = canv.getContext("2d");

            let months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            let values = this.data[this.perf];

            let minvalue = Math.floor(Math.min(...values) / 10) * 10;
            let maxvalue = Math.ceil(Math.max(...values) / 10) * 10;
            let countvalues = maxvalue/10 - minvalue/10 + 1;

            ctx.strokeStyle = "#4c4c71";
            ctx.lineWidth = 1;
            ctx.font = "10px Poppins";
            ctx.fillStyle="white";
            ctx.textAlign = "right";

            let valuesColumn = 30;
            let margin = 10;

            for (let i = 0; i < countvalues; i ++) {
                let y = margin + 14 + 5 + ((canv.height-(margin+14+5)*2 - 20)/(countvalues-1)*(i));
                ctx.fillText(maxvalue - i*10, valuesColumn, y);
            }

            ctx.textAlign = "center";
            ctx.fillStyle = "red";
            let pointarr = [];
            for (let i = 0; i < months.length; i++) {
                let month = months[i];
                let x = (canv.width/months.length)*(i) + valuesColumn*2;

                ctx.beginPath();
                ctx.moveTo(x, margin);
                ctx.lineTo(x, canv.height-40);
                ctx.closePath();
                ctx.stroke();

                ctx.fillStyle = "white";
                ctx.fillText(month, x, canv.height-20);

                ctx.fillStyle = "#389466";
                let pointy = margin + 14 + ((canv.height-(margin+14+5)*2-20)/(countvalues-1))*((maxvalue-values[i])/10);
                ctx.arc(x, pointy, 4, 0, 2*Math.PI)
                ctx.fill();

                pointarr.push({
                    x: x,
                    y: pointy
                });
            }

            ctx.strokeStyle = "#389466";
            ctx.lineWidth = 1;

            for (let i = 0; i < pointarr.length-1; i++) {
                ctx.beginPath();
                ctx.moveTo(pointarr[i].x, pointarr[i].y);
                ctx.lineTo(pointarr[i+1].x, pointarr[i+1].y);
                ctx.stroke();
            }

            canv.addEventListener("mousemove", (ev) => {
                let gapcolumn = pointarr[1].x - pointarr[0].x;
                for (let i = 0; i < pointarr.length; i++) {
                    if (Math.abs((ev.clientX-canv.getBoundingClientRect().x)-pointarr[i].x)<gapcolumn/2) {
                        let info = document.querySelector(".admin_main_subscription_info_container");
                        info.style.top = pointarr[i].y + 'px';
                        info.style.left = pointarr[i].x + 'px';
                        info.querySelector(".admin_main_subscription_info").innerHTML = values[i];

                        info.style.opacity = 1;
                    }
                }
            });

            canv.addEventListener("mouseleave", (ev) => {
                let info = document.querySelector(".admin_main_subscription_info_container");
                info.style.opacity = '';
            })
        },
        showinfo (cl, text) {
            document.querySelectorAll(`.${cl}`).forEach((el) => {
                let info;

                el.addEventListener("mouseenter", () => {
                    let child = document.body.appendChild(document.createElement("div"));
                    child.innerHTML = `
                    <div class="admin_main_tasks_el_edit_info">
                        <div class="admin_main_tasks_el_edit_info_triangle"></div>
                        <div class="admin_main_tasks_el_edit_info_main">
                            ${text}
                        </div>
                    </div>
                `;
                    let div = child.querySelector(".admin_main_tasks_el_edit_info")
                    div.style.top = `${el.getBoundingClientRect().top + window.scrollY-30}px`;
                    div.style.left = `${el.getBoundingClientRect().left+el.clientWidth/2}px`;

                    div.style.opacity = 1;
                    info = div;
                });

                el.addEventListener("mouseleave",() => {
                    info.style.opacity = 0;
                    setTimeout(() => {
                        info.remove();
                    }, 200)
                })
            })
        },
        async fetchstats() {
            await fetch (this.backend + "admin/stats", {
                method: "GET",
                credentials: "include",
            }).then((response) => {
                if (response.status === 401) this.$router.push("/admin/login");
                if (!response.ok) return alert ("Error");
                return response.json();
            }).then((response) => {
                this.data = response;

                this.time_repeat = response.settings.time_repeat;
                this.cooldown = response.settings.cooldown;
                this.price = response.settings.price;
                removeLoading();

                this.canvinit();
            });
        },
        async sendSettings (field, value) {
            await axios.post(this.backend + "admin/settings", {
                [field]:value,
            }).then((response) => {
                alert("Успешно сохранено!");
            })
        }
    },
    async mounted() {
        axios.defaults.withCredentials = true;
        window.addEventListener("resize", this.canvinit);

        this.showinfo("fa-rotate-right", "reload");
        this.showinfo("admin_main_tasks_el_edit", "Edit task");
        this.showinfo("fa-x", "Remove");

        document.body.style.backgroundColor = "#12121c";

        await this.fetchstats();
    }
}
</script>

<template>
    <div class="admin_main_tasks_popup popup">
        <div>
            <h2 v-if="task !== -1"><i class="fa-solid fa-pen"></i>&nbsp;Edit task #{{updtask.id}}</h2>
            <h2 v-else>New task</h2>
            <div>
                <label>
                    Title:
                    <input type="text" v-model="updtask.title">
                </label>
            </div>
            <div>
                <label>
                    Desciription:
                    <input type="text" v-model="updtask.task">
                </label>
            </div>
            <div>
                <label>
                    Deadline:
                    <input type="date" v-model="updtask.deadline">
                </label>
            </div>
            <div class="admin_main_tasks_popup_buttons">
                <button v-if="task !== -1" @click="deletetask()" style="background-color:transparent; border: 1px solid rgba(255,255,255,0.1)">Delete task</button>
                <button v-if="task !== -1" @click="savetask()">Save task</button>
                <button v-else @click="createtask()">Create task</button>
            </div>
        </div>
    </div>
    <adminnav>
        <div class="admin_main_subscriptions">
            <div class="admin_main_subscription_header">
                <div class="admin_main_subscription_title">
                    <h2>Пользователи</h2>
                    <h1>Эффективность</h1>
                </div>
            </div>
            <div class="admin_main_subscriptions_canvas">
                <canvas>Your browser is not supported canvas.</canvas>
                <div class="admin_main_subscription_info_container">
                    <div class="admin_main_subscription_info_triangle"></div>
                    <div class="admin_main_subscription_info"></div>
                </div>
            </div>
        </div>
        <div class="admin_main_statistics">
            <div>
                <div class="admin_main_statistics_el_main">
                    <div style="background:linear-gradient(45deg, #3abd2a, #0e880a);" class="admin_main_statistics_el_main_img">
                        <i class="fa-solid fa-money-bill"></i>
                    </div>
                    <div class="admin_main_statistics_el_main_title">
                        <h4>Платные функции</h4>
                        <h3>{{ data.money }} платных функций</h3>
                    </div>
                </div>
                <div class="admin_main_statistics_el_footer">
                    <div class="admin_main_statistics_el_footer_line"></div>
                    <div class="admin_main_statistics_el_footer_info">
                        <i class="fa-regular fa-calendar"></i>
                        Куплено за последние 30 дней
                    </div>
                </div>
            </div>
            <div>
                <div class="admin_main_statistics_el_main">
                    <div style="background:linear-gradient(45deg, #3abd2a, #0e880a);" class="admin_main_statistics_el_main_img">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div class="admin_main_statistics_el_main_title">
                        <h4>Новых постов</h4>
                        <h3>{{ data.money30 }} постов</h3>
                    </div>
                </div>
                <div class="admin_main_statistics_el_footer">
                    <div class="admin_main_statistics_el_footer_line"></div>
                    <div class="admin_main_statistics_el_footer_info">
                        <i class="fa-regular fa-calendar"></i>
                        Создано за последние 30 дней
                    </div>
                </div>
            </div>
            <div>
                <div class="admin_main_statistics_el_main">
                    <div class="admin_main_statistics_el_main_img">
                        <div></div>
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="admin_main_statistics_el_main_title">
                        <h4>Регистрации</h4>
                        <h3>{{ data.logsPerDay }} пользователей</h3>
                    </div>
                </div>
                <div class="admin_main_statistics_el_footer">
                    <div class="admin_main_statistics_el_footer_line"></div>
                    <div class="admin_main_statistics_el_footer_info">
                        <i class="fa-solid fa-rotate-right"></i>
                        Зарегистрировались за последние 30 дней
                    </div>
                </div>
            </div>
            <div>
                <div class="admin_main_statistics_el_main">
                    <div class="admin_main_statistics_el_main_img">
                        <div></div>
                        <i class="fa-regular fa-message"></i>
                    </div>
                    <div class="admin_main_statistics_el_main_title">
                        <h4>Отправки</h4>
                        <h3>{{ data.usersPerDay }} сообщений</h3>
                    </div>
                </div>
                <div class="admin_main_statistics_el_footer">
                    <div class="admin_main_statistics_el_footer_line"></div>
                    <div class="admin_main_statistics_el_footer_info">
                        <i class="fa-solid fa-rotate-right"></i>
                        Отправлено за последние 30 дней
                    </div>
                </div>
            </div>
        </div>
        <div class="admin_main_tasks_management">
            <div class="admin_main_tasks">
                <div>
                    <label for="time">Мин. время повторов (мин): </label>
                    <div>
                        <input id="number" v-model="time_repeat" type="text">
                        <button @click="sendSettings('time_repeat', time_repeat)">Сохранить</button>
                    </div>
                </div>
                <div>
                    <label for="time">Интервал между постами от одного бота (сек): </label>
                    <div>
                        <input id="number" v-model="cooldown" type="text">
                        <button @click="sendSettings('cooldown', cooldown)">Сохранить</button>
                    </div>
                </div>
                <div>
                    <label for="time">Стоимость премиум-функции: </label>
                    <div>
                        <input id="number" v-model="price" type="text">
                        <button @click="sendSettings('price', price)">Сохранить</button>
                    </div>
                </div>
            </div>
            <div class="admin_main_tasks">
                <div class="admin_main_tasks_header">
                    <div class="admin_main_tasks_title">
                        <h1>Топ популярности</h1>
                        <h2>Ивенты</h2>
                    </div>
                </div>
                <div class="admin_main_tasks_main">
                    <div @click="$router.push('/admin/webinars/' + el.id)" v-for="el in data.webinars">
                        <div class="admin_main_tasks_el_text">
                            <div class="admin_main_tasks_el_title">
                                <span>{{ el.title }}</span>
                            </div>
                            <div class="admin_main_tasks_el_description">
                                {{ el.description }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </adminnav>
</template>

<style scoped>

</style>