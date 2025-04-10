import { createStore } from 'vuex';

const store = createStore({
    state: {
        user: {},
        backend: "http://127.0.0.1:8000/api/",
        storage: "http://127.0.0.1:8000/storage/",
    },
    mutations: {
        setUser(state, newValue) {
            state.user = newValue;
        },
    },
    actions: {
        updateUser ({ commit }, newValue) {
            commit('setUser', newValue);
        },
    },
});

export default store;