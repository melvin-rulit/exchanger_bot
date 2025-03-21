import axios from "axios";

export class BotsService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getUserBots() {
        let url = `${this.serverUrl}/user/bots`;
        return axios.get(url);
    }
    static getAdminBots() {
        let url = `${this.serverUrl}/admin/bots`;
        return axios.get(url);
    }
    static store(send_bot) {
        let url = `${this.serverUrl}/user/bot/add`;
        return axios.post(url, send_bot)
    }
    static delete(value) {
        let url = `${this.serverUrl}/user/bot/${value.id}`;
        return axios.delete(url, value)
    }
}
