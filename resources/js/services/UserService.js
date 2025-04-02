import axios from "axios";

export class UserService {
    static serverUrl = import.meta.env.VITE_BASE_URL;

    static currentUser() {
        let url = `${this.serverUrl}/users/me`
        return axios.get(url)
    }
    static getUsers()
    {
        let url = `${this.serverUrl}/users`;
        return axios.get(url)
    }
    static getManagers()
    {
        let url = `${this.serverUrl}/users/managers`;
        return axios.get(url)
    }
    // static getUserStatistic()
    // {
    //     let url = `${this.serverUrl}/user/statistics`;
    //     return axios.get(url)
    // }
}
