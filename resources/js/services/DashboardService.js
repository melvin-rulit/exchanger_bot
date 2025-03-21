import axios from "axios";

export class DashboardService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getDashboardInfo() {
        let url = `${this.serverUrl}/user/info`;
        return axios.get(url);
    }
}
