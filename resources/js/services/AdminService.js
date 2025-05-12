import axios from "axios";

export class AdminService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getUsers(page) {
        let url = `${this.serverUrl}/admin/users?page=` + page;
        return axios.get(url);
    }
    static getBots(page) {
        let url = `${this.serverUrl}/admin/bots?page=` + page;
        return axios.get(url);
    }
    static getAdminBots() {
        let url = `${this.serverUrl}/admin/bots`;
        return axios.get(url);
    }
    static createUser(user) {
        let url = `${this.serverUrl}/admin/user/add`;
        return axios.post(url, user)
    }
}
