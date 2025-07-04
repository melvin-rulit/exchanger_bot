import axios from "axios";

export class RoleService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static getRoles() {
        let url = `${this.serverUrl}/admin/roles`;
        return axios.get(url);
    }
    static createUser(user) {
        let url = `${this.serverUrl}/admin/user/add`;
        return axios.post(url, user)
    }
    static deleteUser(user) {
        let url = `${this.serverUrl}/admin/user/add`;
        return axios.post(url, user)
    }
}
