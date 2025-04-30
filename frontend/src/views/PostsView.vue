<script>
import PanelNavComponent from "../components/PanelNavComponent.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import notify from "../utils.js";
import axios from "axios";

export default {
    name: "PostsView.vue",
    components: {PanelNavComponent, VueDatePicker},
    data () {
        return {
            photo: "",
            confirming: false,
            date: "",
            time: {hours:0, minutes:0, seconds:0},
            dateTime: "",
            repeat: 0,
            not_repeat: "count",
            not_repeat_date: "",
            repeat_time: "",
            count: "",
            editedPost: {},
            timeKey: 0,
            newPostText: "",
        }
    },
    computed: {
        // newPostText () {
        //     return document.querySelector('.post .newPost_text');
        // },
        user () {
            return this.$store.state.user;
        },
        storage () {
            return this.$store.state.storage;
        },
        backend () {
            return this.$store.state.backend;
        },
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
        ondragover(ev) {
            ev.preventDefault();
        },
        drop (ev) {
            ev.preventDefault();
            const files = ev.dataTransfer.files;

            this.photo = files[0];
            document.querySelectorAll(".newPost_image>img").forEach((el) => el.src = URL.createObjectURL(files[0]));
        },
        addimg (ev) {
            let file = ev.target.files[0];
            if (file && file.type.startsWith("image/")) {
                this.photo = file;
                console.log(URL.createObjectURL(file));
                document.querySelectorAll(".newPost_image>img").forEach((el) => el.src = URL.createObjectURL(file));
            }
            this.$refs.photoInput.value = "";
        },
        checkpost () {
            // let body = document.querySelector(".popup.post .newPost_text").innerHTML;
            // let body = document.querySelector(".popup.post .newPost_text").innerText;
            let body = this.newPostText;
            if (body.length < 10) return this.notify("Количество символов в теле поста должно быть больше 10!", 1)

            this.notify ("Пост успешно сохранен! Выберите время публикации.")
            this.hidepopup();
            this.showpopup('schedule')
        },
        async checkedit () {
            document.querySelector(".popup.edit_post .selectDate_publishDate_calendar").style.border = "";
            document.querySelector(".popup.edit_post .selectDate_repeat_main_title .dp__main input").style.border = "";
            document.querySelector(".popup.edit_post .selectDate_repeat_interval>input").style.border = "";

            // let body = document.querySelector(".popup.edit_post .newPost_text").innerHTML;
            // let body = document.querySelector(".popup.edit_post .newPost_text").innerText;
            let body = this.newPostText;
            if (body.length < 10) return this.notify("Количество символов в теле поста должно быть больше 10!", 1);

            if (!this.date) {
                document.querySelector(".popup.edit_post .selectDate_publishDate_calendar").style.border = "2px #f44336 solid";
                return this.notify("Выберите дату публикации!", 1);
            }
            if (this.repeat) {
                if (this.not_repeat === "date" && !this.editedPost.end_date) {
                    document.querySelector(".popup.edit_post .selectDate_repeat_main_title .dp__main input").style.border = "1px #f44336 solid"
                    return this.notify("Выберите дату окончания!", 1);
                }
                if (!this.editedPost.time_repeat) this.editedPost.time_repeat = this.user.time_repeat;
                if (this.editedPost.time_repeat < this.user.time_repeat) {
                    document.querySelector(".popup.edit_post .selectDate_repeat_interval>input").style.border = "1px #f44336 solid";
                    return this.notify(`Время повтора не может быть меньше ${this.user.time_repeat} минуты!`, 1);
                }
                if (this.not_repeat === "count" && this.editedPost.end_count < 1) this.editedPost.end_count = 1;
            }

            let datetime = new Date(this.date);
            datetime.setHours(this.time.hours + 3);
            datetime.setMinutes(this.time.minutes);

            if (this.not_repeat === "date") {
                this.editedPost.end_date = new Date(this.editedPost.end_date);
                this.editedPost.end_date.setHours(this.editedPost.end_date.getHours() + 3);
                this.editedPost.end_date = this.editedPost.end_date.toISOString();
            }

            let original = this.user.posts.filter(el => el.id === this.editedPost.id)[0];
            if (!original) return this.notify("401. Ошибка авторизации", 1);

            // this.editedPost["text"] = document.querySelector(".edit_post .newPost_text").innerHTML;
            // this.editedPost["text"] = document.querySelector(".edit_post .newPost_text").innerText;

            let formdata = new FormData();
            for (let key in this.editedPost) {
                console.log(key);
                if (key === "attachment") {
                    if (!this.editedPost[key] && this.photo) formdata.append("attachment", this.photo);
                    if (!this.editedPost[key] && !this.photo) formdata.append("attachment", null);
                }
                else if (key === "date") formdata.append("date", datetime.toISOString());
                else if (key === "time_repeat" && !this.repeat) formdata.append("time_repeat", null);
                else if (key === "end_date" && !this.repeat) formdata.append("end_date", null);
                else if (key === "end_count" && !this.repeat) formdata.append("end_count", null);
                else if (this.editedPost[key] !== original[key]) formdata.append(key, this.editedPost[key]);
            }
            for (let pair of formdata.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            await axios.post(this.backend + "post/" + this.editedPost.id, formdata).then((response) => {
                if (response.data["error"]) return notify (response.data["error"], 1);

                notify(`Пост №${this.editedPost.id} успешно обновлен!`);
                axios.post(this.backend + "auth/profile").then((response) => {
                    this.$store.dispatch("updateUser", response.data);
                    this.hidepopup();
                })
            }).catch((response) => {
                notify(`Непредвиденная ошибка! ${response}`, 1);
            })
        },
        saveSchedule () {
            if (!this.user.subscription) {
                let data = new FormData();
                data.append("text", this.newPostText);
                // data.append("text", this.newPostText.innerHTML.replace(/<br\s*\/?>/gi, '\n')
                //     .replace(/<\/div>/gi, '\n')
                //     .replace(/<div.*?>/gi, ''));
                if (this.photo) data.append("attachment", this.photo);

                axios.post(this.backend + "post", data).then((response) => {
                    notify("Новый пост успешно добавлен!");
                    this.hidepopup();

                    axios.post(this.backend + "auth/profile").then((response) => {
                        this.$store.dispatch("updateUser", response.data);
                    })
                }).catch((response) => {
                    notify(`Произошла ошибка в ходе сохранения поста! ${response}`, 1);
                })
                return "";
            }

            document.querySelector(".selectDate_publishDate_calendar").style.border = "";
            document.querySelector(".selectDate_repeat_main_title .dp__main input").style.border = "";
            document.querySelector(".selectDate_repeat_interval>input").style.border = "";

            if (!this.date) {
                // document.querySelector(".selectDate_publishDate_calendar .dp__menu_inner").style.background = "repeating-linear-gradient(45deg, #F4433624 0px, #F4433624 5px, transparent 5px, transparent 30px)"
                document.querySelector(".selectDate_publishDate_calendar").style.border = "2px #f44336 solid";
                return this.notify("Выберите дату публикации!", 1);
            }
            if (this.repeat) {
                if (this.not_repeat === "date" && !this.not_repeat_date) {
                    document.querySelector(".selectDate_repeat_main_title .dp__main input").style.border = "1px #f44336 solid"
                    return this.notify("Выберите дату окончания!", 1);
                }
                if (!this.repeat_time) this.repeat_time = this.user.time_repeat;
                if (this.repeat_time < this.user.time_repeat) {
                    document.querySelector(".selectDate_repeat_interval>input").style.border = "1px #f44336 solid";
                    return this.notify(`Время повтора не может быть меньше ${this.user.time_repeat} минуты!`, 1);
                }
                if (this.not_repeat === "count" && this.count < 1) this.count = 1;
            }

            let datetime = new Date(this.date);
            datetime.setHours(this.time.hours + 3);
            datetime.setMinutes(this.time.minutes);

            let notDate = 0;
            if (this.not_repeat === "date") {
                notDate = new Date(this.not_repeat_date);
                notDate.setHours(notDate.getHours() + 3);
            }

            let data = new FormData();
            data.append("text", this.newPostText);
            // data.append("text", this.newPostText.innerHTML.replace(/<br\s*\/?>/gi, '\n')
            //     .replace(/<\/div>/gi, '\n')
            //     .replace(/<div.*?>/gi, ''));
            if (this.photo) data.append("attachment", this.photo);
            if (this.date) data.append("date", datetime.toISOString());
            if (this.repeat) {
                data.append("time_repeat", this.repeat_time);
                if (this.not_repeat === "date") data.append("end_date", notDate.toISOString());
                else if (this.not_repeat === "count") data.append("end_count", this.count);
            }

            axios.post(this.backend + "post", data).then((response) => {
                if (response.data["error"]) return notify (response.data["error"], 1);
                notify("Новый пост успешно добавлен!");

                this.hidepopup();
                axios.post(this.backend + "auth/profile").then((response) => {
                    this.$store.dispatch("updateUser", response.data);
                })
            }).catch((response) => {
                notify(`Произошла ошибка в ходе сохранения поста! ${response}`, 1);
            })
        },
        updateEdit (post) {
            this.editedPost = { ...post };
            this.photo = null;
            this.showpopup('edit_post');

            let datetime = new Date(post.date + "+03:00");
            this.date = new Date(datetime);
            this.time = {hours:datetime.getHours(), minutes:datetime.getMinutes(), seconds:0};
            this.timeKey += 1;

            if (post.time_repeat) this.repeat = true;
            if (post.end_count) this.not_repeat = "count";
            else if (post.end_date) this.not_repeat = "date";
        }
    },
    async mounted () {
        axios.defaults.withCredentials = true;
        // document.querySelectorAll('.popup').forEach(el => {
        //     el.addEventListener("click", (ev) => {
        //         if (ev.srcElement === el) el.classList.remove("active");
        //     });
        // })

        document.querySelectorAll('textarea').forEach(t => {
            t.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        });
    }
}
</script>

<template>
    <div class="notification_container"></div>
    <div class="popup wait">
        <div>
            <div class="nav_main_block">
                <div>
                    <i class="fa-regular fa-clock"></i>
                    <div>Отправьте пост боту<br>в Telegram!</div>
                </div>
            </div>
            <div class="popup_buttons">
                <button @click="hidepopup" class="popup_cancel">Отмена</button>
            </div>
        </div>
    </div>
    <div class="popup schedule">
        <div>
            <div class="selectDate">
                <div v-if="!user.subscription" class="locked">
                    <div>
                        <img src="/lock.webp" alt="">
                        <div class="locked_title">Это премиум функция!</div>
                        <div class="locked_description">Приобретите <a href="/balance">по ссылке</a> для возможности<br>выбрать конкретную дату публикации</div>
                    </div>
                </div>
                <div class="selectDate_title">Расписание</div>
                <div class="selectDate_publishDate">
                    <div class="selectDate_publishDate_title">Выберите дату публикации</div>
                    <div class="selectDate_publishDate_calendar">
                        <VueDatePicker v-model="date" :min-date="new Date()" inline auto-apply :enable-time-picker="false"/>
                        <VueDatePicker v-model="time" inline auto-apply :time-picker="true" :enable-time-picker="true" :enable-seconds="false"/>
                    </div>
                </div>
                <div class="selectDate_repeat">
                    <div class="selectDate_repeat_header">
                        <div class="selectDate_repeat_title">
                            <input v-model="repeat" type="checkbox" name="" id="selectDate_repeat">
                            <label for="selectDate_repeat">Повторять</label>
                        </div>
                        <div :style="repeat ? '' : 'color:gray;'" class="selectDate_repeat_interval">
                            Через каждые <input :disabled="!repeat" type="number" v-model="repeat_time" :min="this.user.time_repeat" :placeholder="this.user.time_repeat"> минут
                        </div>
                    </div>
                    <div :style="repeat ? '' : 'color:gray;'" class="selectDate_repeat_main">
                        <div class="selectDate_repeat_main_title_text">Не повторять после:</div>
                        <div>
                            <div class="selectDate_repeat_main_title">
                                <div>
                                    <input v-model="not_repeat" :disabled="!repeat" value="date" type="radio"
                                           name="selectDate_repeat_not" id="selectDate_repeat_not_date">
                                    <label for="selectDate_repeat_not_date">Определенной даты</label>
                                </div>
                                <div>
                                    <VueDatePicker :min-date="new Date()" v-model="not_repeat_date" auto-apply :disabled="!repeat || not_repeat !== 'date'"/>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <div class="selectDate_repeat_main_title">
                                <div>
                                    <input v-model="not_repeat" :disabled="!repeat" value="count" type="radio"
                                           name="selectDate_repeat_not" id="selectDate_repeat_not_count">
                                    <label for="selectDate_repeat_not_count">Определенного количества раз</label>
                                </div>
                                <div class="selectDate_repeat_main_count_input">
                                    <input v-model="count" type="number" :disabled="!repeat || not_repeat !== 'count'" min="1" placeholder="1"><label for="">раз</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popup_buttons">
                <button @click="hidepopup" class="popup_cancel">Отмена</button>
                <button @click="saveSchedule" class="newPost_button">Сохранить</button>
            </div>
        </div>
    </div>
    <div class="popup edit_post">
        <div>
            <div class="edit_post_main">
                <div class="newPost">
                    <div class="newPost_groupName">Название группы</div>
                    <div class="newPost_image">
                        <img :style="confirming ? 'filter:blur(3px);' : ''" @click="confirming = true" :src="storage + editedPost.attachment" v-show="editedPost.attachment || photo" alt="">
                        <div v-if="confirming" class="newPost_image_confirm">
                            <button @click="confirming = 0; editedPost.attachment = null; photo = null" class="newPost_button delete">Удалить</button>
                            <button @click="confirming = 0" class="newPost_button cancel">Отмена</button>
                        </div>
                        <label @drop="drop" @dragover="ondragover" class="newPost_image_borders" for="photo">
                            <div>
                                <i class="fa-regular fa-image"></i>
                                <div>Выберите или перетащите картинку для поста</div>
                            </div>
                        </label>
                        <input ref="photoInput" @change="addimg" style="display:none" type="file" id="photo">
                    </div>
<!--                    <div class="newPost_text" v-html="editedPost.text?.replace(/\n/g, '<br>')" contenteditable></div>-->
<!--                    <textarea name="" id="" cols="30" rows="10"></textarea>-->
                    <textarea v-model="editedPost['text']" class="newPost_text" name="" id=""></textarea>
                    <div class="newPost_statistics"><i class="fa-solid fa-eye"></i><div>100K</div><div>20:31</div></div>
                </div>
                <div class="selectDate">
                    <div class="selectDate_title">Расписание</div>
                    <div class="selectDate_publishDate">
                        <div class="selectDate_publishDate_title">Выберите дату публикации</div>
                        <div class="selectDate_publishDate_calendar">
                            <VueDatePicker v-model="date" :min-date="new Date()" inline auto-apply :enable-time-picker="false"/>
                            <VueDatePicker :key="timeKey" v-model="time" inline auto-apply :time-picker="true" :enable-time-picker="true" :enable-seconds="false"/>
                        </div>
                    </div>
                    <div class="selectDate_repeat">
                        <div class="selectDate_repeat_header">
                            <div class="selectDate_repeat_title">
                                <input v-model="repeat" type="checkbox" name="" id="selectDate_repeat">
                                <label for="selectDate_repeat">Повторять</label>
                            </div>
                            <div :style="repeat ? '' : 'color:gray;'" class="selectDate_repeat_interval">
                                Через каждые <input :disabled="!repeat" type="number" v-model="editedPost.time_repeat" :min="this.user.time_repeat" :placeholder="this.user.time_repeat"> минут
                            </div>
                        </div>
                        <div :style="repeat ? '' : 'color:gray;'" class="selectDate_repeat_main">
                            <div class="selectDate_repeat_main_title_text">Не повторять после:</div>
                            <div>
                                <div class="selectDate_repeat_main_title">
                                    <div>
                                        <input v-model="not_repeat" :disabled="!repeat" value="date" type="radio"
                                               name="selectDate_repeat_not" id="selectDate_repeat_not_date">
                                        <label for="selectDate_repeat_not_date">Определенной даты</label>
                                    </div>
                                    <div>
                                        <VueDatePicker :min-date="new Date()" v-model="editedPost.end_date" auto-apply :disabled="!repeat || not_repeat !== 'date'"/>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <div class="selectDate_repeat_main_title">
                                    <div>
                                        <input v-model="not_repeat"  :disabled="!repeat" value="count" type="radio"
                                               name="selectDate_repeat_not" id="selectDate_repeat_not_count">
                                        <label for="selectDate_repeat_not_count">Определенного количества раз</label>
                                    </div>
                                    <div class="selectDate_repeat_main_count_input">
                                        <input v-model="editedPost.end_count" type="number" :disabled="!repeat || not_repeat !== 'count'" min="1" placeholder="1"><label for="">раз</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="popup_buttons">
                <button @click="hidepopup" class="popup_cancel">Отмена</button>
                <button @click="checkedit" class="newPost_button">Сохранить</button>
            </div>
        </div>
    </div>
    <div class="popup post">
        <div>
            <div class="newPost">
                <div class="newPost_groupName">Название группы</div>
                <div class="newPost_image">
                    <img :style="confirming ? 'filter:blur(3px);' : ''" @click="confirming = true" v-show="photo" alt="">
                    <div v-if="confirming" class="newPost_image_confirm">
                        <button @click="confirming = 0; photo = null" class="newPost_button delete">Удалить</button>
                        <button @click="confirming = 0" class="newPost_button cancel">Отмена</button>
                    </div>
                    <label @drop="drop" @dragover="ondragover" class="newPost_image_borders" for="photo">
                        <div>
                            <i class="fa-regular fa-image"></i>
                            <div>Выберите или перетащите картинку для поста</div>
                        </div>
                    </label>
                    <input ref="photoInput" @change="addimg" style="display:none" type="file" id="photo">
                </div>
<!--                <div class="newPost_text" contenteditable aria-placeholder="123"></div>-->
                <textarea v-model="newPostText" class="newPost_text" name="" id=""></textarea>
                <div class="newPost_statistics"><i class="fa-solid fa-eye"></i><div>100K</div><div>20:31</div></div>
            </div>
            <div class="popup_buttons">
                <button @click="hidepopup" class="popup_cancel">Отмена</button>
                <button @click="checkpost" class="newPost_button">Сохранить</button>
            </div>
        </div>
    </div>
    <panel-nav-component>
        <div class="nav_main_title">Мои посты</div>
        <div class="nav_main_block">
            <div class="nav_main_block_title">Новый пост</div>
            <div class="nav_main_buttons">
                <button @click="showpopup('post'); newPostText.focus()">Создать в Web</button>
<!--                <button @click="showpopup('wait')">Отправить в Telegram</button>-->
            </div>
        </div>
        <div class="nav_main_block" v-if="user.subscription">
            <div class="nav_main_block_title">Запланированные посты</div>
            <div class="nav_main_list">
                <div v-for="post in user?.posts" @click="updateEdit(post)">
                    <div class="nav_main_list_img">
                        <img v-if="post.attachment" :src="storage + post.attachment" alt="Ошибка загрузки">
                        <div v-else><p>Нет изображения</p></div>
                    </div>
                    <div class="nav_main_list_info">
                        <div lang="ru">
                            {{post.text}}
                        </div>
<!--                        <div class="nav_main_list_buttons">-->
<!--                            <i class="fa-solid fa-pencil"></i>-->
<!--                            <i class="fa-solid fa-trash-can"></i>-->
<!--                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </panel-nav-component>
</template>

<style scoped>

</style>