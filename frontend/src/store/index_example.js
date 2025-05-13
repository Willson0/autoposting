import { createStore } from 'vuex';

const store = createStore({
    state: {
        user: {},
        backend: "https://exobloom.ru/api/",
        storage: "https://exobloom.ru/storage/",
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
