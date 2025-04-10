import axios from "axios";

export default function notify (text, error) {
    let notifyContainer = document.querySelector(".notification_container");
    let div = document.createElement("div");

    if (error) {
        div.innerHTML = `<div class="notification error">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    <div>
                                        ${text}
                                    </div>
                                </div>`
    } else {
        div.innerHTML = `<div class="notification success">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <div>
                                        ${text}
                                    </div>
                                </div>`
    }
    notifyContainer.appendChild(div);

    let height = div.querySelector(".notification").getBoundingClientRect().height + 10;
    div.style.visibility = "visible";
    div.style.transform = `translateY(-${height}px)`;

    requestAnimationFrame(() => {
        div.style.transition = "0.2s";
        div.style.transform = "";
        div.style.height = height + "px";
    });

    setTimeout(() => {
        div.style.opacity = '0';
        setTimeout (() => {
            div.remove();
        }, 200);
    }, 5000);
}

export function addClickScroll (el) {
    let startX = 0;
    let startScroll = 0;
    let ismousedown = false;

    el.addEventListener("mousedown", (ev) => {
        ismousedown = true;
        el.style.cursor = "grabbing";

        startX = ev.pageX - el.offsetLeft;
        startScroll = el.scrollLeft;
    })

    let removeDefaultPrevent = (ev) => {
        ev.preventDefault();
    }
    let leavefunc = () => {
        ismousedown = false;
        el.style.cursor = "grab";

        document.querySelectorAll("a").forEach((el) => {
            el.addEventListener("click", removeDefaultPrevent);
            setTimeout(() => {
                el.removeEventListener("click", removeDefaultPrevent);
            }, 100);
        })
    };

    el.addEventListener("mouseup", () => {
        leavefunc();
    })
    el.addEventListener("mouseleave", () => {
        leavefunc();
    })
    el.addEventListener("mousemove", (ev) => {
        if (ismousedown) {
            ev.preventDefault();
            let x = ev.pageX - el.offsetLeft;
            let walk = (x - startX);
            el.scrollLeft = startScroll - walk;
        }
    })
    document.querySelectorAll("a").forEach((el) => {
        el.addEventListener("dragstart", (ev) => {
            ev.preventDefault();
        })
    });

}

export function parseLog (log, withdate = true) {
    let data = JSON.parse(log.data);
    console.log(data);
    let str = log.message

    console.log(str);
    if (data.user) str = str.replace('%user', `&nbsp;<a href='/admin/users/${data.user.id}'>${data.user.name}</a>&nbsp;`)
    if (data.order) str = str.replace('%order', `&nbsp;<a href='/admin/orders/${data.order.id}'>order #${data.order.id}</a>&nbsp;`);
    if (data.admin) str = str.replace('%admin', `&nbsp;<a href='/admin/admins#${data.admin.id}'>${data.admin.login}</a>&nbsp;` )
    if (data.product) str = str.replace('%product', `&nbsp;<a href='/admin/products/${data.product.id}'>${data.product.name}</a>&nbsp;` )
    if (data.task) str = str.replace('%task', `&nbsp;[${data.task.id}] ${data.task.title} (Desciription: ${data.task.task})&nbsp;` )

    if (withdate) str = `[${formatDate(log.created_at)}]&nbsp;` + str;
    return str;
}

export function formatDate(dateString, withTime = 1) {
    const date = new Date(dateString);
    const months = [
        'янв', 'фев', 'мар', 'апр', 'мая', 'июн',
        'июл', 'авг', 'сен', 'окт', 'ноя', 'дек'
    ];

    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear();
    const hour = String(date.getHours()).padStart(2, '0');
    const minute = String(date.getMinutes()).padStart(2, '0');

    if (withTime)
        return `${day} ${month} ${year} год, ${hour}:${minute}`;
    else return `${day} ${month} ${year} год`;
}

export function showLoading () {
    document.body.style.overflow = "hidden";
}

export function removeLoading() {
    let popup = document.querySelector(".loadPopup");
    document.body.style.overflow = "";
    popup.style.opacity = "0";
    setTimeout (() => {
        popup.style.display = "none";
    }, 200)
}

export function togglePopup(classname) {
    let el = document.querySelector(`.${classname}`);
    if (el.style.display === "") {
        el.style.display = "block";
        document.body.style.overflow = "hidden";

        el.addEventListener("click", (ev) => {
            if (ev.target === el) el.style.display = "";
        });
    } else {
        el.style.display = "";
        document.body.style.overflow = "";
    }
}

export function addTitle (cl, text) {
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
            div.style.top = `${el.getBoundingClientRect().top + window.scrollY - 30}px`;
            div.style.left = `${el.getBoundingClientRect().left + el.clientWidth / 2}px`;

            div.style.opacity = 1;
            info = div;
        });

        el.addEventListener("mouseleave", () => {
            info.style.opacity = 0;
            setTimeout(() => {
                info.remove();
            }, 200)
        })
    })
}

export function addLoad (cl) {
    let el = document.querySelector (`.${cl}`);
    el.style.position = "relative";
    el.style.overflow = "hidden";

    let loadbar = document.createElement("div");
    loadbar.className = "loadingbar";
    loadbar.innerHTML = `<div class="load"></div>`

    el.appendChild(loadbar);
}

export function removeLoad (cl) {
    console.log(`.${cl}>.loadingbar`);
    let el = document.querySelector (`.${cl}>.loadingbar`);
    el.remove();
}